<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;
interface MissionsRepositoryInterface extends BaseRepositoryInterface
{
    public function toggleStatus(int $id): bool;
}
