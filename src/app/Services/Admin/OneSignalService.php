<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\OneSignalServiceInterface;
use App\Interfaces\Repositories\Admin\OneSignalRepositoryInterface;
use Illuminate\Support\Facades\Log;

class OneSignalService implements OneSignalServiceInterface
{
    private OneSignalRepositoryInterface $oneSignalRepository;

    /**
     * Constructor
     *
     * @param OneSignalRepositoryInterface $oneSignalRepository
     */
    public function __construct(OneSignalRepositoryInterface $oneSignalRepository)
    {
        $this->oneSignalRepository = $oneSignalRepository;
    }

    /**
     * Tüm bildirimleri getirir
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAllNotifications(array $filters = [], int $perPage = 15)
    {
        return $this->oneSignalRepository->getAll($filters, $perPage);
    }

    /**
     * ID'ye göre bildirim detayını getirir
     *
     * @param int $id
     * @return mixed
     */
    public function getNotificationById(int $id)
    {
        return $this->oneSignalRepository->getById($id);
    }

    /**
     * Tek bir kullanıcıya bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendNotificationToUser(array $data): bool
    {
        try {
            // Gerekli alanların validasyonu
            if (empty($data['player_id']) || empty($data['title']) || empty($data['message'])) {
                Log::error('OneSignal kullanıcıya bildirim gönderimi için gerekli alanlar eksik');
                return false;
            }

            return $this->oneSignalRepository->sendToUser($data);
        } catch (\Exception $e) {
            Log::error('OneSignal kullanıcıya bildirim gönderimi servisi hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Belirli kullanıcı gruplarına bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendNotificationToSegment(array $data): bool
    {
        try {
            // Gerekli alanların validasyonu
            if (empty($data['segment']) || empty($data['title']) || empty($data['message'])) {
                Log::error('OneSignal segment bildirim gönderimi için gerekli alanlar eksik');
                return false;
            }

            return $this->oneSignalRepository->sendToSegment($data);
        } catch (\Exception $e) {
            Log::error('OneSignal segment bildirim gönderimi servisi hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tüm kullanıcılara bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendNotificationToAll(array $data): bool
    {
        try {
            // Gerekli alanların validasyonu
            if (empty($data['title']) || empty($data['message'])) {
                Log::error('OneSignal toplu bildirim gönderimi için gerekli alanlar eksik');
                return false;
            }

            return $this->oneSignalRepository->sendToAll($data);
        } catch (\Exception $e) {
            Log::error('OneSignal toplu bildirim gönderimi servisi hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bildirim şablonu oluşturur
     *
     * @param array $data
     * @return mixed
     */
    public function createNotificationTemplate(array $data)
    {
        try {
            // Gerekli alanların validasyonu
            if (empty($data['name']) || empty($data['title']) || empty($data['message'])) {
                Log::error('Bildirim şablonu oluşturma için gerekli alanlar eksik');
                return null;
            }

            return $this->oneSignalRepository->createTemplate($data);
        } catch (\Exception $e) {
            Log::error('Bildirim şablonu oluşturma servisi hatası: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Bildirim şablonunu günceller
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateNotificationTemplate(int $id, array $data)
    {
        try {
            // Gerekli alanların validasyonu
            if (empty($data['name']) || empty($data['title']) || empty($data['message'])) {
                Log::error('Bildirim şablonu güncelleme için gerekli alanlar eksik');
                return null;
            }

            return $this->oneSignalRepository->updateTemplate($id, $data);
        } catch (\Exception $e) {
            Log::error('Bildirim şablonu güncelleme servisi hatası: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Bildirim şablonunu siler
     *
     * @param int $id
     * @return bool
     */
    public function deleteNotificationTemplate(int $id): bool
    {
        try {
            return $this->oneSignalRepository->deleteTemplate($id);
        } catch (\Exception $e) {
            Log::error('Bildirim şablonu silme servisi hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bildirim iptal eder
     *
     * @param string $notificationId
     * @return bool
     */
    public function cancelNotification(string $notificationId): bool
    {
        try {
            return $this->oneSignalRepository->cancelNotification($notificationId);
        } catch (\Exception $e) {
            Log::error('Bildirim iptal servisi hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bildirimlere ait istatistikleri getirir
     *
     * @param array $filters
     * @return mixed
     */
    public function getNotificationStatistics(array $filters = [])
    {
        try {
            return $this->oneSignalRepository->getNotificationStatistics($filters);
        } catch (\Exception $e) {
            Log::error('Bildirim istatistikleri servisi hatası: ' . $e->getMessage());
            return [
                'total_count' => 0,
                'by_type' => [],
                'last_7_days' => []
            ];
        }
    }
} 