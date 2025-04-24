<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class CourseChapterLessonResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'course_chapter_id' => $this->course_chapter_id,
            'slug' => $this->slug,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'thumbnail_url' => $this->thumbnail_url,
            'duration' => $this->duration,
            'xp_reward' => $this->xp_reward,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'course_chapter' => $this->whenLoaded('courseChapter', function() {
                return new CourseChapterResource($this->courseChapter);
            })
        ]);
    }
}