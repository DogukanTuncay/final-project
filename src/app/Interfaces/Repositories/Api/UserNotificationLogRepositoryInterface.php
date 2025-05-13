<?php

namespace App\Interfaces\Repositories\Api;

interface UserNotificationLogRepositoryInterface
{
    /**
     * Kullanıcı ID'sine göre bildirim günlüklerini getirir
     *
     * @param int $userId
     * @param array $filters
     * @return mixed
     */
    public function getByUserId(int $userId, array $filters = []);

    /**
     * Belirli bir bildirim günlüğünü ID'ye göre getirir
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Belirli bir kullanıcı için belirli bir türdeki son bildirimi getirir
     *
     * @param int $userId
     * @param string $type
     * @return mixed
     */
    public function getLastNotificationOfType(int $userId, string $type);

    /**
     * Belirli bir bildirimi siler
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Belirli bir kullanıcının tüm bildirimlerini siler
     *
     * @param int $userId
     * @return bool
     */
    public function deleteAllByUserId(int $userId): bool;
} 