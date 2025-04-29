<?php

namespace App\Interfaces\Services\Api;

interface NotificationServiceInterface
{
    /**
     * Kullanıcıya login streak bildirimi gönderir
     * 
     * @param int $userId
     * @param int $streakCount
     * @return bool
     */
    public function sendLoginStreakNotification(int $userId, int $streakCount): bool;
    
    /**
     * Kullanıcıya kurs tamamlama bildirimi gönderir
     * 
     * @param int $userId
     * @param string $courseName
     * @return bool
     */
    public function sendCourseCompletionNotification(int $userId, string $courseName): bool;
    
    /**
     * Kullanıcıya kurs devam et hatırlatması gönderir
     * 
     * @param int $userId
     * @param string $courseName
     * @param int $progress
     * @return bool
     */
    public function sendCourseReminderNotification(int $userId, string $courseName, int $progress): bool;
    
    /**
     * Özel bir bildirim gönderir
     * 
     * @param int|array $userIds Tek bir kullanıcı ID'si veya ID'lerin dizisi
     * @param string $title Bildirim başlığı
     * @param string $message Bildirim mesajı
     * @param array $additionalData Ek veri
     * @return bool
     */
    public function sendCustomNotification($userIds, string $title, string $message, array $additionalData = []): bool;
    
    /**
     * Tüm kullanıcılara bildirim gönderir
     * 
     * @param string $title Bildirim başlığı
     * @param string $message Bildirim mesajı
     * @param array $additionalData Ek veri
     * @return bool
     */
    public function sendBroadcastNotification(string $title, string $message, array $additionalData = []): bool;
} 