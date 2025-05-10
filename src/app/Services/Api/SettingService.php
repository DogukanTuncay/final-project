<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\SettingServiceInterface;
use App\Interfaces\Repositories\Api\SettingRepositoryInterface;

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
        
        // Site bilgileri için gerekli alanları burada düzenleyebilirsiniz
        $siteInfo = [
            'name' => $settings['site_name'] ?? null,
            'description' => $settings['site_description'] ?? null,
            'logo' => $settings['site_logo'] ?? null,
            'favicon' => $settings['site_favicon'] ?? null,
            'email' => $settings['site_email'] ?? null,
            'phone' => $settings['site_phone'] ?? null,
            'address' => $settings['site_address'] ?? null,
            'social' => [
                'facebook' => $settings['site_facebook'] ?? null,
                'twitter' => $settings['site_twitter'] ?? null,
                'instagram' => $settings['site_instagram'] ?? null,
                'linkedin' => $settings['site_linkedin'] ?? null,
                'youtube' => $settings['site_youtube'] ?? null,
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
        $settings = $this->repository->getMobileSettings();
        
        // Platform belirtilmişse sadece o platforma özel ayarları döndür
        if ($platform) {
            $platform = strtolower($platform);
            
            if ($platform === 'android') {
                return [
                    'version' => $settings['android_version'] ?? null,
                    'force_update' => (bool)($settings['android_force_update'] ?? false),
                    'update_message' => $settings['android_update_message'] ?? null,
                    'store_url' => $settings['android_store_url'] ?? null,
                    'min_version' => $settings['android_min_version'] ?? null,
                    'maintenance' => (bool)($settings['mobile_maintenance'] ?? false),
                    'maintenance_message' => $settings['mobile_maintenance_message'] ?? null,
                ];
            } elseif ($platform === 'ios') {
                return [
                    'version' => $settings['ios_version'] ?? null,
                    'force_update' => (bool)($settings['ios_force_update'] ?? false),
                    'update_message' => $settings['ios_update_message'] ?? null,
                    'store_url' => $settings['ios_store_url'] ?? null,
                    'min_version' => $settings['ios_min_version'] ?? null,
                    'maintenance' => (bool)($settings['mobile_maintenance'] ?? false),
                    'maintenance_message' => $settings['mobile_maintenance_message'] ?? null,
                ];
            }
        }
        
        // Platforma özel değilse tümünü döndür
        return $settings;
    }

    /**
     * Belirli bir grup ayarını getir
     */
    public function getGroupSettings(string $group)
    {
        return $this->repository->getSettingsByGroup($group);
    }
}