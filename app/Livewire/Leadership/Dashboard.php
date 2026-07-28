<?php

namespace App\Livewire\Leadership;

use App\Services\Leadership\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    public array $metrics = [];
    private LeadershipService $service;

    public function mount()
    {
        $this->service = app(LeadershipService::class, [
            'user' => Auth::user()
        ]);
        $this->metrics = $this->service->dashboardMetrics();
    }

    public function render()
    {
        return view('livewire.leadership.dashboard', [
            'metrics' => $this->metrics,
        ]);
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
