<?php

namespace App\Interfaces\Services\Admin;
use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Http\UploadedFile;
interface CourseServiceInterface extends BaseServiceInterface
{
    
    public function updateOrder($id, int $order);
    public function toggleStatus($id);
    public function toggleFeatured($id);
    public function findByCategory(string $category);
    public function handleImage($course, UploadedFile $image);
    public function handleImages($course, array $images);
}