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
        $validatedData = $request->validated();
        
        // Resim yükleme işlemini burada yapma ihtiyacı olmayabilir, çünkü service'e taşıyoruz
        $category = $this->service->create($validatedData);
        
        // Eğer request'te resim varsa, yükleyelim
        if ($request->hasFile('image')) {
            $category->uploadImage($request->file('image'), 'image');
        }
        
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
        
        $validatedData = $request->validated();
        
        // Eğer request'te resim varsa
        if ($request->hasFile('image')) {
            $category->uploadImage($request->file('image'), 'image');
            // Resim trait tarafından kaydedildiği için validatedData'dan çıkaralım
            unset($validatedData['image']);
        }
        
        // Diğer verileri güncelleyelim (eğer varsa)
        if (!empty($validatedData)) {
            $updatedCategory = $this->service->update($id, $validatedData);
        } else {
            $updatedCategory = $category;
        }
        
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