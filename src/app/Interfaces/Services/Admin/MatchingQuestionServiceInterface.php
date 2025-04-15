<?php

namespace App\Interfaces\Services\Admin;
use App\Interfaces\Services\BaseServiceInterface;
interface MatchingQuestionServiceInterface extends BaseServiceInterface
{
    public function toggleStatus($id);
}
