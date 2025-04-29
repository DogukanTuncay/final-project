<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // API Rotaları - api.php dosyasındaki rotalar 'api' prefix'ini kullanıyor
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Web Rotaları
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
                
            // Auth Rotaları - auth.php dosyasındaki rotalar
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/auth.php'));
                
            // Admin Rotaları - admin.php dosyasındaki rotalar 
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/admin.php'));
        });
    }
} 