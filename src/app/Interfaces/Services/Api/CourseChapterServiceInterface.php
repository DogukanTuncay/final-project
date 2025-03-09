<?php

namespace App\Interfaces\Services\Api;
interface CourseChapterServiceInterface
{
    public function findByCourse(int $courseId);
    public function findActiveWithCourse(int $id);
}