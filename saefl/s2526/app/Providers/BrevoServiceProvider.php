<?php

namespace App\Providers;

use App\Services\BrevoService;
use Illuminate\Support\ServiceProvider;

class BrevoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\BrevoService::class, function ($app) {
            return new BrevoService();
        });
    }

    public function provides()
    {
        return [BrevoService::class];
    }
}
