<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;

// Kimlik doğrulama rotaları - ayrı bir prefix olarak tanımlanıyor
Route::prefix('auth')->name('auth.')->group(function () {
    // Public rotalar - Email doğrulaması gerektirmeyen rotalar
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('verified')
        ->name('forgot-password');

    // Email doğrulama rotaları
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->middleware(['throttle:6,1'])
        ->name('verification.resend');
    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->name('verification.notice');

    // Korumalı rotalar - Kimlik doğrulaması gerektiren
    Route::middleware(['JWT', 'verified'])->group(function () {
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('me', [AuthController::class, 'me'])->name('me');
    });
});
