<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\StoryServiceInterface;
use App\Interfaces\Repositories\Api\StoryRepositoryInterface;

class StoryService implements StoryServiceInterface
{
    protected $repository;

    public function __construct(StoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function getWithPagination(array $params)
    {
        return $this->repository->getWithPagination($params);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}