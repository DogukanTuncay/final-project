<?php

namespace App\Interfaces\Repositories\Admin;

interface OneSignalRepositoryInterface
{
    /**
     * Tüm bildirimleri getirir
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAll(array $filters = [], int $perPage = 15);

    /**
     * ID'ye göre bildirim detayını getirir
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id);

    /**
     * Tek bir kullanıcıya bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendToUser(array $data): bool;

    /**
     * Belirli kullanıcı gruplarına bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendToSegment(array $data): bool;

    /**
     * Tüm kullanıcılara bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendToAll(array $data): bool;

    /**
     * Bildirim şablonu oluşturur
     *
     * @param array $data
     * @return mixed
     */
    public function createTemplate(array $data);

    /**
     * Bildirim şablonunu günceller
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateTemplate(int $id, array $data);

    /**
     * Bildirim şablonunu siler
     *
     * @param int $id
     * @return bool
     */
    public function deleteTemplate(int $id): bool;

    /**
     * Bildirim iptal eder
     *
     * @param string $notificationId
     * @return bool
     */
    public function cancelNotification(string $notificationId): bool;

    /**
     * Bildirim istatistiklerini getirir
     *
     * @param array $filters
     * @return mixed
     */
    public function getNotificationStatistics(array $filters = []);
} 