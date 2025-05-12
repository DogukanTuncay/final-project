<?php

namespace App\Interfaces\Services\Api;

use App\Models\User;

interface UserServiceInterface
{
    /**
     * Get the authenticated user's profile information.
     *
     * @param int $userId
     * @return User|null
     */
    public function getProfile(int $userId): ?User;

    /**
     * Update the authenticated user's profile information.
     *
     * @param int $userId
     * @param array $data
     * @return User|null
     */
    public function updateProfile(int $userId, array $data): ?User;

    /**
     * Update the authenticated user's locale.
     *
     * @param int $userId
     * @param string $locale
     * @return User|null
     */
    public function updateLocale(int $userId, string $locale): ?User;

    /**
     * Kullanıcının şifresini değiştirir.
     *
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool|string Başarılıysa true, değilse hata mesajı döner
     */
    public function updatePassword(int $userId, string $currentPassword, string $newPassword);
} 