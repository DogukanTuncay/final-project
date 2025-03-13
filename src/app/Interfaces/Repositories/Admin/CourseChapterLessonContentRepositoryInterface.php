<?php

namespace App\Interfaces\Repositories\Admin;

use App\Models\CourseChapterLessonContent;
use Illuminate\Database\Eloquent\Collection;

interface CourseChapterLessonContentRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    
    /**
     * Bir ders için içerikleri getir
     * 
     * @param int $lessonId
     * @return Collection
     */
    public function getByLessonId(int $lessonId): Collection;
    
    /**
     * Belirli tipteki içerikleri getir
     * 
     * @param int $lessonId
     * @param string $contentType
     * @return Collection
     */
    public function getByContentType(int $lessonId, string $contentType): Collection;
    
    /**
     * Polimorfik içerik oluştur
     * 
     * @param int $lessonId
     * @param object $contentable
     * @param array $data
     * @return CourseChapterLessonContent
     */
    public function createWithContent(int $lessonId, object $contentable, array $data = []): CourseChapterLessonContent;
    
    /**
     * İçeriğin sıralamasını güncelle
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