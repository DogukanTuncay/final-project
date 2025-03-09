<?php

namespace App\Interfaces\Repositories\Api;

use App\Models\CourseChapterLesson;
use Illuminate\Database\Eloquent\Collection;

interface CourseChapterLessonRepositoryInterface
{

    /**
     * Belirli bir bölüme ait aktif dersleri getir
     * @param int $chapterId
     * @return Collection
     */
    public function findByChapter(int $chapterId);
    
    /**
     * Belirli bir dersi bölüm bilgisiyle beraber getir
     * @param int $id
     * @return CourseChapterLesson
     */
    public function findActive(int $id);
}