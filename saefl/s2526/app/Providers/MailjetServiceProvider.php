<?php

namespace App\Providers;

use App\Services\MailjetService;
use Illuminate\Support\ServiceProvider;

class MailjetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mailjet', function () {
            return new MailjetService();
        });
    }

    public function boot()
    {
        //
    }
}