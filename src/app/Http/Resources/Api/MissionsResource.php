<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use App\Models\UserMissionProgress;
use App\Models\UserMission;
use App\Models\Mission;
use Illuminate\Support\Facades\Auth;

class MissionsResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        $userId = Auth::id();
        
        // Kullanıcı oturum açmışsa ve ID değeri mevcutsa
        $userProgress = null;
        $isCompleted = false;
        $completedAt = null;
        $progressPercentage = 0;
        $currentAmount = 0;
        
        if ($userId) {
            // İlerleme kaydını al
            $userProgress = UserMissionProgress::where('user_id', $userId)
                ->where('mission_id', $this->id)
                ->first();
            // Tamamlama kayıtlarını kontrol et
            $completions = UserMission::where('user_id', $userId)
                ->where('mission_id', $this->id)
                ->orderBy('completed_date', 'desc')
                ->get();
            
            // Görev tipine göre tamamlanma durumunu belirle
            switch ($this->type) {
                case Mission::TYPE_DAILY:
                    // Günlük görevler için bugün tamamlanmış mı?
                    $todayCompletion = $completions->first(function($completion) {
                        return $completion->completed_date->isToday();
                    });
                    $isCompleted = $todayCompletion !== null;
                    $completedAt = $todayCompletion ? $todayCompletion->completed_date : null;
                    break;
                    
                case Mission::TYPE_WEEKLY:
                    // Haftalık görevler için bu hafta tamamlanmış mı?
                    $weeklyCompletion = $completions->first(function($completion) {
                        return $completion->completed_date->isCurrentWeek();
                    });
                    $isCompleted = $weeklyCompletion !== null;
                    $completedAt = $weeklyCompletion ? $weeklyCompletion->completed_date : null;
                    break;
                    
                case Mission::TYPE_ONE_TIME:
                case Mission::TYPE_MANUAL:
                default:
                    // Tek seferlik görevler için herhangi bir zaman tamamlanmış mı?
                    $isCompleted = $completions->isNotEmpty();
                    $completedAt = $completions->first() ? $completions->first()->completed_date : null;
                    break;
            }
            
            // İlerleme değerlerini ayarla
            if ($userProgress) {
                $currentAmount = $userProgress->current_amount;
            }
            
            $requiredAmount = $this->required_amount ?? 1;
            $progressPercentage = min(100, ($currentAmount / $requiredAmount) * 100);
        }

        return array_merge($translated, [
            'id' => $this->id,
            'type' => $this->type,
            'xp_reward' => $this->xp_reward,
            'is_active' => $this->is_active,
            'required_amount' => $this->required_amount ?? 1,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            // Kullanıcı ilerleme bilgileri
            'user_progress' => [
                'is_completed' => $isCompleted,
                'completed_at' => $completedAt?->toDateTimeString(),
                'current_amount' => $currentAmount,
                'progress_percentage' => $progressPercentage
            ]
        ]);
    }
}
