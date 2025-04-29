<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | Location servis sağlayıcısının hangi sürücüyü kullanacağı.
    | Desteklenen: "maxmind", "maxmind-api", "ip-api", "ipinfo", "ipdata", "geoip2", "geoPlugin"
    |
    */
    'driver' => env('LOCATION_DRIVER', 'ip-api'),

    /*
    |--------------------------------------------------------------------------
    | Driver Fallbacks
    |--------------------------------------------------------------------------
    |
    | Ana sürücü başarısız olursa, bu sırayla denenen sürücü dizisi.
    |
    */
    'fallbacks' => [
        'ipinfo',
        'geoPlugin', 
        'maxmind',
    ],

    /*
    |--------------------------------------------------------------------------
    | Position
    |--------------------------------------------------------------------------
    |
    | Konum bilgileri için kullanılacak IP adresi.
    | "auto" için isteğin IP adresi kullanılır.
    |
    */
    'position' => 'auto',

    /*
    |--------------------------------------------------------------------------
    | MaxMind Configuration
    |--------------------------------------------------------------------------
    |
    | MaxMind sürücüsü için yapılandırma.
    |
    */
    'maxmind' => [
        'database' => env('MAXMIND_DATABASE', storage_path('app/maxmind/GeoLite2-City.mmdb')),
        'license_key' => env('MAXMIND_LICENSE_KEY'),
        'user_id' => env('MAXMIND_USER_ID'),
        'webservice' => [
            'host' => 'geoip.maxmind.com',
            'account_id' => env('MAXMIND_ACCOUNT_ID'),
            'license_key' => env('MAXMIND_LICENSE_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IP-API Configuration
    |--------------------------------------------------------------------------
    |
    | IP-API sürücüsünün yapılandırması.
    |
    */
    'ip-api' => [
        'token' => env('IP_API_TOKEN'),
        'endpoint' => env('IP_API_ENDPOINT', 'http://pro.ip-api.com'),
        'secure' => env('IP_API_SECURE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | IPInfo Configuration
    |--------------------------------------------------------------------------
    |
    | IPInfo sürücüsünün yapılandırması.
    |
    */
    'ipinfo' => [
        'token' => env('IPINFO_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | IPData Configuration
    |--------------------------------------------------------------------------
    |
    | IPData sürücüsünün yapılandırması.
    |
    */
    'ipdata' => [
        'token' => env('IPDATA_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Lokasyon için test IP adresi
    |--------------------------------------------------------------------------
    |
    | Geliştirme sırasında kullanılacak test IP adresi.
    |
    */
    'testing' => [
        'enabled' => env('LOCATION_TESTING', false),
        'ip' => env('LOCATION_TESTING_IP', '8.8.8.8'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Önbellek
    |--------------------------------------------------------------------------
    |
    | Konum bilgileri önbelleğe alınmalı mı?
    |
    */
    'cache' => [
        'enabled' => env('LOCATION_CACHE_ENABLED', true),
        'key' => 'location',
        'time' => 30, // Dakika cinsinden
    ],
]; 