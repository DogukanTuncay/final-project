<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class CheckMobileAppVersion
{
    /**
     * Mobil uygulama versiyonunu kontrol eder.
     * Eğer uygulama versiyonu minimum gereksinimden düşükse hata fırlatır.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Mobil uygulama isteği mi kontrol et
        if ($this->isMobileApp($request)) {
            $platform = $this->getPlatform($request);
            $version = $this->getAppVersion($request);
            
            if ($platform && $version) {
                // Platformun minimum ve güncel versiyonlarını ayarlardan al
                $minVersion = Setting::getByKey("{$platform}_min_version", "1.0.0");
                $latestVersion = Setting::getByKey("{$platform}_latest_version", "1.0.0");
                $forceUpdate = (bool) Setting::getByKey("{$platform}_force_update", false);
                
                // Versiyon kontrolü
                if ($this->isVersionLowerThan($version, $minVersion)) {
                    // Mobil uygulama tarafından anlaşılabilir özel hata kodlu yanıt
                    $message = [
                        'status' => 'error',
                        'error_code' => 'APP_VERSION_TOO_LOW',
                        'message' => __('api.app_version_too_low', [
                            'current' => $version,
                            'required' => $minVersion
                        ]),
                        'force_update' => $forceUpdate,
                        'store_url' => Setting::getByKey("{$platform}_store_url"),
                        'latest_version' => $latestVersion
                    ];
                    
                    throw new PreconditionFailedHttpException(json_encode($message));
                }
            }
        }
        
        return $next($request);
    }
    
    /**
     * İsteğin mobil uygulamadan gelip gelmediğini kontrol eder
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isMobileApp(Request $request): bool
    {
        // Kullanıcı ajanı veya özel başlık kontrolü yapılabilir
        return $request->hasHeader('X-App-Version') || 
               $request->hasHeader('X-Platform') ||
               (
                   $request->hasHeader('User-Agent') && 
                   (
                       str_contains($request->header('User-Agent'), 'DavahApp/') ||
                       str_contains($request->header('User-Agent'), 'Davah-Android') ||
                       str_contains($request->header('User-Agent'), 'Davah-iOS')
                   )
               );
    }
    
    /**
     * İsteğin hangi platformdan geldiğini belirler (ios/android)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    private function getPlatform(Request $request): ?string
    {
        if ($request->hasHeader('X-Platform')) {
            $platform = strtolower($request->header('X-Platform'));
            return $platform === 'ios' || $platform === 'android' ? $platform : null;
        }
        
        $userAgent = $request->header('User-Agent');
        
        if (str_contains($userAgent, 'iOS') || str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            return 'ios';
        }
        
        if (str_contains($userAgent, 'Android')) {
            return 'android';
        }
        
        return null;
    }
    
    /**
     * İstekteki uygulama versiyonunu alır
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    private function getAppVersion(Request $request): ?string
    {
        if ($request->hasHeader('X-App-Version')) {
            return $request->header('X-App-Version');
        }
        
        $userAgent = $request->header('User-Agent');
        
        // DavahApp/1.0.0 veya Davah-iOS/1.0.0 formatındaki user agent'dan versiyon çıkarma
        if (preg_match('/Davah(?:-iOS|-Android|App)\/([0-9]+\.[0-9]+\.[0-9]+)/', $userAgent, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Versiyon karşılaştırması yapar
     *
     * @param  string  $currentVersion
     * @param  string  $minVersion
     * @return bool
     */
    private function isVersionLowerThan(string $currentVersion, string $minVersion): bool
    {
        return version_compare($currentVersion, $minVersion, '<');
    }
} 