<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\VideoContentServiceInterface;
use App\Interfaces\Repositories\Admin\VideoContentRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class VideoContentService extends BaseService implements VideoContentServiceInterface
{
    public function __construct(VideoContentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Tüm video içeriklerini getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->repository->all();
    }

    /**
     * ID'ye göre video içeriğini bul
     *
     * @param int $id
     * @return \App\Models\VideoContent|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Yeni video içeriği oluştur
     *
     * @param array $data
     * @return \App\Models\VideoContent
     */
    public function create(array $data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            Log::error('VideoContent oluşturulurken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Video içeriğini güncelle
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\VideoContent
     */
    public function update($id, array $data)
    {
        try {
            return $this->repository->update($id, $data);
        } catch (\Exception $e) {
            Log::error('VideoContent güncellenirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Video içeriğini sil
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            Log::error('VideoContent silinirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Filtreleme ve sayfalama ile video içeriklerini getir
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilteredAndPaginated(array $filters = [], $perPage = 15)
    {
        return $this->repository->getFilteredAndPaginated($filters, $perPage);
    }

    /**
     * Video URL'sinden provider ve video ID bilgilerini çıkar
     *
     * @param string $url
     * @return array
     */
    public function parseVideoUrl($url)
    {
        $result = [
            'provider' => 'custom',
            'video_id' => null
        ];

        // YouTube URL kontrolü
        if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $result['provider'] = 'youtube';
            $result['video_id'] = $matches[1];
        }
        // Vimeo URL kontrolü
        elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|)(\d+)(?:|\/\?)/', $url, $matches)) {
            $result['provider'] = 'vimeo';
            $result['video_id'] = $matches[1];
        }

        return $result;
    }

    /**
     * Birden fazla video içeriğini toplu olarak güncelle
     *
     * @param array $ids
     * @param array $data
     * @return bool
     */
    public function bulkUpdate(array $ids, array $data)
    {
        return $this->repository->bulkUpdate($ids, $data);
    }
}