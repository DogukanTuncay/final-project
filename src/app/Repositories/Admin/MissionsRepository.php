<?php

namespace App\Repositories\Admin;

use App\Models\Mission;
use App\Interfaces\Repositories\Admin\MissionsRepositoryInterface;
use App\Repositories\BaseRepository;

class MissionsRepository extends BaseRepository implements MissionsRepositoryInterface
{
    public function __construct(Mission $model)
    {
        parent::__construct($model);
    }

    public function toggleStatus(int $id): bool
    {
        $mission = $this->find($id);
        $mission->is_active = !$mission->is_active;
        return $mission->save();
    }
}
