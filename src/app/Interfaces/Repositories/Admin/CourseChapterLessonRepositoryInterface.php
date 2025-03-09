<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use App\Models\CourseChapterLesson;

interface CourseChapterLessonRepositoryInterface extends BaseRepositoryInterface
{

    public function findByChapter(int $chapterId);
    public function toggleStatus(int $id): ?CourseChapterLesson;
    public function updateOrder(int $id, int $order): ?CourseChapterLesson;
}