<?php

namespace App\Interfaces\Repositories\Admin;

interface SettingRepositoryInterface
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
     * Bir ayarı anahtarına göre günceller veya yeni ekler
     */
    public function updateOrCreate(string $key, $value, string $type = 'text', string $group = 'general', array $attributes = []);
    
    /**
     * Toplu güncelleme yapar
     */
    public function bulkUpdate(array $settings);
}