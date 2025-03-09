<?php

namespace App\Repositories\Admin;

use App\Models\CourseChapter;
use App\Interfaces\Repositories\Admin\CourseChapterRepositoryInterface;
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
            ->orderBy('order')
            ->get();
    }
    public function updateOrder(int $id, int $order)
    {
        $courseChapter = $this->find($id);
        $courseChapter->order = $order;
        $courseChapter->save();
        return $courseChapter;
    }

    public function toggleStatus(int $id)
    {
        $courseChapter = $this->find($id);
        $courseChapter->is_active = !$courseChapter->is_active;
        $courseChapter->save();
        return $courseChapter;
    }

}