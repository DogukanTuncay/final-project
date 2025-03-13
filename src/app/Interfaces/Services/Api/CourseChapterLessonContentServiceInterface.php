<?php

namespace App\Interfaces\Services\Api;

use Illuminate\Database\Eloquent\Collection;

interface CourseChapterLessonContentServiceInterface
{
    /**
     * ID'ye göre içerik bul
     * 
     * @param int $id
     * @return mixed
     */
    public function findById($id);
    
    /**
     * Ders ID'sine göre içerikleri getir
     * 
     * @param int $lessonId
     * @return Collection
     */
    public function getByLessonId(int $lessonId): Collection;
    
    /**
     * Ders ID'sine ve içerik tipine göre içerikleri getir
     * 
     * @param int $lessonId
     * @param string $contentType
     * @return Collection
     */
    public function getByContentType(int $lessonId, string $contentType): Collection;
    
    /**
     * Sayfalama ile içerikleri getir
     * 
     * @param array $params
     * @return mixed
     */
    public function getWithPagination(array $params);
}