<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\CourseChapterServiceInterface;
use App\Interfaces\Repositories\Api\CourseChapterRepositoryInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\ChapterCompletion;
use App\Models\LessonCompletion;

class CourseChapterService implements CourseChapterServiceInterface
{
    private CourseChapterRepositoryInterface $courseChapterRepository;

    public function __construct(CourseChapterRepositoryInterface $courseChapterRepository)
    {
        $this->courseChapterRepository = $courseChapterRepository;
    }
    
    /**
     * Belirli bir kursa ait bölümleri getir
     * @param int $courseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByCourse(int $courseId)
    {
        return $this->courseChapterRepository->findByCourse($courseId);
    }

    /**
     * Belirli bir bölümü kurs bilgisiyle getir
     * @param int $id
     * @return \App\Models\CourseChapter
     */
    public function findActiveWithCourse(int $id)
    {
        return $this->courseChapterRepository->findActiveWithCourse($id);
    }
    
    /**
     * Bir bölümü tamamlandı olarak işaretle
     * @param int $id
     * @return mixed
     */
    public function markAsCompleted(int $id)
    {
        $user = JWTAuth::user();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'responses.auth.unauthorized',
                'code' => 401
            ];
        }

        $chapter = $this->courseChapterRepository->find($id);
        if (!$chapter) {
            return [
                'success' => false,
                'message' => 'responses.course_chapter.not_found',
                'code' => 404
            ];
        }

        // Ön koşulların kontrolü
        if ($chapter->prerequisites()->exists()) {
            $prerequisites = $chapter->prerequisites()->get();
            $completedPrerequisites = ChapterCompletion::where('user_id', $user->id)
                ->whereIn('chapter_id', $prerequisites->pluck('id'))
                ->count();

            if ($completedPrerequisites < $prerequisites->count()) {
                $missingPrerequisites = $chapter->missing_prerequisites;
                return [
                    'success' => false,
                    'message' => 'responses.course_chapter.prerequisites_not_completed',
                    'code' => 403,
                    'data' => [
                        'missing_prerequisites' => $missingPrerequisites,
                        'completed_count' => $completedPrerequisites,
                        'total_count' => $prerequisites->count()
                    ]
                ];
            }
        }

        // Derslerin tamamlanma kontrolü
        $lessons = $chapter->lessons()->get();
        if ($lessons->isEmpty()) {
            return [
                'success' => false,
                'message' => 'responses.course_chapter.no_lessons',
                'code' => 403
            ];
        }

        $completedLessons = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->count();

        if ($completedLessons < $lessons->count()) {
            return [
                'success' => false,
                'message' => 'responses.course_chapter.lessons_not_completed',
                'code' => 403,
                'data' => [
                    'completed_count' => $completedLessons,
                    'total_count' => $lessons->count()
                ]
            ];
        }

        $completion = $this->courseChapterRepository->markAsCompleted($id, $user->id);
        if (!$completion) {
            return [
                'success' => false,
                'message' => 'responses.course_chapter.completion_failed',
                'code' => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'responses.course_chapter.completed_success',
            'code' => 200,
            'data' => $completion
        ];
    }
    
    /**
     * Bölümün ön koşullarını getir
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $id)
    {
        return $this->courseChapterRepository->getPrerequisites($id);
    }
    
    /**
     * Bölümün kilit durumunu kontrol et
     * @param int $id
     * @return array
     */
    public function checkLockStatus(int $id)
    {
        $user = JWTAuth::user();
        if (!$user) {
            return [
                'is_locked' => false,
                'missing_prerequisites' => []
            ];
        }

        return $this->courseChapterRepository->checkLockStatus($id, $user->id);
    }
}