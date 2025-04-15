<?php

namespace App\Repositories\Admin;

use App\Models\MultipleChoiceQuestion;
use App\Models\QuestionOption;
use App\Interfaces\Repositories\Admin\MultipleChoiceQuestionRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class MultipleChoiceQuestionRepository extends BaseRepository implements MultipleChoiceQuestionRepositoryInterface
{
    public function __construct(MultipleChoiceQuestion $model)
    {
        parent::__construct($model);
    }
    
    /**
     * Çoktan seçmeli soruyu seçenekleriyle birlikte oluşturur
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
                'created_by' => $data['created_by'] ?? auth()->id(),
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
     * Çoktan seçmeli soruyu ve seçeneklerini günceller
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
            $updateData = [];
            if (isset($data['question'])) $updateData['question'] = $data['question'];
            if (isset($data['feedback'])) $updateData['feedback'] = $data['feedback'];
            if (isset($data['points'])) $updateData['points'] = $data['points'];
            if (isset($data['is_multiple_answer'])) $updateData['is_multiple_answer'] = $data['is_multiple_answer'];
            if (isset($data['shuffle_options'])) $updateData['shuffle_options'] = $data['shuffle_options'];
            if (isset($data['is_active'])) $updateData['is_active'] = $data['is_active'];
            
            if (!empty($updateData)) {
                parent::update($id, $updateData);
            }
            
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
     * İlişkili verileri sorgular
     * 
     * @param array $relations
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }
    
    /**
     * Çoktan seçmeli soruyu ve ilişkili seçenekleri siler
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $question = $this->find($id);
            if (!$question) {
                return false;
            }
            
            // Önce seçenekleri sil
            $question->options()->delete();
            
            // Sonra soruyu sil
            return parent::delete($id);
        });
    }
}