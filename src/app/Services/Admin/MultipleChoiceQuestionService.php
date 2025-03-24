<?php

namespace App\Services\Admin;

use App\Models\MultipleChoiceQuestion;
use App\Models\QuestionOption;
use App\Services\BaseService;
use App\Interfaces\Services\Admin\MultipleChoiceQuestionServiceInterface;
use App\Interfaces\Repositories\Admin\MultipleChoiceQuestionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class MultipleChoiceQuestionService extends BaseService implements MultipleChoiceQuestionServiceInterface
{
    public function __construct(MultipleChoiceQuestionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
    
    /**
     * Çoktan seçmeli soruyu oluştur (seçenekleriyle birlikte)
     * 
     * @param array $data
     * @return MultipleChoiceQuestion
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Önce ana soruyu oluştur
            $question = parent::create([
                'question' => $data['question'],
                'feedback' => $data['feedback'] ?? null,
                'points' => $data['points'] ?? 1,
                'is_multiple_answer' => $data['is_multiple_answer'] ?? false,
                'shuffle_options' => $data['shuffle_options'] ?? true,
                'created_by' => auth()->id(),
                'is_active' => $data['is_active'] ?? true,
            ]);
            
            // Sonra seçenekleri oluştur
            if (isset($data['options']) && is_array($data['options'])) {
                foreach ($data['options'] as $index => $optionData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                        'order' => $optionData['order'] ?? $index,
                        'feedback' => $optionData['feedback'] ?? null,
                    ]);
                }
            }
            
            return $question->fresh(['options']);
        });
    }
    
    /**
     * Çoktan seçmeli soruyu güncelle (seçenekleriyle birlikte)
     * 
     * @param int $id
     * @param array $data
     * @return MultipleChoiceQuestion
     */
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $question = $this->find($id);
            
            // Ana soruyu güncelle
            parent::update($id, [
                'question' => $data['question'] ?? $question->question,
                'feedback' => $data['feedback'] ?? $question->feedback,
                'points' => $data['points'] ?? $question->points,
                'is_multiple_answer' => $data['is_multiple_answer'] ?? $question->is_multiple_answer,
                'shuffle_options' => $data['shuffle_options'] ?? $question->shuffle_options,
                'is_active' => $data['is_active'] ?? $question->is_active,
            ]);
            
            // Eğer yeni seçenekler gönderildiyse, mevcut seçenekleri sil ve yenilerini ekle
            if (isset($data['options']) && is_array($data['options'])) {
                // Mevcut seçenekleri sil
                $question->options()->delete();
                
                // Yeni seçenekleri ekle
                foreach ($data['options'] as $index => $optionData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                        'order' => $optionData['order'] ?? $index,
                        'feedback' => $optionData['feedback'] ?? null,
                    ]);
                }
            }
            
            return $question->fresh(['options']);
        });
    }
    
    /**
     * Çoktan seçmeli soruyu seçenekleriyle birlikte getir
     * 
     * @param int $id
     * @return MultipleChoiceQuestion
     */
    public function find($id)
    {
        return $this->repository->with(['options' => function($query) {
            $query->orderBy('order', 'asc');
        }])->find($id);
    }
}