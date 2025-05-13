<?php

namespace App\Interfaces\Services\Api;

interface UserNotificationLogServiceInterface
{
    /**
     * Oturum açmış kullanıcının bildirim günlüklerini getirir
     *
     * @param array $filters
     * @return mixed
     */
    public function getCurrentUserNotifications(array $filters = []);

    /**
     * Belirli bir bildirim günlüğünü ID'ye göre getirir
     *
     * @param int $id
     * @return mixed
     */
    public function getNotificationById(int $id);

    /**
     * Belirli bir türdeki son bildirimi getirir
     *
     * @param string $type
     * @return mixed
     */
    public function getLastNotificationOfType(string $type);

    /**
     * Belirli bir bildirimi siler
     *
     * @param int $id
     * @return bool
     */
    public function deleteNotification(int $id): bool;

    /**
     * Kullanıcının tüm bildirimlerini siler
     *
     * @return bool
     */
    public function deleteAllNotifications(): bool;
} 