<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\MatchingQuestionServiceInterface;
use App\Interfaces\Repositories\Admin\MatchingQuestionRepositoryInterface;
use App\Services\BaseService;

class MatchingQuestionService extends BaseService implements MatchingQuestionServiceInterface
{
    public function __construct(MatchingQuestionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}