<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BaseResource;
class LevelResource extends BaseResource
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
            'name' => $translated['title'], // Seviye adı (muhtemelen çevrilebilir)
            'level_number' => $this->level_number, // Seviye numarası (varsa)
            'min_xp' => $this->min_xp, // Bu seviye için minimum XP
            'max_xp' => $this->max_xp, // Bu seviyeden sonraki seviye için gereken XP
        ]);
    }
} 