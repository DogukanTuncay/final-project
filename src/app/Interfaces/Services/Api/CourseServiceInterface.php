<?php

namespace App\Interfaces\Services\Api;

use App\Models\Course;

interface CourseServiceInterface
{
    /**
     * Tüm aktif kursları getir
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allActive();
    
    /**
     * Belirli bir aktif kursu ID'ye göre getir
     * @param int $id
     * @return \App\Models\Course
     */
    public function findActive(int $id);
    
    /**
     * Öne çıkarılan aktif kursları getir
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findFeatured();
    
    /**
     * Belirli bir kategorideki aktif kursları getir
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findActiveByCategory(string $category);
    
    /**
     * Belirli bir kursu slug'a göre getir
     * @param string $slug
     * @return \App\Models\Course
     */
    public function findBySlug(string $slug);
}