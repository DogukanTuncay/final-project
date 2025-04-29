<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OneSignal App ID
    |--------------------------------------------------------------------------
    |
    | Burada OneSignal uygulamanızın App ID'sini belirtebilirsiniz
    |
    */
    'app_id' => env('ONESIGNAL_APP_ID'),

    /*
    |--------------------------------------------------------------------------
    | OneSignal REST API Key
    |--------------------------------------------------------------------------
    |
    | Burada OneSignal uygulamanızın REST API Key'ini belirtebilirsiniz
    |
    */
    'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OneSignal Guzzle Client Config
    |--------------------------------------------------------------------------
    |
    | Guzzle İstemcisi için yapılandırma ayarları
    |
    */
    'guzzle_client_timeout' => env('ONESIGNAL_GUZZLE_CLIENT_TIMEOUT', 30),
]; 