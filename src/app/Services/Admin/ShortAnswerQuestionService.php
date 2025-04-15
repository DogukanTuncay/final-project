<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\ShortAnswerQuestionServiceInterface;
use App\Interfaces\Repositories\Admin\ShortAnswerQuestionRepositoryInterface;
use App\Services\BaseService;

class ShortAnswerQuestionService extends BaseService implements ShortAnswerQuestionServiceInterface
{
    public function __construct(ShortAnswerQuestionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
    
    /**
     * Sorunun aktiflik durumunu değiştir
     *
     * @param int $id
     * @return bool Yeni durum
     */
    public function toggleStatus($id)
    {
        $question = $this->find($id);
        
        if (!$question) {
            return false;
        }
        
        $newStatus = !$question->is_active;
        
        $this->update($id, [
            'is_active' => $newStatus
        ]);
        
        return $newStatus;
    }
}