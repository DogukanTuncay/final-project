<?php

namespace App\Interfaces\Services\Admin;
use App\Interfaces\Services\BaseServiceInterface;
interface FillInTheBlankServiceInterface extends BaseServiceInterface
{
    public function toggleStatus($id);
    public function with($relations);
}
