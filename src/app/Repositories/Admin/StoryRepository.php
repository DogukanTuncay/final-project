<?php

namespace App\Repositories\Admin;

use App\Models\Story;
use App\Interfaces\Repositories\Admin\StoryRepositoryInterface;
use App\Repositories\BaseRepository;

class StoryRepository extends BaseRepository implements StoryRepositoryInterface
{
    public function __construct(Story $model)
    {
        parent::__construct($model);
    }
}