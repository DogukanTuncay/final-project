<?php

namespace App\Interfaces\Services\Api;

interface QuestionContentServiceInterface
{
    public function findById($id);
    public function findBySlug($slug);
    public function getWithPagination(array $params);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}