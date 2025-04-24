<?php

namespace App\Interfaces\Repositories\Api;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     *
     * @param int $userId
     * @return User|null
     */
    public function findById(int $userId): ?User;

    /**
     * Update user data by ID.
     *
     * @param int $userId
     * @param array $data
     * @return User|null
     */
    public function update(int $userId, array $data): ?User;

    /**
     * Update the user's locale by ID.
     *
     * @param int $userId
     * @param string $locale
     * @return User|null
     */
    public function updateLocale(int $userId, string $locale): ?User;
} 