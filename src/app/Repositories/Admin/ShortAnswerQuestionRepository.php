<?php

namespace App\Repositories\Admin;

use App\Models\ShortAnswerQuestion;
use App\Interfaces\Repositories\Admin\ShortAnswerQuestionRepositoryInterface;
use App\Repositories\BaseRepository;

class ShortAnswerQuestionRepository extends BaseRepository implements ShortAnswerQuestionRepositoryInterface
{
    public function __construct(ShortAnswerQuestion $model)
    {
        parent::__construct($model);
    }
}