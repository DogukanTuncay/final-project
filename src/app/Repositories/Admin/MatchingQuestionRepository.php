<?php

namespace App\Repositories\Admin;

use App\Models\MatchingQuestion;
use App\Interfaces\Repositories\Admin\MatchingQuestionRepositoryInterface;
use App\Repositories\BaseRepository;

class MatchingQuestionRepository extends BaseRepository implements MatchingQuestionRepositoryInterface
{
    public function __construct(MatchingQuestion $model)
    {
        parent::__construct($model);
    }
}