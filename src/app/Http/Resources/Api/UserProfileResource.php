<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\LevelResource; // LevelResource varsa
use App\Http\Resources\BaseResource;
use Carbon\Carbon;

class UserProfileResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translated = $this->getTranslated($this->resource);
        
        // Kullanıcı istatistikleri için verileri hazırlayalım
        $completedMissions = $this->missions()
            ->wherePivotNotNull('completed_at')
            ->count();
            
        // Tamamlanan dersler
        $completedLessonsCount = $this->whenLoaded(
            'completedLessons', 
            fn() => $this->completedLessons->count(), 
            fn() => \App\Models\LessonCompletion::where('user_id', $this->id)->count()
        );
        
        return array_merge($translated, [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'phone' => $this->phone,
            'zip_code' => $this->zip_code,
            'locale' => $this->locale,
            'experience_points' => $this->experience_points,
            // Seviye bilgisi (LevelResource kullanılarak)
            'level' => new LevelResource($this->whenLoaded('level')), // Modelde $with ile yüklendiği için whenLoaded gerekmeyebilir
            // Veya doğrudan:
            // 'level' => [
            //     'id' => $this->level?->id,
            //     'name' => $this->level?->name, // Veya çevirisi: $this->level?->getTranslation('name', $request->getPreferredLanguage() ?? 'en'),
            //     'min_xp' => $this->level?->min_xp,
            //     'max_xp' => $this->level?->max_xp,
            // ],
            'level_progress' => $this->level_progress, // Modeldeki accessor
            'roles' => $this->getRoleNames(), // Spatie HasRoles trait
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            
            // Kullanıcı istatistikleri
            'statistics' => [
                'current_streak' => $this->current_streak,
                'longest_streak' => $this->longest_streak,
                'total_logins' => $this->logins()->count(),
                'completed_missions' => $completedMissions,
                'completed_lessons' => $completedLessonsCount,
                'join_date' => $this->created_at?->toIso8601String(),
                'join_days' => $this->created_at ? Carbon::now()->diffInDays($this->created_at) : 0,
                'last_login' => $this->logins()->latest('login_date')->first()?->login_date?->toIso8601String(),
            ],
        ]);
    }
} 