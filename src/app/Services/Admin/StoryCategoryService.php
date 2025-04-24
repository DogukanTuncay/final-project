<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\StoryCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\StoryCategoryServiceInterface;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class StoryCategoryService extends BaseService implements StoryCategoryServiceInterface
{

    public function __construct(StoryCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

  

    public function findById(int $id): ?Model
    {
        return $this->repository->findById($id);
    }

  
}