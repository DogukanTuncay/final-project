<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\LessonCompleted;
use App\Listeners\UpdateMissionProgressOnLessonCompletion;
use App\Events\ChapterCompleted;
use App\Events\CourseCompleted;
use App\Listeners\SendEmailVerificationNotification;
use App\Listeners\UpdateMissionProgressOnChapterCompletion;
use App\Listeners\UpdateMissionProgressOnCourseCompletion;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string|string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // ... diğer event/listener eşleşmeleri ...

        // Mission Progress Listeners
        \App\Events\LessonCompleted::class => [
            \App\Listeners\UpdateMissionProgressOnLessonCompletion::class,
            \App\Listeners\CheckBadgeEligibilityListener::class,
        ],
        \App\Events\ChapterCompleted::class => [
            \App\Listeners\UpdateMissionProgressOnChapterCompletion::class,
            \App\Listeners\CheckBadgeEligibilityListener::class,
        ],
        \App\Events\CourseCompleted::class => [
            \App\Listeners\UpdateMissionProgressOnCourseCompletion::class,
            \App\Listeners\CheckBadgeEligibilityListener::class,
        ],
        \App\Events\MissionCompleted::class => [
            \App\Listeners\HandleMissionCompleted::class,
        ],
        \App\Events\UserLoggedIn::class => [
            \App\Listeners\CheckBadgeEligibilityListener::class,
        ],
        \App\Events\BadgeEarned::class => [
            // \App\Listeners\SendBadgeNotification::class, // İleride rozet bildirimlerini ekleyebilirsiniz
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        // UserCourseProgress modeli için observer kaydı
        \App\Models\UserCourseProgress::observe(\App\Observers\UserCourseProgressObserver::class);
    }
} 