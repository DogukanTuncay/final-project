<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Level;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rolleri oluştur (eğer yoksa)
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // İlk seviyeyi al veya oluştur
        $firstLevel = Level::orderBy('level_number', 'asc')->first();
        if ($firstLevel == null) {
            $firstLevel = Level::factory()->create([
                'level_number' => 1,
                'title' => ['en' => 'Beginner', 'tr' => 'Başlangıç'],
                'min_xp' => 0,
                'max_xp' => 100 // Örnek değer
            ]);
        }

        // Admin Kullanıcısı Oluştur
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'), // Güvenli bir şifre kullanın
                'email_verified_at' => now(),
                'level_id' => $firstLevel->id,
                'experience_points' => 0,
                'locale' => 'tr', // Varsayılan dil
            ]
        );
        $adminUser->assignRole($adminRole);

        // İsteğe bağlı: Super Admin Kullanıcısı Oluştur
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'level_id' => $firstLevel->id,
                'experience_points' => 0,
                'locale' => 'tr',
            ]
        );
        $superAdminUser->assignRole($superAdminRole); // Super Admin rolünü ata

        // Örnek Kullanıcılar Oluştur (İsteğe Bağlı)
        if (app()->environment(['local', 'testing'])) {
            User::factory()->count(5)->create([
                'level_id' => $firstLevel->id,
            ])->each(function ($user) use ($userRole) {
                $user->assignRole($userRole); // User rolünü ata
            });
        }
    }
} 