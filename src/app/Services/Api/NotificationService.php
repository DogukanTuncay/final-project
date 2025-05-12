<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\NotificationServiceInterface;
use App\Models\User;
use App\Models\UserNotificationSetting;
use App\Models\UserNotificationLog;
use App\Models\UserLogin;
use OneSignal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\BaseService;

class NotificationService implements NotificationServiceInterface
{
    /**
     * Bildirim için kullanıcı ID'lerini alır
     * 
     * @param int|array $userIds
     * @return array
     */
    private function getPlayerIds($userIds): array
    {
        if (is_array($userIds)) {
            $users = User::whereIn('id', $userIds)->get();
        } else {
            $users = User::where('id', $userIds)->get();
        }
        
        return $users->pluck('onesignal_player_id')
            ->filter() // Null veya boş player_id'leri filtreler
            ->toArray();
    }
    
    /**
     * Kullanıcıya bildirim gönderilip gönderilemeyeceğini kontrol eder
     * 
     * @param int $userId
     * @param string $notificationType
     * @return bool
     */
    private function canSendNotification(int $userId, string $notificationType): bool
    {
        // Kullanıcıyı bul
        $user = User::find($userId);
        
        if (!$user) {
            return false;
        }
        
        // Kullanıcının bildirim tipini alabileceğini kontrol et
        $notificationSettings = $user->notificationSettings()->first();
        
        if (!$notificationSettings) {
            // Kullanıcının bildirim ayarları yoksa varsayılan olarak gönderebiliriz
            return true;
        }
        
        if (!$notificationSettings->canReceiveNotificationType($notificationType)) {
            return false;
        }
        
        // Bildirim sıklığı kontrolü - son 24 saat içinde bu tipte bir bildirim gönderilmişse tekrar gönderme
        $cacheKey = "notification_cooldown:{$userId}:{$notificationType}";
        if (Cache::has($cacheKey)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Bildirim gönderimini loglar ve sıklık kontrolü için cache ekler
     * 
     * @param int $userId
     * @param string $notificationType
     * @param string $title
     * @param string $message
     * @return void
     */
    private function logNotification(int $userId, string $notificationType, string $title, string $message): void
    {
        // Bildirim gönderimini logla
        UserNotificationLog::create([
            'user_id' => $userId,
            'notification_type' => $notificationType,
            'title' => $title,
            'message' => $message,
            'sent_at' => now()
        ]);
        
        // Bildirim sıklığı kontrolü için cache ekle
        $cacheDuration = $this->getNotificationCooldown($notificationType);
        $cacheKey = "notification_cooldown:{$userId}:{$notificationType}";
        Cache::put($cacheKey, true, Carbon::now()->addMinutes($cacheDuration));
    }
    
    /**
     * Bildirim türüne göre bekleme süresini belirler (dakika cinsinden)
     * 
     * @param string $notificationType
     * @return int
     */
    private function getNotificationCooldown(string $notificationType): int
    {
        $cooldowns = [
            'login_streak' => 1440, // 24 saat (günde bir)
            'course_completion' => 60, // 1 saat
            'course_reminder' => 720, // 12 saat
            'custom' => 30, // 30 dakika
            'broadcast' => 1440, // 24 saat
        ];
        
        return $cooldowns[$notificationType] ?? 60; // Varsayılan 1 saat
    }
    
    /**
     * Her bir bildirim türü için kullanıcının bildirim alıp alamayacağını kontrol eder
     * Bu metot bildirim ayarlarını ve kullanıcı tercihlerini detaylı olarak kontrol eder
     *
     * @param User $user
     * @param string $notificationType
     * @return bool
     */
    public function canUserReceiveNotification(User $user, string $notificationType): bool
    {
        // Kullanıcı bildirim ayarlarını al
        $settings = $user->notificationSettings;
        
        if (!$settings) {
            // Kullanıcının bildirim ayarları yoksa, varsayılan ayarları kullan
            $settings = new UserNotificationSetting([
                'preferences' => UserNotificationSetting::getDefaultPreferences()
            ]);
        }
        
        // 1. Önce 'all' (tüm bildirimler) kapalı mı kontrol et
        if (isset($settings->preferences['all']) && $settings->preferences['all'] === false) {
            \Log::info("Kullanıcı {$user->id}: Tüm bildirimler kapalı olduğu için {$notificationType} bildirimi gönderilemedi");
            return false;
        }
        
        // 2. Belirli bildirim türü için ayarı kontrol et
        if (isset($settings->preferences[$notificationType]) && $settings->preferences[$notificationType] === false) {
            \Log::info("Kullanıcı {$user->id}: {$notificationType} türü bildirimler kapalı");
            return false;
        }
        
        // 3. Son bildirim gönderim zamanını kontrol et (sıklık limiti)
        $lastNotification = UserNotificationLog::where('user_id', $user->id)
            ->where('notification_type', $notificationType)
            ->orderBy('sent_at', 'desc')
            ->first();
            
        if ($lastNotification) {
            $hoursSinceLastNotification = $lastNotification->sent_at->diffInHours(now());
            $minimumHours = $this->getNotificationCooldownHours($notificationType);
            
            if ($hoursSinceLastNotification < $minimumHours) {
                \Log::info("Kullanıcı {$user->id}: Son {$notificationType} bildirimi üzerinden {$hoursSinceLastNotification} saat geçmiş, minimum {$minimumHours} saat gerekli");
                return false;
            }
        }
        
        // 4. Bildirim türüne özel kontroller
        if ($notificationType === 'login_streak') {
            // Login streak bildirimleri için özel kontroller
            // Kullanıcının streak'i yoksa veya çok kısaysa (örn. 1-2 gün) bildirim göndermeyebiliriz
            $userLogins = UserLogin::where('user_id', $user->id)
                ->orderBy('login_date', 'desc')
                ->limit(10)
                ->get();
                
            $streak = $this->calculateLoginStreak($userLogins);
            
            // Streak 2 günden azsa bildirim gönderme (1 günlük streak'i korumak yeterince önemli değil)
            if ($streak < 2) {
                \Log::info("Kullanıcı {$user->id}: Streak {$streak} gün olduğu için bildirim gönderilemedi (minimum 2 gün)");
                return false;
            }
        }
        
        \Log::info("Kullanıcı {$user->id}: {$notificationType} bildirimi gönderilebilir");
        return true;
    }
    
    /**
     * Bildirim türüne göre minimum bekleme süresini saat cinsinden döndürür
     *
     * @param string $notificationType
     * @return int
     */
    private function getNotificationCooldownHours(string $notificationType): int
    {
        $cooldowns = [
            'login_streak' => 8,     // Günde en fazla 3 kez gönder (sabah/öğlen/akşam)
            'course_reminder' => 24, // Günde en fazla 1 kez
            'custom' => 6,           // Günde en fazla 4 kez
            'broadcast' => 24,       // Günde en fazla 1 kez
        ];
        
        return $cooldowns[$notificationType] ?? 12; // Varsayılan 12 saat
    }
    
    /**
     * Login streak bildirimi için kontrol yapar ve gerekirse gönderir
     * Streak'i olan ama bugün giriş yapmamış kullanıcılara bildirim gönderir
     *
     * @param User $user
     * @return bool
     */
    public function checkAndSendLoginStreakNotification(User $user): bool
    {
        // Kullanıcı login streak bildirimlerini alabilir mi kontrol et
        if (!$this->canUserReceiveNotification($user, 'login_streak')) {
            return false;
        }
        
        // Bugün zaten giriş yapılmışsa bildirim gönderme
        $today = now()->toDateString();
        $hasLoginToday = UserLogin::where('user_id', $user->id)
            ->where('login_date', $today)
            ->exists();
            
        if ($hasLoginToday) {
            return false; // Bugün zaten giriş yapmış, bildirime gerek yok
        }
        
        // Kullanıcının son giriş kaydını al
        $lastLogin = UserLogin::where('user_id', $user->id)
            ->orderBy('login_date', 'desc')
            ->first();
            
        if (!$lastLogin) {
            return false; // Hiç giriş yapmamış, streak yok
        }
        
        // Son giriş tarihi dün mü kontrol et
        $lastLoginDate = Carbon::parse($lastLogin->login_date);
        $isYesterday = $lastLoginDate->isYesterday();
        
        if (!$isYesterday) {
            // Son giriş dün değilse streak zaten kırılmış, bildirim göndermeye gerek yok
            return false;
        }
        
        // Kullanıcının mevcut streak'ini hesapla
        $userLogins = UserLogin::where('user_id', $user->id)
            ->orderBy('login_date', 'asc')
            ->limit(30) // Son 30 günlük veriyi al
            ->get();
            
        $streak = $this->calculateLoginStreak($userLogins);
        
        // Son 24 saat içinde bu tür bir bildirim gönderilmiş mi kontrol et
        $lastNotification = UserNotificationLog::getLastNotificationOfType($user->id, 'login_streak');
            
        if ($lastNotification && $lastNotification->sent_at->diffInHours(now()) < 24) {
            return false; // Son 24 saat içinde zaten bildirim gönderilmiş
        }
        
        // Streak hatırlatma bildirimi gönder
        return $this->sendLoginStreakReminderNotification($user, $streak);
    }
    
    /**
     * Kullanıcının ardışık giriş sayısını hesaplar
     *
     * @param \Illuminate\Database\Eloquent\Collection $userLogins
     * @return int
     */
    private function calculateLoginStreak($userLogins): int
    {
        if ($userLogins->isEmpty()) {
            return 0;
        }
        
        $streak = 0;
        $prevDate = null;
        
        foreach ($userLogins as $login) {
            $loginDate = Carbon::parse($login->login_date);
            
            if ($prevDate === null) {
                $streak = 1;
                $prevDate = $loginDate;
                continue;
            }
            
            // Bir önceki giriş ile arasında tam 1 gün varsa streak devam ediyor
            if ($prevDate->diffInDays($loginDate) === 1) {
                $streak++;
                $prevDate = $loginDate;
            } else {
                // Ardışık gün yoksa döngüyü sonlandır
                break;
            }
        }
        
        return $streak;
    }
    
    /**
     * Login streak hatırlatma bildirimi gönderir
     *
     * @param User $user
     * @param int $streak
     * @return bool
     */
    private function sendLoginStreakReminderNotification(User $user, int $streak): bool
    {
        $title = "Streak'inizi Koruyun!";
        $message = "{$streak} günlük giriş streak'inizi kaybetmek üzeresiniz! Bugün giriş yaparak devam ettirin.";
        
        // Bildirimi logla
        UserNotificationLog::create([
            'user_id' => $user->id,
            'notification_type' => 'login_streak',
            'title' => $title,
            'message' => $message,
            'sent_at' => now(),
        ]);
        
        // Bu noktada gerçek push notification, email, vb gönderimi yapılacak
        try {
            // OneSignal ile bildirim gönder
            $playerIds = $this->getPlayerIds($user->id);
            
            if (!empty($playerIds)) {
                $additionalData = [
                    'type' => 'login_streak',
                    'streak' => $streak,
                ];
                
                $this->sendNotification($playerIds, $title, $message, $additionalData);
            }
        } catch (\Exception $e) {
            \Log::error('Login streak bildirim gönderimi başarısız: ' . $e->getMessage());
            // Bildirim gönderimi başarısız olsa bile log kayıt edildi, true döndürülebilir
        }
        
        return true;
    }
    
    /**
     * Kurs hatırlatıcı bildirimini kontrol eder ve gerekirse gönderir
     *
     * @param User $user
     * @return bool
     */
    public function checkAndSendCourseReminderNotification(User $user): bool
    {
        // Kullanıcı kurs hatırlatıcı bildirimlerini alabilir mi kontrol et
        if (!$this->canUserReceiveNotification($user, 'course_reminder')) {
            return false;
        }
        
        // Kurs hatırlatıcısı için gerekli kontroller burada yapılacak
        // Şimdilik simüle ediyoruz
        
        return false;
    }
    
    /**
     * Özel bildirim gönderebilme durumunu kontrol eder
     *
     * @param User $user
     * @return bool
     */
    public function canSendCustomNotification(User $user): bool
    {
        return $this->canUserReceiveNotification($user, 'custom');
    }
    
    /**
     * Genel duyuru bildirimlerini alabilme durumunu kontrol eder
     *
     * @param User $user
     * @return bool
     */
    public function canSendBroadcastNotification(User $user): bool
    {
        return $this->canUserReceiveNotification($user, 'broadcast');
    }
    
    /**
     * Tüm bildirim kontrollerini tek bir yerden yapar
     *
     * @param User $user
     * @return array
     */
    public function checkAllNotifications(User $user): array
    {
        $results = [];
        
        // Login streak bildirimi
        $results['login_streak'] = $this->checkAndSendLoginStreakNotification($user);
        
        // Kurs hatırlatıcı bildirimi
        $results['course_reminder'] = $this->checkAndSendCourseReminderNotification($user);
        
        // Diğer bildirim türleri için kontroller eklenebilir
        
        return $results;
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendCourseCompletionNotification(int $userId, string $courseName): bool
    {
        // Bildirim gönderme kontrolü
        if (!$this->canSendNotification($userId, 'course_completion')) {
            return false;
        }
        
        $playerIds = $this->getPlayerIds($userId);
        
        if (empty($playerIds)) {
            return false;
        }
        
        $title = 'Tebrikler!';
        $message = $courseName . ' kursunu başarıyla tamamladınız!';
        $additionalData = [
            'type' => 'course_completion',
            'course_name' => $courseName
        ];
        
        $result = $this->sendNotification($playerIds, $title, $message, $additionalData);
        
        if ($result) {
            $this->logNotification($userId, 'course_completion', $title, $message);
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendCourseReminderNotification(int $userId, string $courseName, int $progress): bool
    {
        // İlerleme %50'nin altındaysa hatırlatma gönder
        if ($progress > 50) {
            return false;
        }
        
        // Bildirim gönderme kontrolü
        if (!$this->canSendNotification($userId, 'course_reminder')) {
            return false;
        }
        
        $playerIds = $this->getPlayerIds($userId);
        
        if (empty($playerIds)) {
            return false;
        }
        
        $title = 'Öğrenmeye Devam Et!';
        $message = $courseName . ' kursunda %' . $progress . ' ilerleme kaydettiniz. Tamamlamak için hemen gelin!';
        $additionalData = [
            'type' => 'course_reminder',
            'course_name' => $courseName,
            'progress' => $progress
        ];
        
        $result = $this->sendNotification($playerIds, $title, $message, $additionalData);
        
        if ($result) {
            $this->logNotification($userId, 'course_reminder', $title, $message);
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendCustomNotification($userIds, string $title, string $message, array $additionalData = []): bool
    {
        if (is_array($userIds)) {
            $filteredUserIds = [];
            foreach ($userIds as $userId) {
                if ($this->canSendNotification($userId, 'custom')) {
                    $filteredUserIds[] = $userId;
                }
            }
            $userIds = $filteredUserIds;
        } else {
            if (!$this->canSendNotification($userIds, 'custom')) {
                return false;
            }
        }
        
        $playerIds = $this->getPlayerIds($userIds);
        
        if (empty($playerIds)) {
            return false;
        }
        
        $additionalData['type'] = 'custom';
        
        $result = $this->sendNotification($playerIds, $title, $message, $additionalData);
        
        if ($result && is_array($userIds)) {
            foreach ($userIds as $userId) {
                $this->logNotification($userId, 'custom', $title, $message);
            }
        } elseif ($result) {
            $this->logNotification($userIds, 'custom', $title, $message);
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendBroadcastNotification(string $title, string $message, array $additionalData = []): bool
    {
        $additionalData['type'] = 'broadcast';
        
        $result = $this->sendNotificationToAll($title, $message, $additionalData);
        
        if ($result) {
            // Broadcast bildirimler için genel log tutma
            UserNotificationLog::create([
                'user_id' => null,
                'notification_type' => 'broadcast',
                'title' => $title,
                'message' => $message,
                'sent_at' => now()
            ]);
        }
        
        return $result;
    }
    
    /**
     * OneSignal API kullanarak belirli player ID'lerine bildirim gönderir
     * 
     * @param array $playerIds
     * @param string $title
     * @param string $message
     * @param array $additionalData
     * @return bool
     */
    private function sendNotification(array $playerIds, string $title, string $message, array $additionalData = []): bool
    {
        if (empty($playerIds)) {
            return false;
        }
        
        try {
            OneSignal::sendNotificationToUser(
                $message,
                $playerIds,
                null,
                [
                    'headings' => ['en' => $title],
                    'data' => $additionalData
                ]
            );
            
            return true;
        } catch (\Exception $e) {
            \Log::error('OneSignal bildirim gönderimi başarısız: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * OneSignal API kullanarak tüm kullanıcılara bildirim gönderir
     * 
     * @param string $title
     * @param string $message
     * @param array $additionalData
     * @return bool
     */
    private function sendNotificationToAll(string $title, string $message, array $additionalData = []): bool
    {
        try {
            OneSignal::sendNotificationToAll(
                $message,
                null,
                [
                    'headings' => ['en' => $title],
                    'data' => $additionalData
                ]
            );
            
            return true;
        } catch (\Exception $e) {
            \Log::error('OneSignal toplu bildirim gönderimi başarısız: ' . $e->getMessage());
            return false;
        }
    }
} 