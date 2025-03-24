<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;

Route::prefix('auth')->group(function () {
    // Public routes - Email verification not required
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('verified');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('verified');

    // Email verification routes
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->middleware(['throttle:6,1'])
        ->name('verification.resend');
    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->name('verification.notice');

    // Protected routes
    Route::middleware(['JWT', 'verified'])->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
