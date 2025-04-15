<?php

namespace App\Services\Api;

use App\Models\User;
use App\Models\Level;
use App\Interfaces\Services\Api\UserExperienceServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserExperienceService implements UserExperienceServiceInterface
{
    /**
     * Kullanıcıya XP ekle ve seviye kontrolü yap
     *
     * @param int $userId
     * @param int $amount
     * @param string|null $actionType
     * @param int|null $actionId
     * @param string|null $description
     * @return array|null
     */
    public function addExperience(int $userId, int $amount, ?string $actionType = null, ?int $actionId = null, ?string $description = null): ?array
    {
        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($userId);
            $result = $user->addExperiencePoints($amount, $actionType, $actionId);
            
            // Seviye değişimi olmuşsa event tetiklenebilir
            if ($result['level_changed'] && $result['new_level']) {
                // event(new UserLeveledUp($user, $result['new_level'], $result['old_level']));
                
                // Eğer Event yoksa buradan bildirim gönderilebilir
                // $user->notify(new LevelUpNotification($result['new_level']));
            }
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('XP ekleme hatası: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Kullanıcının deneyim ve seviye bilgilerini getir
     *
     * @param int $userId
     * @return array|null
     */
    public function getUserExperience(int $userId): ?array
    {
        try {
            $user = User::with('level')->findOrFail($userId);
            
            $result = [
                'total_xp' => $user->experience_points,
                'level' => null,
                'level_progress' => 0,
                'next_level' => null
            ];
            
            if ($user->level) {
                $result['level'] = [
                    'id' => $user->level->id,
                    'level_number' => $user->level->level_number,
                    'title' => $user->level->title,
                    'description' => $user->level->description,
                    'min_xp' => $user->level->min_xp,
                    'max_xp' => $user->level->max_xp,
                    'icon_url' => $user->level->icon_url,
                    'color_code' => $user->level->color_code,
                ];
                
                $result['level_progress'] = $user->level_progress;
                
                // Bir sonraki seviyeyi bul
                $nextLevel = Level::where('level_number', $user->level->level_number + 1)
                    ->active()
                    ->first();
                    
                if ($nextLevel) {
                    $result['next_level'] = [
                        'id' => $nextLevel->id,
                        'level_number' => $nextLevel->level_number,
                        'title' => $nextLevel->title,
                        'min_xp' => $nextLevel->min_xp,
                        'xp_needed' => $nextLevel->min_xp - $user->experience_points,
                    ];
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Kullanıcı deneyim bilgisi getirme hatası: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Seviye için gereken aktivite miktarını hesapla
     * 
     * @param Level $level
     * @param int $xpPerActivity
     * @return int
     */
    public function calculateActivitiesForNextLevel(Level $level, int $xpPerActivity): int
    {
        if ($xpPerActivity <= 0) {
            return 0;
        }
        
        $nextLevel = Level::where('level_number', $level->level_number + 1)
            ->active()
            ->first();
            
        if (!$nextLevel) {
            return 0;
        }
        
        $xpNeeded = $nextLevel->min_xp - $level->min_xp;
        return ceil($xpNeeded / $xpPerActivity);
    }
} 