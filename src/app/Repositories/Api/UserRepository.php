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
        try {
            $user = $this->findById($userId);

            if (!$user) {
                return null;
            }

            // İzin verilen alanların güncellenmesini sağla ve sadece değişen alanları güncelle
            $dataChanged = false;
            foreach ($data as $key => $value) {
                if (in_array($key, $user->getFillable()) && $user->{$key} !== $value) {
                    $user->{$key} = $value;
                    $dataChanged = true;
                }
            }

            // Sadece değişiklik varsa kaydet
            if ($dataChanged) {
                $user->save();
            }

            // Güncel kullanıcıyı döndür
            return $user->fresh();
        } catch (\Exception $e) {
            \Log::error('UserRepository update error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'data' => $data
            ]);
            return null;
        }
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