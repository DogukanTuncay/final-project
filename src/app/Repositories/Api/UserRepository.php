<?php

namespace App\Repositories\Api;

use App\Models\User;
use App\Interfaces\Repositories\Api\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID.
     *
     * @param int $userId
     * @return User|null
     */
    public function findById(int $userId): ?User
    {
        return User::find($userId);
    }

    /**
     * Update user data by ID.
     *
     * @param int $userId
     * @param array $data
     * @return User|null
     */
    public function update(int $userId, array $data): ?User
    {
        $user = $this->findById($userId);
        if ($user) {
            // Parola ve locale gibi özel alanları doğrudan update ile değiştirmeyelim
            // Bu örnekte sadece belirli alanların güncelleneceğini varsayıyoruz
            // Daha güvenli bir yol, fillable'dan gelenleri filtrelemek olabilir
            $user->update($data);
            return $user->fresh();
        }
        return null;
    }

    /**
     * Update the user's locale by ID.
     *
     * @param int $userId
     * @param string $locale
     * @return User|null
     */
    public function updateLocale(int $userId, string $locale): ?User
    {
        $user = $this->findById($userId); // findById metodunu kullan
        if ($user) {
            $user->update(['locale' => $locale]);
            return $user->fresh(); // Güncellenmiş modeli döndür
        }
        return null;
    }
} 