<?php

namespace App\Livewire\App\Notifications;

use App\Services\NotificationService;
use App\Services\NotificationTargetResolver;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Página "Ver todas las notificaciones" (blueprint/notifications): histórico
 * paginado de las notificaciones del usuario autenticado, con filtros por
 * estado de lectura y acciones para marcarlas como leídas.
 */
#[Layout('layouts.dashboard')]
class NotificationsIndex extends Component
{
    use WithPagination;

    public const PER_PAGE = 15;

    /** @var array<string, string> */
    public const TABS = [
        'all' => 'Todas',
        'unread' => 'No leídas',
        'read' => 'Leídas',
    ];

    #[Url(as: 'tab')]
    public string $tab = 'all';

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->tab = $this->normalizeTab($this->tab);
    }

    public function setTab(string $tab): void
    {
        $this->tab = $this->normalizeTab($tab);
        $this->resetPage();
    }

    public function markAsRead(string $id): void
    {
        $user = auth()->user();
        $notification = $user->notifications()->whereKey($id)->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            app(NotificationService::class)->invalidateUnreadCount($user->id);
        }
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();

        if ($user->unreadNotifications()->exists()) {
            $user->unreadNotifications()->getQuery()->update(['read_at' => now()]);
            app(NotificationService::class)->invalidateUnreadCount($user->id);
        }
    }

    public function targetUrl(array $data): string
    {
        return app(NotificationTargetResolver::class)->resolveFor(auth()->user(), $data);
    }

    private function normalizeTab(string $tab): string
    {
        return array_key_exists($tab, self::TABS) ? $tab : 'all';
    }

    public function render(): \Illuminate\View\View
    {
        $query = auth()->user()->notifications();

        if ($this->tab === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->tab === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->orderByDesc('created_at')->paginate(self::PER_PAGE);

        return view('livewire.app.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
