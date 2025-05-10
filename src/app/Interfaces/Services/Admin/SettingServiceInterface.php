<?php

namespace App\Interfaces\Services\Admin;

interface SettingServiceInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    
    /**
     * Ayarları gruplarına göre getirir
     */
    public function getByGroup(string $group);
    
    /**
     * Bir ayarı anahtarına göre getirir
     */
    public function getByKey(string $key, $default = null);
    
    /**
     * Site genel ayarlarını günceller
     */
    public function updateSiteSettings(array $data);
    
    /**
     * Mobil uygulama ayarlarını günceller
     */
    public function updateMobileSettings(array $data);
    
    /**
     * Görsel (logo, favicon, vb) içeren ayarları günceller
     */
    public function updateImageSettings(array $data, array $files);
    
    /**
     * Önbelleği temizler
     */
    public function clearCache();
}