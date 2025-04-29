<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Her gün saat 10'da login streak bildirimleri
        $schedule->command('notifications:login-streak')->dailyAt('10:00');
        
        // Her Pazartesi ve Perşembe saat 18:00'da kurs hatırlatma bildirimleri
        $schedule->command('notifications:course-reminder')
            ->days([Schedule::MONDAY, Schedule::THURSDAY])
            ->at('18:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 