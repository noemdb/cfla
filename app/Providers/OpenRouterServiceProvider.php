<?php

namespace App\Providers;

use App\Services\OpenRouterService;
use Illuminate\Support\ServiceProvider;

class OpenRouterServiceProvider extends ServiceProvider
{
    /**
     * Register the OpenRouter service as a singleton.
     */
    public function register(): void
    {
        $this->app->singleton(OpenRouterService::class, function ($app) {
            return new OpenRouterService();
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
