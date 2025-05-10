<?php

namespace App\Repositories\Admin;

use App\Models\Setting;
use App\Interfaces\Repositories\Admin\SettingRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    /**
     * Ayarları gruplarına göre getirir
     */
    public function getByGroup(string $group)
    {
        return $this->model->where('group', $group)->get();
    }
    
    /**
     * Bir ayarı anahtarına göre getirir
     */
    public function getByKey(string $key, $default = null)
    {
        $cacheKey = "settings:{$key}";
        
        // Cache'ten kontrolü
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $setting = $this->model->where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        $value = $setting->typed_value;
        
        // Cache'e kaydet
        Cache::put($cacheKey, $value, now()->addDay());
        
        return $value;
    }
    
    /**
     * Bir ayarı anahtarına göre günceller veya yeni ekler
     */
    public function updateOrCreate(string $key, $value, string $type = 'text', string $group = 'general', array $attributes = [])
    {
        $data = array_merge([
            'key' => $key,
            'value' => $value,
            'type' => $type,
            'group' => $group,
        ], $attributes);
        
        $setting = $this->model->updateOrCreate(['key' => $key], $data);
        
        // Cache'i temizle
        Cache::forget("settings:{$key}");
        Cache::forget("settings:group:{$group}");
        
        return $setting;
    }
    
    /**
     * Toplu güncelleme yapar
     */
    public function bulkUpdate(array $settings)
    {
        $updated = [];
        $affectedGroups = [];
        
        foreach ($settings as $key => $value) {
            $setting = $this->model->where('key', $key)->first();
            
            if ($setting) {
                $setting->value = $value;
                $setting->save();
                
                // Cache'i temizle
                Cache::forget("settings:{$key}");
                $affectedGroups[$setting->group] = true;
                
                $updated[] = $setting;
            }
        }
        
        // Etkilenen grupların cache'lerini temizle
        foreach (array_keys($affectedGroups) as $group) {
            Cache::forget("settings:group:{$group}");
        }
        
        return $updated;
    }
    
    /**
     * Bir ayarın özel/gizli durumunu ayarlar
     */
    public function setPrivate($id, bool $isPrivate = true)
    {
        $setting = $this->model->findOrFail($id);
        $setting->is_private = $isPrivate;
        $setting->save();
        
        // Cache'i temizle
        Cache::forget("settings:{$setting->key}");
        Cache::forget("settings:group:{$setting->group}");
        
        return $setting;
    }
    
    /**
     * Özel ayarları getirir
     */
    public function getPrivateSettings()
    {
        return $this->model->private()->get();
    }
    
    /**
     * Genel ayarları getirir
     */
    public function getPublicSettings()
    {
        return $this->model->public()->get();
    }
}