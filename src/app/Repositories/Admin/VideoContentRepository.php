<?php

namespace App\Repositories\Admin;

use App\Models\VideoContent;
use App\Interfaces\Repositories\Admin\VideoContentRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoContentRepository extends BaseRepository implements VideoContentRepositoryInterface
{
    public function __construct(VideoContent $model)
    {
        parent::__construct($model);
    }

    /**
     * Tüm video içeriklerini getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    /**
     * ID'ye göre video içeriğini bul
     *
     * @param int $id
     * @return VideoContent|null
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Yeni video içeriği oluştur
     *
     * @param array $data
     * @return VideoContent
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Video içeriğini güncelle
     *
     * @param int $id
     * @param array $data
     * @return VideoContent
     */
    public function update($id, array $data)
    {
        $video = $this->find($id);
        $video->update($data);
        return $video;
    }

    /**
     * Video içeriğini sil
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    /**
     * Filtreleme ve sayfalama ile video içeriklerini getir
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFilteredAndPaginated(array $filters = [], $perPage = 15)
    {
        $query = $this->model->query();

        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (isset($filters['provider'])) {
            $query->where('provider', $filters['provider']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->get();
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
        return $this->model->whereIn('id', $ids)->update($data);
    }
}