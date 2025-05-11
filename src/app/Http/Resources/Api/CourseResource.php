<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Auth;
use App\Models\LessonCompletion;

class CourseResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        // İlerleme Hesaplaması (Course seviyesinde)
        $total_lessons_in_course = 0;
        $completed_lessons_in_course = 0;
        $user = Auth::user();

        // Kursun tüm derslerini (bölümler aracılığıyla) hesapla
        if ($this->relationLoaded('chapters')) {
            $lessonsIds = [];
            
            foreach ($this->chapters as $chapter) {
                if ($chapter->relationLoaded('lessons')) {
                    $chapterLessonsIds = $chapter->lessons->pluck('id')->toArray();
                    $lessonsIds = array_merge($lessonsIds, $chapterLessonsIds);
                    $total_lessons_in_course += count($chapterLessonsIds);
                }
            }
            
            // Eğer kullanıcı giriş yapmışsa, tamamlanmış dersleri say
            if ($user) {
                $completed_lessons_in_course = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonsIds)
                    ->count();
            }
        } else {
            // Kurs ilişkileri yüklenmemişse, lessons() ilişkisini kullanarak hesapla
            $total_lessons_in_course = $this->lessons()->count();
            
            if ($user) {
                $lessonsIds = $this->lessons()->pluck('course_chapter_lessons.id')->toArray();
                $completed_lessons_in_course = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonsIds)
                    ->count();
            }
        }

        $course_completion_percentage = $total_lessons_in_course > 0 ? round(($completed_lessons_in_course / $total_lessons_in_course) * 100) : 0;

        return array_merge($translated, [
            'id' => $this->id,
            'slug' => $this->slug,
            'difficulty' => $this->difficulty,
            'completion_percentage' => $course_completion_percentage,
            'completion_status' => [
                'completed' => $total_lessons_in_course > 0 && $total_lessons_in_course === $completed_lessons_in_course,
                'progress' => $course_completion_percentage,
                'total_lessons' => $total_lessons_in_course,
                'completed_lessons' => $completed_lessons_in_course
            ],
            'chapters' => CourseChapterResource::collection($this->whenLoaded('chapters')),
            'image_url' => $this->image_url,
            'images_url' => $this->images_url,
            // Add other non-translatable attributes here
        ]);
    }
}