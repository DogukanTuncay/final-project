<?php

namespace App\Services\Api;

use App\Interfaces\Repositories\Api\StoryCategoryRepositoryInterface;
use App\Interfaces\Services\Api\StoryCategoryServiceInterface;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use App\Models\StoryCategory;
use Illuminate\Support\Facades\Log;

class StoryCategoryService extends BaseService implements StoryCategoryServiceInterface
{

    public function __construct(StoryCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Aktif ve sıralı kategorileri getirir.
     *
     * @return Collection
     */
    public function getActiveOrdered(): Collection
    {
        return $this->repository->getActiveOrdered();
    }

    /**
     * Aktif bir kategoriyi slug ile bulur.
     *
     * @param string $slug
     * @return StoryCategory|null
     */
    public function findActiveBySlug(string $slug): ?StoryCategory
    {
        return $this->repository->findActiveBySlug($slug);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function getWithPagination(array $params)
    {
        return $this->repository->getWithPagination($params);
    }

    /**
     * Belirli bir kategoriye ait hikayeleri getir
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStoriesByCategory($categoryId)
    {
        try {
            return $this->repository->getStoriesByCategory($categoryId);
        } catch (\Exception $e) {
            Log::error('Kategori hikayeleri getirilirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}