<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseChapterController;
use App\Http\Controllers\Admin\CourseChapterLessonController;
use App\Http\Controllers\Admin\CourseChapterLessonContentController;


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
    
    // Ders İçerikleri (CourseChapterLessonContent) Route'ları
    Route::group(['prefix' => 'lesson-contents', 'controller' => CourseChapterLessonContentController::class], function () {
        Route::get('/', 'index')->name('admin.lesson-contents.index');
        Route::post('/', 'store')->name('admin.lesson-contents.store');
        Route::get('{id}', 'show')->name('admin.lesson-contents.show');
        Route::put('{id}', 'update')->name('admin.lesson-contents.update');
        Route::delete('{id}', 'destroy')->name('admin.lesson-contents.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.lesson-contents.toggle-status');
        Route::patch('{id}/order', 'updateOrder')->name('admin.lesson-contents.update-order');
        Route::post('bulk-update-order', 'bulkUpdateOrder')->name('admin.lesson-contents.bulk-update-order');
        Route::get('lesson/{lessonId}', 'byLesson')->name('admin.lesson-contents.by-lesson');
        
        // İçerik türlerine özel route'lar
        Route::post('text', 'createTextContent')->name('admin.lesson-contents.create-text');
        Route::post('video', 'createVideoContent')->name('admin.lesson-contents.create-video');
        Route::post('fill-in-the-blank', 'createFillInTheBlankContent')->name('admin.lesson-contents.create-fill-in-the-blank');
    });
    
});