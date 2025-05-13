<?php

namespace App\Repositories\Api;

use App\Models\Setting;
use App\Interfaces\Repositories\Api\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    protected $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    /**
     * ID'ye göre ayar bulma
     */
    public function findById($id)
    {
        return $this->model->public()->findOrFail($id);
    }

    /**
     * Anahtara göre ayar bulma
     */
    public function findByKey($key)
    {
        $setting = $this->model->public()->where('key', $key)->first();
        
        if ($setting) {
            return $setting;
        }
        
        return null;
    }

    /**
     * Tüm ayarları sayfalama ile getirme
     */
    public function getWithPagination(array $params)
    {
        $query = $this->model->public();

        // Gruba göre filtreleme
        if (isset($params['group'])) {
            $query->where('group', $params['group']);
        }

        // Tip'e göre filtreleme
        if (isset($params['type'])) {
            $query->where('type', $params['type']);
        }

        // Anahtar arama
        if (isset($params['search'])) {
            $query->where('key', 'like', "%{$params['search']}%");
        }

        // Sıralama işlemleri
        $orderBy = $params['order_by'] ?? 'id';
        $orderDirection = $params['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        // Sayfalama
        $perPage = $params['per_page'] ?? 15;
        return $query->get();
    }

    /**
     * Site ayarlarını getir (site adı, logo, vs.)
     */
    public function getSiteSettings()
    {
        return $this->model->public()->where('group', 'site')->get();
    }

    /**
     * Mobil uygulama ayarlarını getir
     */
    public function getMobileSettings()
    {
        return $this->model->public()->where('group', 'mobile')->get();
    }

    /**
     * Bir grup ayarı getir
     */
    public function getSettingsByGroup(string $group)
    {
        $settings = $this->model->public()->where('group', $group)->get();
        return $settings;
    }
    
    /**
     * Kullanıcı tarafından erişilebilecek tüm ayar gruplarını getir
     * 
     * @return array
     */
    public function getAvailableGroups(): array
    {
        $groups = $this->model->public()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->toArray();
            
        return $groups;
    }
    
    /**
     * Kullanıcı tarafından erişilebilecek tüm ayar anahtarlarını getir
     * 
     * @param string|null $group Grup filtresi (opsiyonel)
     * @return array
     */
    public function getAvailableKeys(?string $group = null): array
    {
        $query = $this->model->public()
            ->select('key', 'group', 'type', 'description', 'is_translatable')
            ->orderBy('group')
            ->orderBy('key');
            
        if ($group) {
            $query->where('group', $group);
        }
        
        $settings = $query->get();
        
     
        
        return $settings;
    }
}