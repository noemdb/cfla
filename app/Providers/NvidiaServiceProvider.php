<?php

namespace App\Providers;

use App\Services\NvidiaService;
use Illuminate\Support\ServiceProvider;

class NvidiaServiceProvider extends ServiceProvider
{
    /**
     * Register the NVIDIA service as a singleton.
     */
    public function register(): void
    {
        $this->app->singleton(NvidiaService::class, function ($app) {
            return new NvidiaService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
