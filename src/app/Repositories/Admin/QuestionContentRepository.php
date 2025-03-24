<?php

namespace App\Repositories\Admin;

use App\Models\QuestionContent;
use App\Interfaces\Repositories\Admin\QuestionContentRepositoryInterface;
use App\Repositories\BaseRepository;

class QuestionContentRepository extends BaseRepository implements QuestionContentRepositoryInterface
{
    public function __construct(QuestionContent $model)
    {
        parent::__construct($model);
    }
}