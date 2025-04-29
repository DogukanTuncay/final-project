<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Auth;

class CourseResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        // İlerleme Hesaplaması (Course seviyesinde)
        $total_lessons_in_course = 0;
        $completed_lessons_in_course = 0;
        $user = Auth::user();

        // Kursun tüm derslerini (bölümler aracılığıyla) ve kullanıcı ilerlemesini yüklediğimizi varsayalım
        if ($this->relationLoaded('courseChapters')) {
            foreach ($this->courseChapters as $chapter) {
                if ($chapter->relationLoaded('lessons')) {
                    $total_lessons_in_course += $chapter->lessons->count();
                    if ($user) {
                        $completed_lessons_in_course += $chapter->lessons->filter(function ($lesson) use ($user) {
                            // 'userProgress' ilişkisi ve 'is_completed' alanı olduğunu varsayıyoruz
                            return $lesson->relationLoaded('userProgress') && $lesson->userProgress?->is_completed;
                        })->count();
                    }
                }
            }
        }

        $course_completion_percentage = $total_lessons_in_course > 0 ? round(($completed_lessons_in_course / $total_lessons_in_course) * 100) : 0;

        return array_merge($translated, [
            'id' => $this->id,
            'slug' => $this->slug,
            'difficulty' => $this->difficulty,
            'completion_percentage' => $course_completion_percentage,
            'chapters' => CourseChapterResource::collection($this->whenLoaded('courseChapters')),
            'image_url' => $this->image ? asset($this->image) : null,
            'images_url' => $this->images ? collect($this->images)->map(fn($image) => asset($image)) : [],
            // Add other non-translatable attributes here
        ]);
    }
}