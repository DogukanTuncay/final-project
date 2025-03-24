<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Api\CourseChapterLessonServiceInterface;
use App\Http\Resources\Api\CourseChapterLessonResource;
use Illuminate\Support\Facades\Auth;
class CourseChapterLessonController extends BaseController
{
    protected $service;

    public function __construct(CourseChapterLessonServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Bölüme ait dersleri listele
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byChapter(int $chapterId)
    {
        $lessons = $this->service->findByChapter($chapterId);
         // Hata ayıklama
       foreach($lessons as $lesson){
        \Log::info('Ön koşullar: ' . $lesson->prerequisites()->get());
       }

       if(!$lessons){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }

        return $this->successResponse(CourseChapterLessonResource::collection($lessons), 'responses.course_chapter_lesson.list_by_chapter_success');
    }

    /**
     * Ders detayını göster
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $lesson = $this->service->findActive($id);
        if(!$lesson){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }
        
        // Dersin kilit durumunu service üzerinden kontrol et
        $lockStatus = $this->service->checkLockStatus($id);
        if (!$lockStatus['is_unlocked']) {
            // Eksik ön koşulları mesaj olarak ekle
            $missingPrerequisites = $lesson->missing_prerequisites;
            $errors = [];
            
            if (!empty($missingPrerequisites)) {
                $errors['missing_prerequisites'] = $missingPrerequisites;
                
                // Mesaj oluştur
                $prerequisiteNames = collect($missingPrerequisites)->pluck('name')->map(function($name) {
                    return is_array($name) ? $name[app()->getLocale()] ?? $name['en'] : $name;
                })->implode(', ');
                
                return $this->errorResponse(
                    'responses.course_chapter_lesson.locked',
                    403,
                    $errors,
                    ['prerequisites' => $prerequisiteNames]
                );
            }
        }
        
        return $this->successResponse(new CourseChapterLessonResource($lesson), 'responses.course_chapter_lesson.detail_success');
    }

    /**
     * Dersin ön koşullarını listele
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function prerequisites(int $id)
    {
        $lesson = $this->service->findActive($id);
        if(!$lesson){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }

        $prerequisites = $this->service->getPrerequisites($id);
        return $this->successResponse(
            CourseChapterLessonResource::collection($prerequisites),
            'responses.course_chapter_lesson.prerequisites_list_success'
        );
    }

    /**
     * Dersin kilit durumunu kontrol et
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLockStatus(int $id)
    {
        $lesson = $this->service->findActive($id);
        if(!$lesson){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }

        $status = $this->service->checkLockStatus($id);
        return $this->successResponse([
            'is_locked' => !$status['is_unlocked'],
            'completed_prerequisites' => $status['completed_prerequisites'],
            'total_prerequisites' => $status['total_prerequisites']
        ], 'responses.course_chapter_lesson.lock_status_success');
    }

    /**
     * Ders tamamlama durumunu güncelle
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsCompleted(int $id)
    {
        $lesson = $this->service->findActive($id);
        if(!$lesson){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }

        // Dersin kilit durumunu service üzerinden kontrol et
        $lockStatus = $this->service->checkLockStatus($id);
        if (!$lockStatus['is_unlocked']) {
            $missingPrerequisites = $lesson->missing_prerequisites;
            $errors = [];
            
            if (!empty($missingPrerequisites)) {
                $errors['missing_prerequisites'] = $missingPrerequisites;
                
                // Mesaj oluştur
                $prerequisiteNames = collect($missingPrerequisites)->pluck('name')->map(function($name) {
                    return is_array($name) ? $name[app()->getLocale()] ?? $name['en'] : $name;
                })->implode(', ');
                
                return $this->errorResponse(
                    'responses.course_chapter_lesson.locked',
                    403,
                    $errors,
                    ['prerequisites' => $prerequisiteNames]
                );
            }
        }

        $completion = $this->service->markAsCompleted($id);
        if(!$completion){
            return $this->errorResponse('responses.lesson_completion.already_completed', 400);
        }

        return $this->successResponse([
            'completed' => true,
            'completion_id' => $completion->id
        ], 'responses.lesson_completion.completed');
    }

    /**
     * Ders ön koşullarını ve kilit durumunu test et 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function testPrerequisites(int $id)
    {
        $lesson = $this->service->findActive($id);
        if(!$lesson){
            return $this->errorResponse('responses.course_chapter_lesson.not_found', 404);
        }
        
        // Ön koşulları yükle
        $lesson->load('prerequisites');
        
        // Tamamlanmış dersler
        $userId = Auth::id();
        $completedLessonIds = [];
        
        if (Auth::check()) {
            $completedLessonIds = \App\Models\LessonCompletion::where('user_id', $userId)
                ->pluck('lesson_id')
                ->toArray();
        }
        
        // Kilit durumunu service üzerinden kontrol et
        $lockStatus = $this->service->checkLockStatus($id);
        
        // Debug bilgileri
        $prerequisites = $lesson->prerequisites->map(function($prerequisite) use ($completedLessonIds) {
            $name = is_array($prerequisite->name) 
                ? ($prerequisite->name[app()->getLocale()] ?? reset($prerequisite->name)) 
                : $prerequisite->name;
                
            return [
                'id' => $prerequisite->id,
                'name' => $name,
                'is_completed' => in_array($prerequisite->id, $completedLessonIds)
            ];
        })->toArray();
        
        $debug = [
            'lesson_id' => $lesson->id,
            'lesson_name' => is_array($lesson->name) 
                ? ($lesson->name[app()->getLocale()] ?? reset($lesson->name)) 
                : $lesson->name,
            'is_locked' => !$lockStatus['is_unlocked'],
            'prerequisites_count' => count($prerequisites),
            'prerequisites' => $prerequisites,
            'missing_prerequisites' => $lesson->missing_prerequisites,
            'auth_check' => Auth::check(),
            'user_id' => Auth::id(),
            'completed_lesson_ids' => $completedLessonIds,
            'lock_status_from_service' => $lockStatus
        ];
        
        return $this->successResponse($debug, 'Ön koşul testi başarılı');
    }
}