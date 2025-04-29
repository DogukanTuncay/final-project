<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Models\Level;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translated = $this->getTranslated($this->resource);
        
        $nextLevel = null;
        
        // Mevcut level varsa, bir sonraki seviyeyi bul
        if ($this->level) {
            $nextLevel = Level::where('level_number', $this->level->level_number + 1)
                ->where('is_active', true)
                ->first();
        }
        
        return array_merge($translated, [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'zip_code' => $this->zip_code,
            'locale' => $this->locale,
            'profile_image' => $this->profile_image,
            'profile_image_url' => $this->profile_image_url,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Level bilgileri
            'experience_points' => $this->experience_points,
            'level' => $this->when($this->level, function() {
                return new LevelResource($this->level);
            }),
            'level_progress' => $this->level_progress, // User modelinde tanımlı attribute
            
            // Bir sonraki seviye bilgileri
            'next_level' => $this->when($nextLevel, function() use ($nextLevel) {
                $levelResource = new LevelResource($nextLevel);
                $data = $levelResource->toArray(request());
                $data['xp_needed'] = $nextLevel->min_xp - $this->experience_points; // Bir sonraki seviyeye ulaşmak için gereken XP
                return $data;
            }),
        ]);
    }
} 