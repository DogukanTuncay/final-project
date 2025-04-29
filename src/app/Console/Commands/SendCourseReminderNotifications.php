<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Course;
use App\Interfaces\Services\Api\NotificationServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendCourseReminderNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:course-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tamamlanmamış kursları olan kullanıcılara hatırlatma bildirimleri gönderir';

    /**
     * Execute the console command.
     */
    public function handle(NotificationServiceInterface $notificationService)
    {
        $this->info('Kurs hatırlatma bildirimleri gönderiliyor...');
        
        try {
            // 7 gündür giriş yapmayan ve tamamlanmamış kursu olan kullanıcıları bul
            $lastActiveThreshold = Carbon::now()->subDays(7);
            
            // Bu sorgu, en son giriş tarihi 7 günden eski olan kullanıcıları getirir
            $inactiveUsers = User::whereHas('logins', function ($query) use ($lastActiveThreshold) {
                $query->selectRaw('user_id, MAX(login_date) as last_login')
                    ->groupBy('user_id')
                    ->havingRaw('MAX(login_date) < ?', [$lastActiveThreshold]);
            })->limit(100)->get(); // Performans için limitliyoruz
            
            $this->info('İnaktif kullanıcı sayısı: ' . $inactiveUsers->count());
            
            $sentCount = 0;
            
            foreach ($inactiveUsers as $user) {
                // Kullanıcının başladığı ama tamamlamadığı kursları bul
                // Bu kısım sizin Course ve UserCourseProgress modellerinize göre uyarlanmalıdır
                $incompleteCourses = Course::whereHas('userProgress', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('progress', '<', 100)
                        ->where('progress', '>', 0);
                })->get();
                
                if ($incompleteCourses->count() > 0) {
                    // En çok ilerlenen ama tamamlanmamış kursu seç
                    $mostProgressedCourse = $incompleteCourses->sortByDesc(function ($course) use ($user) {
                        return $course->userProgress->where('user_id', $user->id)->first()->progress ?? 0;
                    })->first();
                    
                    if ($mostProgressedCourse) {
                        $progress = $mostProgressedCourse->userProgress->where('user_id', $user->id)->first()->progress ?? 0;
                        
                        // Bildirim gönder
                        $result = $notificationService->sendCourseReminderNotification(
                            $user->id, 
                            $mostProgressedCourse->name, 
                            $progress
                        );
                        
                        if ($result) {
                            $sentCount++;
                        }
                    }
                }
            }
            
            $this->info('Toplam gönderilen bildirim sayısı: ' . $sentCount);
            return 0;
        } catch (\Exception $e) {
            $this->error('Bildirim gönderilirken hata oluştu: ' . $e->getMessage());
            Log::error('Kurs hatırlatma bildirimleri gönderilirken hata: ' . $e->getMessage());
            return 1;
        }
    }
} 