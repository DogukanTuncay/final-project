<?php

namespace App\Interfaces\Services\Admin;
use App\Interfaces\Services\BaseServiceInterface;
interface CourseChapterServiceInterface extends BaseServiceInterface
{
   
    public function findByCourse(int $courseId);
    public function updateOrder(int $id, int $order);
    public function toggleStatus(int $id);
}