<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Level;

class UserObserver
{
    /**
     * Kullanıcı oluşturulduğunda
     */
    public function created(User $user): void
    {
        // Kullanıcıya varsayılan level atama (level_number = 1 olan)
        $defaultLevel = Level::where('level_number', 1)->first();
        
        if ($defaultLevel && is_null($user->level_id)) {
            $user->level_id = $defaultLevel->id;
            $user->save();
        }
        
        // Kullanıcıya varsayılan "user" rolünü ata
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }
    }

    /**
     * Kullanıcının XP'si değiştiğinde level kontrolü
     */
    public function updated(User $user): void
    {
        // Eğer experience_points alanı değiştiyse level kontrolü yap
        if ($user->isDirty('experience_points')) {
            $this->checkAndUpdateLevel($user);
        }
    }

    /**
     * XP değişimine göre seviye kontrolü ve güncelleme
     */
    private function checkAndUpdateLevel(User $user): void
    {
        // Kullanıcının XP'sine göre uygun seviyeyi bul
        $appropriateLevel = Level::where('min_xp', '<=', $user->experience_points)
            ->where('max_xp', '>', $user->experience_points)
            ->where('is_active', true)
            ->first();
        
        // Eğer uygun bir seviye bulunduysa ve kullanıcının mevcut seviyesinden farklıysa güncelle
        if ($appropriateLevel && $user->level_id != $appropriateLevel->id) {
            $user->level_id = $appropriateLevel->id;
            $user->save();
        }
    }
} 