<?php

namespace App\Console\Commands;

use App\Interfaces\Services\Api\NotificationServiceInterface;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Console\Command;

class CheckLoginStreakNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check-login-streak';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kullanıcıların login streak durumlarını kontrol eder ve bildirim gönderir';

    /**
     * Bildirim servisi
     *
     * @var NotificationServiceInterface
     */
    protected $notificationService;

    /**
     * Oluştur: yeni komut örneği.
     *
     * @param NotificationServiceInterface $notificationService
     * @return void
     */
    public function __construct(NotificationServiceInterface $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Komutun uygulanması.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Login streak bildirimleri kontrol ediliyor...');
        
        // Bugünün tarihini al
        $today = now()->toDateString();
        
        // Dün giriş yapmış kullanıcıları bul
        $yesterday = now()->subDay()->toDateString();
        
        $this->info("Dün ({$yesterday}) giriş yapmış kullanıcılar aranıyor...");
        
        $userIdsWithLoginYesterday = UserLogin::where('login_date', $yesterday)
            ->pluck('user_id')
            ->unique()
            ->toArray();
            
        $this->info('Dün giriş yapan kullanıcı sayısı: ' . count($userIdsWithLoginYesterday));
        
        if (empty($userIdsWithLoginYesterday)) {
            $this->info('Dün giriş yapan kullanıcı bulunmadı.');
            return 0;
        }
        
        // Bugün giriş yapmış kullanıcıları bul ve filtrele
        $userIdsWithLoginToday = UserLogin::where('login_date', $today)
            ->pluck('user_id')
            ->unique()
            ->toArray();
            
        $this->info('Bugün giriş yapan kullanıcı sayısı: ' . count($userIdsWithLoginToday));
        
        // Dün giriş yapıp bugün henüz giriş yapmamış kullanıcıları belirle
        $targetUserIds = array_diff($userIdsWithLoginYesterday, $userIdsWithLoginToday);
        
        $this->info('Potansiyel bildirim alacak kullanıcı sayısı: ' . count($targetUserIds));
        
        if (empty($targetUserIds)) {
            $this->info('Dün giriş yapıp bugün giriş yapmamış kullanıcı bulunmadı.');
            return 0;
        }
        
        // Bildirim ayarları açık olan kullanıcıları filtreleme
        $eligibleUserIds = [];
        $skippedCount = 0;
        
        foreach ($targetUserIds as $userId) {
            $user = User::find($userId);
            
            if (!$user) {
                $this->warn("ID: {$userId} - Kullanıcı bulunamadı, atlanıyor.");
                $skippedCount++;
                continue;
            }
            
            // Bildirim ayarını kontrol et
            $notificationSettings = $user->notificationSettings;
            
            if (!$notificationSettings) {
                // Kullanıcının bildirim ayarları yoksa varsayılan olarak bildirim alabilir
                $eligibleUserIds[] = $userId;
                continue;
            }
            
            // Login streak bildirimleri açık mı kontrol et
            if ($notificationSettings->canReceiveNotificationType('login_streak')) {
                $eligibleUserIds[] = $userId;
            } else {
                $this->warn("Kullanıcı {$user->id} ({$user->email}) login streak bildirimlerini kapattığı için atlanıyor.");
                $skippedCount++;
            }
        }
        
        $this->info("Toplam {$skippedCount} kullanıcı bildirim ayarları nedeniyle atlandı.");
        $this->info("Bildirim uygun kullanıcı sayısı: " . count($eligibleUserIds));
        
        if (empty($eligibleUserIds)) {
            $this->info('Bildirim gönderilecek uygun kullanıcı bulunamadı.');
            return 0;
        }
        
        $usersProcessed = 0;
        $notificationsSent = 0;
        
        // Her kullanıcı için bildirim kontrolü yap
        foreach ($eligibleUserIds as $userId) {
            $user = User::find($userId);
            
            if (!$user) {
                continue;
            }
            
            $usersProcessed++;
            
            // Detaylı bilgi log etme
            $this->line("İşleniyor: Kullanıcı {$user->id} ({$user->email})");
            
            // Kontrol et ve bildirim gönder
            $result = $this->notificationService->checkAndSendLoginStreakNotification($user);
            
            if ($result) {
                $notificationsSent++;
                $this->info("  ✓ Başarılı: Kullanıcı {$user->id} ({$user->email}) için login streak bildirimi gönderildi.");
            } else {
                $this->warn("  ✗ Atlandı: Kullanıcı {$user->id} ({$user->email}) için login streak bildirimi gönderilmedi.");
            }
        }
        
        $this->info("İşlem tamamlandı! {$usersProcessed} kullanıcı işlendi, {$notificationsSent} bildirim gönderildi.");
        $this->info("Özet: {$skippedCount} kullanıcı ayarları nedeniyle atlandı, " . 
                    ($usersProcessed - $notificationsSent) . " kullanıcı diğer kontroller nedeniyle atlandı.");
        
        return 0;
    }
} 