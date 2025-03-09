<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\CourseChapterServiceInterface;
use App\Interfaces\Repositories\Api\CourseChapterRepositoryInterface;

class CourseChapterService implements CourseChapterServiceInterface
{
    private CourseChapterRepositoryInterface $courseChapterRepository;

    public function __construct(CourseChapterRepositoryInterface $courseChapterRepository)
    {
        $this->courseChapterRepository = $courseChapterRepository;
    }
    public function findByCourse(int $courseId)
    {
        return $this->courseChapterRepository->findByCourse($courseId);
    }

    public function findActiveWithCourse(int $id)
    {
        return $this->courseChapterRepository->findActiveWithCourse($id);
    }
    
}