<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\StoryServiceInterface;
use App\Interfaces\Repositories\Admin\StoryRepositoryInterface;
use App\Services\BaseService;

class StoryService extends BaseService implements StoryServiceInterface
{
    public function __construct(StoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}