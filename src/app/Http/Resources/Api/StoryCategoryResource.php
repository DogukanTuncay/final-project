<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Resource'un BaseResource'tan kalıtım alıp almadığını kontrol etmek gerekir.
        // Eğer BaseResource ve getTranslated metodu varsa aşağıdaki gibi kullanılabilir:
         $translated = $this->getTranslated($this->resource);
        return array_merge($translated, [
            'id' => $this->id,
            'slug' => $this->slug,
        ]);
     
    }
}