<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Comentar o remover cualquier política de VotingPoll por ahora
        // 'App\Models\VotingPoll' => 'App\Policies\VotingPollPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir gates si es necesario
        // Gate::define('manage-polls', function ($user) {
        //     return true; // Por ahora permitir a todos
        // });
    }
}
