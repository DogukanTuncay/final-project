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
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\NotificationSettingController;
use App\Http\Controllers\Api\ContactController;
// API Route grubu için ana yapılandırma - prefix kaldırıldı, çünkü RouteServiceProvider.php zaten ekliyor
Route::name('api.')->group(function () {
    // JWT ile korunan kullanıcı bilgisi endpoint'i
    Route::middleware('JWT')->get('/user', function (Request $request) {
        return $request->user();
    });

    // Ayarlar (Settings) - Kimlik doğrulaması gerektirmeyen public rotalar
    Route::group(['prefix' => 'settings', 'controller' => SettingController::class], function () {
        Route::get('/site-info', 'getSiteInfo');
        Route::get('/mobile/{platform?}', 'getMobileInfo');
    });

    // İletişim Formu - Kimlik doğrulaması gerektirmeyen public rotalar
    Route::group(['prefix' => 'contact', 'controller' => ContactController::class], function () {
        Route::post('/', 'store');
    });

    // Kullanıcı doğrulaması gerektiren API rotaları
    Route::group(['middleware' => ['JWT', 'verified', 'role:user|admin|super-admin', 'record.login'], 'as' => 'api.'], function () {
        // Ayarlar (Settings) - Kimlik doğrulaması gerektiren rotalar
        Route::group(['prefix' => 'settings', 'controller' => SettingController::class], function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::get('/key/{key}', 'showByKey');
            Route::get('/group/{group}', 'getGroupSettings');
        });

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

            // Şifre değiştirme
            Route::patch('/password', [\App\Http\Controllers\Api\UserController::class, 'updatePassword'])->name('user.updatePassword');
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
        Route::prefix('story-categories')->group(function () {
            Route::get('/', [StoryCategoryController::class, 'index'])->name('story-categories.index');
            Route::get('/{slug}', [StoryCategoryController::class, 'showBySlug'])->name('story-categories.showBySlug');
            Route::get('/{slug}/stories', [StoryCategoryController::class, 'getStoriesByCategory'])->name('story-categories.stories');
        });

        // Stories
        Route::prefix('stories')->group(function () {
            Route::get('/', [StoryController::class, 'index'])->name('stories.index');
            Route::get('/{id}', [StoryController::class, 'show'])->name('stories.show')->where('id', '[0-9]+');
            Route::get('/slug/{slug}', [StoryController::class, 'showBySlug'])->name('stories.showBySlug');
        });

        // Bildirim Rotaları
        Route::prefix('notifications')->group(function () {
            Route::post('/custom', [NotificationController::class, 'sendCustomNotification']);
            Route::post('/broadcast', [NotificationController::class, 'sendBroadcastNotification']);
            Route::get('/logs', [NotificationController::class, 'getNotificationLogs']);
            Route::get('/check-all', [NotificationController::class, 'checkAllNotifications']);
            Route::get('/check-login-streak', [NotificationController::class, 'checkLoginStreakNotification']);
            Route::get('/check-course-reminder', [NotificationController::class, 'checkCourseReminderNotification']);
            
            // Bildirim Ayarları
            Route::get('/settings', [NotificationSettingController::class, 'getNotificationSettings']);
            Route::put('/settings', [NotificationSettingController::class, 'updateNotificationSettings']);
            Route::get('/settings/defaults', [NotificationSettingController::class, 'getDefaultSettings']);
        });

        // Video İçerik Rotaları
        Route::group(['prefix' => 'video-contents', 'controller' => VideoContentController::class], function () {
            Route::get('/{id}', 'show');
        });
    });

    // AI Sohbet (AiChat) Route'ları
    Route::prefix('ai-chat')->name('ai-chat.')->middleware(['JWT', 'verified'])->group(function () {
        Route::get('/', [App\Http\Controllers\Api\AiChatController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Api\AiChatController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Api\AiChatController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Api\AiChatController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Api\AiChatController::class, 'destroy'])->name('destroy');
        
        
    });

    // AI Chat Message Routes
    Route::prefix('ai-chat-messages')->name('ai-chat-messages.')->middleware(['auth:api'])->group(function () {
        Route::get('/', [App\Http\Controllers\Api\AiChatMessageController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Api\AiChatMessageController::class, 'show'])->name('show');
        Route::post('/send', [App\Http\Controllers\Api\AiChatMessageController::class, 'send'])->name('send');
        Route::get('/chat/{chatId}', [App\Http\Controllers\Api\AiChatMessageController::class, 'getByChatId'])->name('by-chat');
        Route::delete('/{id}', [App\Http\Controllers\Api\AiChatMessageController::class, 'destroy'])->name('destroy');
    });

    // Bildirim kontrolleri
    Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
        Route::get('/check-all', [NotificationController::class, 'checkAllNotifications']);
        Route::get('/check-login-streak', [NotificationController::class, 'checkLoginStreakNotification']);
        Route::get('/check-course-reminder', [NotificationController::class, 'checkCourseReminderNotification']);
    });
});

