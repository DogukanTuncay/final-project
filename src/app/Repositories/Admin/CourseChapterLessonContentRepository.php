<?php

namespace App\Repositories\Admin;

use App\Models\CourseChapterLessonContent;
use App\Interfaces\Repositories\Admin\CourseChapterLessonContentRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class CourseChapterLessonContentRepository extends BaseRepository implements CourseChapterLessonContentRepositoryInterface
{
    public function __construct(CourseChapterLessonContent $model)
    {
        parent::__construct($model);
    }
    
    /**
     * Bir ders için içerikleri getir
     * 
     * @param int $lessonId
     * @return Collection
     */
    public function getByLessonId(int $lessonId): Collection
    {
        // Önce tüm içerikleri getir
        $contents = $this->model
            ->with('contentable')
            ->where('course_chapter_lesson_id', $lessonId)
            ->orderBy('order')
            ->get();
            
        // Soft delete edilmiş içerikleri filtrele
        return $contents->filter(function ($content) {
            return $content->contentable !== null;
        });
    }
    
    /**
     * Belirli tipteki içerikleri getir
     * 
     * @param int $lessonId
     * @param string $contentType
     * @return Collection
     */
    public function getByContentType(int $lessonId, string $contentType): Collection
    {
        // Önce tüm içerikleri getir
        $contents = $this->model
            ->with('contentable')
            ->where('course_chapter_lesson_id', $lessonId)
            ->where('contentable_type', $contentType)
            ->orderBy('order')
            ->get();
            
        // Soft delete edilmiş içerikleri filtrele
        return $contents->filter(function ($content) {
            return $content->contentable !== null;
        });
    }
    
    /**
     * Polimorfik içerik oluştur
     * 
     * @param int $lessonId
     * @param object $contentable
     * @param array $data
     * @return CourseChapterLessonContent
     */
    public function createWithContent(int $lessonId, object $contentable, array $data = []): CourseChapterLessonContent
    {
        return DB::transaction(function() use ($lessonId, $contentable, $data) {
            // İçeriği kaydet
            $contentable->save();
            
            // CourseChapterLessonContent oluştur
            $contentData = array_merge($data, [
                'course_chapter_lesson_id' => $lessonId,
                'contentable_id' => $contentable->id,
                'contentable_type' => get_class($contentable)
            ]);
            
            return $this->model->create($contentData);
        });
    }
    
    /**
     * İçeriğin sıralamasını güncelle
     * 
     * @param int $contentId
     * @param int $newOrder
     * @return bool
     */
    public function updateOrder(int $contentId, int $newOrder): bool
    {
        return (bool) $this->model->where('id', $contentId)->update(['order' => $newOrder]);
    }
    
    /**
     * İçeriklerin sıralamasını toplu güncelle
     * 
     * @param array $orderData [content_id => order, ...]
     * @return bool
     */
    public function bulkUpdateOrder(array $orderData): bool
    {
        return DB::transaction(function() use ($orderData) {
            foreach ($orderData as $contentId => $order) {
                $this->model->where('id', $contentId)->update(['order' => $order]);
            }
            return true;
        });
    }
}