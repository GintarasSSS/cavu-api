<?php

namespace App\Providers;

use App\Interfaces\AuthenticationRepositoryInterface;
use App\Interfaces\BookingRepositoryInterface;
use App\Repositories\AuthenticationRepository;
use App\Repositories\BookingRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(AuthenticationRepositoryInterface::class, AuthenticationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
