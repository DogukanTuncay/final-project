<?php

namespace App\Interfaces\Services\Admin;
use App\Interfaces\Services\BaseServiceInterface;
use App\Models\CourseChapterLesson;

interface CourseChapterLessonServiceInterface extends BaseServiceInterface
{
    public function findByChapter(int $chapterId);
    public function toggleStatus(int $id): ?CourseChapterLesson;
    public function updateOrder(int $id, int $order): ?CourseChapterLesson;
}