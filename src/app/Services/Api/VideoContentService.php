<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\VideoContentServiceInterface;
use App\Interfaces\Repositories\Api\VideoContentRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;

class VideoContentService extends BaseService implements VideoContentServiceInterface
{
    public function __construct(VideoContentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * ID'ye göre video içeriğini bul
     *
     * @param int $id
     * @return \App\Models\VideoContent
     */
    public function findById($id)
    {
        try {
            return $this->repository->findById($id);
        } catch (\Exception $e) {
            Log::error('VideoContent bulunurken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Slug'a göre video içeriğini bul
     *
     * @param string $slug
     * @return VideoContentResource
     */
    public function findBySlug($slug)
    {
        try {
            $video = $this->repository->findBySlug($slug);
            return new VideoContentResource($video);
        } catch (\Exception $e) {
            Log::error('Video içeriği bulunamadı: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sayfalama ve filtreleme ile video içeriklerini getir
     *
     * @param array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWithPagination(array $params)
    {
        try {
            return $this->repository->getWithPagination($params);
        } catch (\Exception $e) {
            Log::error('VideoContent listelenirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sadece aktif video içeriklerini getir
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveVideos($limit = 10)
    {
        try {
            return $this->repository->getActiveVideos($limit);
        } catch (\Exception $e) {
            Log::error('Aktif VideoContent listelenirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Provider'a göre video içeriklerini getir
     *
     * @param string $provider
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVideosByProvider($provider, $limit = 10)
    {
        try {
            return $this->repository->getVideosByProvider($provider, $limit);
        } catch (\Exception $e) {
            Log::error('Provider\'a göre VideoContent listelenirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Video URL'sinin geçerli olup olmadığını kontrol et
     *
     * @param string $url
     * @return bool
     */
    public function isValidVideoUrl($url)
    {
        try {
            return $this->repository->isValidVideoUrl($url);
        } catch (\Exception $e) {
            Log::error('Video URL doğrulanırken hata: ' . $e->getMessage());
            throw $e;
        }
    }
}