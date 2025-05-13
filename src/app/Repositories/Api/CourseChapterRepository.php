<?php

namespace App\Repositories\Api;

use App\Models\CourseChapter;
use App\Models\ChapterCompletion;
use App\Interfaces\Repositories\Api\CourseChapterRepositoryInterface;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class CourseChapterRepository extends BaseRepository implements CourseChapterRepositoryInterface
{
    public function __construct(CourseChapter $model)
    {
        parent::__construct($model);
    }
    
    /**
     * Belirli bir kursa ait bölümleri getir
     * @param int $courseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByCourse(int $courseId)
    {
        return $this->model->where('course_id', $courseId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Belirli bir bölümü kurs bilgisiyle getir
     * @param int $id
     * @return \App\Models\CourseChapter
     */
    public function findActiveWithCourse(int $id)
    {
        return $this->model->where('id', $id)
            ->where('is_active', true)
            ->with('course')
            ->firstOrFail();
    }
    
    /**
     * Bir bölümü tamamlandı olarak işaretle
     * @param int $id
     * @param int $userId
     * @return \App\Models\ChapterCompletion
     */
    public function markAsCompleted(int $id, int $userId)
    {
        // Bölümü bul
        $chapter = $this->find($id);
        
        // Tamamlama kaydı oluştur veya güncelle
        $completion = ChapterCompletion::updateOrCreate(
            ['user_id' => $userId, 'chapter_id' => $chapter->id],
            ['completed_at' => Carbon::now()]
        );
        
        return $completion;
    }
    
    /**
     * Bölümün ön koşullarını getir
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $id)
    {
        $chapter = $this->find($id);
        return $chapter->prerequisites;
    }
    
    /**
     * Belirli bir bölümün kilit durumunu kontrol et
     * @param int $id
     * @param int $userId
     * @return array
     */
    public function checkLockStatus(int $id, int $userId)
    {
        $chapter = $this->find($id);
        
        // Bölümün ön koşulları varsa
        if ($chapter->activePrerequisites()->exists()) {
            // Ön koşul bölüm ID'lerini al
            $prerequisiteIds = $chapter->activePrerequisites()->pluck('id');
            
            // Kullanıcının tamamladığı bölüm sayısını al
            $completedCount = ChapterCompletion::where('user_id', $userId)
                ->whereIn('chapter_id', $prerequisiteIds)
                ->count();
            
            // Tüm ön koşullar tamamlandı mı kontrol et
            $isLocked = $completedCount < $prerequisiteIds->count();
            
            // Eksik ön koşulları bul
            $missingPrerequisites = [];
            if ($isLocked) {
                // Tamamlanmamış ön koşul bölümleri
                $completedIds = ChapterCompletion::where('user_id', $userId)
                    ->whereIn('chapter_id', $prerequisiteIds)
                    ->pluck('chapter_id')
                    ->toArray();
                
                $missingIds = array_diff($prerequisiteIds->toArray(), $completedIds);
                $missingPrerequisites = CourseChapter::whereIn('id', $missingIds)
                    ->get()
                    ->map(function($chapter) {
                        return [
                            'id' => $chapter->id,
                            'name' => $chapter->name,
                            'slug' => $chapter->slug
                        ];
                    })
                    ->toArray();
            }
            
            return [
                'is_locked' => $isLocked,
                'missing_prerequisites' => $missingPrerequisites
            ];
        }
        
        // Ön koşul yoksa kilitli değil
        return [
            'is_locked' => false,
            'missing_prerequisites' => []
        ];
    }
}