<?php

namespace App\Repositories\Admin;

use App\Models\CourseChapter;
use App\Interfaces\Repositories\Admin\CourseChapterRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CourseChapterRepository extends BaseRepository implements CourseChapterRepositoryInterface
{
    public function __construct(CourseChapter $model)
    {
        parent::__construct($model);
    }

    public function findByCourse(int $courseId)
    {
        return $this->model->where('course_id', $courseId)
            ->orderBy('order')
            ->get();
    }
    public function updateOrder(int $id, int $order)
    {
        $courseChapter = $this->find($id);
        $courseChapter->order = $order;
        $courseChapter->save();
        return $courseChapter;
    }

    public function toggleStatus(int $id)
    {
        $courseChapter = $this->find($id);
        $courseChapter->is_active = !$courseChapter->is_active;
        $courseChapter->save();
        return $courseChapter;
    }

    /**
     * Belirli bir bölüm için ön koşul bölümleri ekler
     * 
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function addPrerequisites(int $chapterId, array $prerequisiteIds): bool
    {
        try {
            $chapter = $this->find($chapterId);
            // Kendisini ön koşul olarak eklemesini engelle
            $prerequisiteIds = array_filter($prerequisiteIds, function($id) use ($chapterId) {
                return $id != $chapterId;
            });

            if (empty($prerequisiteIds)) {
                return true;
            }

            // Önkoşulları ekle
            $existingPrerequisites = $chapter->prerequisites()->pluck('course_chapters.id')->toArray();

            $newPrerequisites = array_diff($prerequisiteIds, $existingPrerequisites);

            
            if (!empty($newPrerequisites)) {
                $chapter->prerequisites()->attach($newPrerequisites);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Belirli bir bölüm için ön koşul bölümlerini kaldırır
     * 
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function removePrerequisites(int $chapterId, array $prerequisiteIds): bool
    {
        try {
            $chapter = $this->find($chapterId);
            
            if (empty($prerequisiteIds)) {
                return true;
            }
            
            // Önkoşulları kaldır
            $chapter->prerequisites()->detach($prerequisiteIds);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Belirli bir bölüm için tüm ön koşulları kaldırır
     * 
     * @param int $chapterId
     * @return bool
     */
    public function clearPrerequisites(int $chapterId): bool
    {
        try {
            $chapter = $this->find($chapterId);
            $chapter->prerequisites()->detach();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Belirli bir bölüm için ön koşulları günceller (mevcut olanları kaldırıp yenilerini ekler)
     * 
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function updatePrerequisites(int $chapterId, array $prerequisiteIds): bool
    {
        try {
            // Kendisini ön koşul olarak eklemesini engelle
            $prerequisiteIds = array_filter($prerequisiteIds, function($id) use ($chapterId) {
                return $id != $chapterId;
            });
            
            $chapter = $this->find($chapterId);
            $chapter->prerequisites()->sync($prerequisiteIds);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Belirli bir bölüm için ön koşulları getirir
     * 
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $chapterId)
    {
        $chapter = $this->find($chapterId);
        return $chapter->prerequisites;
    }
}