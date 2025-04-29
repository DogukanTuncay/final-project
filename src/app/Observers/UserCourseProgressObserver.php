<?php

namespace App\Observers;

use App\Models\UserCourseProgress;
use App\Interfaces\Services\Api\NotificationServiceInterface;

class UserCourseProgressObserver
{
    protected $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the UserCourseProgress "updated" event.
     */
    public function updated(UserCourseProgress $progress): void
    {
        // Eğer ilerleme %100'e ulaştıysa ve daha önce %100 değilse
        if ($progress->progress == 100 && $progress->getOriginal('progress') < 100) {
            // Kurs tamamlama bildirimi gönder
            $this->notificationService->sendCourseCompletionNotification(
                $progress->user_id,
                $progress->course->name ?? 'Kurs'
            );
        }
    }
} 