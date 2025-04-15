<?php

namespace App\Http\Controllers\Admin;

use App\Interfaces\Services\Admin\MissionsServiceInterface;
use App\Http\Requests\Admin\MissionsRequest;
use App\Http\Resources\Admin\MissionsResource;
use App\Http\Controllers\BaseController;
class MissionsController extends BaseController
{
    protected $service;

    public function __construct(MissionsServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Görevler listesi
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(MissionsResource::collection($items), 'api.missions.index.success');
    }

    /**
     * Belirli bir görevi detaylı olarak getir
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = $this->service->find($id);
        if ($item) {
            return $this->successResponse(new MissionsResource($item), 'api.missions.show.success');
        }

        return $this->errorResponse('api.missions.show.not_found', 404);
    }

    /**
     * Yeni görev oluştur
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MissionsRequest $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'min_xp' => 'required|integer',
            'max_xp' => 'required|integer',
        ]);

        $item = $this->service->create($validated);

        return $this->successResponse(new MissionsResource($item), 'api.missions.store.success');
    }

    /**
     * Görevi güncelle
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MissionsRequest $request, $id)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'min_xp' => 'nullable|integer',
            'max_xp' => 'nullable|integer',
        ]);

        $item = $this->service->update($id, $validated);

        if ($item) {
            return $this->successResponse(new MissionsResource($item), 'api.missions.update.success');
        }

        return $this->errorResponse('api.missions.update.not_found', 404);
    }

    /**
     * Görevi sil
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if ($deleted) {
            return $this->successResponse([], 'api.missions.destroy.success');
        }

        return $this->errorResponse('api.missions.destroy.not_found', 404);
    }

    /**
     * Görev durumunu aktif/pasif yap
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus($id)
    {
        $item = $this->service->toggleStatus($id);

        if ($item) {
            return $this->successResponse(new MissionsResource($item), 'api.missions.toggle_status.success');
        }

        return $this->errorResponse('api.missions.toggle_status.not_found', 404);
    }
}
