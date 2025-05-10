<?php

namespace App\Repositories\Api;

use App\Models\FillInTheBlank;
use App\Interfaces\Repositories\Api\FillInTheBlankRepositoryInterface;
use App\Repositories\BaseRepository;

class FillInTheBlankRepository extends BaseRepository implements FillInTheBlankRepositoryInterface
{
    public function __construct(FillInTheBlank $model)
    {
        parent::__construct($model);
    }

    public function findById($id)
    {
        return $this->model->where('id', $id)->where('is_active', 1)->first();
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->where('is_active', 1)->first();
    }

    public function get()
    {
        return $this->model->where('is_active', 1)->get();
    }
    public function with($relations)
    {
        $this->with = is_string($relations) ? func_get_args() : $relations;
        return $this;
    }
} 