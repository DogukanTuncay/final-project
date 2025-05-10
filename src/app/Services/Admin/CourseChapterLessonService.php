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

    public function create(array $data)
    {
        $courseChapterLesson = parent::create($data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($courseChapterLesson, $data['image']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($courseChapterLesson, $data['images']);
        }

        return $courseChapterLesson;
    }

    public function update($id, array $data)
    {
        $courseChapter = parent::update($id, $data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($courseChapter, $data['image']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($courseChapter, $data['images']);
        }

        return $courseChapter;
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

    
    public function handleImage($courseChapterLesson, UploadedFile $image)
    {
        return $courseChapterLesson->uploadImage($image);
    }

    public function handleImages($courseChapterLesson, array $images)
    {
        return $courseChapterLesson->uploadImages($images);
    }
}