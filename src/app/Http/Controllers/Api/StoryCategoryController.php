<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\StoryCategoryResource;
use App\Interfaces\Services\Api\StoryCategoryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\Api\StoryResource;

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

    /**
     * Belirli bir kategoriye ait hikayeleri getir
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStoriesByCategory($slug)
    {
            $category = $this->service->findBySlug($slug);
            
            if (!$category) {
                return $this->errorResponse(
                    'responses.story_category.not_found',
                    Response::HTTP_NOT_FOUND
                );
            }

            $stories = $this->service->getStoriesByCategory($category->id);
            
            return $this->successResponse(
                StoryResource::collection($stories),
                'responses.story_category.stories.success'
            );
       
    }
}