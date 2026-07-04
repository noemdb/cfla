<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BootstrapMarkdownService;

class MarkdownServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('markdown.parser', function ($app) {
            return new BootstrapMarkdownService();
        });
        
        // O registrar como binding con nombre
        $this->app->bind('bootstrap.markdown', function ($app) {
            return new BootstrapMarkdownService();
        });
        
        // Para inyección de dependencias automática
        $this->app->bind(BootstrapMarkdownService::class, function ($app) {
            return new BootstrapMarkdownService(
                config('markdown.bootstrap_version', '4.3'),
                config('markdown.default_options', [])
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function registerMarkdownService()
    {
        // Configuración adicional si es necesaria
    }
}