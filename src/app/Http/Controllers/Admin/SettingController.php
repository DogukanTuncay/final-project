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
    public function update(Request $request, $id)
    {
        // İsteği validasyondan geçiriyoruz
        $validated = $request->validate([
            'key' => 'sometimes|required|string|max:255',
            'value' => 'nullable',
            'type' => 'sometimes|required|string|in:text,boolean,number,json,image',
            'group' => 'sometimes|required|string|max:255',
            'description' => 'nullable',
            'is_translatable' => 'boolean',
            'is_private' => 'boolean'
        ]);

        try {
            // Değeri ayarın tipine göre işleyelim
            if (isset($validated['type']) && isset($validated['value'])) {
                switch ($validated['type']) {
                    case 'boolean':
                        $validated['value'] = filter_var($validated['value'], FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'number':
                        $validated['value'] = (float) $validated['value'];
                        break;
                    case 'json':
                        // JSON formatı doğrulama
                        if (!is_array($validated['value'])) {
                            $decodedValue = json_decode($validated['value'], true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $validated['value'] = $validated['value']; // Zaten JSON string
                            } else {
                                return $this->errorResponse('admin.setting.update.invalid_json', 422);
                            }
                        } else {
                            $validated['value'] = json_encode($validated['value']);
                        }
                        break;
                }
            }

            $item = $this->service->update($id, $validated);
            
            if (isset($validated['is_private'])) {
                $this->service->setPrivate($id, (bool)$validated['is_private']);
            }
            
            // Cache'i temizleyelim
            $this->service->clearCache(isset($validated['group']) ? $validated['group'] : null);
            
            return $this->successResponse(new SettingResource($item), 'admin.setting.update.success');
        } catch (\Exception $e) {
            return $this->errorResponse('admin.setting.update.error', 500, ['message' => $e->getMessage()]);
        }
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
        $item = $this->service->find($id);
        if (!$item){
            return $this->errorResponse('admin.setting.not_found', 404);
        }
        $isPrivate = $item->is_private;
        $item = $this->service->setPrivate($id, !$isPrivate);
        
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