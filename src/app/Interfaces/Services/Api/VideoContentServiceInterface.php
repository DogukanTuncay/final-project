<?php

namespace App\Interfaces\Services\Api;

interface VideoContentServiceInterface
{
    public function findById($id);
    public function findBySlug($slug);
    public function getWithPagination(array $params);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}