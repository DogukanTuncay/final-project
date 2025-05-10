<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\CourseChapterLessonServiceInterface;
use App\Interfaces\Repositories\Api\CourseChapterLessonRepositoryInterface;
use App\Models\User;
use App\Models\CourseChapterLesson;
use App\Models\LessonCompletion;
use Illuminate\Database\Eloquent\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class CourseChapterLessonService implements CourseChapterLessonServiceInterface
{
    private CourseChapterLessonRepositoryInterface $repository;

    public function __construct(
        CourseChapterLessonRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Belirli bir bölüme ait dersleri getir
     * @param int $chapterId
     * @return Collection
     */
    public function findByChapter(int $chapterId)
    {
        return $this->repository->findByChapter($chapterId);
    }
    
    /**
     * Belirli bir dersi bölüm bilgisiyle getir
     * @param int $id
     * @return CourseChapterLesson
     */
    public function findActive(int $id)
    {
        return $this->repository->findActive($id);
    }
    
    /**
     * Bir dersi tamamlandı olarak işaretle ve dersin XP ödülünü ver
     * @param int $id Ders ID'si
     * @return LessonCompletion|null
     */
    public function markAsCompleted(int $id): ?LessonCompletion
    {
        $user = JWTAuth::user();
        if (!$user) return null;
        $userId = $user->id;

        // Dersin varlığını ve ilişkilerini kontrol etmeye gerek yok,
        // sadece dersi bulmak yeterli.
        $lesson = $this->repository->findActive($id); // findActive veya find ile dersi al
        if (!$lesson) {
             Log::warning("CourseChapterLessonService::markAsCompleted - Lesson not found for lesson ID: {$id}");
             return null;
        }
        
        // $course = $lesson->chapter->course; // Artık gerekli değil
        // $courseId = $course->id; // Artık gerekli değil

        return DB::transaction(function () use ($id, $userId, $user, $lesson) { // $course ve $courseId kaldırıldı
            // Daha önce tamamlanmış mı kontrol et (lock ile)
            $existing = LessonCompletion::where('lesson_id', $id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();
                
            if ($existing) {
                return null; // Zaten tamamlanmış
            }
            
            // Yeni tamamlama kaydı oluştur
            $completion = LessonCompletion::create([
                'lesson_id' => $id,
                'user_id' => $userId,
                'completed_at' => now()
            ]);

            // Dersin XP ödülünü ver (eğer varsa)
            if ($lesson->xp_reward > 0) {
                // $this->checkAndAwardCourseCompletionXp($user, $courseId); // Eski kurs tamamlama kontrolü kaldırıldı
                Log::info("Awarding XP for lesson completion. User ID: {$userId}, Lesson ID: {$id}, XP: {$lesson->xp_reward}");
                $user->addExperiencePoints($lesson->xp_reward);
            }

            // LessonCompleted olayını tetikle
            Log::info("CourseChapterLessonService: Firing LessonCompleted event. User ID: {$userId}, Lesson ID: {$id}");
            
            // Senkron işletim için event dispatch edilir
            // Dikkat: Tüm event listeners işlenene kadar bu kısımdan çıkılmaz
            Event::dispatch(
                new \App\Events\LessonCompleted($user, $lesson)
            );

            return $completion;
        });
    }

    /**
     * Kullanıcının bir kursu tamamlayıp tamamlamadığını kontrol eder ve XP verir.
     * @param User $user
     * @param int $courseId
     * @return void
     */
    // Bu metot artık `markAsCompleted` içinde çağrılmıyor.
    // Eğer kurs tamamlandığında ayrıca bir ödül verilmesi gerekiyorsa,
    // bu mantık farklı bir şekilde (örn. Observer ile) ele alınmalıdır.
    private function checkAndAwardCourseCompletionXp(User $user, int $courseId): void
    {
        // ... (Metot içeriği şimdilik burada kalabilir veya tamamen silinebilir)
        $userId = $user->id;
        $totalLessonsInCourseIds = CourseChapterLesson::query()
            ->whereHas('chapter', fn($q) => $q->where('course_id', $courseId))
            ->where('is_active', true)
            ->pluck('id');

        $totalLessonsCount = $totalLessonsInCourseIds->count();

        if ($totalLessonsCount === 0) {
            return;
        }

        $completedLessonsCount = LessonCompletion::where('user_id', $userId)
            ->whereIn('lesson_id', $totalLessonsInCourseIds)
            ->count();

        if ($completedLessonsCount === $totalLessonsCount) {
            $course = \App\Models\Course::find($courseId);
            if ($course && $course->xp_reward > 0) {
                Log::info("Awarding XP for course completion. User ID: {$userId}, Course ID: {$courseId}, XP: {$course->xp_reward}");
                $user->addExperiencePoints($course->xp_reward);
            }
        }
    }

    /**
     * Dersin ön koşullarını getir
     * @param int $id
     * @return Collection
     */
    public function getPrerequisites(int $id)
    {
        $lesson = $this->findActive($id);
        if (!$lesson) {
            return collect();
        }

        return $lesson->prerequisites;
    }

    /**
     * Dersin kilit durumunu kontrol et
     * @param int $id
     * @return array
     */
    public function checkLockStatus(int $id): array
    {
        $lesson = $this->findActive($id);
        if (!$lesson) {
            return [
                'is_unlocked' => false,
                'completed_prerequisites' => 0,
                'total_prerequisites' => 0
            ];
        }

        $user = JWTAuth::user();
        if (!$user) {
            return [
                'is_unlocked' => false,
                'completed_prerequisites' => 0,
                'total_prerequisites' => 0
            ];
        }

        $userId = $user->id;
        $prerequisites = $lesson->prerequisites;
        $totalPrerequisites = $prerequisites->count();
        
        if ($totalPrerequisites === 0) {
            return [
                'is_unlocked' => true,
                'completed_prerequisites' => 0,
                'total_prerequisites' => 0
            ];
        }

        $completedPrerequisites = LessonCompletion::where('user_id', $userId)
            ->whereIn('lesson_id', $prerequisites->pluck('id'))
            ->count();
            
        return [
            'is_unlocked' => $completedPrerequisites === $totalPrerequisites,
            'completed_prerequisites' => $completedPrerequisites,
            'total_prerequisites' => $totalPrerequisites
        ];
    }
}