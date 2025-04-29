<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Interfaces\Services\Api\NotificationServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendLoginStreakNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:login-streak';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kullanıcılara login streak bildirimlerini gönderir';

    /**
     * Execute the console command.
     */
    public function handle(NotificationServiceInterface $notificationService)
    {
        $this->info('Login streak bildirimleri gönderiliyor...');
        
        try {
            // Streak değeri 3 veya daha büyük olan kullanıcıları bul
            $eligibleUsers = User::whereHas('logins', function ($query) {
                $query->selectRaw('user_id, count(*) as count')
                    ->groupBy('user_id')
                    ->havingRaw('count >= 3');
            })->get();
            
            $this->info('Bildirim gönderilecek kullanıcı sayısı: ' . $eligibleUsers->count());
            
            $sent = 0;
            foreach ($eligibleUsers as $user) {
                $currentStreak = $user->current_streak;
                
                // Streak 3 veya daha büyükse bildirim gönder
                if ($currentStreak >= 3) {
                    $result = $notificationService->sendLoginStreakNotification($user->id, $currentStreak);
                    
                    if ($result) {
                        $sent++;
                    }
                }
            }
            
            $this->info('Toplam gönderilen bildirim sayısı: ' . $sent);
            return 0;
        } catch (\Exception $e) {
            $this->error('Bildirim gönderilirken hata oluştu: ' . $e->getMessage());
            Log::error('Login streak bildirimleri gönderilirken hata: ' . $e->getMessage());
            return 1;
        }
    }
} 