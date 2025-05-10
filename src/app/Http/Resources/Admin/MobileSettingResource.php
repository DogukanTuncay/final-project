<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class MobileSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Eğer bir kaynak yerine direkt array gönderilmişse
        if (is_array($this->resource)) {
            return $this->handleArrayResource($this->resource);
        }
        
        // Ayarlar objesi için
        return $this->handleArrayResource($this->resource);
    }

    /**
     * İçeriği dil ayarlarına göre düzenle
     * 
     * @param array $resource
     * @return array
     */
    protected function handleArrayResource(array $resource): array
    {
        // Kullanıcı yerelini al
        $userLocale = $this->getUserLocale();
        
        // Çevrilebilir alanları temizle
        $cleanResource = $this->parseTranslatableFields($resource, $userLocale);
        
        return [
            'android' => [
                'version' => $this->when(isset($cleanResource['android_version']), $cleanResource['android_version']),
                'force_update' => $this->when(isset($cleanResource['android_force_update']), (bool)$cleanResource['android_force_update']),
                'update_message' => $this->when(isset($cleanResource['android_update_message']), $cleanResource['android_update_message']),
                'store_url' => $this->when(isset($cleanResource['android_store_url']), $cleanResource['android_store_url']),
                'min_version' => $this->when(isset($cleanResource['android_min_version']), $cleanResource['android_min_version']),
                'maintenance_mode' => $this->when(isset($cleanResource['android_maintenance_mode']), (bool)$cleanResource['android_maintenance_mode']),
            ],
            'ios' => [
                'version' => $this->when(isset($cleanResource['ios_version']), $cleanResource['ios_version']),
                'force_update' => $this->when(isset($cleanResource['ios_force_update']), (bool)$cleanResource['ios_force_update']),
                'update_message' => $this->when(isset($cleanResource['ios_update_message']), $cleanResource['ios_update_message']),
                'store_url' => $this->when(isset($cleanResource['ios_store_url']), $cleanResource['ios_store_url']),
                'min_version' => $this->when(isset($cleanResource['ios_min_version']), $cleanResource['ios_min_version']),
                'maintenance_mode' => $this->when(isset($cleanResource['ios_maintenance_mode']), (bool)$cleanResource['ios_maintenance_mode']),
            ],
            'maintenance' => $this->when(isset($cleanResource['mobile_maintenance']), (bool)$cleanResource['mobile_maintenance']),
            'maintenance_message' => $this->when(isset($cleanResource['mobile_maintenance_message']), $cleanResource['mobile_maintenance_message']),
        ];
    }
    
    /**
     * Çevrilebilir JSON alanları kullanıcı diline göre işle
     * 
     * @param array $resource
     * @param string $locale
     * @return array
     */
    protected function parseTranslatableFields(array $resource, string $locale): array
    {
        $translatableFields = [
            'android_update_message',
            'ios_update_message',
            'mobile_maintenance_message',
            'android_maintenance_message',
            'ios_maintenance_message'
        ];
        
        $fallbackLocale = config('app.fallback_locale', 'en');
        
        foreach ($translatableFields as $field) {
            if (isset($resource[$field]) && is_string($resource[$field])) {
                try {
                    $translations = json_decode($resource[$field], true);
                    if (is_array($translations)) {
                        // Önce kullanıcı dilini dene
                        if (isset($translations[$locale])) {
                            $resource[$field] = $translations[$locale];
                        }
                        // Yoksa varsayılan dili kullan
                        elseif (isset($translations[$fallbackLocale])) {
                            $resource[$field] = $translations[$fallbackLocale];
                        }
                        // Yine bulamazsan ilk değeri al
                        elseif (!empty($translations)) {
                            $resource[$field] = reset($translations);
                        }
                    }
                } catch (\Exception $e) {
                    // JSON dekode hatası durumunda orijinal değeri koru
                }
            }
        }
        
        return $resource;
    }
    
    /**
     * Kullanıcı yerelini al
     * 
     * @return string
     */
    protected function getUserLocale(): string
    {
        // JWT ile authenticate edilmiş kullanıcıyı al
        $user = JWTAuth::user();
        if ($user && !empty($user->locale)) {
            return $user->locale;
        }
        
        // Kullanıcı locale'i yoksa uygulama locale'ini kullan
        return app()->getLocale();
    }
} 