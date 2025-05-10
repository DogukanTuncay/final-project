<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\SettingServiceInterface;
use App\Interfaces\Repositories\Admin\SettingRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\Cache;

class SettingService extends BaseService implements SettingServiceInterface
{
    public function __construct(SettingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
    
    /**
     * Ayarları gruplarına göre getirir
     */
    public function getByGroup(string $group)
    {
        return $this->repository->getByGroup($group);
    }
    
    /**
     * Bir ayarı anahtarına göre getirir
     */
    public function getByKey(string $key, $default = null)
    {
        return $this->repository->getByKey($key, $default);
    }
    
    /**
     * Site genel ayarlarını günceller
     */
    public function updateSiteSettings(array $data)
    {
        $result = [];
        
        foreach ($data as $key => $value) {
            // Anahtar başına 'site_' öneki ekliyoruz
            $fullKey = (strpos($key, 'site_') === 0) ? $key : "site_{$key}";
            
            $setting = $this->repository->updateOrCreate(
                $fullKey, 
                $value, 
                'text', 
                'site', 
                [
                    'is_translatable' => in_array($key, ['name', 'description', 'keywords', 'footer_text'])
                ]
            );
            
            $result[$key] = $setting;
        }
        
        $this->clearCache('site');
        
        return $result;
    }
    
    /**
     * Mobil uygulama ayarlarını günceller
     */
    public function updateMobileSettings(array $data)
    {
        $result = [];
        
        foreach ($data as $key => $value) {
            // Ayarın tipini belirleme
            $type = 'text';
            if (strpos($key, 'version') !== false) {
                $type = 'text';
            } elseif (strpos($key, 'force_update') !== false) {
                $type = 'boolean';
            } elseif (strpos($key, 'maintenance') !== false) {
                $type = 'boolean';
            }
            
            $setting = $this->repository->updateOrCreate(
                $key, 
                $value, 
                $type, 
                'mobile'
            );
            
            $result[$key] = $setting;
        }
        
        $this->clearCache('mobile');
        
        return $result;
    }
    
    /**
     * Görsel (logo, favicon, vb) içeren ayarları günceller
     */
    public function updateImageSettings(array $data, array $files)
    {
        $result = [];
        
        // Yüklenen dosyaları işleme
        foreach ($files as $key => $file) {
            if ($file) {
                $setting = $this->repository->getModel()->where('key', $key)->first();
                
                if (!$setting) {
                    $setting = $this->repository->create([
                        'key' => $key,
                        'value' => '',
                        'type' => 'image',
                        'group' => 'site',
                        'is_translatable' => false,
                    ]);
                }
                
                $setting->uploadImage($file);
                $result[$key] = $setting;
            }
        }
        
        // Diğer verileri güncelleme
        foreach ($data as $key => $value) {
            if (!isset($files[$key]) || !$files[$key]) {
                $setting = $this->repository->updateOrCreate(
                    $key, 
                    $value, 
                    'text', 
                    'site'
                );
                
                $result[$key] = $setting;
            }
        }
        
        $this->clearCache('site');
        
        return $result;
    }
    
    /**
     * Önbelleği temizler
     */
    public function clearCache($group = null)
    {
        if ($group) {
            Cache::forget("settings:group:{$group}");
            
            if ($group === 'site') {
                Cache::forget("settings:site");
            } elseif ($group === 'mobile') {
                Cache::forget("settings:mobile");
                Cache::forget("settings:mobile:ios");
                Cache::forget("settings:mobile:android");
            }
        } else {
            // Tüm ayar cache'lerini temizle
            $patterns = [
                "settings:*"
            ];
            
            foreach ($patterns as $pattern) {
                $keys = Cache::get($pattern);
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Bir ayarın özel/gizli durumunu günceller
     */
    public function setPrivate($id, bool $isPrivate = true)
    {
        return $this->repository->setPrivate($id, $isPrivate);
    }
    
    /**
     * Tüm özel ayarları getirir
     */
    public function getPrivateSettings()
    {
        return $this->repository->getPrivateSettings();
    }
    
    /**
     * Tüm genel ayarları getirir
     */
    public function getPublicSettings()
    {
        return $this->repository->getPublicSettings();
    }
    
    /**
     * Yeni bir özel ayar oluşturur
     */
    public function createPrivateSetting(array $data)
    {
        $key = $data['key'];
        $value = $data['value'];
        $type = $data['type'] ?? 'text';
        $group = $data['group'] ?? 'system';
        $description = $data['description'] ?? null;
        $isTranslatable = $data['is_translatable'] ?? false;
        
        return $this->repository->updateOrCreate(
            $key, 
            $value, 
            $type, 
            $group, 
            [
                'is_translatable' => $isTranslatable,
                'description' => $description,
                'is_private' => true
            ]
        );
    }
}