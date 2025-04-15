<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\MatchingQuestionServiceInterface;
use App\Interfaces\Repositories\Admin\MatchingQuestionRepositoryInterface;
use App\Services\BaseService;

class MatchingQuestionService extends BaseService implements MatchingQuestionServiceInterface
{
    public function __construct(MatchingQuestionRepositoryInterface $repository)
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