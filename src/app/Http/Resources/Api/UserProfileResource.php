<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\LevelResource; // LevelResource varsa

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
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
            // İzinleri de eklemek isterseniz:
            // 'permissions' => $this->getAllPermissions()->pluck('name'),
        ];
    }
} 