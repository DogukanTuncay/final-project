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
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\StoryCategoryController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VideoContentController;

// API Route grubu için ana yapılandırma - prefix kaldırıldı, çünkü RouteServiceProvider.php zaten ekliyor
Route::name('api.')->group(function () {
    // JWT ile korunan kullanıcı bilgisi endpoint'i
    Route::middleware('JWT')->get('/user', function (Request $request) {
        return $request->user();
    });

    // Kullanıcı doğrulaması gerektiren API rotaları
    Route::group(['middleware' => ['JWT', 'verified', 'role:user|admin|super-admin', 'record.login'], 'as' => 'api.'], function () {
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

        // Görevler
        Route::prefix('missions')->name('missions.')->group(function () {
            Route::get('/', [MissionsController::class, 'index'])->name('index');
            Route::get('{id}', [MissionsController::class, 'show'])->name('show')->where('id', '[0-9]+');
            Route::get('/available', [MissionsController::class, 'availableForUser'])->name('available');
            Route::get('/{id}/complete', [MissionsController::class, 'complete'])->name('complete');
            Route::get('/my-progress', [MissionsController::class, 'myProgress'])->name('my-progress');
        });

        // === YENİ KULLANICI ROTALARI ===
        Route::group(['prefix' => 'user', 'controller' => UserController::class], function () {
            // Profil bilgilerini getirme
            Route::get('/profile', 'profile')->name('user.profile');

            // Profil bilgilerini güncelleme (PATCH veya PUT)
            Route::patch('/profile', 'updateProfile')->name('user.updateProfile');

            // Dil tercihini güncelleme
            Route::patch('/locale', 'updateLocale')->name('user.updateLocale');

            // Gelecekte diğer kullanıcı endpointleri eklenebilir (örn: şifre değiştirme)
            // Route::patch('/password', 'updatePassword')->name('user.updatePassword');
        });
        // === YENİ KULLANICI ROTALARI BİTİŞ ===

        // Leaderboard
        Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

        // Rozetler (Badges)
        Route::prefix('badges')->name('badges.')->group(function () {
            Route::get('/', [BadgeController::class, 'index'])->name('index');
            Route::get('/user', [BadgeController::class, 'userBadges'])->name('user');
            Route::get('/check', [BadgeController::class, 'checkBadges'])->name('check');
            Route::get('/{id}', [BadgeController::class, 'show'])->name('show');
        });

        // Story Categories
        Route::get('/story-categories', [StoryCategoryController::class, 'index'])->name('story-categories.index');
        Route::get('/story-categories/{slug}', [StoryCategoryController::class, 'showBySlug'])->name('story-categories.showBySlug');

        // Stories
        Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
        Route::get('/stories/{id}', [StoryController::class, 'show'])->name('stories.show')->where('id', '[0-9]+');

        // Belirli bir kategoriye ait story'leri getirmek için route (opsiyonel, index üzerinden filtreleme yapılabilir)
        // Route::get('/story-categories/{category_slug}/stories', [StoryController::class, 'index'])->name('stories.byCategorySlug');

        // Bildirim Rotaları
        Route::prefix('notifications')->group(function () {
            Route::post('/custom', [NotificationController::class, 'sendCustomNotification']);
            Route::post('/broadcast', [NotificationController::class, 'sendBroadcastNotification']);
            Route::get('/logs', [NotificationController::class, 'getNotificationLogs']);
            
            // Bildirim Ayarları
            Route::get('/settings', [App\Http\Controllers\Api\NotificationSettingController::class, 'getSettings']);
            Route::put('/settings', [App\Http\Controllers\Api\NotificationSettingController::class, 'updateSettings']);
            Route::post('/settings/reset', [App\Http\Controllers\Api\NotificationSettingController::class, 'resetSettings']);
        });

        // Video İçerik Rotaları
        Route::group(['prefix' => 'video-contents', 'controller' => VideoContentController::class], function () {
            Route::get('/', 'index');
            Route::get('/active', 'getActiveVideos');
            Route::get('/provider/{provider}', 'getVideosByProvider');
            Route::get('/{id}', 'show');
            Route::get('/slug/{slug}', 'showBySlug');
            Route::post('/validate-url', 'validateVideoUrl');
        });
    });
});

// Admin ve Auth route'larını ayrı dosyalardan yükle
// NOT: admin.php ve auth.php içinde kendi prefix tanımlamaları yapılmalıdır.
// Artık burada require kullanılmayacak
