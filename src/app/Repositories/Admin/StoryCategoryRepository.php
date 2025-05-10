<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\StoryCategoryRepositoryInterface;
use App\Models\StoryCategory;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class StoryCategoryRepository extends BaseRepository implements StoryCategoryRepositoryInterface
{
    public function __construct(StoryCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Tüm kategorileri sayfalı olarak getirir.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->ordered()->get();
    }

  
}