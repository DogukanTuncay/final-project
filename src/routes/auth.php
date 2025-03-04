<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])
->name('verification.resend');

Route::middleware(['JWT'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('refresh', [AuthController::class, 'refresh']);
});
