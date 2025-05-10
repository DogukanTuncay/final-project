<?php

namespace App\Repositories\Api;

use App\Models\Setting;
use App\Interfaces\Repositories\Api\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

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
        $cacheKey = "settings:{$key}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $setting = $this->model->public()->where('key', $key)->first();
        
        if ($setting) {
            $value = $setting->typed_value;
            Cache::put($cacheKey, $value, now()->addDay());
            return $value;
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
        $cacheKey = "settings:site";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $settings = $this->model->public()->where('group', 'site')->get();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->typed_value;
        }
        
        Cache::put($cacheKey, $result, now()->addHour());
        
        return $result;
    }

    /**
     * Mobil uygulama ayarlarını getir
     */
    public function getMobileSettings()
    {
        $cacheKey = "settings:mobile";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $query = $this->model->public()->where('group', 'mobile');
        $settings = $query->get();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->typed_value;
        }
        
        Cache::put($cacheKey, $result, now()->addHour());
        
        return $result;
    }

    /**
     * Bir grup ayarı getir
     */
    public function getSettingsByGroup(string $group)
    {
        $cacheKey = "settings:group:{$group}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $settings = $this->model->public()->where('group', $group)->get();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->typed_value;
        }
        
        Cache::put($cacheKey, $result, now()->addHour());
        
        return $result;
    }
}