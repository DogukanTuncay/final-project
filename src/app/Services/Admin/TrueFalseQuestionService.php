<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\TrueFalseQuestionServiceInterface;
use App\Interfaces\Repositories\Admin\TrueFalseQuestionRepositoryInterface;
use App\Services\BaseService;

class TrueFalseQuestionService extends BaseService implements TrueFalseQuestionServiceInterface
{
    public function __construct(TrueFalseQuestionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}