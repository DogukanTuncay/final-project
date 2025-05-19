<?php

namespace App\Repositories\Api;

use App\Interfaces\Repositories\Api\UserNotificationLogRepositoryInterface;
use App\Models\UserNotificationLog;

class UserNotificationLogRepository implements UserNotificationLogRepositoryInterface
{
    /**
     * Kullanıcı ID'sine göre bildirim günlüklerini getirir
     *
     * @param int $userId
     * @param array $filters
     */
    public function getByUserId(int $userId, array $filters = [])
    {
        $query = UserNotificationLog::where('user_id', $userId);

        // Filtreleme işlemleri
        if (isset($filters['notification_type']) && !empty($filters['notification_type'])) {
            $query->where('notification_type', $filters['notification_type']);
        }

        if (isset($filters['start_date']) && !empty($filters['start_date'])) {
            $query->whereDate('sent_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date']) && !empty($filters['end_date'])) {
            $query->whereDate('sent_at', '<=', $filters['end_date']);
        }

        // Sıralama - varsayılan olarak en yeniden en eskiye doğru
        $sortField = $filters['sort_field'] ?? 'sent_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Sayfalama
        
        return $query->get();
    }

    /**
     * Belirli bir bildirim günlüğünü ID'ye göre getirir
     *
     * @param int $id
     * @return UserNotificationLog|null
     */
    public function findById(int $id): ?UserNotificationLog
    {
        return UserNotificationLog::find($id);
    }

    /**
     * Belirli bir kullanıcı için belirli bir türdeki son bildirimi getirir
     *
     * @param int $userId
     * @param string $type
     * @return UserNotificationLog|null
     */
    public function getLastNotificationOfType(int $userId, string $type): ?UserNotificationLog
    {
        return UserNotificationLog::where('user_id', $userId)
            ->where('notification_type', $type)
            ->orderBy('sent_at', 'desc')
            ->first();
    }

    /**
     * Belirli bir bildirimi siler
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return UserNotificationLog::where('id', $id)->delete() ? true : false;
    }

    /**
     * Belirli bir kullanıcının tüm bildirimlerini siler
     *
     * @param int $userId
     * @return bool
     */
    public function deleteAllByUserId(int $userId): bool
    {
        UserNotificationLog::where('user_id', $userId)->delete();  // kaç kayıt silindiği önemli değil
        return true;
    }
} 