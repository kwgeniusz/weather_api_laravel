<?php

namespace App\Providers;

use App\Services\FavoriteService;
use App\Services\HistoryService;
use App\Services\Interfaces\FavoriteServiceInterface;
use App\Services\Interfaces\HistoryServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\WeatherServiceInterface;
use App\Services\UserService;
use App\Services\WeatherService;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(WeatherServiceInterface::class, WeatherService::class);
        $this->app->bind(FavoriteServiceInterface::class, FavoriteService::class);
        $this->app->bind(HistoryServiceInterface::class, HistoryService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
