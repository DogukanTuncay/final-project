<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\CourseServiceInterface;
use App\Interfaces\Repositories\Admin\CourseRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;

class CourseService extends BaseService implements CourseServiceInterface
{
    public function __construct(CourseRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data)
    {
        $course = parent::create($data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($course, $data['image']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($course, $data['images']);
        }

        return $course;
    }

    public function update($id, array $data)
    {
        $course = parent::update($id, $data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($course, $data['image']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($course, $data['images']);
        }

        return $course;
    }

    public function updateOrder($id, int $order)
    {
        return $this->repository->updateOrder($id, $order);
    }

    public function toggleStatus($id)
    {
        return $this->repository->toggleStatus($id);
    }

    public function toggleFeatured($id)
    {
        return $this->repository->toggleFeatured($id);
    }

    public function findByCategory(string $category)
    {
        return $this->repository->findByCategory($category);
    }

    public function handleImage($course, UploadedFile $image)
    {
        return $course->uploadImage($image);
    }

    public function handleImages($course, array $images)
    {
        return $course->uploadImages($images);
    }
}