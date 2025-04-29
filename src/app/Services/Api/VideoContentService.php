<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\VideoContentServiceInterface;
use App\Interfaces\Repositories\Api\VideoContentRepositoryInterface;
use App\Http\Resources\Api\VideoContentResource;
use Illuminate\Support\Facades\Log;

class VideoContentService implements VideoContentServiceInterface
{
    protected $repository;

    public function __construct(VideoContentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ID'ye göre video içeriğini bul
     *
     * @param int $id
     * @return VideoContentResource
     */
    public function findById($id)
    {
        try {
            $video = $this->repository->findById($id);
            return new VideoContentResource($video);
        } catch (\Exception $e) {
            Log::error('Video içeriği bulunamadı: ' . $e->getMessage());
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
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getWithPagination(array $params)
    {
        try {
            $videos = $this->repository->getWithPagination($params);
            return VideoContentResource::collection($videos);
        } catch (\Exception $e) {
            Log::error('Video içerikleri listelenirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Aktif video içeriklerini getir
     *
     * @param int $limit
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getActiveVideos($limit = 10)
    {
        try {
            $videos = $this->repository->getActiveVideos($limit);
            return VideoContentResource::collection($videos);
        } catch (\Exception $e) {
            Log::error('Aktif video içerikleri listelenirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Belirli bir provider'a ait videoları getir
     *
     * @param string $provider
     * @param int $limit
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getVideosByProvider($provider, $limit = 10)
    {
        try {
            $videos = $this->repository->getVideosByProvider($provider, $limit);
            return VideoContentResource::collection($videos);
        } catch (\Exception $e) {
            Log::error("$provider provider'ına ait videolar listelenirken hata: " . $e->getMessage());
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
        return $this->repository->isValidVideoUrl($url);
    }
}