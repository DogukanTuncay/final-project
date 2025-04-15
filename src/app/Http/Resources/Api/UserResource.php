<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Level;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $nextLevel = null;
        
        // Mevcut level varsa, bir sonraki seviyeyi bul
        if ($this->level) {
            $nextLevel = Level::where('level_number', $this->level->level_number + 1)
                ->where('is_active', true)
                ->first();
        }
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'zip_code' => $this->zip_code,
            'locale' => $this->locale,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Level bilgileri
            'experience_points' => $this->experience_points,
            'level' => $this->when($this->level, function() {
                return [
                    'id' => $this->level->id,
                    'name' => $this->level->name,
                    'level_number' => $this->level->level_number,
                    'min_xp' => $this->level->min_xp,
                    'max_xp' => $this->level->max_xp,
                    'icon' => $this->level->icon,
                ];
            }),
            'level_progress' => $this->level_progress, // User modelinde tanımlı attribute
            
            // Bir sonraki seviye bilgileri
            'next_level' => $this->when($nextLevel, function() use ($nextLevel) {
                return [
                    'id' => $nextLevel->id,
                    'name' => $nextLevel->name,
                    'level_number' => $nextLevel->level_number,
                    'min_xp' => $nextLevel->min_xp,
                    'max_xp' => $nextLevel->max_xp,
                    'icon' => $nextLevel->icon,
                    'xp_needed' => $nextLevel->min_xp - $this->experience_points, // Bir sonraki seviyeye ulaşmak için gereken XP
                ];
            }),
        ];
    }
} 