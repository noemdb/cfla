<?php

namespace App\Livewire\Admin\Notifications;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public ?string $filterType = null;

    #[Url(history: true)]
    public ?string $filterDataType = null;

    #[Url(history: true)]
    public ?string $filterRead = null; // '', 'unread', 'read'

    #[Url(history: true)]
    public ?string $filterUser = null;

    #[Url(as: 'from', history: true)]
    public ?string $dateFrom = null;

    #[Url(as: 'to', history: true)]
    public ?string $dateTo = null;

    public int $paginate = 15;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public ?string $viewingNotificationId = null;

    public bool $showDetails = false;

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterType', 'filterDataType', 'filterRead', 'filterUser', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDataType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRead(): void
    {
        $this->resetPage();
    }

    public function updatingFilterUser(): void
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

    public function updatingPaginate(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openDetails(string $id): void
    {
        $this->viewingNotificationId = $id;
        $this->showDetails = true;
    }

    public function closeDetails(): void
    {
        $this->viewingNotificationId = null;
        $this->showDetails = false;
    }

    public function markAsRead(string $id): void
    {
        $notification = DatabaseNotification::whereKey($id)->first();
        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }
    }

    public function markAsUnread(string $id): void
    {
        $notification = DatabaseNotification::whereKey($id)->first();
        if ($notification && ! is_null($notification->read_at)) {
            $notification->update(['read_at' => null]);
        }
    }

    public function deleteNotification(string $id): void
    {
        DatabaseNotification::whereKey($id)->delete();
    }

    private function applyFilters($query)
    {
        return $query
            ->when($this->search, function ($q) {
                $needle = '%'.$this->search.'%';
                $q->where(function ($sub) use ($needle) {
                    $sub->where('type', 'like', $needle)
                        ->orWhere('data', 'like', $needle)
                        ->orWhere('id', 'like', $needle)
                        ->orWhereHasMorph('notifiable', [\App\Models\User::class], function ($uq) use ($needle) {
                            $uq->where('username', 'like', $needle)
                               ->orWhere('email', 'like', $needle)
                               ->orWhereHas('profile', function ($pq) use ($needle) {
                                   $pq->where('firstname', 'like', $needle)
                                      ->orWhere('lastname', 'like', $needle);
                               });
                        });
                });
            })
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->when($this->filterDataType, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('data->type', $this->filterDataType)
                        ->orWhere('data->event_type', $this->filterDataType);
                });
            })
            ->when($this->filterRead === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($this->filterRead === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->when($this->filterUser, function ($q) {
                $needle = '%'.$this->filterUser.'%';
                $q->whereHasMorph('notifiable', [\App\Models\User::class], function ($uq) use ($needle) {
                    $uq->where('username', 'like', $needle)
                       ->orWhere('email', 'like', $needle)
                       ->orWhere('id', $this->filterUser);
                });
            })
            ->when($this->dateFrom, fn ($q) => $q->where('created_at', '>=', $this->dateFrom.' 00:00:00'))
            ->when($this->dateTo, fn ($q) => $q->where('created_at', '<=', $this->dateTo.' 23:59:59'));
    }

    public function query()
    {
        $query = DatabaseNotification::query()->with('notifiable');

        $query = $this->applyFilters($query);

        if (in_array($this->sortField, ['created_at', 'read_at', 'type'], true)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderByDesc('created_at');
        }

        return $query;
    }

    public function render()
    {
        $notifications = $this->query()->paginate($this->paginate);

        $meta = [
            'types' => DatabaseNotification::query()
                ->select('type')
                ->distinct()
                ->orderBy('type')
                ->pluck('type')
                ->filter()
                ->values(),
            'dataTypes' => $this->distinctDataTypes(),
        ];

        return view('livewire.admin.notifications.index', [
            'notifications' => $notifications,
            'meta' => $meta,
            'viewingNotification' => $this->viewingNotificationId
                ? DatabaseNotification::with('notifiable')->find($this->viewingNotificationId)
                : null,
        ]);
    }

    private function distinctDataTypes()
    {
        try {
            $types = DB::table('notifications')
                ->selectRaw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(data, '$.type')) as dtype")
                ->whereRaw("JSON_EXTRACT(data, '$.type') IS NOT NULL")
                ->pluck('dtype');

            $eventTypes = DB::table('notifications')
                ->selectRaw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(data, '$.event_type')) as dtype")
                ->whereRaw("JSON_EXTRACT(data, '$.event_type') IS NOT NULL")
                ->pluck('dtype');

            return $types->merge($eventTypes)->filter()->unique()->sort()->values();
        } catch (\Throwable $e) {
            // Fallback en SQLite/tests donde JSON_EXTRACT no existe: recolectar en PHP
            return DatabaseNotification::query()
                ->pluck('data')
                ->map(fn ($d) => $d['type'] ?? $d['event_type'] ?? null)
                ->filter()
                ->unique()
                ->sort()
                ->values();
        }
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
