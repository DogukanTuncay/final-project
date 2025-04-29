<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class StoryResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        // getTranslated mevcut dilin çevirisini getirir (title)
        $translated = $this->getTranslated($this->resource);

        return [
            'id' => $this->id,
            // 'story_category_id' => $this->story_category_id, // API'de belki gerekli değil?
            'title' => $translated['title'] ?? null, // Sadece mevcut dilin başlığı
            'media_url' => $this->media_url,
            'content' => $this->content,
            'order_column' => $this->order_column,
            // 'is_active' => $this->is_active, // API'de sadece aktifler listelendiği için gereksiz olabilir
            'created_at' => $this->created_at?->toIso8601String(),
            // İlişkili kategori bilgisini de ekleyebiliriz (eğer yüklendiyse)
            // Kategori Resource'unun da API versiyonu olmalı
            'category' => new StoryCategoryResource($this->whenLoaded('storyCategory')),
        ];
    }
}