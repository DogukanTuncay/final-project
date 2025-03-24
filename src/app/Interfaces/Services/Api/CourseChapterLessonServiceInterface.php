<?php

namespace App\Interfaces\Services\Api;

interface CourseChapterLessonServiceInterface
{
    /**
     * Belirli bir bölüme ait dersleri getir
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByChapter(int $chapterId);
    
    /**
     * Belirli bir dersi bölüm bilgisiyle getir
     * @param int $id
     * @return \App\Models\CourseChapterLesson
     */
    public function findActive(int $id);
    
    /**
     * Bir dersi tamamlandı olarak işaretle
     * @param int $id
     * @return \App\Models\LessonCompletion
     */
    public function markAsCompleted(int $id);

    /**
     * Dersin ön koşullarını getir
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $id);

    /**
     * Dersin kilit durumunu kontrol et
     * @param int $id
     * @return array
     */
    public function checkLockStatus(int $id);
}