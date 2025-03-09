<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\CourseChapterLessonServiceInterface;
use App\Interfaces\Repositories\Api\CourseChapterLessonRepositoryInterface;
use App\Models\CourseChapterLesson;
use App\Models\LessonCompletion;
use Illuminate\Database\Eloquent\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;
class CourseChapterLessonService implements CourseChapterLessonServiceInterface
{
    private CourseChapterLessonRepositoryInterface $repository;

    public function __construct(CourseChapterLessonRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Belirli bir bölüme ait dersleri getir
     * @param int $chapterId
     * @return Collection
     */
    public function findByChapter(int $chapterId)
    {
        return $this->repository->findByChapter($chapterId);
    }
    
    /**
     * Belirli bir dersi bölüm bilgisiyle getir
     * @param int $id
     * @return CourseChapterLesson
     */
    public function findActive(int $id)
    {
        return $this->repository->findActive($id);
    }
    
    /**
     * Bir dersi tamamlandı olarak işaretle
     * @param int $id
     * @return LessonCompletion
     */
    public function markAsCompleted(int $id)
    {
        $userId = JWTAuth::user()->id;
        
        // Daha önce tamamlanmış mı kontrol et
        $existing = LessonCompletion::where('lesson_id', $id)
            ->where('user_id', $userId)
            ->first();
            
        if ($existing) {
            return null;
        }
        
        // Yeni tamamlama kaydı oluştur
        return LessonCompletion::create([
            'lesson_id' => $id,
            'user_id' => $userId,
            'completed_at' => now()
        ]);
    }
}