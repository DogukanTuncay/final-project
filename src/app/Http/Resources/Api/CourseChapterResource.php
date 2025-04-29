<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Auth;

class CourseChapterResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        // İlerleme Hesaplaması
        $total_lessons = $this->whenLoaded('lessons', fn() => $this->lessons->count(), 0);
        $completed_lessons_count = 0;
        $user = Auth::user();

        if ($user && $this->relationLoaded('lessons')) {
            $completed_lessons_count = $this->lessons->filter(function ($lesson) use ($user) {
                return $lesson->relationLoaded('userProgress') && $lesson->userProgress?->is_completed;
            })->count();
        }   

        $completion_percentage = $total_lessons > 0 ? round(($completed_lessons_count / $total_lessons) * 100) : 0;

        return array_merge($translated, [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'slug' => $this->slug,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'completion_percentage' => $completion_percentage,
            'lessons' => CourseChapterLessonResource::collection($this->whenLoaded('lessons')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Add other non-translatable attributes here
        ]);
    }
}