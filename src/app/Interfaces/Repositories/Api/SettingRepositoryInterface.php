<?php

namespace App\Interfaces\Repositories\Api;

interface SettingRepositoryInterface
{
    /**
     * ID'ye göre ayar bulma
     */
    public function findById($id);
    
    /**
     * Anahtara göre ayar bulma
     */
    public function findByKey($key);
    
    /**
     * Tüm ayarları sayfalama ile getirme
     */
    public function getWithPagination(array $params);
    
    /**
     * Site ayarlarını getir (site adı, logo, vs.)
     */
    public function getSiteSettings();
    
    /**
     * Mobil uygulama ayarlarını getir
     */
    public function getMobileSettings();
    
    /**
     * Bir grup ayarı getir
     */
    public function getSettingsByGroup(string $group);
    
    /**
     * Kullanıcı tarafından erişilebilecek tüm ayar gruplarını getir
     * 
     * @return array
     */
    public function getAvailableGroups(): array;
    
    /**
     * Kullanıcı tarafından erişilebilecek tüm ayar anahtarlarını getir
     * 
     * @param string|null $group Grup filtresi (opsiyonel)
     * @return array
     */
    public function getAvailableKeys(?string $group = null): array;
}