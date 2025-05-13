<?php

namespace App\Interfaces\Repositories\Admin;
use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CourseChapterRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCourse(int $courseId);
    public function updateOrder(int $id, int $order);
    public function toggleStatus(int $id);

    /**
     * Belirli bir bölüm için ön koşul bölümleri ekler
     * 
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function addPrerequisites(int $chapterId, array $prerequisiteIds): bool;

    /**
     * Belirli bir bölüm için ön koşul bölümlerini kaldırır
     * 
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function removePrerequisites(int $chapterId, array $prerequisiteIds): bool;

    /**
     * Belirli bir bölüm için tüm ön koşulları kaldırır
     * 
     * @param int $chapterId
     * @return bool
     */
    public function clearPrerequisites(int $chapterId): bool;

    /**
     * Belirli bir bölüm için ön koşulları günceller (mevcut olanları kaldırıp yenilerini ekler)
     * 
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function updatePrerequisites(int $chapterId, array $prerequisiteIds): bool;

    /**
     * Belirli bir bölüm için ön koşulları getirir
     * 
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $chapterId);
}