<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CourseChapterLessonRepositoryInterface;
use App\Models\CourseChapterLesson;
use App\Repositories\BaseRepository;

class CourseChapterLessonRepository extends BaseRepository implements CourseChapterLessonRepositoryInterface
{
    public function __construct(CourseChapterLesson $model)
    {
        parent::__construct($model);
    }

    /**
     * Bölüme göre dersleri bulur
     *
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByChapter(int $chapterId)
    {
        return $this->model->where('course_chapter_id', $chapterId)->orderBy('order')->get();
    }

    /**
     * Ders durumunu değiştirir
     *
     * @param int $id
     * @return CourseChapterLesson|null
     */
    public function toggleStatus(int $id): ?CourseChapterLesson
    {
        $courseChapterLesson = $this->find($id);
        
        if (!$courseChapterLesson) {
            return null;
        }
        
        $courseChapterLesson->is_active = !$courseChapterLesson->is_active;
        $courseChapterLesson->save();
        
        return $courseChapterLesson;
    }

    /**
     * Ders sırasını günceller
     *
     * @param int $id
     * @param int $order
     * @return CourseChapterLesson|null
     */
    public function updateOrder(int $id, int $order): ?CourseChapterLesson
    {
        $courseChapterLesson = $this->find($id);
        
        if (!$courseChapterLesson) {
            return null;
        }
        
        $courseChapterLesson->order = $order;
        $courseChapterLesson->save();
        
        return $courseChapterLesson;
    }
}