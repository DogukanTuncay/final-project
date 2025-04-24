<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\StoryCategoryRequest;
use App\Interfaces\Services\Admin\StoryCategoryServiceInterface;
use App\Models\StoryCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Resources\Admin\StoryCategoryResource;
class StoryCategoryController extends BaseController
{
    private StoryCategoryServiceInterface $service;

    public function __construct(StoryCategoryServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     * JSON yanıtı varsayıldı, view döndürülecekse değiştirilmeli.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->service->all();
        return $this->successResponse(StoryCategoryResource::collection($categories), 'responses.crud.list_success');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoryCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());
        return $this->successResponse(new StoryCategoryResource($category), 'responses.crud.create_success', 201);
    }

  

    /**
     * Update the specified resource in storage.
     */
    public function update(StoryCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->service->find($id);
        if (!$category) {
            return $this->errorResponse('responses.crud.not_found', 404);
        }
        $updatedCategory = $this->service->update($id, $request->validated());
        return $this->successResponse(new StoryCategoryResource($updatedCategory), 'responses.crud.update_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoryCategory $storyCategory): JsonResponse
    {
        $deleted = $this->service->delete($storyCategory->id);
        if (!$deleted) {
            return $this->errorResponse('responses.crud.delete_error', 500);
        }
        return $this->successResponse(null, 'responses.crud.delete_success');
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->service->find($id);
        if (!$category) {
            return $this->errorResponse('responses.crud.not_found', 404);
        }
        return $this->successResponse(new StoryCategoryResource($category), 'responses.crud.show_success');
    }
}