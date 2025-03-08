<?php

namespace App\Repositories\Admin;

use App\Models\Course;
use App\Interfaces\Repositories\Admin\CourseRepositoryInterface;
use App\Repositories\BaseRepository;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    public function updateOrder($id, int $order)
    {
        return $this->find($id)->update(['order' => $order]);
    }

    public function toggleStatus($id)
    {
        $course = $this->find($id);
        $course->is_active = !$course->is_active;
        $course->save();
        return $course;
    }

    public function toggleFeatured($id)
    {
        $course = $this->find($id);
        $course->is_featured = !$course->is_featured;
        $course->save();
        return $course;
    }

    public function findByCategory(string $category)
    {
        return $this->model->where('category', $category)
            ->orderBy('order')
            ->get();
    }
}