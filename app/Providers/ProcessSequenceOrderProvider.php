<?php

namespace App\Providers;

use App\Interfaces\ProcessSequenceOrderInterface;
use App\Repositories\ProcessSequenceOrderRepository;
use Illuminate\Support\ServiceProvider;

class ProcessSequenceOrderProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProcessSequenceOrderInterface::class, ProcessSequenceOrderRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
