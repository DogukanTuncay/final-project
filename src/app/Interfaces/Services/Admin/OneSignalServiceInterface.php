<?php

namespace App\Interfaces\Services\Admin;

interface OneSignalServiceInterface
{
    /**
     * Tüm bildirimleri getirir
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAllNotifications(array $filters = [], int $perPage = 15);

    /**
     * ID'ye göre bildirim detayını getirir
     *
     * @param int $id
     * @return mixed
     */
    public function getNotificationById(int $id);

    /**
     * Tek bir kullanıcıya bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendNotificationToUser(array $data): bool;

    /**
     * Belirli kullanıcı gruplarına bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendNotificationToSegment(array $data): bool;

    /**
     * Tüm kullanıcılara bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendNotificationToAll(array $data): bool;

    /**
     * Bildirim şablonu oluşturur
     *
     * @param array $data
     * @return mixed
     */
    public function createNotificationTemplate(array $data);

    /**
     * Bildirim şablonunu günceller
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateNotificationTemplate(int $id, array $data);

    /**
     * Bildirim şablonunu siler
     *
     * @param int $id
     * @return bool
     */
    public function deleteNotificationTemplate(int $id): bool;

    /**
     * Bildirim iptal eder
     *
     * @param string $notificationId
     * @return bool
     */
    public function cancelNotification(string $notificationId): bool;

    /**
     * Bildirimlere ait istatistikleri getirir
     *
     * @param array $filters
     * @return mixed
     */
    public function getNotificationStatistics(array $filters = []);
} 