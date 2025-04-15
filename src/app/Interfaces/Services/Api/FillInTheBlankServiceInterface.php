<?php

namespace App\Interfaces\Services\Api;

interface FillInTheBlankServiceInterface
{
    public function findById($id);
    public function findBySlug($slug);
    public function getWithPagination(array $params);
} 