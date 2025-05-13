<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Auth;
use App\Models\LessonCompletion;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            $total_lessons = $this->activeLessons()->count();
            
            if ($user) {
                $lessonsIds = $this->activeLessons()->pluck('id')->toArray();
                $completed_lessons_count = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonsIds)
                    ->count();
            }
        } else {
            // İlişki yüklenmemişse, modelden çağırıp hesaplama yap
            $total_lessons = $this->activeLessons()->count();
            
            if ($user) {
                $lessonsIds = $this->activeLessons()->pluck('id')->toArray();
                $completed_lessons_count = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonsIds)
                    ->count();
            }
        }

        $completion_percentage = $total_lessons > 0 ? round(($completed_lessons_count / $total_lessons) * 100) : 0;

        // Ön koşul ilişkileri yüklü değilse şimdi yükle
        if (!$this->relationLoaded('prerequisites')) {
            $this->load('prerequisites');
        }

        // Kullanıcıyı al
        try {
            $user = JWTAuth::user();
        } catch (\Exception $e) {
            $user = null;
        }

        // Tamamlanma bilgisi ve önkoşul durumu
        $is_completed = $this->is_completed; // Model'deki accessor kullanılacak
        
        // Ön koşul bilgileri
        $prerequisites = $this->prerequisites->map(function($prerequisite) use ($user) {
            // Ön koşulun tamamlanıp tamamlanmadığını kontrol et
            $prereq_completed = false;
            if ($user) {
                $prereq_completed = $prerequisite->is_completed;
            }
            
            return [
                'id' => $prerequisite->id,
                'name' => $prerequisite->name,
                'is_completed' => $prereq_completed,
                'order' => $prerequisite->order,
                'image_url' => $prerequisite->image_url
            ];
        });
        
        // Bölüm kilitli mi kontrolü
        $is_locked = false;
        if ($user && $this->activePrerequisites()->exists()) {
            $missing_prerequisites = $this->missing_prerequisites;
            $is_locked = !empty($missing_prerequisites);
        }

        return array_merge($translated, [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'slug' => $this->slug,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'difficulty' => $this->difficulty,
            'image_url' => $this->image_url,
            'images_url' => $this->images_url,
            'completion_status' => [
                'completed' => $is_completed,
                'progress' => $completion_percentage,
                'total_lessons' => $total_lessons,
                'completed_lessons' => $completed_lessons_count
            ],
            'is_locked' => $is_locked,
            'prerequisites' => $prerequisites,
            'missing_prerequisites' => $this->missing_prerequisites,
            'lessons' => CourseChapterLessonResource::collection($this->whenLoaded('lessons')),
            'lessons_count' => $total_lessons,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Add other non-translatable attributes here
        ]);
    }
}