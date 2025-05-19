<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\BaseResource;
use App\Models\LessonCompletion;

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
        
        // Kullanıcıyı al
        try {
            $user = JWTAuth::user();
        } catch (\Exception $e) {
            $user = null;
        }
        
        // Dersi kullanıcının tamamlayıp tamamlamadığını kontrol et
        $is_completed = false;
        if ($user) {
            $is_completed = LessonCompletion::where('user_id', $user->id)
                ->where('lesson_id', $this->id)
                ->exists();
        }
        
        // Ön koşul bilgileri
        $prerequisites = $this->activePrerequisites()->get()->map(function($prerequisite) use ($user) {
            // Ön koşulun tamamlanıp tamamlanmadığını kontrol et
            $prereq_completed = false;
            if ($user) {
                $prereq_completed = LessonCompletion::where('user_id', $user->id)
                    ->where('lesson_id', $prerequisite->id)
                    ->exists();
            }
            
            return [
                'id' => $prerequisite->id,
                'name' => $prerequisite->name,
                'is_completed' => $prereq_completed,
                'order' => $prerequisite->order,
                'thumbnail_url' => $prerequisite->thumbnail_url
            ];
        });
        
        // Ders kilitli mi kontrolü
        $is_locked = false;
        if ($user && $this->activePrerequisites()->exists()) {
            $prerequisiteIds = $this->activePrerequisites()->pluck('course_chapter_lessons.id');
            $completedCount = LessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', $prerequisiteIds)
                ->count();

            $is_locked = $completedCount < $prerequisiteIds->count();
        }
        
        // Eksik ön koşullar
        $missing_prerequisites = [];
        if ($user && $is_locked) {
            $prerequisiteIds = $this->activePrerequisites()->pluck('course_chapter_lessons.id');
            $completedIds = LessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', $prerequisiteIds)
                ->pluck('lesson_id')
                ->toArray();
            
            // Tamamlanmamış ön koşulları bul
            $missingIds = array_diff($prerequisiteIds->toArray(), $completedIds);
            
            if (!empty($missingIds)) {
                $missingPrerequisites = $this->activePrerequisites()->whereIn('course_chapter_lessons.id', $missingIds)->get();
                
                $missing_prerequisites = $missingPrerequisites->map(function($lesson) {
                    return [
                        'id' => $lesson->id,
                        'name' => $lesson->name,
                        'slug' => $lesson->slug
                    ];
                })->toArray();
            }
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
            'is_completed' => $is_completed,
            'missing_prerequisites' => $missing_prerequisites,
            'contents' => $this->whenLoaded('contents', function() {
                return CourseChapterLessonContentResource::collection($this->contents);
            }),
            'contents_count' => $this->whenLoaded('contents', fn() => $this->contents->count(), 
                $this->contents()->count()),
            'chapter' => $this->whenLoaded('courseChapter', function() {
                return new CourseChapterResource($this->courseChapter);
            }),
            'is_locked' => $is_locked,
            'prerequisites' => $this->when($is_locked, $missing_prerequisites),
            'thumbnail' => $this->thumbnail,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}