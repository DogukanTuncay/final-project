<?php

namespace App\Interfaces\Services\Api;

interface MissionsServiceInterface
{
    public function getAvailableMissionsForUser();
    public function all();  // Tüm görevleri al
    public function find($id);  // ID ile görev bul
    public function complete($id);
    public function getByType($type);  // Türe göre görevleri getir
}
