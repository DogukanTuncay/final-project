<?php

namespace App\Interfaces\Services\Api;

use App\Models\User;

interface NotificationServiceInterface
{
    /**
     * Kullanıcının belirli türde bildirim alıp alamayacağını kontrol eder
     *
     * @param User $user
     * @param string $notificationType
     * @return bool
     */
    public function canUserReceiveNotification(User $user, string $notificationType): bool;
    
    /**
     * Login streak bildirimi için kontrol yapar ve gerekirse gönderir
     *
     * @param User $user
     * @return bool
     */
    public function checkAndSendLoginStreakNotification(User $user): bool;
    
    /**
     * Kurs hatırlatıcı bildirimini kontrol eder ve gerekirse gönderir
     *
     * @param User $user
     * @return bool
     */
    public function checkAndSendCourseReminderNotification(User $user): bool;
    
    /**
     * Özel bildirim gönderebilme durumunu kontrol eder
     *
     * @param User $user
     * @return bool
     */
    public function canSendCustomNotification(User $user): bool;
    
    /**
     * Genel duyuru bildirimlerini alabilme durumunu kontrol eder
     *
     * @param User $user
     * @return bool
     */
    public function canSendBroadcastNotification(User $user): bool;
    
    /**
     * Tüm bildirim kontrollerini tek bir yerden yapar
     *
     * @param User $user
     * @return array
     */
    public function checkAllNotifications(User $user): array;
} 