<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\BadgeServiceInterface;
use App\Interfaces\Repositories\Api\BadgeRepositoryInterface;
use App\Models\Badge;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Events\BadgeEarned;
use Illuminate\Support\Facades\Log;

class BadgeService implements BadgeServiceInterface
{
    protected BadgeRepositoryInterface $repository;

    public function __construct(BadgeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Tüm aktif rozetleri al
     */
    public function all()
    {
        return $this->repository->all();
    }

    /**
     * ID ile rozet bul
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Kullanıcının kazandığı rozetleri al
     */
    public function getUserBadges()
    {
        $user = JWTAuth::user();
        if (!$user) {
            return collect([]);
        }
        
        return $this->repository->getUserBadges($user->id);
    }

    /**
     * Kullanıcıya rozeti ver
     */
    public function awardBadge(int $badgeId): bool
    {
        $user = JWTAuth::user();
        if (!$user) {
            return false;
        }
        
        $badge = $this->repository->find($badgeId);
        if (!$badge) {
            return false;
        }
        
        $awarded = $this->repository->awardBadgeToUser($user->id, $badgeId);
        
        if ($awarded) {
            event(new BadgeEarned($user, $badge));
        }
        
        return $awarded;
    }

    /**
     * Tüm rozet koşullarını kontrol et ve uygun olanları kullanıcıya ver
     * 
     * @param User $user
     * @return array Kazanılan rozetlerin ID'leri
     */
    public function checkAndAwardBadges(User $user): array
    {
        $awardedBadges = [];
        
        // Kullanıcının henüz kazanmadığı aktif rozetleri al
        $notEarnedBadges = $this->repository->getNotEarnedBadgesForUser($user->id);
        
        foreach ($notEarnedBadges as $badge) {
            if ($this->userQualifiesForBadge($user, $badge)) {
                $this->repository->awardBadgeToUser($user->id, $badge->id);
                $awardedBadges[] = $badge->id;
                
                // Rozet kazanıldı olayını tetikle
                event(new BadgeEarned($user, $badge));
            }
        }
        
        return $awardedBadges;
    }

    /**
     * Kullanıcının rozet için koşulları sağlayıp sağlamadığını kontrol et
     * 
     * @param User $user
     * @param Badge $badge
     * @return bool
     */
    public function userQualifiesForBadge(User $user, $badge): bool
    {
        $conditions = $badge->conditions;
        $conditionLogic = $badge->condition_logic ?? 'all';
        
        if (empty($conditions)) {
            return false;
        }
        
        $conditionResults = [];
        Log::info("Checking badge eligibility for User ID: {$user->id}, Badge ID: {$badge->id}, Name: " . json_encode($badge->name));
        
        foreach ($conditions as $condition) {
            $type = $condition['type'] ?? '';
            $value = $condition['value'] ?? null;
            $result = false;
            
            switch ($type) {
                case 'login_count':
                    $result = $user->login_count >= $value;
                    Log::info("Badge condition 'login_count': User: {$user->login_count}, Required: {$value}, Result: " . ($result ? 'true' : 'false'));
                    $conditionResults[] = $result;
                    break;
                    
                case 'mission_completion_count':
                    $completedCount = $user->missions()
                        ->wherePivotNotNull('completed_at')
                        ->count();
                    $result = $completedCount >= $value;
                    Log::info("Badge condition 'mission_completion_count': User: {$completedCount}, Required: {$value}, Result: " . ($result ? 'true' : 'false'));
                    $conditionResults[] = $result;
                    break;
                    
                case 'specific_mission_completion':
                    $missionId = $condition['mission_id'] ?? null;
                    if (!$missionId) continue 2;
                    
                    $completed = $user->missions()
                        ->wherePivot('mission_id', $missionId)
                        ->wherePivotNotNull('completed_at')
                        ->exists();
                    $result = $completed;
                    Log::info("Badge condition 'specific_mission_completion': Mission ID: {$missionId}, Completed: " . ($result ? 'true' : 'false'));
                    $conditionResults[] = $result;
                    break;
                    
                case 'chapter_completion_count':
                    // Eğer Chapter tamamlama metodu mevcut ise
                    if (method_exists($user, 'completedChapters')) {
                        $completedCount = $user->completedChapters()->count();
                    } else {
                        // Alternatif olarak bir pivot tablodan kontrolü
                        $completedCount = DB::table('user_chapter_progress')
                            ->where('user_id', $user->id)
                            ->where('completed', true)
                            ->count();
                    }
                    $result = $completedCount >= $value;
                    Log::info("Badge condition 'chapter_completion_count': User: {$completedCount}, Required: {$value}, Result: " . ($result ? 'true' : 'false'));
                    $conditionResults[] = $result;
                    break;
                    
                case 'specific_chapter_completion':
                    $chapterId = $condition['chapter_id'] ?? null;
                    if (!$chapterId) continue 2;
                    
                    // Eğer Chapter tamamlama metodu mevcut ise
                    if (method_exists($user, 'completedChapters')) {
                        $completed = $user->completedChapters()
                            ->where('id', $chapterId)
                            ->exists();
                    } else {
                        // Alternatif olarak bir pivot tablodan kontrolü
                        $completed = DB::table('user_chapter_progress')
                            ->where('user_id', $user->id)
                            ->where('chapter_id', $chapterId)
                            ->where('completed', true)
                            ->exists();
                    }
                    $result = $completed;
                    Log::info("Badge condition 'specific_chapter_completion': Chapter ID: {$chapterId}, Completed: " . ($result ? 'true' : 'false'));
                    $conditionResults[] = $result;
                    break;
                
                case 'xp_points':
                    $result = $user->experience_points >= $value;
                    Log::info("Badge condition 'xp_points': User: {$user->experience_points}, Required: {$value}, Result: " . ($result ? 'true' : 'false'));
                    $conditionResults[] = $result;
                    break;
                    
                case 'level_reached':
                    $result = $user->level_id >= $value;
                    Log::info("Badge condition 'level_reached': User Level: {$user->level_id}, Required: {$value}, Result: " . ($result ? 'true' : 'false'));
                    $conditionResults[] = $result;
                    break;
                    
                case 'lesson_completion_count':
                    $completedCount = DB::table('lesson_completions')
                        ->where('user_id', $user->id)
                        ->count();
                    $result = $completedCount >= $value;
                    Log::info("Badge condition 'lesson_completion_count': User: {$completedCount}, Required: {$value}, Result: " . ($result ? 'true' : 'false'));
                    $conditionResults[] = $result;
                    break;
                
                default:
                    Log::warning("Unknown badge condition type: {$type}");
                    continue 2;
            }
        }
        
        if (empty($conditionResults)) {
            Log::info("No valid conditions found for badge ID: {$badge->id}");
            return false;
        }
        
        // Tüm koşullar sağlanmalı (AND)
        if ($conditionLogic === 'all') {
            $qualified = !in_array(false, $conditionResults);
            Log::info("Badge qualification result (all conditions must be met): " . ($qualified ? 'QUALIFIED' : 'NOT QUALIFIED'));
            return $qualified;
        } 
        // Herhangi bir koşul sağlanmalı (OR)
        else {
            $qualified = in_array(true, $conditionResults);
            Log::info("Badge qualification result (any condition must be met): " . ($qualified ? 'QUALIFIED' : 'NOT QUALIFIED'));
            return $qualified;
        }
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function getWithPagination(array $params)
    {
        return $this->repository->getWithPagination($params);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}