<?php

namespace App\Repositories\Api;

use App\Models\TrueFalseQuestion;
use App\Interfaces\Repositories\Api\TrueFalseQuestionRepositoryInterface;

class TrueFalseQuestionRepository implements TrueFalseQuestionRepositoryInterface
{
    protected $model;

    public function __construct(TrueFalseQuestion $model)
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
        return $query->paginate($perPage);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}