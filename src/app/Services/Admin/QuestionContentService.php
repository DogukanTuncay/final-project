<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\QuestionContentServiceInterface;
use App\Interfaces\Repositories\Admin\QuestionContentRepositoryInterface;
use App\Services\BaseService;

class QuestionContentService extends BaseService implements QuestionContentServiceInterface
{
    public function __construct(QuestionContentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}