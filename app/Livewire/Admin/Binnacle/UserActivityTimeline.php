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

    public function clearFilters(): void
    {
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->eventType = null;
        $this->category = null;
        $this->severity = null;
        $this->search = '';
    }

    public function render()
    {
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

        $hasFilters = (bool) ($this->dateFrom || $this->dateTo || $this->eventType || $this->category || $this->severity || $this->search);

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
