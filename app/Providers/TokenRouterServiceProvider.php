<?php

namespace App\Providers;

use App\Services\TokenRouterService;
use Illuminate\Support\ServiceProvider;

class TokenRouterServiceProvider extends ServiceProvider
{
    /**
     * Register the TokenRouter service as a singleton.
     */
    public function register(): void
    {
        $this->app->singleton(TokenRouterService::class, function ($app) {
            return new TokenRouterService;
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
