<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CourseController;


Route::group(['middleware' => ['JWT'], 'prefix' => 'admin', 'namespace' => 'App\Http\Controllers\Admin'], function () {
    Route::group(['prefix' => 'courses', 'controller' => CourseController::class], function () {
        Route::get('/', 'index')->name('admin.courses.index');
        Route::post('/', 'store')->name('admin.courses.store');
        Route::get('{id}', 'show')->name('admin.courses.show');
        Route::put('{id}', 'update')->name('admin.courses.update');
        Route::delete('{id}', 'destroy')->name('admin.courses.destroy');
        Route::put('{id}/status', 'toggleStatus')->name('admin.courses.toggle-status');
        Route::put('{id}/featured', 'toggleFeatured')->name('admin.courses.toggle-featured');
        Route::put('{id}/order', 'updateOrder')->name('admin.courses.update-order');
        Route::get('category/{category}', 'byCategory')->name('admin.courses.by-category');
    });
});