<?php

namespace App\Interfaces\Repositories\Api;

interface ContactRepositoryInterface
{
    public function create(array $data);
    public function getWithPagination(array $params);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}