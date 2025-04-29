<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\CourseChapterLessonContentServiceInterface;
use App\Interfaces\Repositories\Admin\CourseChapterLessonContentRepositoryInterface;
use App\Services\BaseService;
use App\Models\TextContent;
use App\Models\VideoContent;
use App\Models\FillInTheBlank;
use App\Models\Quiz;
use App\Models\MultipleChoiceQuestion;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\Repositories\Admin\MultipleChoiceQuestionRepositoryInterface;

class CourseChapterLessonContentService extends BaseService implements CourseChapterLessonContentServiceInterface
{
    public function __construct(CourseChapterLessonContentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
    
    /**
     * Bir ders için tüm içerikleri getir
     * 
     * @param int $lessonId
     * @return Collection
     */
    public function getByLessonId(int $lessonId): Collection
    {
        $contents = $this->repository->getByLessonId($lessonId);
        
        // Soft delete edilmiş içerikleri filtrele
        return $contents->filter(function ($content) {
            return $content->contentable !== null;
        });
    }
    
    /**
     * Text içeriği oluştur
     * 
     * @param int $lessonId
     * @param array $contentData
     * @param array $lessonContentData
     * @return mixed
     */
    public function createTextContent(int $lessonId, array $contentData, array $lessonContentData = [])
    {
        $textContent = new TextContent($contentData);
        return $this->repository->createWithContent($lessonId, $textContent, $lessonContentData);
    }
    
    /**
     * Video içeriği oluştur
     * 
     * @param int $lessonId
     * @param array $contentData
     * @param array $lessonContentData
     * @return mixed
     */
    public function createVideoContent(int $lessonId, array $contentData, array $lessonContentData = [])
    {
        $videoContent = new VideoContent($contentData);
        return $this->repository->createWithContent($lessonId, $videoContent, $lessonContentData);
    }
    
    /**
     * Boşluk doldurma içeriği oluştur
     * 
     * @param int $lessonId
     * @param array $contentData
     * @param array $lessonContentData
     * @return mixed
     */
    public function createFillInTheBlankContent(int $lessonId, array $contentData, array $lessonContentData = [])
    {
        $fillInTheBlank = new FillInTheBlank($contentData);
        return $this->repository->createWithContent($lessonId, $fillInTheBlank, $lessonContentData);
    }
    
    /**
     * Çoktan seçmeli soru içeriği oluştur
     * 
     * @param int $lessonId
     * @param array $contentData
     * @param array $lessonContentData
     * @return mixed
     */
    public function createMultipleChoiceContent(int $lessonId, array $contentData, array $lessonContentData = [])
    {
        // MultipleChoiceQuestionRepository'yi enjekte et
        $mcqRepository = app(MultipleChoiceQuestionRepositoryInterface::class);
        
        // Repository aracılığıyla çoktan seçmeli soruyu oluştur
        $mcq = $mcqRepository->create($contentData);
        
        // İçerik oluştur ve ilişkilendir
        return $this->repository->createWithContent($lessonId, $mcq, $lessonContentData);
    }
    
    /**
     * İçerik sıralamasını güncelle
     * 
     * @param int $contentId
     * @param int $newOrder
     * @return bool
     */
    public function updateOrder(int $contentId, int $newOrder): bool
    {
        return $this->repository->updateOrder($contentId, $newOrder);
    }
    
    /**
     * İçeriklerin sıralamasını toplu güncelle
     * 
     * @param array $orderData
     * @return bool
     */
    public function bulkUpdateOrder(array $orderData): bool
    {
        return $this->repository->bulkUpdateOrder($orderData);
    }

    /**
     * Mevcut bir içerikle ders içeriği oluştur
     * 
     * @param int $lessonId
     * @param object $contentable
     * @param array $lessonContentData
     * @return mixed
     */
    public function createWithContent(int $lessonId, object $contentable, array $lessonContentData = [])
    {
        return $this->repository->createWithContent($lessonId, $contentable, $lessonContentData);
    }
}