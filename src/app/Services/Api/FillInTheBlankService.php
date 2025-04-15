<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\FillInTheBlankServiceInterface;
use App\Interfaces\Repositories\Api\FillInTheBlankRepositoryInterface;

class FillInTheBlankService implements FillInTheBlankServiceInterface
{
    protected $repository;

    public function __construct(FillInTheBlankRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ID'ye göre soru bul
     * 
     * @param int $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Slug'a göre soru bul (ID kullanılacak)
     * 
     * @param string $slug
     * @return mixed
     */
    public function findBySlug($slug)
    {
        // Slug özelliği yoksa ID'ye göre arayalım
        if (is_numeric($slug)) {
            return $this->findById($slug);
        }
        
        return $this->findById($slug);
    }

    /**
     * Sayfalandırma ile soruları listeleme
     * 
     * @param array $params
     * @return mixed
     */
    public function getWithPagination(array $params)
    {
        $perPage = $params['per_page'] ?? 15;
        return $this->repository->paginate($perPage);
    }
} 