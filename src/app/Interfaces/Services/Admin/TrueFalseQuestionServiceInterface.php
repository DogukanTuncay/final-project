<?php

namespace App\Interfaces\Services\Admin;

use Illuminate\Database\Eloquent\Collection;
use App\Models\TrueFalseQuestion;
use App\Interfaces\Services\BaseServiceInterface;
interface TrueFalseQuestionServiceInterface extends BaseServiceInterface
{
    
    
    /**
     * Doğru/Yanlış sorusunun aktiflik durumunu değiştir
     * 
     * @param int $id
     * @return TrueFalseQuestion|null
     */
    public function toggleStatus(int $id): ?TrueFalseQuestion;
    
    /**
     * Doğru/Yanlış sorusunu derse ekle
     * 
     * @param int $questionId
     * @param int $lessonId
     * @param array $additionalData
     * @return mixed
     */
    public function addToLesson(int $questionId, int $lessonId, array $additionalData = []): mixed;
}