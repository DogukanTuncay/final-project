<?php

namespace App\Interfaces\Repositories\Api;

interface MatchingQuestionRepositoryInterface
{
    public function findById($id);
    public function findBySlug($slug);
    public function getWithPagination(array $params);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}