<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Auth\AuthRepository;
use App\Models\User;
use App\Observers\UserObserver;
use App\Interfaces\Services\Api\NotificationServiceInterface;
use App\Services\Api\NotificationService;
use App\Services\Api\EventService;
use App\Services\Api\MissionProgressService;
/* use App\Interfaces\Repositories\Api\UserNotificationLogRepositoryInterface;
use App\Interfaces\Services\Api\UserNotificationLogServiceInterface;
use App\Repositories\Api\UserNotificationLogRepository;
use App\Services\Api\UserNotificationLogService;
*/
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {


        $this->app->bind(AuthRepository::class, function ($app) {
            return new AuthRepository();
        });

        // UserExperience Service için binding
        $this->app->bind(
            \App\Interfaces\Services\Api\UserExperienceServiceInterface::class,
            \App\Services\Api\UserExperienceService::class
        );

        // FillInTheBlank Service bağlantısı
        $this->app->bind(
            \App\Services\Interfaces\FillInTheBlankServiceInterface::class,
            \App\Services\FillInTheBlankService::class
        );

        // Notification Service bağlama
        $this->app->bind(
            NotificationServiceInterface::class,
            NotificationService::class
        );

        $this->app->singleton(EventService::class);
        $this->app->singleton(MissionProgressService::class);

  /*      // UserNotificationLog interface bağlamaları (bindings)
        $this->app->bind(UserNotificationLogRepositoryInterface::class, UserNotificationLogRepository::class);
        $this->app->bind(UserNotificationLogServiceInterface::class, UserNotificationLogService::class);
    */
        }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        
        // User modelini UserObserver ile gözlemle
        User::observe(UserObserver::class);
    }
}
