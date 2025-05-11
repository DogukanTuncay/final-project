<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\StoryCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\StoryCategoryServiceInterface;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;

class StoryCategoryService extends BaseService implements StoryCategoryServiceInterface
{

    public function __construct(StoryCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

  

    public function findById(int $id): ?Model
    {
        return $this->repository->findById($id);
    }

  
    /**
     * Kategori oluştur (image_url kontrolü ile)
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {

        $storyCategory = $this->repository->create($data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($storyCategory, $data['image']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($storyCategory, $data['images']);
        }

       return $storyCategory;
       
    }
    
    /**
     * Kategori güncelle (image_url kontrolü ile)
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update($id, array $data)
    {

        $storyCategory = $this->repository->update($id, $data);
          if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($storyCategory, $data['image']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($storyCategory, $data['images']);
        }
        return $storyCategory;
    }

    public function handleImage($storyCategory, UploadedFile $image)
    {
        return $storyCategory->uploadImage($image);
    }

    public function handleImages($storyCategory, array $images)
    {
        return $storyCategory->uploadImages($images);
    }

}