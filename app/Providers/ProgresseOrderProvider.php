<?php

namespace App\Providers;

use App\Interfaces\ProgressOrderInterface;
use App\Repositories\ProgressOrderRepository;
use Illuminate\Support\ServiceProvider;

class ProgresseOrderProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProgressOrderInterface::class, ProgressOrderRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
