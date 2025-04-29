<?php

namespace App\Interfaces\Services\Api;

use App\Models\User;

interface BadgeServiceInterface
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserBadges();
    
    /**
     * Kullanıcıya rozet ver
     * 
     * @param int $badgeId
     * @return bool
     */
    public function awardBadge(int $badgeId): bool;
    
    /**
     * Tüm rozet koşullarını kontrol et ve uygun olanları kullanıcıya ver
     * 
     * @param User $user
     * @return array Kazanılan rozetlerin ID'leri
     */
    public function checkAndAwardBadges(User $user): array;
    
    /**
     * Belirli bir kullanıcının belirli bir rozet için uygun olup olmadığını kontrol et
     * 
     * @param User $user
     * @param \App\Models\Badge $badge
     * @return bool
     */
    public function userQualifiesForBadge(User $user, $badge): bool;
}