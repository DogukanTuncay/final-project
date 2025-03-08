<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class CourseResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'slug' => $this->slug,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'images' => $this->images,
            'images_url' => $this->images_url,
            'is_active' => $this->is_active,
            'order' => $this->order,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'is_featured' => $this->is_featured,
            'completion_status' => $this->completion_status,
            'created_at' => $this->when($this->created_at, $this->created_at),
            'updated_at' => $this->when($this->updated_at, $this->updated_at),
        ]);
    }
}