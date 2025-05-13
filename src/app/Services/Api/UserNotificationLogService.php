<?php

namespace App\Services\Api;

use App\Interfaces\Repositories\Api\UserNotificationLogRepositoryInterface;
use App\Interfaces\Services\Api\UserNotificationLogServiceInterface;
use Illuminate\Support\Facades\Auth;

class UserNotificationLogService implements UserNotificationLogServiceInterface
{
    protected UserNotificationLogRepositoryInterface $userNotificationLogRepository;

    /**
     * Servis sınıfının oluşturucusu
     *
     * @param UserNotificationLogRepositoryInterface $userNotificationLogRepository
     */
    public function __construct(UserNotificationLogRepositoryInterface $userNotificationLogRepository)
    {
        $this->userNotificationLogRepository = $userNotificationLogRepository;
    }

    /**
     * Oturum açmış kullanıcının bildirim günlüklerini getirir
     *
     * @param array $filters
     */
    public function getCurrentUserNotifications(array $filters = [])
    {
        $userId = Auth::id();
        return $this->userNotificationLogRepository->getByUserId($userId, $filters);
    }

    /**
     * Belirli bir bildirim günlüğünü ID'ye göre getirir
     *
     * @param int $id
     * @return mixed
     */
    public function getNotificationById(int $id)
    {
        $userId = Auth::id();
        $notification = $this->userNotificationLogRepository->findById($id);
        
        if (!$notification || $notification->user_id !== $userId) {
            return null;
        }
        
        return $notification;
    }

    /**
     * Belirli bir türdeki son bildirimi getirir
     *
     * @param string $type
     * @return mixed
     */
    public function getLastNotificationOfType(string $type)
    {
        $userId = Auth::id();
        return $this->userNotificationLogRepository->getLastNotificationOfType($userId, $type);
    }

    /**
     * Belirli bir bildirimi siler
     *
     * @param int $id
     * @return bool
     */
    public function deleteNotification(int $id): bool
    {
        $userId = Auth::id();
        $notification = $this->userNotificationLogRepository->findById($id);
        
        if (!$notification || $notification->user_id !== $userId) {
            return false;
        }
        
        return $this->userNotificationLogRepository->delete($id);
    }

    /**
     * Kullanıcının tüm bildirimlerini siler
     *
     * @return bool
     */
    public function deleteAllNotifications(): bool
    {
        $userId = Auth::id();
        return $this->userNotificationLogRepository->deleteAllByUserId($userId);
    }
} 