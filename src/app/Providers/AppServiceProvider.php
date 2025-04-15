<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Repositories\BaseRepository;
use App\Repositories\Auth\AuthRepository;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BaseRepository::class, function ($app) {
            // BaseRepository'i User modeliyle inject et
            return new BaseRepository(new User());
        });

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
