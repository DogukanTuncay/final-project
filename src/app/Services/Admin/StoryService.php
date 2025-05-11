<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\StoryServiceInterface;
use App\Interfaces\Repositories\Admin\StoryRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;

class StoryService extends BaseService implements StoryServiceInterface
{
    public function __construct(StoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data)
    {
        $story = $this->repository->create($data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->handleImage($story, $data['image']);
        }
        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImages($story, $data['images']);
        }

        return $story;
    }

    public function handleImage($story, UploadedFile $image)
    {
        return $story->uploadImage($image);
    }

    public function handleImages($story, array $images)
    {
        return $story->uploadImages($images);
    }
    
}