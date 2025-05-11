<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Varsayılan notification ayarları
        $defaultNotificationSettings = [
            'all' => true,
            'login_streak' => true,
            'course_completion' => true,
            'course_reminder' => true,
            'custom' => true,
            'broadcast' => true,
        ];

        // Tüm kullanıcıları güncelle
        User::chunk(100, function ($users) use ($defaultNotificationSettings) {
            foreach ($users as $user) {
                $currentSettings = $user->settings ?? [];
                $currentSettings['notifications'] = $defaultNotificationSettings;
                
                $user->settings = $currentSettings;
                $user->save();
            }
        });
    }
} 