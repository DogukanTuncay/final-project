<?php

namespace App\Interfaces\Repositories\Api;

interface MultipleChoiceQuestionRepositoryInterface
{
    public function all();
    public function find($id);
    public function with($relations);
    public function paginate($perPage, $columns, $pageName, $page);
    public function findById($id);
    public function findBySlug($slug);
    public function getWithPagination(array $params);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}