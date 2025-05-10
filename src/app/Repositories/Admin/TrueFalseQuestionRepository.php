<?php

namespace App\Repositories\Admin;

use App\Models\TrueFalseQuestion;
use App\Interfaces\Repositories\Admin\TrueFalseQuestionRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class TrueFalseQuestionRepository extends BaseRepository implements TrueFalseQuestionRepositoryInterface
{
    public function __construct(TrueFalseQuestion $model)
    {
        parent::__construct($model);
    }

    /**
     * Sayfalandırma ile Doğru/Yanlış sorularını getir
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    /**
     * Doğru/Yanlış sorusunun aktiflik durumunu değiştir
     *
     * @param int $id
     * @return TrueFalseQuestion|null
     */
    public function toggleStatus(int $id): ?TrueFalseQuestion
    {
        $question = $this->find($id);
        
        if (!$question) {
            return null;
        }
        
        $question->is_active = !$question->is_active;
        $question->save();
        
        return $question;
    }
}