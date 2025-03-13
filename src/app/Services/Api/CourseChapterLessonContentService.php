<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\CourseChapterLessonContentServiceInterface;
use App\Interfaces\Repositories\Api\CourseChapterLessonContentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseChapterLessonContentService implements CourseChapterLessonContentServiceInterface
{
    protected $repository;

    public function __construct(CourseChapterLessonContentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ID'ye göre içerik bul
     * 
     * @param int $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Ders ID'sine göre içerikleri getir
     * 
     * @param int $lessonId
     * @return Collection
     */
    public function getByLessonId(int $lessonId): Collection
    {
        return $this->repository->getByLessonId($lessonId);
    }

    /**
     * Ders ID'sine ve içerik tipine göre içerikleri getir
     * 
     * @param int $lessonId
     * @param string $contentType
     * @return Collection
     */
    public function getByContentType(int $lessonId, string $contentType): Collection
    {
        return $this->repository->getByContentType($lessonId, $contentType);
    }

    /**
     * Sayfalama ile içerikleri getir
     * 
     * @param array $params
     * @return mixed
     */
    public function getWithPagination(array $params)
    {
        return $this->repository->getWithPagination($params);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}