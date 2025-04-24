<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\StoryCategoryResource;
use App\Interfaces\Services\Api\StoryCategoryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryCategoryController extends BaseController
{
    private StoryCategoryServiceInterface $service;

    public function __construct(StoryCategoryServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Aktif hikaye kategorilerini listeler.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->service->getActiveOrdered();
        $resource = StoryCategoryResource::collection($categories);
        
        return $this->successResponse($resource, 'responses.story_category.list_success');
    }

    /**
     * Belirli bir kategoriyi slug ile getirir (Opsiyonel, API'de gerekirse).
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $category = $this->service->findActiveBySlug($slug);
        if (!$category) {
            return $this->errorResponse('responses.story_category.not_found', 404);
        }
        $resource = new StoryCategoryResource($category);

        return $this->successResponse($resource, 'responses.story_category.show_success');
    }
}