<?php

namespace App\Livewire;
// app/Livewire/VisitsDashboard.php

use App\Models\Visit;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class VisitsDashboard extends Component
{
    use WithPagination, WireUiActions;

    public $perPage = 5;
    public $search = '';
    public $daysFilter = 7;
    public $deviceType = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'daysFilter' => ['except' => 7],
        'deviceType' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'daysFilter', 'deviceType']);
    }

    protected function getQuery()
    {
        return Visit::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('url', 'like', '%' . $this->search . '%')
                        ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->daysFilter, function ($query) {
                $query->where('created_at', '>=', now()->subDays($this->daysFilter));
            })
            ->when($this->deviceType, function ($query) {
                $query->where('device_type', $this->deviceType);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->with('user');
    }

    public function render()
    {
        $visits = $this->getQuery()->paginate($this->perPage);

        $stats = [
            'total' => $this->getQuery()->count(),
            'unique' => $this->getQuery()->distinct('ip_address')->count('ip_address'),
            'mobile' => $this->getQuery()->clone()->where('device_type', 'mobile')->count(),
        ];

        return view('livewire.visits-dashboard', [
            'visits' => $visits,
            'stats' => $stats,
            'deviceTypes' => ['mobile', 'desktop', 'tablet'],
        ]);
    }
}
