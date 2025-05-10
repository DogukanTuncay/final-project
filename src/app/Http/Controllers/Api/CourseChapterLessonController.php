<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Api\CourseChapterLessonServiceInterface;
use App\Http\Resources\Api\CourseChapterLessonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseChapterLesson;
use App\Traits\HandlesEvents;
use Illuminate\Support\Facades\Log;
use App\Services\Api\EventService;
use App\Models\Badge;

class CourseChapterLessonController extends BaseController
{
    use HandlesEvents;
    

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
            return $this->successResponse(
                null, 
                'responses.lesson_completion.completed'
            );
        }

        // Ders tamamlamaya özel event ekle
        $eventData = [
            'lesson_id' => $id,
            'lesson_name' => is_array($lesson->name) 
                ? ($lesson->name[app()->getLocale()] ?? reset($lesson->name)) 
                : $lesson->name,
            'completion_id' => $completion->id,
            'xp_reward' => $lesson->xp_reward ?? 0,
            'event_reason' => 'lesson_completed',
            'event_source' => 'lesson',
            'timestamp' => now()->toDateTimeString()
        ];
        
        // Özel olayı oluştur
        $this->createEvent(
            'lesson_completed', 
            $eventData, 
            __('events.lesson_completed', [
                'lesson' => $eventData['lesson_name'],
                'xp' => $eventData['xp_reward']
            ]), 
            'lesson'
        );

        // EventService'den eventleri al
        $eventService = app(EventService::class);
        $events = $eventService->getEvents();
        $eventService->clearEvents();
        Log::info("Eventler: " . json_encode($events));
        // Data'yı hazırla
        $responseData = [
            'completed' => true,
            'completion_id' => $completion->id,
            'events' => $events
        ];

        return $this->successResponse(
            $responseData, 
            'responses.lesson_completion.completed'
        );
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

    private function checkAndAwardBadges($user, $lesson): void
    {
        // Ders tamamlama sayısına göre rozet kontrolü
        $completedLessonsCount = $user->completedLessons()->count();
        
        // Örnek rozet kontrolleri
        if ($completedLessonsCount >= 10) {
            $badge = Badge::where('type', 'lesson_master_10')->first();
            if ($badge) {
                $user->addBadge($badge);
            }
        }
        
        if ($completedLessonsCount >= 50) {
            $badge = Badge::where('type', 'lesson_master_50')->first();
            if ($badge) {
                $user->addBadge($badge);
            }
        }

        // Bölüm tamamlama rozeti kontrolü
        $chapter = $lesson->chapter;
        $completedLessonsInChapter = $chapter->lessons()
            ->whereHas('completions', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        if ($completedLessonsInChapter === $chapter->lessons()->count()) {
            $badge = Badge::where('type', 'chapter_master')->first();
            if ($badge) {
                $user->addBadge($badge);
            }
        }
    }
}