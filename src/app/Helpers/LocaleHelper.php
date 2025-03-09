<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class LocaleHelper
{
    /**
     * Kullanıcının dilini döndür
     *
     * @return string
     */
    public static function getUserLocale(): string
    {
        if (Auth::check() && Auth::user()->locale) {
            return Auth::user()->locale;
        }
        
        return App::getLocale();
    }
    
    /**
     * Kullanıcının dilini ayarla
     *
     * @param string $locale
     * @return void
     */
    public static function setUserLocale(string $locale): void
    {
        $supportedLocales = config('app.supported_locales', ['tr', 'en']);
        
        if (!in_array($locale, $supportedLocales)) {
            return;
        }
        
        // Kullanıcı giriş yapmışsa, dil tercihini güncelle
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }
        
        // Global olarak dili ayarla
        App::setLocale($locale);
    }
}