<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class BadgeResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        $data = array_merge($translated, [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        
        // Kullanıcı giriş yapmışsa, kazanıp kazanmadığını bilgisini ekle
        $user = JWTAuth::user();
        if ($user) {
            $data['is_earned'] = $this->isEarnedByUser($user);
            
            // Pivottan earned_at bilgisi varsa ekle
            if (isset($this->pivot) && $this->pivot->earned_at) {
                $data['earned_at'] = $this->pivot->earned_at;
            }
        }
        
        return $data;
    }
}