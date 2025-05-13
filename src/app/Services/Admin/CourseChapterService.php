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

    /**
     * Belirli bir bölüm için ön koşul bölümleri ekle
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function addPrerequisites(int $chapterId, array $prerequisiteIds): bool
    {
        return $this->repository->addPrerequisites($chapterId, $prerequisiteIds);
    }

    /**
     * Belirli bir bölüm için ön koşul bölümlerini kaldır
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function removePrerequisites(int $chapterId, array $prerequisiteIds): bool
    {
        return $this->repository->removePrerequisites($chapterId, $prerequisiteIds);
    }

    /**
     * Belirli bir bölüm için tüm ön koşulları kaldır
     * @param int $chapterId
     * @return bool
     */
    public function clearPrerequisites(int $chapterId): bool
    {
        return $this->repository->clearPrerequisites($chapterId);
    }

    /**
     * Belirli bir bölüm için ön koşulları güncelle
     * @param int $chapterId
     * @param array $prerequisiteIds
     * @return bool
     */
    public function updatePrerequisites(int $chapterId, array $prerequisiteIds): bool
    {
        return $this->repository->updatePrerequisites($chapterId, $prerequisiteIds);
    }

    /**
     * Belirli bir bölüm için ön koşulları getir
     * @param int $chapterId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrerequisites(int $chapterId)
    {
        return $this->repository->getPrerequisites($chapterId);
    }

}