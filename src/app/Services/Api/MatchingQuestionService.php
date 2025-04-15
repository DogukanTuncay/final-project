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

    /**
     * ID'ye göre eşleştirme sorusunu ve çiftlerini getir
     * 
     * @param int $id
     * @return mixed
     */
    public function findByIdWithPairs($id)
    {
        return $this->repository->with(['pairs' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findById($id);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function getWithPagination(array $params)
    {
        $perPage = $params['per_page'] ?? 15;
        
        return $this->repository->with(['pairs' => function($query) {
            $query->orderBy('order', 'asc');
        }])->paginate($perPage);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}