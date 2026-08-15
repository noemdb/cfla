<?php

namespace App\Livewire\Admin\Binnacle;

use App\Models\User;
use App\Services\Binnacle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class UserActivityTimeline extends Component
{
    #[Url]
    public ?int $userId = null;

    #[Url(as: 'from')]
    public ?string $dateFrom = null;

    #[Url(as: 'to')]
    public ?string $dateTo = null;

    #[Url]
    public ?string $eventType = null;

    #[Url]
    public ?string $category = null;

    #[Url]
    public ?string $severity = null;

    #[Url]
    public ?string $search = '';

    public ?string $userSearch = '';

    /** Rango de días a visualizar (7 por defecto). */
    public int $rangeDays = 7;

    /** Modo "mi actividad": bloquea la consulta al usuario autenticado. */
    public bool $selfMode = false;

    public const DATE_RANGES = [
        7 => 'Últimos 7 días',
        15 => 'Últimos 15 días',
        30 => 'Últimos 30 días',
        90 => 'Últimos 3 meses',
        0 => 'Todos los eventos',
    ];

    public function mount(): void
    {
        if (request()->routeIs('admin.binnacle.mi-actividad')) {
            $this->selfMode = true;
            $this->userId = (int) auth()->id();
        }

        if ($this->dateFrom === null) {
            $this->applyDateRange();
        }
    }

    public const EVENT_TYPES = [
        'user_login' => 'Inicio de sesión',
        'user_logout' => 'Cierre de sesión',
        'model_created' => 'Registro creado',
        'model_updated' => 'Registro actualizado',
        'model_deleted' => 'Registro eliminado',
        'model_viewed' => 'Consulta de registro',
        'access' => 'Acceso a ruta',
        'binnacle_accessed' => 'Acceso al panel',
        'exception_thrown' => 'Excepción',
        'info_probe' => 'Sonda de sistema',
    ];

    public const CATEGORIES = [
        'system' => 'Sistema',
        'security' => 'Seguridad',
        'user_action' => 'Acción de usuario',
        'authentication' => 'Autenticación',
        'error' => 'Error',
    ];

    public const SEVERITIES = [
        'debug' => 'debug',
        'info' => 'info',
        'warning' => 'warning',
        'alert' => 'alert',
        'critical' => 'critical',
    ];

    public function selectUser(int $id): void
    {
        $this->userId = $id;
        $this->userSearch = '';
    }

    public function applyDateRange(): void
    {
        if ($this->rangeDays <= 0) {
            $this->dateFrom = null;
            $this->dateTo = null;

            return;
        }

        $this->dateFrom = now()->subDays($this->rangeDays)->startOfDay()->format('Y-m-d H:i:s');
        $this->dateTo = now()->endOfDay()->format('Y-m-d H:i:s');
    }

    public function updatedRangeDays(int $value): void
    {
        $this->rangeDays = $value;
        $this->applyDateRange();
    }

    public function clearFilters(): void
    {
        $this->rangeDays = 7;
        $this->applyDateRange();
        $this->eventType = null;
        $this->category = null;
        $this->severity = null;
        $this->search = '';
    }

    public function render()
    {
        if ($this->selfMode) {
            $this->userId = (int) auth()->id();
            $this->userSearch = '';
        }

        $candidates = User::query()
            ->when($this->userSearch, function ($q) {
                $needle = '%'.$this->userSearch.'%';
                $q->where(fn ($sub) => $sub
                    ->where('username', 'like', $needle)
                    ->orWhere('email', 'like', $needle));
            })
            ->orderBy('username')
            ->limit(10)
            ->get(['id', 'username', 'email']);

        $entries = $this->userId
            ? Binnacle::getUserActivityTimeline($this->userId, $this->dateFrom, $this->dateTo, [
                'event_type' => $this->eventType,
                'category' => $this->category,
                'severity' => $this->severity,
                'search' => $this->search,
            ])
            : collect();

        $grouped = $entries->groupBy(fn ($e) => $e->created_at?->toDateString());

        $hasFilters = $this->rangeDays !== 7
            || (bool) ($this->eventType || $this->category || $this->severity || $this->search);

        return view('livewire.admin.binnacle.user-activity-timeline', [
            'candidates' => $candidates,
            'selected' => $this->userId ? User::find($this->userId) : null,
            'entries' => $entries,
            'grouped' => $grouped,
            'hasFilters' => $hasFilters,
        ]);
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
