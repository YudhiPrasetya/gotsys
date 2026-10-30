<?php

namespace App\Providers;

use App\Interfaces\ProgressOrderDetailInterface;
use App\Repositories\ProgressOrderDetailRepository;
use Illuminate\Support\ServiceProvider;

class ProgresseOrderDetailProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProgressOrderDetailInterface::class, ProgressOrderDetailRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
