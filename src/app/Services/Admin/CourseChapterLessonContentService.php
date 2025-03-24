<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\CourseChapterLessonContentServiceInterface;
use App\Interfaces\Repositories\Admin\CourseChapterLessonContentRepositoryInterface;
use App\Services\BaseService;
use App\Models\Contents\TextContent;
use App\Models\Contents\VideoContent;
use App\Models\FillInTheBlank;
use App\Models\Quiz;
use App\Models\MultipleChoiceQuestion;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Collection;

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
        return $this->repository->getByLessonId($lessonId);
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
        // Önce çoktan seçmeli soruyu oluştur
        $mcq = new MultipleChoiceQuestion([
            'question' => $contentData['question'],
            'feedback' => $contentData['feedback'] ?? null,
            'points' => $contentData['points'] ?? 1,
            'is_multiple_answer' => $contentData['is_multiple_answer'] ?? false,
            'shuffle_options' => $contentData['shuffle_options'] ?? true,
            'created_by' => $contentData['created_by'] ?? auth()->id(),
            'is_active' => true,
        ]);
        
        $mcq->save();
        
        // Seçenekleri oluştur
        if (isset($contentData['options']) && is_array($contentData['options'])) {
            foreach ($contentData['options'] as $index => $optionData) {
                QuestionOption::create([
                    'question_id' => $mcq->id,
                    'text' => $optionData['text'],
                    'is_correct' => $optionData['is_correct'] ?? false,
                    'order' => $optionData['order'] ?? $index,
                    'feedback' => $optionData['feedback'] ?? null,
                ]);
            }
        }
        
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
}