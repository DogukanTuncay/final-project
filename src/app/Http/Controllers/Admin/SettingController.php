<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\SettingServiceInterface;
use App\Http\Requests\Admin\SettingRequest;
use App\Http\Resources\Admin\SettingResource;
use App\Http\Resources\Admin\MobileSettingResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(SettingServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Tüm ayarları listele
     */
    public function index(Request $request)
    {
        $group = $request->get('group');
        $showPrivate = $request->boolean('private');
        
        if ($showPrivate) {
            $items = $this->service->getPrivateSettings();
        } elseif ($group) {
            $items = $this->service->getByGroup($group);
        } else {
            $items = $this->service->all();
        }
        
        return $this->successResponse(SettingResource::collection($items), 'admin.setting.list.success');
    }

    /**
     * Yeni ayar oluştur
     */
    public function store(SettingRequest $request)
    {
        $isPrivate = $request->boolean('is_private', false);
        
        if ($isPrivate) {
            $item = $this->service->createPrivateSetting($request->validated());
        } else {
            $item = $this->service->create($request->validated());
        }
        
        return $this->successResponse(new SettingResource($item), 'admin.setting.create.success');
    }

    /**
     * Belirli bir ayarı göster
     */
    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new SettingResource($item), 'admin.setting.show.success');
    }

    /**
     * Belirli bir ayarı güncelle
     */
    public function update(SettingRequest $request, $id)
    {
        $data = $request->validated();
        $item = $this->service->update($id, $data);
        
        if (isset($data['is_private'])) {
            $this->service->setPrivate($id, (bool)$data['is_private']);
        }
        
        return $this->successResponse(new SettingResource($item), 'admin.setting.update.success');
    }

    /**
     * Belirli bir ayarı sil
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.setting.delete.success');
    }
    
    /**
     * Site genel ayarlarını güncelle
     */
    public function updateSiteSettings(Request $request)
    {
        $settings = $this->service->updateSiteSettings($request->except(['_token', '_method']));
        return $this->successResponse($settings, 'admin.setting.site.update.success');
    }
    
    /**
     * Mobil uygulama ayarlarını güncelle
     */
    public function updateMobileSettings(Request $request)
    {
        $settings = $this->service->updateMobileSettings($request->except(['_token', '_method']));
        return $this->successResponse(new MobileSettingResource($settings), 'admin.setting.mobile.update.success');
    }
    
    /**
     * Logo, favicon gibi görsel ayarları güncelle
     */
    public function updateImageSettings(Request $request)
    {
        $settings = $this->service->updateImageSettings(
            $request->except(['_token', '_method', 'site_logo', 'site_favicon']),
            $request->only(['site_logo', 'site_favicon'])
        );
        
        return $this->successResponse($settings, 'admin.setting.image.update.success');
    }
    
    /**
     * Ayar önbelleğini temizle
     */
    public function clearCache(Request $request)
    {
        $group = $request->get('group');
        $this->service->clearCache($group);
        
        return $this->successResponse(null, 'admin.setting.cache.clear.success');
    }
    
    /**
     * Bir ayarın özel/gizli durumunu değiştirir
     */
    public function togglePrivate(Request $request, $id)
    {
        $isPrivate = $request->boolean('is_private', true);
        $item = $this->service->setPrivate($id, $isPrivate);
        
        return $this->successResponse(
            new SettingResource($item), 
            $isPrivate ? 'admin.setting.private.set.success' : 'admin.setting.private.unset.success'
        );
    }
    
    /**
     * Tüm özel ayarları listeler
     */
    public function privateSettings()
    {
        $items = $this->service->getPrivateSettings();
        return $this->successResponse(SettingResource::collection($items), 'admin.setting.private.list.success');
    }
}