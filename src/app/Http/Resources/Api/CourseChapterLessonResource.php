<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\BaseResource;

class CourseChapterLessonResource extends BaseResource
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
        $translated = $this->getTranslated($this->resource);
        return array_merge($translated, [
            'id' => $this->id,
            'course_chapter_id' => $this->course_chapter_id,
            'slug' => $this->slug,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'difficulty' => $this->difficulty,
            'thumbnail_url' => $this->thumbnail_url,
            'duration' => $this->duration,
            'is_free' => $this->is_free,
            'xp_reward' => $this->xp_reward,
            'is_completed' => $this->is_completed,
            'missing_prerequisites' => $this->missing_prerequisites,
            'contents' => $this->whenLoaded('contents', function() {
                return CourseChapterLessonContentResource::collection($this->contents);
            }),
            'contents_count' => $this->whenLoaded('contents', fn() => $this->contents->count()),
            'chapter' => $this->whenLoaded('courseChapter', function() {
                return new CourseChapterResource($this->courseChapter);
            }),
            'is_locked' => $is_locked,
            'prerequisites' => $this->when($is_locked, $this->missing_prerequisites),
            'thumbnail' => $this->thumbnail,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}