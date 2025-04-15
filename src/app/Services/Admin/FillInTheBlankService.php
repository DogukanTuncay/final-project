<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\FillInTheBlankServiceInterface;
use App\Interfaces\Repositories\Admin\FillInTheBlankRepositoryInterface;
use App\Services\BaseService;

class FillInTheBlankService extends BaseService implements FillInTheBlankServiceInterface
{
    public function __construct(FillInTheBlankRepositoryInterface $repository)
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
    public function with($relations)
    {
        return $this->repository->with($relations);
    }
} 