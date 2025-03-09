<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class CourseChapterResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'slug' => $this->slug,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}