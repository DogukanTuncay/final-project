<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class CourseResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'slug' => $this->slug,
            'difficulty' => $this->difficulty,
            'chapters' => CourseChapterResource::collection($this->whenLoaded('courseChapters')),
            'image_url' => $this->image ? asset($this->image) : null,
            'images_url' => $this->images ? collect($this->images)->map(fn($image) => asset($image)) : [],
            // Add other non-translatable attributes here
        ]);
    }
}