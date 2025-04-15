<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\TrueFalseQuestionServiceInterface;
use App\Interfaces\Repositories\Admin\TrueFalseQuestionRepositoryInterface;
use App\Interfaces\Repositories\Admin\CourseChapterLessonContentRepositoryInterface;
use App\Models\TrueFalseQuestion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TrueFalseQuestionService implements TrueFalseQuestionServiceInterface
{
    private TrueFalseQuestionRepositoryInterface $trueFalseQuestionRepository;
    private CourseChapterLessonContentRepositoryInterface $lessonContentRepository;

    public function __construct(
        TrueFalseQuestionRepositoryInterface $trueFalseQuestionRepository,
        CourseChapterLessonContentRepositoryInterface $lessonContentRepository
    ) {
        $this->trueFalseQuestionRepository = $trueFalseQuestionRepository;
        $this->lessonContentRepository = $lessonContentRepository;
    }

    /**
     * Tüm Doğru/Yanlış sorularını getir
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->trueFalseQuestionRepository->all();
    }

    /**
     * ID'ye göre Doğru/Yanlış sorusunu bul
     * 
     * @param int $id
     * @return TrueFalseQuestion|null
     */
    public function find(int $id): ?TrueFalseQuestion
    {
        return $this->trueFalseQuestionRepository->find($id);
    }

    /**
     * Yeni Doğru/Yanlış sorusu oluştur
     * 
     * @param array $data
     * @return TrueFalseQuestion
     */
    public function create(array $data): TrueFalseQuestion
    {
        // Created by alanını otomatik doldur
        if (!isset($data['created_by']) && Auth::check()) {
            $data['created_by'] = Auth::id();
        }
        
        return $this->trueFalseQuestionRepository->create($data);
    }

    /**
     * Doğru/Yanlış sorusunu güncelle
     * 
     * @param int $id
     * @param array $data
     * @return TrueFalseQuestion|null
     */
    public function update(int $id, array $data): ?TrueFalseQuestion
    {
        return $this->trueFalseQuestionRepository->update($id, $data);
    }

    /**
     * Doğru/Yanlış sorusunu sil
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->trueFalseQuestionRepository->delete($id);
    }

    /**
     * Doğru/Yanlış sorusunun aktiflik durumunu değiştir
     * 
     * @param int $id
     * @return TrueFalseQuestion|null
     */
    public function toggleStatus(int $id): ?TrueFalseQuestion
    {
        return $this->trueFalseQuestionRepository->toggleStatus($id);
    }

    /**
     * Doğru/Yanlış sorusunu derse ekle
     * 
     * @param int $questionId
     * @param int $lessonId
     * @param array $additionalData
     * @return mixed
     */
    public function addToLesson(int $questionId, int $lessonId, array $additionalData = []): mixed
    {
        $question = $this->trueFalseQuestionRepository->find($questionId);
        
        if (!$question) {
            throw new \Exception(__('errors.true_false_question.not_found'));
        }
        
        $orderData = [
            'order' => $additionalData['order'] ?? 0,
            'is_active' => $additionalData['is_active'] ?? true,
            'meta_data' => $additionalData['meta_data'] ?? null,
        ];
        
        return $this->lessonContentRepository->createWithContent($lessonId, $question, $orderData);
    }
}