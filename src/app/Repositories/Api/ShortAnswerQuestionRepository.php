<?php

namespace App\Repositories\Api;

use App\Models\ShortAnswerQuestion;
use App\Interfaces\Repositories\Api\ShortAnswerQuestionRepositoryInterface;

class ShortAnswerQuestionRepository implements ShortAnswerQuestionRepositoryInterface
{
    protected $model;

    public function __construct(ShortAnswerQuestion $model)
    {
        $this->model = $model;
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function getWithPagination(array $params)
    {
        $query = $this->model->query();

        // İsteğe bağlı filtreleme işlemleri burada yapılabilir
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }

        // Sıralama işlemleri
        $orderBy = $params['order_by'] ?? 'id';
        $orderDirection = $params['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        // Sayfalama
        $perPage = $params['per_page'] ?? 15;
        return $query->get();
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}