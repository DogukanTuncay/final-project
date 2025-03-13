<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseChapterController;
use App\Http\Controllers\Api\CourseChapterLessonController;
use App\Http\Controllers\Api\CourseChapterLessonContentController;

Route::middleware('JWT')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['JWT'], 'namespace' => 'App\Http\Controllers\Api'], function () {
    // Course routes
    Route::group(['prefix' => 'courses', 'controller' => CourseController::class], function () {
        Route::get('/', 'index');
        Route::get('featured', 'featured');
        Route::get('category/{category}', 'byCategory');
        Route::get('slug/{slug}', 'bySlug');
        Route::get('{id}', 'show');
    });

    // Course Chapter routes
    Route::group(['prefix' => 'chapters', 'controller' => CourseChapterController::class], function () {
        Route::get('course/{courseId}', 'byCourse');
        Route::get('{id}', 'show');
    });
    
    // Course Chapter Lesson routes
    Route::group(['prefix' => 'lessons', 'controller' => CourseChapterLessonController::class], function () {
        Route::get('chapter/{chapterId}', 'byChapter');
        Route::post('{id}/complete', 'markAsCompleted');
        Route::get('{id}', 'show');
    });
    
    // Ders İçerikleri (Lesson Contents) routes
    Route::group(['prefix' => 'lesson-contents', 'controller' => CourseChapterLessonContentController::class], function () {
        Route::get('lesson/{lessonId}', 'getByLessonId');
        Route::get('type/{lessonId}/{contentType}', 'getByContentType');
        Route::get('{id}', 'findById');
    });
});

require __DIR__.'/admin.php';

require __DIR__.'/auth.php';
