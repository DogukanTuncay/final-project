<?php

namespace App\Interfaces\Repositories\Api;

interface BadgeRepositoryInterface
{
    /**
     * Tüm aktif rozetleri al
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * ID ile rozet bul
     * 
     * @param int $id
     * @return \App\Models\Badge|null
     */
    public function find($id);
    
    /**
     * Kullanıcının kazandığı rozetleri al
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserBadges(int $userId);
    
    /**
     * Kullanıcıya rozet ver
     * 
     * @param int $userId
     * @param int $badgeId
     * @return bool
     */
    public function awardBadgeToUser(int $userId, int $badgeId): bool;
    
    /**
     * Kullanıcının henüz kazanmadığı rozetleri al
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotEarnedBadgesForUser(int $userId);
}