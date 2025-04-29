<?php

namespace App\Repositories\Api;

use App\Models\Story;
use App\Interfaces\Repositories\Api\StoryRepositoryInterface;

class StoryRepository implements StoryRepositoryInterface
{
    protected $model;

    public function __construct(Story $model)
    {
        $this->model = $model;
    }

    public function findById($id)
    {
        // Aktif olan story'leri ve ilişkili kategoriyi getir
        return $this->model->where('is_active', true)->with('storyCategory')->findOrFail($id);
    }

    public function getWithPagination(array $params)
    {
        $query = $this->model->query()->where('is_active', true)->with('storyCategory');

        // Kategoriye göre filtrele
        if (isset($params['story_category_id'])) {
            $query->where('story_category_id', $params['story_category_id']);
        }

        // İsteğe bağlı diğer filtreleme işlemleri burada yapılabilir
        // Örnek: if (isset($params['some_filter'])) { ... }

        // Sıralama işlemleri (varsayılan: order_column artan)
        $orderBy = $params['order_by'] ?? 'order_column';
        $orderDirection = $params['order_direction'] ?? 'asc';
        $query->orderBy($orderBy, $orderDirection);

        // Sayfalama
        $perPage = $params['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}