<?php

namespace App\Interfaces\Repositories\Api;

use App\Models\UserMissionProgress;

interface MissionsRepositoryInterface
{
    /**
     * Kullanıcının tamamlayabileceği görevleri al
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableMissionsForUser(int $userId);
    
    /**
     * Tüm görevleri al
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * ID ile görev bul
     * 
     * @param int $id
     * @return \App\Models\Mission|null
     */
    public function find($id);
    
    /**
     * Görevi tamamlandı olarak işaretle
     * 
     * @param int $userId
     * @param int $missionId
     * @param int $amount
     * @return UserMissionProgress
     */
    public function markMissionAsCompleted(int $userId, int $missionId, int $amount = 1): UserMissionProgress;
    
    /**
     * Kullanıcının görev ilerlemesini al
     * 
     * @param int $userId
     * @param int $missionId
     * @return UserMissionProgress|null
     */
    public function getUserMissionProgress(int $userId, int $missionId): ?UserMissionProgress;
    
    /**
     * Kullanıcının tüm görev ilerlemelerini al
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUserMissionProgress(int $userId);

    /**
     * Belirli bir tipteki görevleri al
     * 
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByType(string $type);
}
