<?php

namespace App\Livewire\Admin\Binnacle;

use App\Models\BinnacleEntry;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Log de URLs visitadas por los usuarios, a partir de las entradas de la
 * bitácora (`event_type = 'access'`). No crea modelos nuevos: reutiliza
 * `BinnacleEntry` y agrega en memoria.
 */
class UrlLogComponent extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $range = '7d';

    #[Url(history: true)]
    public ?string $search = null;

    /** Filas por página del listado (mismo control que /admin/users). */
    public $paginate = 15;

    /** Datos en vivo: si está activo, el chart se refresca con wire:poll. */
    public bool $live = true;

    /**
     * Serie del chart: [['x' => '/ruta', 'y' => visitas], …] (top N).
     */
    public array $chartData = [];

    public const RANGES = [
        '24h' => 'Últimas 24 horas',
        '7d' => 'Últimos 7 días',
        '30d' => 'Últimos 30 días',
        '3m' => 'Últimos 3 meses',
        'all' => 'Todo el histórico',
    ];

    /** Máximo de URLs mostradas en el chart. */
    public const CHART_LIMIT = 15;

    public function updatingPaginate(): void
    {
        $this->resetPage();
    }

    public function updatedRange(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->range = '7d';
        $this->search = null;
        $this->resetPage();
    }

    public function render()
    {
        $since = match ($this->range) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '3m' => now()->subMonths(3)->startOfDay(),
            'all' => null,
            default => now()->subDays(7)->startOfDay(),
        };

        $rows = BinnacleEntry::query()
            ->where('event_type', 'access')
            ->whereNotNull('request_url')
            ->when($since, fn ($q) => $q->where('created_at', '>=', $since))
            ->when($this->search, fn ($q) => $q->where('request_url', 'like', '%'.$this->search.'%'))
            ->orderByDesc('created_at')
            ->limit(10000)
            ->get(['id', 'request_url', 'request_method', 'subject_id', 'subject_identifier', 'created_at']);

        $urls = $rows
            ->groupBy(fn (BinnacleEntry $entry): string => $this->normalizePath($entry->request_url))
            ->map(function (Collection $group, string $path): object {
                return (object) [
                    'path' => $path,
                    'visits' => $group->count(),
                    'users' => $group->pluck('subject_id')->filter()->unique()->count(),
                    'methods' => $group->pluck('request_method')->filter()->unique()->sort()->values()->all(),
                    'last_visit' => $group->max('created_at'),
                ];
            })
            ->sortByDesc('visits')
            ->values();

        $this->chartData = $urls->take(self::CHART_LIMIT)->map(fn ($url): array => [
            'x' => $url->path,
            'y' => $url->visits,
        ])->all();

        $summary = [
            'visits' => $rows->count(),
            'urls' => $urls->count(),
            'users' => $rows->pluck('subject_id')->filter()->unique()->count(),
        ];

        $page = $this->getPage();
        $perPage = max(1, (int) $this->paginate);
        $paginatedUrls = new LengthAwarePaginator(
            $urls->forPage($page, $perPage)->values(),
            $urls->count(),
            $perPage,
            $page,
            ['path' => request()->url()],
        );

        return view('livewire.admin.binnacle.url-log-component', [
            'urls' => $paginatedUrls,
            'summary' => $summary,
        ]);
    }

    /**
     * Normaliza una URL completa a su ruta (path), para agrupar visitas.
     */
    private function normalizePath(?string $url): string
    {
        if (! $url) {
            return '(sin URL)';
        }

        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? $path : '/';
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
