<?php

namespace App\Interfaces\Repositories\Admin;

use App\Models\MultipleChoiceQuestion;
use App\Interfaces\Repositories\BaseRepositoryInterface;
interface MultipleChoiceQuestionRepositoryInterface extends BaseRepositoryInterface
{
    
    public function with($relations);
}