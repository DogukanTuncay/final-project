<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Auth;
use App\Models\LessonCompletion;

class CourseChapterResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        // İlerleme Hesaplaması
        $user = Auth::user();
        $total_lessons = 0;
        $completed_lessons_count = 0;

        if ($this->relationLoaded('lessons')) {
            $total_lessons = $this->lessons->count();
            
            if ($user) {
                $lessonsIds = $this->lessons->pluck('id')->toArray();
                $completed_lessons_count = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonsIds)
                    ->count();
            }
        } else {
            // İlişki yüklenmemişse, modelden çağırıp hesaplama yap
            $total_lessons = $this->lessons()->count();
            
            if ($user) {
                $lessonsIds = $this->lessons()->pluck('id')->toArray();
                $completed_lessons_count = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonsIds)
                    ->count();
            }
        }
        $completion_percentage = $total_lessons > 0 ? round(($completed_lessons_count / $total_lessons) * 100) : 0;

        return array_merge($translated, [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'slug' => $this->slug,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'difficulty' => $this->difficulty,
            'image_url' => $this->image_url,
            'images_url' => $this->images_url,
            'completion_percentage' => $completion_percentage,
            'completion_status' => [
                'completed' => $total_lessons > 0 && $total_lessons === $completed_lessons_count,
                'progress' => $completion_percentage,
                'total_lessons' => $total_lessons,
                'completed_lessons' => $completed_lessons_count
            ],
            'lessons' => CourseChapterLessonResource::collection($this->whenLoaded('lessons')),
            'lessons_count' => $total_lessons,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Add other non-translatable attributes here
        ]);
    }
}