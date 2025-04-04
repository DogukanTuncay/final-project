<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\QuestionContentServiceInterface;
use App\Interfaces\Repositories\Api\QuestionContentRepositoryInterface;

class QuestionContentService implements QuestionContentServiceInterface
{
    protected $repository;

    public function __construct(QuestionContentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function getWithPagination(array $params)
    {
        return $this->repository->getWithPagination($params);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}