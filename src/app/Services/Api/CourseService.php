<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\CourseServiceInterface;
use App\Interfaces\Repositories\Api\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Course;

class CourseService implements CourseServiceInterface
{
    private CourseRepositoryInterface $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    /**
     * Tüm aktif kursları getir
     * @return Collection
     */
    public function allActive(): Collection
    {
        return $this->courseRepository->allActive();
    }

    /**
     * Belirli bir aktif kursu ID'ye göre getir
     * @param int $id
     * @return Course
     */
    public function findActive(int $id): Course
    {
        return $this->courseRepository->findActive($id);
    }

    /**
     * Öne çıkarılan aktif kursları getir
     * @return Collection
     */
    public function findFeatured(): Collection
    {
        return $this->courseRepository->findFeatured();
    }

    /**
     * Belirli bir kategorideki aktif kursları getir
     * @param string $category
     * @return Collection
     */
    public function findActiveByCategory(string $category): Collection
    {
        return $this->courseRepository->findActiveByCategory($category);
    }

    /**
     * Belirli bir kursu slug'a göre getir
     * @param string $slug
     * @return Course
     */
    public function findBySlug(string $slug): Course
    {
        return $this->courseRepository->findBySlug($slug);
    }
}