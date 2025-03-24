<?php

namespace App\Repositories\Admin;

use App\Models\MultipleChoiceQuestion;
use App\Interfaces\Repositories\Admin\MultipleChoiceQuestionRepositoryInterface;
use App\Repositories\BaseRepository;

class MultipleChoiceQuestionRepository extends BaseRepository implements MultipleChoiceQuestionRepositoryInterface
{
    public function __construct(MultipleChoiceQuestion $model)
    {
        parent::__construct($model);
    }
}