<?php

namespace App\Repositories\Api;

use App\Models\Badge;
use App\Models\User;
use Carbon\Carbon;
use App\Interfaces\Repositories\Api\BadgeRepositoryInterface;

class BadgeRepository implements BadgeRepositoryInterface
{
    protected Badge $model;

    public function __construct(Badge $model)
    {
        $this->model = $model;
    }

    /**
     * Tüm aktif rozetleri al
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->where('is_active', true)->get();
    }
    
    /**
     * ID ile rozet bul
     * 
     * @param int $id
     * @return \App\Models\Badge|null
     */
    public function find($id)
    {
        return $this->model->where('is_active', true)->find($id);
    }
    
    /**
     * Kullanıcının kazandığı rozetleri al
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserBadges(int $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return collect([]);
        }
        
        return $user->earnedBadges()->get();
    }
    
    /**
     * Kullanıcıya rozet ver
     * 
     * @param int $userId
     * @param int $badgeId
     * @return bool
     */
    public function awardBadgeToUser(int $userId, int $badgeId): bool
    {
        $user = User::find($userId);
        $badge = $this->model->find($badgeId);
        
        if (!$user || !$badge) {
            return false;
        }
        
        // Eğer kullanıcı bu rozeti zaten kazanmışsa, tekrar ekleme
        if ($user->badges()->where('badge_id', $badgeId)->exists()) {
            return true;
        }
        
        // Rozeti kullanıcıya ekle
        $user->badges()->attach($badgeId, ['earned_at' => Carbon::now()]);
        return true;
    }
    
    /**
     * Kullanıcının henüz kazanmadığı rozetleri al
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotEarnedBadgesForUser(int $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return collect([]);
        }
        
        // Kullanıcının kazandığı rozet ID'lerini al
        $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();
        
        // Henüz kazanılmamış aktif rozetleri getir
        return $this->model->where('is_active', true)
                           ->whereNotIn('id', $earnedBadgeIds)
                           ->get();
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function getWithPagination(array $params)
    {
        $query = $this->model->query();

        // İsteğe bağlı filtreleme işlemleri burada yapılabilir
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }

        // Sıralama işlemleri
        $orderBy = $params['order_by'] ?? 'id';
        $orderDirection = $params['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        // Sayfalama
        $perPage = $params['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}