<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

 // Her gün saat 10'da login streak bildirimleri - Sabah hatırlatma
 Schedule::command('notifications:check-login-streak')
 ->dailyAt('10:00')
 ->appendOutputTo(storage_path('logs/login-streak-notifications-morning.log'));

 Schedule::call(function () {
     \Log::info('Zamanlayıcı test görevi çalıştı: ' . now());
 })->everyMinute();

// Her gün saat 18:00'da login streak bildirimleri - Akşam hatırlatma
Schedule::command('notifications:check-login-streak')
 ->dailyAt('18:00')
 ->appendOutputTo(storage_path('logs/login-streak-notifications-evening.log'));

Schedule::command('notifications:course-reminder')
 ->mondays()
 ->thursdays()
 ->at('18:00');
