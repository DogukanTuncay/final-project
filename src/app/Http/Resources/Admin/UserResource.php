<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'level_progress' => $this->level_progress,
            
            // Roller ve izinler (Admin panelde gerekli olabilir)
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ];
    }
} 