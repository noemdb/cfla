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

    public int $perPage = 50;

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
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'severity', 'dateFrom', 'dateTo']);
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
        $entries = $this->query()->paginate($this->perPage);

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

    public function query()
    {
        return BinnacleEntry::query()
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
            ->when($this->dateTo, fn ($q) => $q->where('created_at', '<=', $this->dateTo.' 23:59:59'))
            ->orderByDesc('created_at');
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
