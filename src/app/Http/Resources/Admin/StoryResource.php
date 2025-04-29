<?php

namespace App\Http\Resources\Admin;

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
        // getTranslated sadece çevrilebilir alanları (title) getirir
        $translated = $this->getTranslated($this->resource);

        return [
            'id' => $this->id,
            'story_category_id' => $this->story_category_id,
            'title' => $this->getTranslations('title'), // Tüm çevirileri alalım
            'media_url' => $this->media_url,
            'content' => $this->content,
            'order_column' => $this->order_column,
            'is_active' => $this->is_active,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'images' => $this->images,
            'images_url' => $this->images_url,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            // İlişkili kategori bilgisini de ekleyebiliriz (eğer yüklendiyse)
            'story_category' => new StoryCategoryResource($this->whenLoaded('storyCategory')),
        ];
    }
}