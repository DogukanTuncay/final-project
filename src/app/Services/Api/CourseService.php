<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\CourseServiceInterface;
use App\Interfaces\Repositories\Api\CourseRepositoryInterface;
use App\Services\BaseService;

class CourseService extends BaseService implements CourseServiceInterface
{
    public function __construct(CourseRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}