<?php

namespace App\Interfaces\Services\Admin;
use App\Interfaces\Services\BaseServiceInterface;
use App\Models\CourseChapterLesson;

interface CourseChapterLessonServiceInterface extends BaseServiceInterface
{
    /**
     * Tüm dersleri getir
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Belirli bir bölüme ait dersleri getir
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByChapter(int $chapterId);

    /**
     * Id'ye göre ders getir
     * @param int $id
     * @return CourseChapterLesson
     */
    public function find(int $id);

    /**
     * Ders oluştur
     * @param array $data
     * @return CourseChapterLesson
     */
    public function create(array $data);

    /**
     * Ders güncelle
     * @param int $id
     * @param array $data
     * @return CourseChapterLesson
     */
    public function update(int $id, array $data);

    /**
     * Ders sil
     * @param int $id
     * @return bool
     */
    public function delete(int $id);

    /**
     * Dersin durumunu değiştir
     * @param int $id
     * @return CourseChapterLesson|null
     */
    public function toggleStatus(int $id);

    /**
     * Dersin sırasını güncelle
     * @param int $id
     * @param int $order
     * @return CourseChapterLesson|null
     */
    public function updateOrder(int $id, int $order);

    /**
     * Belirli bir ders için ön koşul dersleri ekle
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function addPrerequisites(int $lessonId, array $prerequisiteIds): bool;

    /**
     * Belirli bir ders için ön koşul derslerini kaldır
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function removePrerequisites(int $lessonId, array $prerequisiteIds): bool;

    /**
     * Belirli bir ders için tüm ön koşulları kaldır
     * @param int $lessonId
     * @return bool
     */
    public function clearPrerequisites(int $lessonId): bool;

    /**
     * Belirli bir ders için ön koşulları güncelle
     * @param int $lessonId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function updatePrerequisites(int $lessonId, array $prerequisiteIds): bool;

    /**
     * Belirli bir ders için ön koşulları getir
     * @param int $lessonId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $lessonId);
}