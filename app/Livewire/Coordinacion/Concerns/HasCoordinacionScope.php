<?php

namespace App\Livewire\Coordinacion\Concerns;

use App\Services\Lms\CoordinacionScopeService;
use Illuminate\Support\Facades\Auth;

trait HasCoordinacionScope
{
    protected CoordinacionScopeService $coordinacionService;

    public function initializeHasCoordinacionScope(): void
    {
        $this->coordinacionService = app(CoordinacionScopeService::class, [
            'user' => Auth::user()
        ]);
    }

    protected function getCoordinacionService(): CoordinacionScopeService
    {
        return $this->coordinacionService;
    }
}
