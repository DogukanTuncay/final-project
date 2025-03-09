<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseChapterController;
use App\Http\Controllers\Admin\CourseChapterLessonController;


Route::group(['middleware' => ['JWT'], 'prefix' => 'admin', 'namespace' => 'App\Http\Controllers\Admin'], function () {
    
    Route::group(['prefix' => 'courses', 'controller' => CourseController::class], function () {
        Route::get('/', 'index')->name('admin.courses.index');
        Route::post('/', 'store')->name('admin.courses.store');
        Route::get('{id}', 'show')->name('admin.courses.show');
        Route::put('{id}', 'update')->name('admin.courses.update');
        Route::delete('{id}', 'destroy')->name('admin.courses.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.courses.toggle-status');
        Route::patch('{id}/featured', 'toggleFeatured')->name('admin.courses.toggle-featured');
        Route::patch('{id}/order', 'updateOrder')->name('admin.courses.update-order');
        Route::get('category/{category}', 'byCategory')->name('admin.courses.by-category');
    });

    Route::group(['prefix' => 'course-chapters', 'controller' => CourseChapterController::class], function () {
        Route::get('/', 'index')->name('admin.course-chapters.index');
        Route::post('/', 'store')->name('admin.course-chapters.store');
        Route::get('{id}', 'show')->name('admin.course-chapters.show');
        Route::put('{id}', 'update')->name('admin.course-chapters.update');
        Route::delete('{id}', 'destroy')->name('admin.course-chapters.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.course-chapters.toggle-status');
        Route::patch('{id}/order', 'updateOrder')->name('admin.course-chapters.update-order');
        Route::get('course/{courseId}', 'byCourse')->name('admin.course-chapters.by-course');
    });
    
    Route::group(['prefix' => 'course-chapter-lessons', 'controller' => CourseChapterLessonController::class], function () {
        Route::get('/', 'index')->name('admin.course-chapter-lessons.index');
        Route::post('/', 'store')->name('admin.course-chapter-lessons.store');
        Route::get('{id}', 'show')->name('admin.course-chapter-lessons.show');
        Route::put('{id}', 'update')->name('admin.course-chapter-lessons.update');
        Route::delete('{id}', 'destroy')->name('admin.course-chapter-lessons.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.course-chapter-lessons.toggle-status');
        Route::patch('{id}/order', 'updateOrder')->name('admin.course-chapter-lessons.update-order');
        Route::get('chapter/{chapterId}', 'byChapter')->name('admin.course-chapter-lessons.by-chapter');
    });
    
});