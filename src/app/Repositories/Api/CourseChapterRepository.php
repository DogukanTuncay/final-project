<?php

namespace App\Repositories\Api;

use App\Models\CourseChapter;
use App\Interfaces\Repositories\Api\CourseChapterRepositoryInterface;
use App\Repositories\BaseRepository;

class CourseChapterRepository extends BaseRepository implements CourseChapterRepositoryInterface
{
    public function __construct(CourseChapter $model)
    {
        parent::__construct($model);
    }
    public function findByCourse(int $courseId)
    {
        return $this->model->where('course_id', $courseId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    public function findActiveWithCourse(int $id)
    {
        return $this->model->where('id', $id)
            ->where('is_active', true)
            ->with('course')
            ->firstOrFail();
    }
}