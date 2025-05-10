<?php

namespace App\Interfaces\Repositories\Api;

interface FillInTheBlankRepositoryInterface
{
    public function findById($id);
    public function findBySlug($slug);
    public function get();
    public function with($relations);
} 