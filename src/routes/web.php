<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
  // Şifre sıfırlama rotaları
  Route::post('password-reset', [ResetPasswordController::class, 'reset'])
  ->middleware('guest')
  ->name('password.update');
Route::get('password-reset/{token}', [ResetPasswordController::class, 'showResetForm'])
  ->middleware('guest')
  ->name('password.reset');


Route::get('/', function () {
    return view('welcome');
});

// Web uygulaması için ek rotalar buraya eklenebilir
// NOT: Admin, API ve Auth rotaları kendi dosyalarında tanımlanmıştır

