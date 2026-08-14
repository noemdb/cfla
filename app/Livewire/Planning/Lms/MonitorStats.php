<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Activity;
use App\Services\Lms\LmsPublicationService;
use Illuminate\Support\Facades\Cache;
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

    /**
     * Recalcula los stats. Se cachean en una sola fila (TTL = poll interval)
     * para que el `wire:poll` de cada página no lance 7 counts a la DB por
     * tick; se invalidan desde el punto central (LmsPublicationService).
     */
    public function refreshStats(): void
    {
        $stats = Cache::remember(
            LmsPublicationService::MONITOR_STATS_CACHE_KEY,
            LmsPublicationService::cacheTtlSeconds(),
            function (): array {
                return [
                    'total' => Activity::count(),
                    'published' => Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'PUBLISHED'))->count(),
                    'scheduled' => Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))->count(),
                    'draft' => Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'DRAFT'))->count(),
                    'archived' => Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'ARCHIVED'))->count(),
                    'withContent' => Activity::whereHas('lmsSections')->count(),
                    'totalActivities' => Activity::count(),
                ];
            }
        );

        $this->total = $stats['total'];
        $this->published = $stats['published'];
        $this->scheduled = $stats['scheduled'];
        $this->draft = $stats['draft'];
        $this->archived = $stats['archived'];
        $this->withContent = $stats['withContent'];
        $this->totalActivities = $stats['totalActivities'];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.planning.lms.monitor-stats');
    }
}
