<?php

namespace App\Interfaces\Services\Admin;

use Illuminate\Database\Eloquent\Collection;

interface CourseChapterLessonContentServiceInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    
    /**
     * Bir ders için tüm içerikleri getir
     * 
     * @param int $lessonId
     * @return Collection
     */
    public function getByLessonId(int $lessonId): Collection;
    
    /**
     * Text içeriği oluştur
     * 
     * @param int $lessonId
     * @param array $contentData
     * @param array $lessonContentData
     * @return mixed
     */
    public function createTextContent(int $lessonId, array $contentData, array $lessonContentData = []);
    
    /**
     * Video içeriği oluştur
     * 
     * @param int $lessonId
     * @param array $contentData
     * @param array $lessonContentData
     * @return mixed
     */
    public function createVideoContent(int $lessonId, array $contentData, array $lessonContentData = []);
    
    /**
     * Boşluk doldurma içeriği oluştur
     * 
     * @param int $lessonId
     * @param array $contentData
     * @param array $lessonContentData
     * @return mixed
     */
    public function createFillInTheBlankContent(int $lessonId, array $contentData, array $lessonContentData = []);
    
    /**
     * İçerik sıralamasını güncelle
     * 
     * @param int $contentId
     * @param int $newOrder
     * @return bool
     */
    public function updateOrder(int $contentId, int $newOrder): bool;
    
    /**
     * İçeriklerin sıralamasını toplu güncelle
     * 
     * @param array $orderData
     * @return bool
     */
    public function bulkUpdateOrder(array $orderData): bool;
}