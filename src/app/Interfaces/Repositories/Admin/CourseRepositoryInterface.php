<?php

namespace App\Interfaces\Repositories\Admin;
use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CourseRepositoryInterface extends BaseRepositoryInterface
{
    public function updateOrder($id, int $order);
    public function toggleStatus($id);
    public function toggleFeatured($id);
    public function findByCategory(string $category);
}