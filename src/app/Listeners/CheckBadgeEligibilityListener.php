<?php

namespace App\Listeners;

use App\Interfaces\Services\Api\BadgeServiceInterface;
use App\Events\MissionCompleted;
use App\Events\LessonCompleted;
use App\Events\CourseCompleted;
use App\Events\ChapterCompleted;
use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckBadgeEligibilityListener implements ShouldQueue
{
    use InteractsWithQueue;
    
    /**
     * Badge servisi
     */
    protected BadgeServiceInterface $badgeService;
    
    /**
     * Create the event listener.
     */
    public function __construct(BadgeServiceInterface $badgeService)
    {
        $this->badgeService = $badgeService;
    }
    
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        // Olayın türüne göre kullanıcı alınır
        $user = $this->getUserFromEvent($event);
        
        if ($user) {
            // Rozet kontrol işlemini gerçekleştir
            $this->badgeService->checkAndAwardBadges($user);
        }
    }
    
    /**
     * Olay tipine göre kullanıcıyı al
     */
    protected function getUserFromEvent($event)
    {
        // Mission tamamlama olayı
        if ($event instanceof MissionCompleted) {
            return $event->user;
        }
        
        // Lesson tamamlama olayı
        if ($event instanceof LessonCompleted) {
            return $event->user;
        }
        
        // Course tamamlama olayı
        if ($event instanceof CourseCompleted) {
            return $event->user;
        }
        
        // Chapter tamamlama olayı
        if ($event instanceof ChapterCompleted) {
            return $event->user;
        }
        
        // Kullanıcı giriş olayı
        if ($event instanceof UserLoggedIn) {
            return $event->user;
        }
        
        return null;
    }
} 