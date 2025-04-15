<?php

namespace App\Interfaces\Services\Admin;
use App\Interfaces\Services\BaseServiceInterface;

interface MissionsServiceInterface extends BaseServiceInterface
{
    public function toggleStatus(int $id): bool;
    public function all();  // Tüm görevleri al
    public function find($id);  // ID ile görev bul
}
