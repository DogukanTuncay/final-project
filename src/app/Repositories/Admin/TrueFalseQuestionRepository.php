<?php

namespace App\Repositories\Admin;

use App\Models\TrueFalseQuestion;
use App\Interfaces\Repositories\Admin\TrueFalseQuestionRepositoryInterface;
use App\Repositories\BaseRepository;

class TrueFalseQuestionRepository extends BaseRepository implements TrueFalseQuestionRepositoryInterface
{
    public function __construct(TrueFalseQuestion $model)
    {
        parent::__construct($model);
    }
}