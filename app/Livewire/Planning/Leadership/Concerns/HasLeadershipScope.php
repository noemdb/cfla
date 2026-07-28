<?php

namespace App\Livewire\Planning\Leadership\Concerns;

use App\Services\Planning\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

trait HasLeadershipScope
{
    protected LeadershipService $leadershipService;

    public function initializeHasLeadershipScope()
    {
        $this->leadershipService = app(LeadershipService::class, [
            'user' => Auth::user()
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
