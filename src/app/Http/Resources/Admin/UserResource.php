<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Api\LevelResource;

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
            'level_progress' => $this->level_progress,
            
            // Roller ve izinler (Admin panelde gerekli olabilir)
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ]);
    }
} 