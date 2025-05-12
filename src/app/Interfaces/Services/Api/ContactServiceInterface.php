<?php

namespace App\Interfaces\Services\Api;

interface ContactServiceInterface
{
    public function create(array $data);
    public function getWithPagination(array $params);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}