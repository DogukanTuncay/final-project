<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\CourseChapterLessonServiceInterface;
use App\Interfaces\Repositories\Admin\CourseChapterLessonRepositoryInterface;
use App\Services\BaseService;
use App\Models\CourseChapterLesson;

class CourseChapterLessonService extends BaseService implements CourseChapterLessonServiceInterface
{
    public function __construct(CourseChapterLessonRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Bölüme göre dersleri bulur
     *
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByChapter(int $chapterId)
    {
        return $this->repository->findByChapter($chapterId);
    }

    /**
     * Ders durumunu değiştirir
     *
     * @param int $id
     * @return CourseChapterLesson|null
     */
    public function toggleStatus(int $id): ?CourseChapterLesson
    {
        return $this->repository->toggleStatus($id);
    }

    /**
     * Ders sırasını günceller
     *
     * @param int $id
     * @param int $order
     * @return CourseChapterLesson|null
     */
    public function updateOrder(int $id, int $order): ?CourseChapterLesson
    {
        return $this->repository->updateOrder($id, $order);
    }
}