<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseChapterController;
use App\Http\Controllers\Api\CourseChapterLessonController;
use App\Http\Controllers\Api\CourseChapterLessonContentController;
use App\Http\Controllers\Api\MultipleChoiceQuestionController;
use App\Http\Controllers\Api\TrueFalseQuestionController;
use App\Http\Controllers\Api\ShortAnswerQuestionController;
use App\Http\Controllers\Api\UserLevelController;
use App\Http\Controllers\Api\MatchingQuestionController;
use App\Http\Controllers\Api\FillInTheBlankController;
use App\Http\Controllers\Api\MissionsController;

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
    Route::prefix('lessons')->group(function () {
        Route::get('chapter/{chapterId}', [CourseChapterLessonController::class, 'byChapter']);
        Route::get('{id}', [CourseChapterLessonController::class, 'show']);
        Route::get('{id}/prerequisites', [CourseChapterLessonController::class, 'prerequisites']);
        Route::get('{id}/lock-status', [CourseChapterLessonController::class, 'checkLockStatus']);
        Route::post('{id}/complete', [CourseChapterLessonController::class, 'markAsCompleted']);
    });

    // Ders İçerikleri (Lesson Contents) routes
    Route::group(['prefix' => 'lesson-contents', 'controller' => CourseChapterLessonContentController::class], function () {
        Route::get('lesson/{lessonId}', 'getByLessonId');
        Route::get('type/{lessonId}/{contentType}', 'getByContentType');
        Route::get('{id}', 'findById');
    });

    // Çoktan Seçmeli Sorular (Multiple Choice Questions) routes
    Route::group(['prefix' => 'multiple-choice', 'controller' => MultipleChoiceQuestionController::class], function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::get('slug/{slug}', 'showBySlug');
        Route::post('{id}/check-answer', 'checkAnswer'); // Cevabı kontrol etmek için
    });

    // Doğru/Yanlış Sorular (True/False Questions) routes
    Route::group(['prefix' => 'true-false', 'controller' => TrueFalseQuestionController::class], function () {
        Route::get('{id}', 'show');
        Route::post('{id}/check-answer', 'checkAnswer'); // Cevabı kontrol etmek için
    });

    // Kısa Cevaplı Sorular (Short Answer Questions) routes
    Route::group(['prefix' => 'short-answer', 'controller' => ShortAnswerQuestionController::class], function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::get('slug/{slug}', 'showBySlug');
        Route::post('{id}/check-answer', 'checkAnswer'); // Cevabı kontrol etmek için
    });

    // Eşleştirme Soruları (Matching Questions) routes
    Route::group(['prefix' => 'matching', 'controller' => MatchingQuestionController::class], function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::get('slug/{slug}', 'showBySlug');
        Route::post('{id}/check-answer', 'checkAnswer'); // Cevapları kontrol etmek için
    });

    // Boşluk Doldurma Soruları (Fill In The Blank Questions) routes
    Route::group(['prefix' => 'fill-in-the-blank', 'controller' => FillInTheBlankController::class], function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::get('slug/{slug}', 'showBySlug');
        Route::post('{id}/check-answer', 'checkAnswer'); // Cevapları kontrol etmek için
    });

    // Kullanıcı seviye ve XP bilgileri
    Route::get('user/level', [UserLevelController::class, 'getUserExperience']);
    Route::get('user/level/next', [UserLevelController::class, 'getNextLevel']);


    Route::prefix('missions')->name('missions.')->group(function () {

        // Görevler listesi
        Route::get('/', [MissionsController::class, 'index'])
            ->name('index'); // Listeleme işlemi



        // Görev detayını getir
        Route::get('{id}', [MissionsController::class, 'show'])
            ->name('show') // Görev detayını gösterme
            ->where('id', '[0-9]+'); // ID'nin sadece sayılar olmasını sağla

            Route::get('/available', [MissionsController::class, 'availableForUser'])->name('available');
            Route::get('/{id}/complete', [MissionsController::class, 'complete'])->name('complete');

});

});


require __DIR__.'/admin.php';

require __DIR__.'/auth.php';
