<?php


namespace App\Interfaces\Repositories\Api;
use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CourseChapterRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCourse(int $courseId);
    public function findActiveWithCourse(int $id);
}