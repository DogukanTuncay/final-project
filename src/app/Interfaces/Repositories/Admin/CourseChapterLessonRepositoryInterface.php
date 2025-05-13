<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use App\Models\CourseChapterLesson;

interface CourseChapterLessonRepositoryInterface extends BaseRepositoryInterface
{

    public function findByChapter(int $chapterId);
    public function toggleStatus(int $id): ?CourseChapterLesson;
    public function updateOrder(int $id, int $order): ?CourseChapterLesson;

    /**
     * Belirli bir ders için ön koşul dersleri ekler
     * 
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function addPrerequisites(int $lessonId, array $prerequisiteIds): bool;

    /**
     * Belirli bir ders için ön koşul derslerini kaldırır
     * 
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function removePrerequisites(int $lessonId, array $prerequisiteIds): bool;

    /**
     * Belirli bir ders için tüm ön koşulları kaldırır
     * 
     * @param int $lessonId
     * @return bool
     */
    public function clearPrerequisites(int $lessonId): bool;

    /**
     * Belirli bir ders için ön koşulları günceller (mevcut olanları kaldırıp yenilerini ekler)
     * 
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function updatePrerequisites(int $lessonId, array $prerequisiteIds): bool;

    /**
     * Belirli bir ders için ön koşulları getirir
     * 
     * @param int $lessonId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $lessonId);
}