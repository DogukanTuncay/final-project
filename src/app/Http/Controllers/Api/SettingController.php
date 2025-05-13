<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\SettingServiceInterface;
use App\Http\Resources\Api\SettingResource;
use App\Http\Resources\Api\MobileSettingResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

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
        $items = $this->service->getWithPagination($request->all());
        return $this->successResponse(SettingResource::collection($items), 'api.setting.list.success');
    }

    /**
     * ID'ye göre ayar göster
     */
    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new SettingResource($item), 'api.setting.show.success');
    }

    /**
     * Anahtar değerine göre ayar göster
     */
    public function showByKey($key)
    {
        $item = $this->service->findByKey($key);
        return $this->successResponse($item, 'api.setting.show.success');
    }
    
    /**
     * Site bilgilerini getir (logo, adı, vb.)
     */
    public function getSiteInfo()
    {
        $info = $this->service->getSiteInfo();
        return $this->successResponse($info, 'api.setting.site.success');
    }
    
    /**
     * Mobil uygulama bilgilerini getir (versiyon, force update, vb.)
     * @param Request $request
     * @param string|null $platform Platform bilgisi (android, ios)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMobileInfo(Request $request, $platform = null)
    {
        $info = $this->service->getMobileInfo($platform);
        return $this->successResponse(new MobileSettingResource($info), 'api.setting.mobile.success');
    }
    
    /**
     * Bir grup ayarını getir
     */
    public function getGroupSettings($group)
    {
        $settings = $this->service->getGroupSettings($group);
        return $this->successResponse(SettingResource::collection($settings), 'api.setting.group.success');
    }
    
    /**
     * Kullanıcı tarafından erişilebilecek tüm ayar gruplarını listele
     */
    public function getAvailableGroups()
    {
        $groups = $this->service->getAvailableGroups();
        
        return $this->successResponse([
            'groups' => $groups
        ], 'api.setting.groups.success');
    }
    
    /**
     * Kullanıcı tarafından erişilebilecek tüm ayar anahtarlarını listele
     */
    public function getAvailableKeys(Request $request)
    {
        $group = $request->query('group');
        $keys = $this->service->getAvailableKeys($group);
        
        return $this->successResponse([
            'keys' => $keys
        ], 'api.setting.keys.success');
    }
}