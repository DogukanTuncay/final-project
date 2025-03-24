<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\MatchingQuestionServiceInterface;
use App\Interfaces\Repositories\Api\MatchingQuestionRepositoryInterface;

class MatchingQuestionService implements MatchingQuestionServiceInterface
{
    protected $repository;

    public function __construct(MatchingQuestionRepositoryInterface $repository)
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