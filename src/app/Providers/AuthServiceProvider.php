<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\Services\Auth\AuthServiceInterface;
use App\Services\Auth\AuthService;
use App\Interfaces\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Auth\AuthRepository;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
                // AuthService ve AuthRepository'yi container'a bağlama
                $this->app->bind(AuthServiceInterface::class, function ($app) {
                    return new AuthService(
                        $app->make(AuthRepositoryInterface::class),
                        $app->make(\App\Services\Auth\VerificationService::class)
                    );
                });
                $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);


                $this->app->bind(
                    \App\Interfaces\Repositories\Auth\VerificationRepositoryInterface::class,
                    \App\Repositories\Auth\VerificationRepository::class
                );

                $this->app->bind(
                    \App\Interfaces\Services\Auth\VerificationServiceInterface::class,
                    \App\Services\Auth\VerificationService::class
                );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
