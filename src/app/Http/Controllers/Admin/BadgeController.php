<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Admin\BadgeServiceInterface;
use App\Http\Requests\Admin\BadgeRequest;
use App\Http\Resources\Admin\BadgeResource;
use Illuminate\Http\Request;

class BadgeController extends BaseController
{
    protected $service;

    public function __construct(BadgeServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Tüm rozetleri listele
     */
    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(BadgeResource::collection($items), 'admin.Badge.list.success');
    }

    /**
     * Rozet oluşturma formunu göster
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Yeni rozet oluştur
     */
    public function store(BadgeRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new BadgeResource($item), 'admin.Badge.create.success');
    }

    /**
     * Rozet detaylarını göster
     */
    public function show($id)
    {
        $item = $this->service->find($id);
        
        if (!$item) {
            return $this->errorResponse('admin.Badge.show.not_found', 404);
        }
        
        return $this->successResponse(new BadgeResource($item), 'admin.Badge.show.success');
    }

    /**
     * Rozet düzenleme formunu göster
     */
    public function edit($id)
    {
        $badge = $this->service->find($id);
        
        if (!$badge) {
            return redirect()->route('admin.badges.index')
                ->with('error', __('messages.badge_not_found'));
        }
        
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Rozeti güncelle
     */
    public function update(BadgeRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        
        if (!$item) {
            return $this->errorResponse('admin.Badge.update.failed', 400);
        }
        
        return $this->successResponse(new BadgeResource($this->service->find($id)), 'admin.Badge.update.success');
    }

    /**
     * Rozeti sil
     */
    public function destroy($id)
    {
        $success = $this->service->delete($id);
        
        if (!$success) {
            return $this->errorResponse('admin.Badge.delete.failed', 400);
        }
        
        return $this->successResponse(null, 'admin.Badge.delete.success');
    }

    /**
     * Rozet durumunu değiştir (aktif/pasif)
     */
    public function toggleStatus($id)
    {
        $success = $this->service->toggleStatus($id);
        
        if (!$success) {
            return $this->errorResponse('admin.Badge.toggle_status.failed', 400);
        }
        
        return $this->successResponse(new BadgeResource($this->service->find($id)), 'admin.Badge.toggle_status.success');
    }
}