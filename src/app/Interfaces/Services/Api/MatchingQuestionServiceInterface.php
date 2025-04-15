<?php

namespace App\Interfaces\Services\Api;

interface MatchingQuestionServiceInterface
{
    public function findById($id);
    public function findByIdWithPairs($id);
    public function findBySlug($slug);
    public function getWithPagination(array $params);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}