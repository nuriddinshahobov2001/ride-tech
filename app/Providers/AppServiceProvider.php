<?php

namespace App\Providers;

use App\Repositories\Interfaces\CarRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\CarRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        CarRepositoryInterface::class => CarRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
