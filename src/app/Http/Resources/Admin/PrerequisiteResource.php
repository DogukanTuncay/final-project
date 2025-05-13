<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class PrerequisiteResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * 
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // Çeviriye sahip alanları getir
        $translated = $this->getTranslated($this->resource);

        // Temel verileri döndür
        $data = array_merge($translated, [
            'id' => $this->id,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        // Resource tipine göre ek bilgiler
        if ($this->resource instanceof \App\Models\CourseChapter) {
            $data = array_merge($data, [
                'course_id' => $this->course_id,
                'course' => new CourseResource($this->whenLoaded('course')),
                'lessons_count' => $this->resource->lessons()->count(),
            ]);
        } elseif ($this->resource instanceof \App\Models\CourseChapterLesson) {
            $data = array_merge($data, [
                'course_chapter_id' => $this->course_chapter_id,
                'course_chapter' => new CourseChapterResource($this->whenLoaded('courseChapter')),
                'is_free' => $this->is_free,
                'duration' => $this->duration,
                'thumbnail_url' => $this->thumbnail_url
            ]);
        }

        return $data;
    }
} 