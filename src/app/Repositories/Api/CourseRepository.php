<?php

namespace App\Repositories\Api;

use App\Models\Course;
use App\Interfaces\Repositories\Api\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository implements CourseRepositoryInterface
{
    protected Course $model;

    public function __construct(Course $model)
    {
        $this->model = $model;
    }

    /**
     * Tüm aktif kursları getir
     * @return Collection
     */
    public function allActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Belirli bir aktif kursu ID'ye göre getir
     * @param int $id
     * @return Course
     */
    public function findActive(int $id): Course
    {
        return $this->model->where('is_active', true)
            ->findOrFail($id);
    }

    /**
     * Öne çıkarılan aktif kursları getir
     * @return Collection
     */
    public function findFeatured(): Collection
    {
        return $this->model->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Belirli bir kategorideki aktif kursları getir
     * @param string $category
     * @return Collection
     */
    public function findActiveByCategory(string $category): Collection
    {
        return $this->model->where('is_active', true)
            ->where('category', $category)
            ->orderBy('order')
            ->get();
    }

    /**
     * Belirli bir kursu slug'a göre getir
     * @param string $slug
     * @return Course
     */
    public function findBySlug(string $slug): Course
    {
        return $this->model->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();
    }
}