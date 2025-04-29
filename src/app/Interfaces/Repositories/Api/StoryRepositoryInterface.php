<?php

namespace App\Interfaces\Repositories\Api;

interface StoryRepositoryInterface
{
    public function findById($id);
    // public function findBySlug($slug); // Slug ile bulma kaldırıldı
    public function getWithPagination(array $params);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}