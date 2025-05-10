<?php

namespace App\Repositories\Api;

use App\Interfaces\Repositories\Api\StoryCategoryRepositoryInterface;
use App\Models\StoryCategory;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class StoryCategoryRepository extends BaseRepository implements StoryCategoryRepositoryInterface
{
    public function __construct(StoryCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Tüm aktif kategorileri sıralı olarak getirir.
     *
     * @return Collection
     */
    public function getActiveOrdered(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Aktif bir kategoriyi slug ile bulur.
     *
     * @param string $slug
     * @return StoryCategory|null
     */
    public function findActiveBySlug(string $slug): ?StoryCategory
    {
        return $this->model->active()->where('slug', $slug)->first();
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

    /**
     * Belirli bir kategoriye ait hikayeleri getir
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStoriesByCategory($categoryId)
    {
        return $this->model
            ->findOrFail($categoryId)
            ->stories()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}