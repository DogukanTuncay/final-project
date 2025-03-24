<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\ShortAnswerQuestionServiceInterface;
use App\Interfaces\Repositories\Admin\ShortAnswerQuestionRepositoryInterface;
use App\Services\BaseService;

class ShortAnswerQuestionService extends BaseService implements ShortAnswerQuestionServiceInterface
{
    public function __construct(ShortAnswerQuestionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}