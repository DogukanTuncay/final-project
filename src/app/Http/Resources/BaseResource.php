<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\Api\EventService;


class BaseResource extends JsonResource
{
    /**
     * Çevrilebilir alanları otomatik olarak dönüştürür
     */
    protected function getTranslated($model): array
    {
        if (!method_exists($model, 'getTranslatableAttributes')) {
            return [];
        }
    
        // JWT ile authenticate edilmiş kullanıcıyı al
        $userLocale = null;
        $user = JWTAuth::user();
        $userLocale = $user && !empty($user->locale) ? $user->locale : null;
        // Kullanıcı locale'i yoksa uygulama locale'ini kullan
        $locale = $userLocale ?? app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $translatable = $model->getTranslatableAttributes();
        $translated = [];
        foreach ($translatable as $attribute) {
            $translated[$attribute] = $model->getTranslation($attribute, $locale, false) 
                ?? $model->getTranslation($attribute, $fallbackLocale, false)
                ?? null;
        }
        return $translated;
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with($request)
    {
        $eventService = app(EventService::class);
        $events = $eventService->getEvents();
        $eventService->clearEvents();
        
        return [
            'success' => true,
            'events' => $events
        ];
    }
}