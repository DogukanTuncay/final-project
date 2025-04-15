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
        // Repository'nin create metodunu çağır
        return $this->repository->create($data);
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
        // Repository'nin update metodunu çağır
        return $this->repository->update($id, $data);
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
    
    /**
     * Çoktan seçmeli soruyu sil
     * Bağlı ders içerikleri otomatik olarak silinecek
     *
     * @param int $id
     * @return bool
     */
    public function delete($id): bool
    {
        return $this->repository->delete($id);
    }
}