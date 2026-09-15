<?php

namespace App\Livewire\Admin\Binnacle;

use App\Models\BinnacleEntry;
use App\Services\Binnacle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;

    public $paginate = 50;

    public ?int $viewingEntryId = null;

    public bool $showEntryDetails = false;

    #[Url(as: 'q', history: true)]
    public ?string $search = null;

    #[Url(history: true)]
    public ?string $category = null;

    #[Url(history: true)]
    public ?string $severity = null;

    #[Url(as: 'from', history: true)]
    public ?string $dateFrom = null;

    #[Url(as: 'to', history: true)]
    public ?string $dateTo = null;

    /**
     * Rango del chart de actividad de la bitácora (dropdown).
     */
    #[Url(as: 'range', history: true)]
    public string $chartRange = '7d';

    /**
     * Serie del chart: [['x' => 'Y-m-d', 'y' => n], …].
     */
    public array $chartEntries = [];

    /**
     * Total de registros en el rango seleccionado (contador en vivo).
     */
    public int $chartTotal = 0;

    public function mount(): void
    {
        // Meta-auditoría (Spec §6 / Fase 4): quién consulta la bitácora.
        if (config('binnacle.meta_audit', true)) {
            Binnacle::log('binnacle_accessed', [
                'title' => 'Consulta al panel de bitácora',
                'category' => 'security',
                'severity' => 'info',
                'subject' => auth()->user(),
            ]);
        }

        $this->loadChartData();
    }

    public function updatedChartRange(): void
    {
        $this->loadChartData();
    }

    /**
     * Refresco en tiempo real del chart (wire:poll).
     */
    public function refreshChart(): void
    {
        $this->loadChartData();
    }

    /**
     * Cuenta los registros de la bitácora agrupados por día para el rango
     * seleccionado, aplicando los mismos filtros de la tabla (búsqueda,
     * categoría, severidad y fechas). Alimenta el chart ApexCharts del panel.
     */
    private function loadChartData(): void
    {
        $since = match ($this->chartRange) {
            '7d' => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '3m' => now()->subMonths(3)->startOfDay(),
            'all' => null,
            default => now()->subDays(7)->startOfDay(),
        };

        $query = $this->applyFilters(BinnacleEntry::query())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date');

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        $this->chartEntries = $query->get()->map(fn ($row) => [
            'x' => $row->date,
            'y' => (int) $row->total,
        ])->toArray();

        $countQuery = $this->applyFilters(BinnacleEntry::query());

        if ($since) {
            $countQuery->where('created_at', '>=', $since);
        }

        $this->chartTotal = $countQuery->count();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'severity', 'dateFrom', 'dateTo']);
        $this->resetPage();
        $this->loadChartData();
    }

    public function updatingPaginate(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSeverity(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * Recalcula el chart cuando cambia cualquier filtro de la tabla.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'category', 'severity', 'dateFrom', 'dateTo'], true)) {
            $this->loadChartData();
        }
    }

    public function openEntryDetails(int $entryId): void
    {
        $this->viewingEntryId = $entryId;
        $this->showEntryDetails = true;
    }

    public function closeEntryDetails(): void
    {
        $this->showEntryDetails = false;
        $this->viewingEntryId = null;
    }

    public function render()
    {
        $entries = $this->query()->paginate($this->paginate);

        $meta = [
            'categories' => BinnacleEntry::query()
                ->select('event_category')
                ->distinct()
                ->orderBy('event_category')
                ->pluck('event_category'),
            'severities' => BinnacleEntry::query()
                ->select('event_severity')
                ->distinct()
                ->orderByRaw("FIELD(event_severity, 'critical','alert','warning','info','debug')")
                ->pluck('event_severity'),
        ];

        return view('livewire.admin.binnacle.index-component', [
            'entries' => $entries,
            'meta' => $meta,
            'viewingEntry' => $this->viewingEntryId
                ? BinnacleEntry::find($this->viewingEntryId)
                : null,
        ]);
    }

    /**
     * Aplica los filtros comunes (búsqueda, categoría, severidad y fechas).
     * Fuente única para la tabla (`query()`) y el chart (`loadChartData()`).
     */
    private function applyFilters($query)
    {
        return $query
            ->when($this->search, function ($q) {
                $needle = '%'.$this->search.'%';
                $q->where(function ($sub) use ($needle) {
                    $sub->where('title', 'like', $needle)
                        ->orWhere('description', 'like', $needle)
                        ->orWhere('subject_identifier', 'like', $needle)
                        ->orWhere('object_identifier', 'like', $needle)
                        ->orWhere('ip_address', 'like', $needle)
                        ->orWhere('request_url', 'like', $needle)
                        ->orWhere('request_id', 'like', $needle);
                });
            })
            ->when($this->category, fn ($q) => $q->where('event_category', $this->category))
            ->when($this->severity, fn ($q) => $q->where('event_severity', $this->severity))
            ->when($this->dateFrom, fn ($q) => $q->where('created_at', '>=', $this->dateFrom.' 00:00:00'))
            ->when($this->dateTo, fn ($q) => $q->where('created_at', '<=', $this->dateTo.' 23:59:59'));
    }

    public function query()
    {
        return $this->applyFilters(BinnacleEntry::query())
            ->orderByDesc('created_at');
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
