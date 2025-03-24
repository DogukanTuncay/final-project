<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\MultipleChoiceQuestionServiceInterface;
use App\Interfaces\Repositories\Api\MultipleChoiceQuestionRepositoryInterface;

class MultipleChoiceQuestionService implements MultipleChoiceQuestionServiceInterface
{
    protected $repository;

    public function __construct(MultipleChoiceQuestionRepositoryInterface $repository)
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