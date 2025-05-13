<?php

namespace App\Interfaces\Services\Api;

interface CourseChapterServiceInterface
{
    /**
     * Belirli bir kursa ait bölümleri getir
     * @param int $courseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByCourse(int $courseId);

    /**
     * Belirli bir bölümü kurs bilgisiyle getir
     * @param int $id
     * @return \App\Models\CourseChapter
     */
    public function findActiveWithCourse(int $id);

    /**
     * Bir bölümü tamamlandı olarak işaretle
     * @param int $id
     * @return mixed
     */
    public function markAsCompleted(int $id);
    
    /**
     * Bölümün ön koşullarını getir
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $id);
    
    /**
     * Bölümün kilit durumunu kontrol et
     * @param int $id
     * @return array
     */
    public function checkLockStatus(int $id);
}