<?php

namespace App\Repositories\Api;

use App\Models\Course;
use App\Interfaces\Repositories\Api\CourseRepositoryInterface;
use App\Repositories\BaseRepository;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }
}