<?php

namespace App\Livewire\App\Notifications;

use App\Services\NotificationService;
use App\Services\NotificationTargetResolver;
use Carbon\CarbonInterface;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

/**
 * Campana de notificaciones del navbar (blueprint/notifications): muestra las
 * últimas notificaciones de base de datos del usuario y se actualiza en tiempo
 * real vía Reverb (`NotificationReceived`).
 *
 * Inserción optimista (hallazgo N5): el broadcast puede llegar antes del commit
 * de la transacción que persistió la fila, así que el payload del evento se
 * antepone sin releer la BD; la reconciliación contra la BD ocurre al abrir el
 * dropdown, al marcar leídas y con el wire:poll de fallback.
 */
class NotificationBell extends Component
{
    /** Máximo de notificaciones recientes mostradas en el dropdown. */
    public const MAX_RECENT = 8;

    /** @var array<int, array<string, mixed>> */
    public array $notifications = [];

    public int $unreadCount = 0;

    protected function getListeners(): array
    {
        return [
            'echo-private:App.Models.User.'.auth()->id().',.notification.received' => 'onNotificationReceived',
            'notification-received' => 'onNotificationReceived',
            'notification-read' => 'reconcile',
        ];
    }

    public function mount(): void
    {
        $this->reconcile();
    }

    public function onNotificationReceived(array $payload): void
    {
        $id = (string) ($payload['id'] ?? '');
        $data = (array) ($payload['data'] ?? []);

        if ($id === '' || $this->containsNotification($id)) {
            return;
        }

        array_unshift($this->notifications, $this->normalizeItem(
            id: $id,
            data: $data,
            readAt: null,
            createdAt: null,
        ));
        $this->notifications = array_slice($this->notifications, 0, self::MAX_RECENT);

        $this->unreadCount++;
        app(NotificationService::class)->invalidateUnreadCount(auth()->id());
    }

    public function reconcile(): void
    {
        $user = auth()->user();

        $this->unreadCount = app(NotificationService::class)->unreadCountFor($user->id);

        $this->notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->limit(self::MAX_RECENT)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->normalizeItem(
                id: $notification->id,
                data: (array) $notification->data,
                readAt: $notification->read_at,
                createdAt: $notification->created_at,
            ))
            ->all();
    }

    public function markAsRead(string $id): void
    {
        $user = auth()->user();
        $notification = $user->notifications()->whereKey($id)->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            app(NotificationService::class)->invalidateUnreadCount($user->id);
        }

        $this->reconcile();
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();

        if ($user->unreadNotifications()->exists()) {
            $user->unreadNotifications()->getQuery()->update(['read_at' => now()]);
            app(NotificationService::class)->invalidateUnreadCount($user->id);
        }

        $this->reconcile();
    }

    public function targetUrl(array $data): string
    {
        return app(NotificationTargetResolver::class)->resolveFor(auth()->user(), $data);
    }

    private function containsNotification(string $id): bool
    {
        return collect($this->notifications)->contains(fn (array $item) => ($item['id'] ?? '') === $id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function normalizeItem(string $id, array $data, ?CarbonInterface $readAt, ?CarbonInterface $createdAt): array
    {
        return [
            'id' => $id,
            'type' => (string) ($data['type'] ?? 'generic'),
            'message' => (string) ($data['message'] ?? 'Nueva notificación'),
            'url' => $this->targetUrl($data),
            'read_at' => $readAt?->toIso8601String(),
            'created_at' => $createdAt
                ? $createdAt->toIso8601String()
                : (string) ($data['created_at'] ?? now()->toIso8601String()),
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.notifications.bell');
    }
}
