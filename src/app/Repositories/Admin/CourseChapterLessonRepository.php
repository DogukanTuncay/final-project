<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CourseChapterLessonRepositoryInterface;
use App\Models\CourseChapterLesson;
use App\Repositories\BaseRepository;

class CourseChapterLessonRepository extends BaseRepository implements CourseChapterLessonRepositoryInterface
{
    public function __construct(CourseChapterLesson $model)
    {
        parent::__construct($model);
    }

    /**
     * Bölüme göre dersleri bulur
     *
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByChapter(int $chapterId)
    {
        return $this->model->where('course_chapter_id', $chapterId)->orderBy('order')->get();
    }

    /**
     * Ders durumunu değiştirir
     *
     * @param int $id
     * @return CourseChapterLesson|null
     */
    public function toggleStatus(int $id): ?CourseChapterLesson
    {
        $courseChapterLesson = $this->find($id);
        
        if (!$courseChapterLesson) {
            return null;
        }
        
        $courseChapterLesson->is_active = !$courseChapterLesson->is_active;
        $courseChapterLesson->save();
        
        return $courseChapterLesson;
    }

    /**
     * Ders sırasını günceller
     *
     * @param int $id
     * @param int $order
     * @return CourseChapterLesson|null
     */
    public function updateOrder(int $id, int $order): ?CourseChapterLesson
    {
        $courseChapterLesson = $this->find($id);
        
        if (!$courseChapterLesson) {
            return null;
        }
        
        $courseChapterLesson->order = $order;
        $courseChapterLesson->save();
        
        return $courseChapterLesson;
    }

    /**
     * Belirli bir ders için ön koşul dersleri ekler
     * 
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function addPrerequisites(int $lessonId, array $prerequisiteIds): bool
    {
        try {
            $lesson = $this->find($lessonId);
            
            // Kendisini ön koşul olarak eklemesini engelle
            $prerequisiteIds = array_filter($prerequisiteIds, function($id) use ($lessonId) {
                return $id != $lessonId;
            });
            
            if (empty($prerequisiteIds)) {
                return true;
            }
            
            // Önkoşulları ekle
            $existingPrerequisites = $lesson->prerequisites()->pluck('course_chapter_lessons.id')->toArray();
            $newPrerequisites = array_diff($prerequisiteIds, $existingPrerequisites);
            
            if (!empty($newPrerequisites)) {
                $lesson->prerequisites()->attach($newPrerequisites);
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Belirli bir ders için ön koşul derslerini kaldırır
     * 
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function removePrerequisites(int $lessonId, array $prerequisiteIds): bool
    {
        try {
            $lesson = $this->find($lessonId);
            
            if (empty($prerequisiteIds)) {
                return true;
            }
            
            // Önkoşulları kaldır
            $lesson->prerequisites()->detach($prerequisiteIds);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Belirli bir ders için tüm ön koşulları kaldırır
     * 
     * @param int $lessonId
     * @return bool
     */
    public function clearPrerequisites(int $lessonId): bool
    {
        try {
            $lesson = $this->find($lessonId);
            $lesson->prerequisites()->detach();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Belirli bir ders için ön koşulları günceller (mevcut olanları kaldırıp yenilerini ekler)
     * 
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function updatePrerequisites(int $lessonId, array $prerequisiteIds): bool
    {
        try {
            // Kendisini ön koşul olarak eklemesini engelle
            $prerequisiteIds = array_filter($prerequisiteIds, function($id) use ($lessonId) {
                return $id != $lessonId;
            });
            
            $lesson = $this->find($lessonId);
            $lesson->prerequisites()->sync($prerequisiteIds);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Belirli bir ders için ön koşulları getirir
     * 
     * @param int $lessonId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $lessonId)
    {
        $lesson = $this->find($lessonId);
        return $lesson->prerequisites;
    }
}