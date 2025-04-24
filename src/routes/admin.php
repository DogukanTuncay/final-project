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

Route::group(['middleware' => ['JWT','role:admin|super-admin'], 'prefix' => 'admin', 'namespace' => 'App\Http\Controllers\Admin'], function () {

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
        Route::post('multiple-choice', 'createMultipleChoiceContent')->name('admin.lesson-contents.create-multiple-choice');
        Route::post('true-false', 'createTrueFalseContent')->name('admin.lesson-contents.create-true-false');
    });

    // Çoktan Seçmeli Sorular (MultipleChoiceQuestion) Route'ları
    Route::group(['prefix' => 'multiple-choice-questions', 'controller' => MultipleChoiceQuestionController::class], function () {
        Route::get('/', 'index')->name('admin.multiple-choice-questions.index');
        Route::post('/', 'store')->name('admin.multiple-choice-questions.store');
        Route::get('{id}', 'show')->name('admin.multiple-choice-questions.show');
        Route::put('{id}', 'update')->name('admin.multiple-choice-questions.update');
        Route::delete('{id}', 'destroy')->name('admin.multiple-choice-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.multiple-choice-questions.toggle-status');
        // Çoktan seçmeli soruyu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('admin.multiple-choice-questions.add-to-lesson');
    });

    // Doğru/Yanlış Sorular (TrueFalseQuestion) Route'ları
    Route::group(['prefix' => 'true-false-questions', 'controller' => TrueFalseQuestionController::class], function () {
        Route::get('/', 'index')->name('admin.true-false-questions.index');
        Route::post('/', 'store')->name('admin.true-false-questions.store');
        Route::get('{id}', 'show')->name('admin.true-false-questions.show');
        Route::put('{id}', 'update')->name('admin.true-false-questions.update');
        Route::delete('{id}', 'destroy')->name('admin.true-false-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.true-false-questions.toggle-status');
        // Doğru/Yanlış soruyu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('admin.true-false-questions.add-to-lesson');
    });

    // Kısa Cevaplı Sorular (ShortAnswerQuestion) Route'ları
    Route::group(['prefix' => 'short-answer-questions', 'controller' => ShortAnswerQuestionController::class], function () {
        Route::get('/', 'index')->name('admin.short-answer-questions.index');
        Route::post('/', 'store')->name('admin.short-answer-questions.store');
        Route::get('{id}', 'show')->name('admin.short-answer-questions.show');
        Route::put('{id}', 'update')->name('admin.short-answer-questions.update');
        Route::delete('{id}', 'destroy')->name('admin.short-answer-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.short-answer-questions.toggle-status');
        // Kısa cevaplı soruyu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('admin.short-answer-questions.add-to-lesson');
    });

    // Eşleştirme Soruları (MatchingQuestion) Route'ları
    Route::group(['prefix' => 'matching-questions', 'controller' => MatchingQuestionController::class], function () {
        Route::get('/', 'index')->name('admin.matching-questions.index');
        Route::post('/', 'store')->name('admin.matching-questions.store');
        Route::get('{id}', 'show')->name('admin.matching-questions.show');
        Route::put('{id}', 'update')->name('admin.matching-questions.update');
        Route::delete('{id}', 'destroy')->name('admin.matching-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.matching-questions.toggle-status');
        // Eşleştirme sorusunu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('admin.matching-questions.add-to-lesson');

        // Eşleştirme çiftleri için route'lar
        Route::post('{questionId}/pairs', 'addPair')->name('admin.matching-questions.add-pair');
        Route::put('pairs/{pairId}', 'updatePair')->name('admin.matching-questions.update-pair');
        Route::delete('pairs/{pairId}', 'deletePair')->name('admin.matching-questions.delete-pair');
    });

    // Boşluk Doldurma Soruları (FillInTheBlank) Route'ları
    Route::group(['prefix' => 'fill-in-the-blank-questions', 'controller' => FillInTheBlankController::class], function () {
        Route::get('/', 'index')->name('admin.fill-in-the-blank-questions.index');
        Route::post('/', 'store')->name('admin.fill-in-the-blank-questions.store');
        Route::get('{id}', 'show')->name('admin.fill-in-the-blank-questions.show');
        Route::put('{id}', 'update')->name('admin.fill-in-the-blank-questions.update');
        Route::delete('{id}', 'destroy')->name('admin.fill-in-the-blank-questions.destroy');
        Route::patch('{id}/status', 'toggleStatus')->name('admin.fill-in-the-blank-questions.toggle-status');
        // Boşluk doldurma sorusunu bir derse ekle
        Route::post('add-to-lesson/{id}', 'addToLesson')->name('admin.fill-in-the-blank-questions.add-to-lesson');
    });
    Route::prefix('missions')->name('missions.')->group(function () {

        // Görevler listesi
        Route::get('/', [MissionsController::class, 'index'])->name('index');

        // Yeni görev oluşturma
        Route::post('/', [MissionsController::class, 'store'])->name('store');

        // Görev detayını getir
        Route::get('{id}', [MissionsController::class, 'show'])
            ->name('show')
            ->where('id', '[0-9]+'); // ID'nin sadece sayılar olmasını sağla

        // Görev güncelleme
        Route::put('{id}', [MissionsController::class, 'update'])
            ->name('update')
            ->where('id', '[0-9]+'); // ID'nin sadece sayılar olmasını sağla

        // Görev silme
        Route::delete('{id}', [MissionsController::class, 'destroy'])
            ->name('destroy')
            ->where('id', '[0-9]+'); // ID'nin sadece sayılar olmasını sağla

        // Görev durumu aktif/pasif yapma
        Route::patch('{id}/toggle-status', [MissionsController::class, 'toggleStatus'])
            ->name('toggleStatus')
            ->where('id', '[0-9]+'); // ID'nin sadece sayılar olmasını sağla
    });


});
