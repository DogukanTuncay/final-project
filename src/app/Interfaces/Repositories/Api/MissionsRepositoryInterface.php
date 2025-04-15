<?php

namespace App\Interfaces\Repositories\Api;

interface MissionsRepositoryInterface
{
    public function getAvailableMissionsForUser(int $userId);
    public function all();  // Tüm görevleri al
    public function find($id);  // ID ile görev bul
    public function markMissionAsCompleted(int $userId, int $missionId): bool;
}
