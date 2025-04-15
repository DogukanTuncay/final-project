<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\MissionsServiceInterface;
use App\Interfaces\Repositories\Admin\MissionsRepositoryInterface;
use App\Services\BaseService;

class MissionsService extends BaseService implements MissionsServiceInterface
{
    public function __construct(MissionsRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
    public function toggleStatus(int $id): bool
    {
        return $this->repository->toggleStatus($id);
    }
}
