<?php

namespace App\Repositories\Admin;

use App\Models\Badge;
use App\Interfaces\Repositories\Admin\BadgeRepositoryInterface;
use App\Repositories\BaseRepository;

class BadgeRepository extends BaseRepository implements BadgeRepositoryInterface
{
    public function __construct(Badge $model)
    {
        parent::__construct($model);
    }

    /**
     * Rozet durumunu değiştir (aktif/pasif)
     * 
     * @param int $id
     * @return bool
     */
    public function toggleStatus(int $id): bool
    {
        $badge = $this->find($id);
        $badge->is_active = !$badge->is_active;
        return $badge->save();
    }
}