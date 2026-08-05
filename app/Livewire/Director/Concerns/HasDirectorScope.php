<?php
// app/Livewire/Director/Concerns/HasDirectorScope.php

namespace App\Livewire\Director\Concerns;

use App\Services\Director\DirectorScopeService;
use Illuminate\Support\Facades\Auth;

trait HasDirectorScope
{
    protected DirectorScopeService $directorService;

    public function initializeHasDirectorScope(): void
    {
        $this->directorService = app(DirectorScopeService::class, [
            'user' => Auth::user()
        ]);
    }

    protected function getDirectorService(): DirectorScopeService
    {
        return $this->directorService;
    }
}
