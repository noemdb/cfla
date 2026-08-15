<?php

namespace App\Livewire\Admin\Binnacle;

use App\Models\BinnacleEntry;
use App\Services\Binnacle;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class DashboardComponent extends Component
{
    #[Url]
    public ?int $days = 30;

    public function render()
    {
        $since = now()->subDays($this->days);

        $byCategory = BinnacleEntry::query()
            ->where('created_at', '>=', $since)
            ->select('event_category', DB::raw('count(*) as total'))
            ->groupBy('event_category')
            ->orderByDesc('total')
            ->get();

        $bySeverity = BinnacleEntry::query()
            ->where('created_at', '>=', $since)
            ->select('event_severity', DB::raw('count(*) as total'))
            ->groupBy('event_severity')
            ->orderByRaw("FIELD(event_severity, 'critical','alert','warning','info','debug')")
            ->get();

        $topActors = BinnacleEntry::query()
            ->where('created_at', '>=', $since)
            ->whereNotNull('subject_identifier')
            ->select('subject_identifier', DB::raw('count(*) as total'))
            ->groupBy('subject_identifier')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return view('livewire.admin.binnacle.dashboard-component', [
            'metrics' => [
                'total' => BinnacleEntry::count(),
                'today' => BinnacleEntry::where('created_at', '>=', now()->startOfDay())->count(),
                'lastDays' => BinnacleEntry::where('created_at', '>=', $since)->count(),
                'archived' => DB::table('binnacle_entries_archive')->count(),
            ],
            'byCategory' => $byCategory,
            'bySeverity' => $bySeverity,
            'topActors' => $topActors,
            'recentCritical' => BinnacleEntry::whereIn('event_severity', ['critical', 'alert'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
            'integrity' => Binnacle::verifyChainIntegrity(),
            'severityTotal' => $bySeverity->sum('total'),
        ]);
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
