<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\NotificationServiceInterface;
use App\Models\User;
use App\Models\UserNotificationSetting;
use App\Models\UserNotificationLog;
use OneSignal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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
        // Kullanıcı bildirim tercihlerini kontrol et
        $userSettings = UserNotificationSetting::firstOrCreate(
            ['user_id' => $userId],
            ['preferences' => json_encode(['all' => true])]
        );
        
        $preferences = json_decode($userSettings->preferences, true);
        
        // Kullanıcı tüm bildirimleri veya bu tür bildirimleri kapatmışsa
        if (!($preferences['all'] ?? true) || !($preferences[$notificationType] ?? true)) {
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
     * {@inheritdoc}
     */
    public function sendLoginStreakNotification(int $userId, int $streakCount): bool
    {
        // Streak değeri anlamlıysa bildirim gönder (en az 3 gün)
        if ($streakCount < 3) {
            return false;
        }
        
        // Bildirim gönderme kontrolü
        if (!$this->canSendNotification($userId, 'login_streak')) {
            return false;
        }
        
        $playerIds = $this->getPlayerIds($userId);
        
        if (empty($playerIds)) {
            return false;
        }
        
        $title = 'Tebrikler!';
        $message = $streakCount . ' gündür kesintisiz giriş yaptınız!';
        $additionalData = [
            'type' => 'login_streak',
            'streak_count' => $streakCount
        ];
        
        $result = $this->sendNotification($playerIds, $title, $message, $additionalData);
        
        if ($result) {
            $this->logNotification($userId, 'login_streak', $title, $message);
        }
        
        return $result;
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