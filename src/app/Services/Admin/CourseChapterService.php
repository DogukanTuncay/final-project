<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\CourseChapterServiceInterface;
use App\Interfaces\Repositories\Admin\CourseChapterRepositoryInterface;
use App\Services\BaseService;

use Illuminate\Http\UploadedFile;

class CourseChapterService extends BaseService implements CourseChapterServiceInterface
{
    public function __construct(CourseChapterRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }


    public function findByCourse(int $courseId)
    {
        return $this->repository->findByCourse($courseId);
    }
    
    public function create(array $data)
    {
        $courseChapter = parent::create($data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($courseChapter, $data['image']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($courseChapter, $data['images']);
        }

        return $courseChapter;
    }

    public function update($id, array $data)
    {
        $courseChapter = parent::update($id, $data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($courseChapter, $data['image']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($courseChapter, $data['images']);
        }

        return $courseChapter;
    }


    public function updateOrder(int $id, int $order)
    {
        return $this->repository->updateOrder($id, $order);
    }

    public function toggleStatus(int $id)
    {
        return $this->repository->toggleStatus($id);
    }

    public function handleImage($courseChapter, UploadedFile $image)
    {
        return $courseChapter->uploadImage($image);
    }

    public function handleImages($courseChapter, array $images)
    {
        return $courseChapter->uploadImages($images);
    }

}