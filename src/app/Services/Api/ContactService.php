<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\ContactServiceInterface;
use App\Interfaces\Repositories\Api\ContactRepositoryInterface;

class ContactService implements ContactServiceInterface
{
    protected $repository;

    public function __construct(ContactRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function getWithPagination(array $params)
    {
        return $this->repository->getWithPagination($params);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}