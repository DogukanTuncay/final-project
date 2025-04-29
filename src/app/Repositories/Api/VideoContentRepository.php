<?php

namespace App\Repositories\Api;

use App\Models\VideoContent;
use App\Interfaces\Repositories\Api\VideoContentRepositoryInterface;

class VideoContentRepository implements VideoContentRepositoryInterface
{
    protected $model;

    public function __construct(VideoContent $model)
    {
        $this->model = $model;
    }

    /**
     * ID'ye göre video içeriğini bul
     *
     * @param int $id
     * @return VideoContent
     */
    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Slug'a göre video içeriğini bul
     *
     * @param string $slug
     * @return VideoContent
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    /**
     * Sayfalama ve filtreleme ile video içeriklerini getir
     *
     * @param array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWithPagination(array $params)
    {
        $query = $this->model->query();

        // İsteğe bağlı filtreleme işlemleri
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }

        if (isset($params['provider'])) {
            $query->where('provider', $params['provider']);
        }

        if (isset($params['title'])) {
            $query->where(function($q) use ($params) {
                $q->where('title', 'LIKE', '%' . $params['title'] . '%')
                  ->orWhere('description', 'LIKE', '%' . $params['title'] . '%');
            });
        }

        // Sıralama işlemleri
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDirection = $params['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        // Sayfalama
        $perPage = $params['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Sadece aktif video içeriklerini getir
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveVideos($limit = 10)
    {
        return $this->model->active()->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    /**
     * Belirli bir provider'a ait videoları getir
     *
     * @param string $provider
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVideosByProvider($provider, $limit = 10)
    {
        return $this->model->active()->byProvider($provider)->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    /**
     * Video URL'sinin geçerli olup olmadığını kontrol et
     *
     * @param string $url
     * @return bool
     */
    public function isValidVideoUrl($url)
    {
        // YouTube URL kontrolü
        $youtubePattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
        
        // Vimeo URL kontrolü  
        $vimeoPattern = '/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|)(\d+)(?:|\/\?)/';
        
        return preg_match($youtubePattern, $url) || preg_match($vimeoPattern, $url) || filter_var($url, FILTER_VALIDATE_URL);
    }
}