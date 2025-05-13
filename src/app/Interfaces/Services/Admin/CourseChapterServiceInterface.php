<?php

namespace App\Interfaces\Services\Admin;
use App\Interfaces\Services\BaseServiceInterface;
interface CourseChapterServiceInterface extends BaseServiceInterface
{
    /**
     * Tüm bölümleri getir
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Belirli bir kursa ait bölümleri getir
     * @param int $courseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByCourse(int $courseId);

    /**
     * Id'ye göre bölüm getir
     * @param int $id
     * @return \App\Models\CourseChapter
     */
    public function find(int $id);

    /**
     * Bölüm oluştur
     * @param array $data
     * @return \App\Models\CourseChapter
     */
    public function create(array $data);

    /**
     * Bölüm güncelle
     * @param int $id
     * @param array $data
     * @return \App\Models\CourseChapter
     */
    public function update(int $id, array $data);

    /**
     * Bölüm sil
     * @param int $id
     * @return bool
     */
    public function delete(int $id);

    /**
     * Bölüm sıralamasını güncelle
     * @param int $id
     * @param int $order
     * @return \App\Models\CourseChapter|null
     */
    public function updateOrder(int $id, int $order);

    /**
     * Bölüm aktiflik durumunu değiştir
     * @param int $id
     * @return \App\Models\CourseChapter|null
     */
    public function toggleStatus(int $id);

    /**
     * Belirli bir bölüm için ön koşul bölümleri ekle
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function addPrerequisites(int $chapterId, array $prerequisiteIds): bool;

    /**
     * Belirli bir bölüm için ön koşul bölümlerini kaldır
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function removePrerequisites(int $chapterId, array $prerequisiteIds): bool;

    /**
     * Belirli bir bölüm için tüm ön koşulları kaldır
     * @param int $chapterId
     * @return bool
     */
    public function clearPrerequisites(int $chapterId): bool;

    /**
     * Belirli bir bölüm için ön koşulları güncelle
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function updatePrerequisites(int $chapterId, array $prerequisiteIds): bool;

    /**
     * Belirli bir bölüm için ön koşulları getir
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $chapterId);
}