<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use App\Models\UserMissionProgress;
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
            $userProgress = UserMissionProgress::where('user_id', $userId)
                ->where('mission_id', $this->id)
                ->first();
                
            if ($userProgress) {
                $isCompleted = $userProgress->isCompleted();
                $completedAt = $userProgress->completed_at;
                $currentAmount = $userProgress->current_amount;
                $requiredAmount = $this->required_amount ?? 1;
                $progressPercentage = min(100, ($currentAmount / $requiredAmount) * 100);
            }
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
