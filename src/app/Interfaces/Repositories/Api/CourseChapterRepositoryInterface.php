<?php

namespace App\Interfaces\Repositories\Api;
use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CourseChapterRepositoryInterface extends BaseRepositoryInterface
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
     * @param int $userId
     * @return \App\Models\ChapterCompletion
     */
    public function markAsCompleted(int $id, int $userId);
    
    /**
     * Bölümün ön koşullarını getir
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $id);
    
    /**
     * Belirli bir bölümün kilit durumunu kontrol et
     * @param int $id
     * @param int $userId
     * @return array
     */
    public function checkLockStatus(int $id, int $userId);
}