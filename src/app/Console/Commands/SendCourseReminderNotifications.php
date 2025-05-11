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
                // Kullanıcının en az bir dersini tamamladığı kursları bul
                $startedCourses = Course::whereHas('lessons.completions', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->get();
                
                $incompleteCourses = [];
                
                foreach ($startedCourses as $course) {
                    // Kurs tamamlama yüzdesini hesapla
                    $totalLessons = $course->lessons()->count();
                    
                    if ($totalLessons > 0) {
                        $completedLessons = $course->lessons()
                            ->whereHas('completions', function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })->count();
                        
                        $progress = ($completedLessons / $totalLessons) * 100;
                        
                        // İlerleme kaydedilmiş ama tamamlanmamışsa listeye ekle
                        if ($progress > 0 && $progress < 100) {
                            $incompleteCourses[] = [
                                'course' => $course,
                                'progress' => $progress
                            ];
                        }
                    }
                }
                
                // Başlanmış ama tamamlanmamış kurslar varsa
                if (count($incompleteCourses) > 0) {
                    // En çok ilerlenen kursu bul
                    usort($incompleteCourses, function ($a, $b) {
                        return $b['progress'] <=> $a['progress'];
                    });
                    
                    $mostProgressedCourse = $incompleteCourses[0]['course'];
                    $progress = $incompleteCourses[0]['progress'];
                    
                    // Bildirim gönder
                    $result = $notificationService->sendCourseReminderNotification(
                        $user->id, 
                        $mostProgressedCourse->name, 
                        round($progress)
                    );
                    
                    if ($result) {
                        $sentCount++;
                        $this->info("Kullanıcı {$user->id} için {$mostProgressedCourse->name} kursuna bildirim gönderildi");
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