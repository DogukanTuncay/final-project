<?php

namespace App\Interfaces\Services\Api;

use App\Models\Level;

interface UserExperienceServiceInterface
{
    /**
     * Kullanıcıya XP ekle ve seviye kontrolü yap
     *
     * @param int $userId
     * @param int $amount
     * @param string|null $actionType
     * @param int|null $actionId
     * @param string|null $description
     * @return array|null
     */
    public function addExperience(int $userId, int $amount, ?string $actionType = null, ?int $actionId = null, ?string $description = null): ?array;
    
    /**
     * Kullanıcının deneyim ve seviye bilgilerini getir
     *
     * @param int $userId
     * @return array|null
     */
    public function getUserExperience(int $userId): ?array;
    
    /**
     * Seviye için gereken aktivite miktarını hesapla
     * 
     * @param Level $level
     * @param int $xpPerActivity
     * @return int
     */
    public function calculateActivitiesForNextLevel(Level $level, int $xpPerActivity): int;
} 