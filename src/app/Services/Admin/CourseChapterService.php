<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\CourseChapterServiceInterface;
use App\Interfaces\Repositories\Admin\CourseChapterRepositoryInterface;
use App\Services\BaseService;

class CourseChapterService extends BaseService implements CourseChapterServiceInterface
{
    public function __construct(CourseChapterRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }


    public function findByCourse(int $courseId)
    {
        return $this->repository->findByCourse($courseId);
    }

    public function updateOrder(int $id, int $order)
    {
        return $this->repository->updateOrder($id, $order);
    }

    public function toggleStatus(int $id)
    {
        return $this->repository->toggleStatus($id);
    }
}