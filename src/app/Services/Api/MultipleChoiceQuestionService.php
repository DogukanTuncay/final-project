<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\MultipleChoiceQuestionServiceInterface;
use App\Interfaces\Repositories\Api\MultipleChoiceQuestionRepositoryInterface;
use App\Services\BaseService;

class MultipleChoiceQuestionService extends BaseService implements MultipleChoiceQuestionServiceInterface
{
    public function __construct(MultipleChoiceQuestionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * ID'ye göre soru bul ve seçenekleri yükle
     * 
     * @param int $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->repository->with(['options' => function($query) {
            $query->orderBy('order', 'asc');
        }])->find($id);
    }

    /**
     * Slug'a göre soru bul ve seçenekleri yükle (Uygulanabilir değilse ID'ye göre arayacak)
     * 
     * @param string $slug
     * @return mixed
     */
    public function findBySlug($slug)
    {
        // Slug özelliği yoksa ID'ye göre arayalım
        if (is_numeric($slug)) {
            return $this->findById($slug);
        }
        
        // Slug özelliği varsa buna göre arama yapabilirsiniz
        // Örnek: return $this->repository->with('options')->findBySlug($slug);
        return $this->findById($slug);
    }

    /**
     * Sayfalandırma ile soruları listeleme
     * 
     * @param array $params
     * @return mixed
     */
    public function getWithPagination(array $params)
    {
        // Sayfalama ve filtreleme mantığı burada uygulanabilir
        // Örnek basit sayfalama:
        $perPage = $params['per_page'] ?? 15;
        
        return $this->repository->with(['options' => function($query) {
            $query->orderBy('order', 'asc');
        }])->paginate($perPage);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}