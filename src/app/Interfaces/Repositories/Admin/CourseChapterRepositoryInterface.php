<?php

namespace App\Interfaces\Repositories\Admin;
use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CourseChapterRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCourse(int $courseId);
    public function updateOrder(int $id, int $order);
    public function toggleStatus(int $id);
}