<?php

namespace App\Livewire\Leadership\Concerns;

use App\Services\Leadership\LeadershipService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait HasLeadershipScope
{
    protected LeadershipService $leadershipService;

    public function initializeHasLeadershipScope()
    {
        $this->leadershipService = app(LeadershipService::class, [
            'user' => Auth::user(),
        ]);
    }

    protected function getAssignedAreaIds(): Collection
    {
        return $this->leadershipService->getAssignedAreaIds();
    }

    protected function getAssignedAsignaturaIds(): Collection
    {
        return $this->leadershipService->getAssignedAsignaturaIds();
    }
}
