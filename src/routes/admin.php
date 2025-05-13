<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseChapterController;
use App\Http\Controllers\Admin\CourseChapterLessonController;
use App\Http\Controllers\Admin\CourseChapterLessonContentController;
use App\Http\Controllers\Admin\MultipleChoiceQuestionController;
use App\Http\Controllers\Admin\TrueFalseQuestionController;
use App\Http\Controllers\Admin\ShortAnswerQuestionController;
use App\Http\Controllers\Admin\MatchingQuestionController;
use App\Http\Controllers\Admin\FillInTheBlankController;
use App\Http\Controllers\Admin\MissionsController;
use App\Http\Controllers\Admin\StoryCategoryController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\OneSignalController;
use App\Http\Controllers\Admin\VideoContentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ChapterPrerequisiteController;
use App\Http\Controllers\Admin\LessonPrerequisiteController;

// Admin rotaları - 'admin' prefix'i ekleniyor, 'api' prefix'i RouteServiceProvider'da ekleniyor
Route::prefix('admin')->name('admin.')->middleware(['JWT', 'verified', 'role:admin|super-admin'])->group(function () {

    // Ayarlar (Settings) rotaları
    Route::group(['prefix' => 'settings', 'controller' => SettingController::class], function () {
        Route::get('/', 'index')->name('settings.index');
        Route::post('/', 'store')->name('settings.store');
        Route::get('{id}', 'show')->name('settings.show');
        Route::put('{id}', 'update')->name('settings.update');
        Route::delete('{id}', 'destroy')->name('settings.destroy');
        
        // Özel ayar rotaları
        Route::put('site', 'updateSiteSettings')->name('settings.update-site');
        Route::put('mobile', 'updateMobileSettings')->name('settings.update-mobile');
        Route::post('images', 'updateImageSettings')->name('settings.update-images');
        Route::post('clear-cache', 'clearCache')->name('settings.clear-cache');
        
        // Özel (private) ayarlar için rotalar
        Route::get('get/private', 'privateSettings')->name('settings.private');
        Route::patch('{id}/toggle-private', 'togglePrivate')->name('settings.toggle-private');
    });

    Route::group(['prefix' => 'courses', 'controller' => CourseController::class], function () {
        Route::get('/', 'index')->name('courses.index');
        Route::post('/', 'store')->name('courses.store');
        Route::get('{id}', 'show')->name('courses.show');
        Route::put('{id}', 'update')->name('courses.update');
        Route::delete('{id}', 'destroy')->name('courses.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('courses.toggle-status');
        Route::patch('{id}/featured', 'toggleFeatured')->name('courses.toggle-featured');
        Route::patch('{id}/order', 'updateOrder')->name('courses.update-order');
        Route::get('category/{category}', 'byCategory')->name('courses.by-category');
    });

    Route::group(['prefix' => 'course-chapters', 'controller' => CourseChapterController::class], function () {
        Route::get('/', 'index')->name('course-chapters.index');
        Route::post('/', 'store')->name('course-chapters.store');
        Route::get('{id}', 'show')->name('course-chapters.show');
        Route::put('{id}', 'update')->name('course-chapters.update');
        Route::delete('{id}', 'destroy')->name('course-chapters.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('course-chapters.toggle-status');
        Route::patch('{id}/order', 'updateOrder')->name('course-chapters.update-order');
        Route::get('course/{courseId}', 'byCourse')->name('course-chapters.by-course');
    });

    Route::group(['prefix' => 'course-chapter-lessons', 'controller' => CourseChapterLessonController::class], function () {
        Route::get('/', 'index')->name('course-chapter-lessons.index');
        Route::post('/', 'store')->name('course-chapter-lessons.store');
        Route::get('{id}', 'show')->name('course-chapter-lessons.show');
        Route::put('{id}', 'update')->name('course-chapter-lessons.update');
        Route::delete('{id}', 'destroy')->name('course-chapter-lessons.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('course-chapter-lessons.toggle-status');
        Route::patch('{id}/order', 'updateOrder')->name('course-chapter-lessons.update-order');
        Route::get('chapter/{chapterId}', 'byChapter')->name('course-chapter-lessons.by-chapter');
    });

    // Ders İçerikleri (CourseChapterLessonContent) Route'ları
    Route::group(['prefix' => 'lesson-contents', 'controller' => CourseChapterLessonContentController::class], function () {
        Route::get('/', 'index')->name('lesson-contents.index');
        Route::post('/', 'store')->name('lesson-contents.store');
        Route::get('{id}', 'show')->name('lesson-contents.show');
        Route::put('{id}', 'update')->name('lesson-contents.update');
        Route::delete('{id}', 'destroy')->name('lesson-contents.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('lesson-contents.toggle-status');
        Route::patch('{id}/order', 'updateOrder')->name('lesson-contents.update-order');
        Route::post('bulk-update-order', 'bulkUpdateOrder')->name('lesson-contents.bulk-update-order');
        Route::get('lesson/{lessonId}', 'byLesson')->name('lesson-contents.by-lesson');

        // İçerik türlerine özel route'lar
        Route::post('text', 'createTextContent')->name('lesson-contents.create-text');
        Route::post('video', 'createVideoContent')->name('lesson-contents.create-video');
        Route::post('simple-video', 'createSimpleVideoContent')->name('lesson-contents.create-simple-video');
        Route::post('attach-video/{videoContentId}', 'attachVideoContent')->name('lesson-contents.attach-video');
        Route::post('fill-in-the-blank', 'createFillInTheBlankContent')->name('lesson-contents.create-fill-in-the-blank');
        Route::post('multiple-choice', 'createMultipleChoiceContent')->name('lesson-contents.create-multiple-choice');
        Route::post('true-false', 'createTrueFalseContent')->name('lesson-contents.create-true-false');
    });

    // Çoktan Seçmeli Sorular (MultipleChoiceQuestion) Route'ları
    Route::group(['prefix' => 'multiple-choice-questions', 'controller' => MultipleChoiceQuestionController::class], function () {
        Route::get('/', 'index')->name('multiple-choice-questions.index');
        Route::post('/', 'store')->name('multiple-choice-questions.store');
        Route::get('{id}', 'show')->name('multiple-choice-questions.show');
        Route::put('{id}', 'update')->name('multiple-choice-questions.update');
        Route::delete('{id}', 'destroy')->name('multiple-choice-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('multiple-choice-questions.toggle-status');
        // Çoktan seçmeli soruyu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('multiple-choice-questions.add-to-lesson');
    });

    // Doğru/Yanlış Sorular (TrueFalseQuestion) Route'ları
    Route::group(['prefix' => 'true-false-questions', 'controller' => TrueFalseQuestionController::class], function () {
        Route::get('/', 'index')->name('true-false-questions.index');
        Route::post('/', 'store')->name('true-false-questions.store');
        Route::get('{id}', 'show')->name('true-false-questions.show');
        Route::put('{id}', 'update')->name('true-false-questions.update');
        Route::delete('{id}', 'destroy')->name('true-false-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('true-false-questions.toggle-status');
        // Doğru/Yanlış soruyu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('true-false-questions.add-to-lesson');
    });

    // Kısa Cevaplı Sorular (ShortAnswerQuestion) Route'ları
    Route::group(['prefix' => 'short-answer-questions', 'controller' => ShortAnswerQuestionController::class], function () {
        Route::get('/', 'index')->name('short-answer-questions.index');
        Route::post('/', 'store')->name('short-answer-questions.store');
        Route::get('{id}', 'show')->name('short-answer-questions.show');
        Route::put('{id}', 'update')->name('short-answer-questions.update');
        Route::delete('{id}', 'destroy')->name('short-answer-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('short-answer-questions.toggle-status');
        // Kısa cevaplı soruyu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('short-answer-questions.add-to-lesson');
    });

    // Eşleştirme Soruları (MatchingQuestion) Route'ları
    Route::group(['prefix' => 'matching-questions', 'controller' => MatchingQuestionController::class], function () {
        Route::get('/', 'index')->name('matching-questions.index');
        Route::post('/', 'store')->name('matching-questions.store');
        Route::get('{id}', 'show')->name('matching-questions.show');
        Route::put('{id}', 'update')->name('matching-questions.update');
        Route::delete('{id}', 'destroy')->name('matching-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('matching-questions.toggle-status');
        // Eşleştirme sorusunu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('matching-questions.add-to-lesson');

        // Eşleştirme çiftleri için route'lar
        Route::post('{questionId}/pairs', 'addPair')->name('matching-questions.add-pair');
        Route::put('pairs/{pairId}', 'updatePair')->name('matching-questions.update-pair');
        Route::delete('pairs/{pairId}', 'deletePair')->name('matching-questions.delete-pair');
    });

    // Boşluk Doldurma Soruları (FillInTheBlank) Route'ları
    Route::group(['prefix' => 'fill-in-the-blank-questions', 'controller' => FillInTheBlankController::class], function () {
        Route::get('/', 'index')->name('fill-in-the-blank-questions.index');
        Route::post('/', 'store')->name('fill-in-the-blank-questions.store');
        Route::get('{id}', 'show')->name('fill-in-the-blank-questions.show');
        Route::put('{id}', 'update')->name('fill-in-the-blank-questions.update');
        Route::delete('{id}', 'destroy')->name('fill-in-the-blank-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('fill-in-the-blank-questions.toggle-status');
        // Boşluk doldurma sorusunu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('fill-in-the-blank-questions.add-to-lesson');
    });

    // Görevler (Missions) Route'ları
    Route::prefix('missions')->name('missions.')->group(function () {
        // Görevler listesi
        Route::get('/', [MissionsController::class, 'index'])->name('index');
        // Yeni görev oluşturma
        Route::post('/', [MissionsController::class, 'store'])->name('store');
        // Görev detayını getir
        Route::get('{id}', [MissionsController::class, 'show'])
            ->name('show')
            ->where('id', '[0-9]+');
        // Görev güncelleme
        Route::put('{id}', [MissionsController::class, 'update'])
            ->name('update')
            ->where('id', '[0-9]+');
        // Görev silme
        Route::delete('{id}', [MissionsController::class, 'destroy'])
            ->name('destroy')
            ->where('id', '[0-9]+');
        // Görev durumu aktif/pasif yapma
        Route::patch('{id}/toggle-status', [MissionsController::class, 'toggleStatus'])
            ->name('toggleStatus')
            ->where('id', '[0-9]+');
    });

    // Hikaye Kategorileri (Story Categories) Route'ları
    Route::resource('story-categories', StoryCategoryController::class);
    
    // Hikayeler (Stories) Route'ları
    Route::resource('stories', StoryController::class);

    // Rozet (Badge) Route'ları
    Route::prefix('badges')->name('badges.')->group(function () {
        Route::get('/', [BadgeController::class, 'index'])->name('index');
        Route::post('/', [BadgeController::class, 'store'])->name('store');
        Route::get('/{id}', [BadgeController::class, 'show'])->name('show');
        Route::put('/{id}', [BadgeController::class, 'update'])->name('update');
        Route::delete('/{id}', [BadgeController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [BadgeController::class, 'toggleStatus'])->name('toggle-status');
    });

    // İletişim Formları Route'ları
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/{id}', [ContactController::class, 'show'])->name('show');
        Route::put('/{id}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // OneSignal Bildirimleri Rota Grubu
    Route::group(['prefix' => 'onesignal', 'controller' => OneSignalController::class], function () {
        // Bildirim Listeleme ve Detay
        Route::get('/', 'index')->name('onesignal.index');
        Route::get('statistics', 'getStatistics')->name('onesignal.statistics');

        Route::get('{id}', 'show')->name('onesignal.show');
        
        // Bildirim Gönderme
        Route::post('send-to-user', 'sendToUser')->name('onesignal.send-to-user');
        Route::post('send-to-segment', 'sendToSegment')->name('onesignal.send-to-segment');
        Route::post('send-to-all', 'sendToAll')->name('onesignal.send-to-all');
        
        // Bildirim İptal
        Route::delete('cancel/{notificationId}', 'cancelNotification')->name('onesignal.cancel');
        
        // Bildirim Şablonları
        Route::post('templates', 'createTemplate')->name('onesignal.templates.create');
        Route::put('templates/{id}', 'updateTemplate')->name('onesignal.templates.update');
        Route::delete('templates/{id}', 'deleteTemplate')->name('onesignal.templates.delete');
        
        // İstatistikler
    });

    // Video İçerikleri (VideoContent) Route'ları
    Route::group(['prefix' => 'video-contents', 'controller' => VideoContentController::class], function () {
        Route::get('/', 'index')->name('video-contents.index');
        Route::post('/', 'store')->name('video-contents.store');
        Route::get('{id}', 'show')->name('video-contents.show');
        Route::put('{id}', 'update')->name('video-contents.update');
        Route::delete('{id}', 'destroy')->name('video-contents.destroy');
        Route::post('bulk-update', 'bulkUpdate')->name('video-contents.bulk-update');
    });

    // AI Sohbet (AiChat) ve Mesajları (AiChatMessage) Route'ları
    Route::prefix('ai-chat')->name('ai-chat.')->group(function () {
        // AiChat rotaları
        Route::get('/', [App\Http\Controllers\Admin\AiChatController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\AiChatController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\AiChatController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Admin\AiChatController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\AiChatController::class, 'destroy'])->name('destroy');
        
        // AiChatMessage rotaları
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AiChatMessageController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\AiChatMessageController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\AiChatMessageController::class, 'show'])->name('show');
            Route::put('/{id}', [App\Http\Controllers\Admin\AiChatMessageController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\AiChatMessageController::class, 'destroy'])->name('destroy');
            Route::get('/chat/{chatId}', [App\Http\Controllers\Admin\AiChatMessageController::class, 'getByChatId'])->name('by-chat');
            Route::get('/user/{userId}/messages', [App\Http\Controllers\Admin\AiChatMessageController::class, 'getUserMessages'])->name('user-messages');
            Route::get('/chat/{chatId}/ai-messages', [App\Http\Controllers\Admin\AiChatMessageController::class, 'getAiMessages'])->name('ai-messages');
        });
    });

    // === Chapter Prerequisites (Bölüm Ön Koşulları) ===
    Route::prefix('chapters/{chapterId}/prerequisites')->group(function () {
        Route::get('/', [ChapterPrerequisiteController::class, 'index']);
        Route::post('/', [ChapterPrerequisiteController::class, 'store']);
        Route::put('/', [ChapterPrerequisiteController::class, 'update']);
        Route::delete('/{prerequisiteId}', [ChapterPrerequisiteController::class, 'destroy']);
        Route::delete('/', [ChapterPrerequisiteController::class, 'clear']);
        Route::get('/available', [ChapterPrerequisiteController::class, 'availablePrerequisites']);
    });
    
    // === Lesson Prerequisites (Ders Ön Koşulları) ===
    Route::prefix('lessons/{lessonId}/prerequisites')->group(function () {
        Route::get('/', [LessonPrerequisiteController::class, 'index']);
        Route::post('/', [LessonPrerequisiteController::class, 'store']);
        Route::put('/', [LessonPrerequisiteController::class, 'update']);
        Route::delete('/{prerequisiteId}', [LessonPrerequisiteController::class, 'destroy']);
        Route::delete('/', [LessonPrerequisiteController::class, 'clear']);
        Route::get('/available', [LessonPrerequisiteController::class, 'availablePrerequisites']);
    });
});
