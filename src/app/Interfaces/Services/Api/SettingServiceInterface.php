<?php

namespace App\Interfaces\Services\Api;

interface SettingServiceInterface
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
     * Site bilgilerini getir (adı, logo, favicon, vb.)
     */
    public function getSiteInfo();
    
    /**
     * Mobil uygulama bilgilerini getir (versiyon kontrol, force update, vb.)
     * 
     * @param string|null $platform Platform bilgisi (android, ios)
     * @return array
     */
    public function getMobileInfo(?string $platform = null): array;
    
    /**
     * Belirli bir grup ayarını getir
     */
    public function getGroupSettings(string $group);
    
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