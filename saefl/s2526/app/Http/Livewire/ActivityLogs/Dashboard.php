<?php

namespace App\Http\Livewire\ActivityLogs;

use Livewire\Component;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use App\Models\sys\Rol;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $start_date;
    public $end_date;
    public $selectedArea = '';
    public $selectedRol = '';
    public $today;
    public $limit = 6;
    public $url;

    protected $queryString = [
        'start_date' => ['except' => ''],
        'end_date' => ['except' => ''],
        'selectedArea' => ['except' => ''],
        'selectedRol' => ['except' => ''],
        'url' => ['except' => ''],
    ];

    public function mount()
    {
        // Obtener la fecha del primer registro
        $firstActivity = Activity::orderBy('created_at', 'asc')->first();
        $this->start_date = $firstActivity ? $firstActivity->created_at->format('Y-m-d') : now()->format('Y-m-d');

        // Establecer fecha final como hoy
        $this->end_date = now()->format('Y-m-d');
        $this->today = Carbon::now()->toDateString();
    }

    public function getStats()
    {
        $baseQuery = Activity::query()
            ->whereDate('activity_log.created_at', '>=', $this->start_date)
            ->whereDate('activity_log.created_at', '<=', $this->end_date)
            ->whereNotNull('causer_id')
            ->where('causer_type', 'App\\User')
            ->when($this->url, function ($query) {
                return $query->where('properties->route', 'like', '%' . $this->url . '%');
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

        // Activities by day with area/role filters
        $activitiesByDay = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();

        // Activities by hour for today
        $activitiesByHour = (clone $baseQuery)
            ->whereDate('created_at', now())
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupByRaw('HOUR(created_at)')
            ->orderByRaw('HOUR(created_at)')
            ->get();

        // Calculate total activities as sum of daily activities
        $totalActivities = $activitiesByDay->sum('count');

        // Get Top URLs with filters
        $topUrls = (clone $baseQuery)
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(properties, "$.route")) as url, COUNT(*) as count')
            ->whereRaw('JSON_EXTRACT(properties, "$.route") IS NOT NULL')
            ->whereRaw('JSON_TYPE(JSON_EXTRACT(properties, "$.route")) != "NULL"')
            ->groupBy('url')
            ->orderByDesc('count')
            ->limit($this->limit)
            ->get()
            ->map(function ($item) {
                return [
                    'url' => $item->url,
                    'count' => $item->count
                ];
            });

        // Get most accessed URL
        $mostAccessedUrl = $topUrls->first();

        // Get Top Users with filters - Optimizado con join
        $topUsers = (clone $baseQuery)
            ->join('users', 'activity_log.causer_id', '=', 'users.id')
            ->select('users.username', DB::raw('COUNT(*) as count'))
            ->where('users.id', '!=', 1)
            ->groupBy('users.username')
            ->orderByDesc('count')
            ->limit($this->limit)
            ->get()
            ->map(function ($item) {
                return [
                    'username' => $item->username,
                    'count' => $item->count
                ];
            });

        // Get most active user
        $mostActiveUser = $topUsers->first();

        $stats = [
            'totalActivities' => $totalActivities,
            'activitiesByDay' => $activitiesByDay,
            'activitiesByHour' => $activitiesByHour,
            'topUrls' => $topUrls,
            'mostAccessedUrl' => $mostAccessedUrl ? (object) [
                'url' => $mostAccessedUrl['url'],
                'count' => $mostAccessedUrl['count']
            ] : null,
            'topUsers' => $topUsers,
            'mostActiveUser' => $mostActiveUser ? (object) [
                'username' => $mostActiveUser['username'],
                'count' => $mostActiveUser['count']
            ] : null,
        ];

        // Emitir los datos actualizados
        $this->emit('topUsersDataUpdated', $stats['topUsers']);
        $this->emit('activitiesByDayDataUpdated', $stats['activitiesByDay']);
        $this->emit('activitiesByHourDataUpdated', $stats['activitiesByHour']);
        $this->emit('topUrlsDataUpdated', $stats['topUrls']);

        return $stats;
    }

    public function updatedStartDate()
    {
        $this->getStats();
    }

    public function updatedEndDate()
    {
        $this->getStats();
    }

    public function updatedSelectedArea()
    {
        $this->getStats();
    }

    public function updatedSelectedRol()
    {
        $this->getStats();
    }

    protected $listeners = [
        'refreshData' => 'getStats',
        'refreshDashboardData' => 'getStats'
    ];

    public function render()
    {
        $stats = $this->getStats();

        $filteredRoles = Rol::query()
            ->where('ffinal', '>=', now())
            ->where('finicial', '<=', now())
            ->orderBy('area')
            ->orderBy('rol')
            ->groupBy('rol', 'area')
            ->get();

        $areas = $filteredRoles->pluck('area')->unique()->values();
        $roles = $filteredRoles->pluck('rol')->unique()->values();

        return view('livewire.activity-logs.dashboard', [
            'stats' => $stats,
            'areas' => $areas,
            'roles' => $roles,
        ]);
    }
}