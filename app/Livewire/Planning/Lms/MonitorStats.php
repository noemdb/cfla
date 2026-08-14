<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Activity;
use Livewire\Component;

/**
 * Grid de estadísticas del monitor LMS (Total, Publicadas, Programadas, …).
 *
 * Se actualiza en tiempo real vía broadcast (Reverb) cuando un profesor
 * programa una lección (`lesson.scheduled`), y usa `wire:poll` como fallback
 * si el WebSocket está caído (blueprint Opción 6 + Opción 3).
 */
class MonitorStats extends Component
{
    public int $total = 0;

    public int $published = 0;

    public int $scheduled = 0;

    public int $draft = 0;

    public int $archived = 0;

    public int $withContent = 0;

    public int $totalActivities = 0;

    protected function getListeners(): array
    {
        return [
            'echo-private:App.Models.User.'.auth()->id().',.lesson.scheduled' => 'refreshStatsFromEcho',
        ];
    }

    public function refreshStatsFromEcho(): void
    {
        $this->refreshStats();
    }

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        $this->total = Activity::count();
        $this->published = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'PUBLISHED'))->count();
        $this->scheduled = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))->count();
        $this->draft = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'DRAFT'))->count();
        $this->archived = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'ARCHIVED'))->count();
        $this->withContent = Activity::whereHas('lmsSections')->count();
        $this->totalActivities = Activity::count();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.planning.lms.monitor-stats');
    }
}
