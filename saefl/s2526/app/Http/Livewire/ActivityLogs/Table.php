<?php

namespace App\Http\Livewire\ActivityLogs;

use App\Models\sys\Rol;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Table extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-5';

    public $start_date;
    public $end_date;
    public $url;
    public $user_id;
    public $selectedArea = '';
    public $selectedRol = '';
    public $roleSearch = '';
    public $roleStatus = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 25;
    public $selectedActivity = null;
    public $showDetails = false;

    protected $listeners = [
        'select2AreaChanged' => 'onAreaChanged',
        'select2RolChanged' => 'onRolChanged',
        'select2UserChanged' => 'onUserChanged',
        'refreshTableData' => '$refresh'
    ];

    protected $queryString = [
        'start_date' => ['except' => ''],
        'end_date' => ['except' => ''],
        'url' => ['except' => ''],
        'user_id' => ['except' => ''],
        'selectedArea' => ['except' => ''],
        'selectedRol' => ['except' => ''],
        'roleSearch' => ['except' => ''],
        'roleStatus' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function updatingUrl()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['start_date', 'end_date', 'url', 'user_id', 'selectedArea', 'selectedRol', 'roleSearch', 'roleStatus', 'sortField', 'sortDirection']);
        $this->resetPage();
    }

    public function showDetails($activityId)
    {
        $this->selectedActivity = Activity::with('causer')->find($activityId);
        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedActivity = null;
    }

    public function getIpAddress($activity)
    {
        return $activity->properties->get('ip_address') ??
            $activity->properties->get('ip') ??
            $activity->getExtraProperty('ip_address') ??
            $activity->getExtraProperty('ip') ?? '-';
    }

    public function onAreaChanged($value)
    {
        $this->selectedArea = $value;
        $this->resetPage();
    }

    public function onRolChanged($value)
    {
        $this->selectedRol = $value;
        $this->resetPage();
    }

    public function onUserChanged($value)
    {
        $this->user_id = $value;
        $this->resetPage();
    }

    public function render()
    {
        $query = Activity::query()
            ->with(['causer' => function ($query) {
                $query->with(['rols' => function ($query) {
                    $query->where('ffinal', '>=', now())
                        ->where('finicial', '<=', now())
                        ->orderBy('created_at', 'desc')
                        ->limit(1);
                }]);
            }])
            ->when($this->roleStatus === 'with_roles', function ($query) {
                return $query->whereHas('causer.rols', function ($q) {
                    $q->where('ffinal', '>=', now())
                        ->where('finicial', '<=', now());
                });
            })
            ->when($this->roleStatus === 'without_roles', function ($query) {
                return $query->whereDoesntHave('causer.rols', function ($q) {
                    $q->where('ffinal', '>=', now())
                        ->where('finicial', '<=', now());
                });
            })
            ->when($this->start_date, function ($query) {
                return $query->whereDate('created_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('created_at', '<=', $this->end_date);
            })
            ->when($this->url, function ($query) {
                return $query->where('properties->route', 'like', '%' . $this->url . '%');
            })
            ->when($this->user_id, function ($query) {
                return $query->where('causer_id', $this->user_id);
            })
            ->when($this->selectedArea || $this->selectedRol, function ($query) {
                return $query->whereHas('causer.rols', function ($q) {
                    $q->where('ffinal', '>=', now())
                        ->where('finicial', '<=', now());
                    if ($this->selectedArea) {
                        $q->where('area', $this->selectedArea);
                    }
                    if ($this->selectedRol) {
                        $q->where('rol', $this->selectedRol);
                    }
                });
            });

        // Ordenamiento personalizado para la columna de rol
        if ($this->sortField === 'role') {
            $query->orderBy(function ($query) {
                $query->select('rol')
                    ->from('rols')
                    ->whereColumn('rols.user_id', 'activity_log.causer_id')
                    ->where('ffinal', '>=', now())
                    ->where('finicial', '<=', now())
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            }, $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $activities = $query->paginate($this->perPage);

        // Cargar solo usuarios activos que han realizado actividades
        $filteredUsers = User::query()
            ->whereHas('activityLogs')
            ->orderBy('username')
            ->get();

        $filteredRoles = Rol::query()
            ->where('ffinal', '>=', now())
            ->where('finicial', '<=', now())
            ->when($this->roleSearch, function ($query) {
                return $query->where(function ($q) {
                    $q->where('rol', 'like', '%' . $this->roleSearch . '%')
                        ->orWhere('rol', 'like', '%' . $this->roleSearch . '%');
                });
            })
            ->orderBy('area')
            ->orderBy('rol')
            ->groupBy('rol', 'area')
            ->get();

        $areas = $filteredRoles->pluck('area')->unique()->values();
        $roles = $filteredRoles->pluck('rol')->unique()->values();

        return view('livewire.activity-logs.table', [
            'activities' => $activities,
            'filteredUsers' => $filteredUsers,
            'filteredRoles' => $filteredRoles,
            'areas' => $areas,
            'roles' => $roles
        ]);
    }
}
