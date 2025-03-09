<?php

namespace App\Repositories\Api;

use App\Models\CourseChapterLesson;
use App\Interfaces\Repositories\Api\CourseChapterLessonRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class CourseChapterLessonRepository extends BaseRepository implements CourseChapterLessonRepositoryInterface
{
    public function __construct(CourseChapterLesson $model)
    {
        parent::__construct($model);
    }
    
    /**
     * Belirli bir bölüme ait aktif dersleri getir
     * @param int $chapterId
     * @return Collection
     */
    public function findByChapter(int $chapterId)
    {
        return $this->model
            ->active()
            ->byChapter($chapterId)
            ->ordered()
            ->get();
    }
    
    /**
     * Belirli bir dersi bölüm bilgisiyle beraber getir
     * @param int $id
     * @return CourseChapterLesson
     */
    public function findActive(int $id)
    {
        return $this->model
            ->active()
            ->find($id);
    }
}