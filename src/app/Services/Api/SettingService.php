<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\SettingServiceInterface;
use App\Interfaces\Repositories\Api\SettingRepositoryInterface;
use App\Http\Resources\Api\SettingResource;

class SettingService implements SettingServiceInterface
{
    protected $repository;

    public function __construct(SettingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ID'ye göre ayar bulma
     */
    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Anahtara göre ayar bulma
     */
    public function findByKey($key)
    {
        return $this->repository->findByKey($key);
    }

    /**
     * Tüm ayarları sayfalama ile getirme
     */
    public function getWithPagination(array $params)
    {
        return $this->repository->getWithPagination($params);
    }

    /**
     * Site bilgilerini getir (adı, logo, favicon, vb.)
     */
    public function getSiteInfo()
    {
        $settings = $this->repository->getSiteSettings();
        $settings = SettingResource::collection($settings);
        
        $formattedSettings = [];
        foreach ($settings as $setting) {
            $formattedSettings[$setting['key']] = $setting['value'];
        }
        
        // Site bilgileri için gerekli alanları burada düzenleyebilirsiniz
        $siteInfo = [
            'name' => $formattedSettings['site_name'] ?? null,
            'description' => $formattedSettings['site_description'] ?? null,
            'logo' => $formattedSettings['site_logo'] ?? null,
            'favicon' => $formattedSettings['site_favicon'] ?? null,
            'email' => $formattedSettings['site_email'] ?? null,
            'phone' => $formattedSettings['site_phone'] ?? null,
            'address' => $formattedSettings['site_address'] ?? null,
            'social' => [
                'facebook' => $formattedSettings['site_facebook'] ?? null,
                'twitter' => $formattedSettings['site_twitter'] ?? null,
                'instagram' => $formattedSettings['site_instagram'] ?? null,
                'linkedin' => $formattedSettings['site_linkedin'] ?? null,
                'youtube' => $formattedSettings['site_youtube'] ?? null,
            ]
        ];
        
        return $siteInfo;
    }

    /**
     * Mobil uygulama bilgilerini getir (versiyon kontrol, force update, vb.)
     * 
     * @param string|null $platform Platform bilgisi (android, ios)
     * @return array
     */
    public function getMobileInfo(?string $platform = null): array
    {
        $mobileSettings = $this->repository->getMobileSettings();
        $mobileSettings = SettingResource::collection($mobileSettings);
        
        $formattedSettings = [];
        foreach ($mobileSettings as $setting) {
            $formattedSettings[$setting['key']] = $setting['value'];
        }
        
        // Platform belirtilmişse sadece o platforma özel ayarları döndür
        if ($platform) {
            $platform = strtolower($platform);
            
            if ($platform === 'android') {
                return [
                    'version' => $formattedSettings['android_version'] ?? null,
                    'force_update' => (bool)($formattedSettings['android_force_update'] ?? false),
                    'update_message' => $formattedSettings['android_update_message'] ?? null,
                    'store_url' => $formattedSettings['android_store_url'] ?? null,
                    'min_version' => $formattedSettings['android_min_version'] ?? null,
                    'maintenance' => (bool)($formattedSettings['mobile_maintenance'] ?? false),
                    'maintenance_message' => $formattedSettings['mobile_maintenance_message'] ?? null,
                ];
            } elseif ($platform === 'ios') {
                return [
                    'version' => $formattedSettings['ios_version'] ?? null,
                    'force_update' => (bool)($formattedSettings['ios_force_update'] ?? false),
                    'update_message' => $formattedSettings['ios_update_message'] ?? null,
                    'store_url' => $formattedSettings['ios_store_url'] ?? null,
                    'min_version' => $formattedSettings['ios_min_version'] ?? null,
                    'maintenance' => (bool)($formattedSettings['mobile_maintenance'] ?? false),
                    'maintenance_message' => $formattedSettings['mobile_maintenance_message'] ?? null,
                ];
            }
        }
        
        // Platforma özel değilse tümünü döndür
        return $formattedSettings;
    }

    /**
     * Belirli bir grup ayarını getir
     */
    public function getGroupSettings(string $group)
    {
        $settings = $this->repository->getSettingsByGroup($group);
        
      
        
        return $settings;
    }
    
    /**
     * Kullanıcı tarafından erişilebilecek tüm ayar gruplarını getir
     * 
     * @return array
     */
    public function getAvailableGroups(): array
    {
        return $this->repository->getAvailableGroups();
    }
    
    /**
     * Kullanıcı tarafından erişilebilecek tüm ayar anahtarlarını getir
     * 
     * @param string|null $group Grup filtresi (opsiyonel)
     * @return array
     */
    public function getAvailableKeys(?string $group = null): array
    {
        return $this->repository->getAvailableKeys($group);
    }
}