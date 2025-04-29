<?php

namespace App\Repositories\Api;

use App\Models\CourseChapterLessonContent;
use App\Interfaces\Repositories\Api\CourseChapterLessonContentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseChapterLessonContentRepository implements CourseChapterLessonContentRepositoryInterface
{
    protected $model;

    public function __construct(CourseChapterLessonContent $model)
    {
        $this->model = $model;
    }

    /**
     * ID'ye göre içerik bul
     * 
     * @param int $id
     * @return CourseChapterLessonContent
     */
    public function findById($id)
    {
        return $this->model->with('contentable')->findOrFail($id);
    }

    /**
     * Ders ID'sine göre içerikleri getir
     * 
     * @param int $lessonId
     * @return Collection
     */
    public function getByLessonId(int $lessonId): Collection
    {
        // Önce tüm içerikleri getir
        $contents = $this->model
            ->with('contentable')
            ->where('course_chapter_lesson_id', $lessonId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
            
        // Soft delete edilmiş içerikleri filtrele
        return $contents->filter(function ($content) {
            return $content->contentable !== null;
        });
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
        // Önce tüm içerikleri getir
        $contents = $this->model
            ->with('contentable')
            ->where('course_chapter_lesson_id', $lessonId)
            ->where('contentable_type', $contentType)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
            
        // Soft delete edilmiş içerikleri filtrele
        return $contents->filter(function ($content) {
            return $content->contentable !== null;
        });
    }

    /**
     * Sayfalama ile içerikleri getir
     * 
     * @param array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWithPagination(array $params)
    {
        $query = $this->model->with('contentable');

        // Ders ID'sine göre filtrele
        if (isset($params['lesson_id'])) {
            $query->where('course_chapter_lesson_id', $params['lesson_id']);
        }

        // İçerik tipine göre filtrele
        if (isset($params['content_type'])) {
            $query->where('contentable_type', $params['content_type']);
        }

        // Sadece aktif içerikleri getir
        $query->where('is_active', true);

        // Sıralama işlemleri
        $orderBy = $params['order_by'] ?? 'order';
        $orderDirection = $params['order_direction'] ?? 'asc';
        $query->orderBy($orderBy, $orderDirection);

        // Sayfalama
        $perPage = $params['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}