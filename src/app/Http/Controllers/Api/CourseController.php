<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Api\CourseServiceInterface;
use App\Http\Resources\Api\CourseResource;

class CourseController extends BaseController
{
    protected $service;

    public function __construct(CourseServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Tüm aktif kursları listele
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $courses = $this->service->allActive();
        return $this->successResponse(CourseResource::collection($courses), 'responses.courses.listed');
    }

    /**
     * Belirli bir kursun detayını göster
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $course = $this->service->findActive($id);
        return $this->successResponse(new CourseResource($course), 'responses.courses.retrieved');
    }

    /**
     * Belirli bir kursu slug'a göre göster
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function bySlug(string $slug)
    {
        $course = $this->service->findBySlug($slug);
        return $this->successResponse(new CourseResource($course), 'responses.courses.retrieved');
    }

    /**
     * Öne çıkarılan kursları listele
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        $courses = $this->service->findFeatured();
        return $this->successResponse(CourseResource::collection($courses), 'responses.courses.listed');
    }

    /**
     * Belirli bir kategorideki kursları listele
     * @param string $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCategory(string $category)
    {
        $courses = $this->service->findActiveByCategory($category);
        return $this->successResponse(CourseResource::collection($courses), 'responses.courses.by_category');
    }
}