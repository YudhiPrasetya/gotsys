<?php

namespace App\Providers;

use App\Interfaces\ProcessSequenceInterface;
use App\Repositories\ProcessSequenceRepository;
use Illuminate\Support\ServiceProvider;

class ProcessSequenceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProcessSequenceInterface::class, ProcessSequenceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
