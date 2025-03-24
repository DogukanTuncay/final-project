<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseChapterLessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Ders yüklenmemişse relation'ları şimdi yükle
        if (!$this->relationLoaded('prerequisites')) {
            $this->load('prerequisites');
        }
        
        // Ön koşul bilgileri
        $prerequisites = $this->prerequisites->map(function($prerequisite) {
            return [
                'id' => $prerequisite->id,
                'name' => $prerequisite->name,
                'is_completed' => $prerequisite->is_completed,
                'order' => $prerequisite->order,
                'thumbnail_url' => $prerequisite->thumbnail_url
            ];
        });
        
        // Ders kilitli mi kontrolü
        $is_locked = false;
        try {
            $user = JWTAuth::user();
            if ($user && $this->prerequisites()->exists()) {
                $prerequisiteIds = $this->prerequisites()->pluck('course_chapter_lessons.id');
                $completedCount = \App\Models\LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $prerequisiteIds)
                    ->count();

                $is_locked = $completedCount < $prerequisiteIds->count();
            }
        } catch (\Exception $e) {
            // Token bulunamadı veya geçersiz, ders kilitli değil
            $is_locked = false;
        }
        
        return [
            'id' => $this->id,
            'course_chapter_id' => $this->course_chapter_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'is_locked' => $is_locked,
            'prerequisites' => $this->when($is_locked, $this->missing_prerequisites),
            'thumbnail' => $this->thumbnail,
            'thumbnail_url' => $this->thumbnail_url,
            'duration' => $this->duration,
            'is_completed' => $this->is_completed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}