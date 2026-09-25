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
 *
 * Digest (ítem 4): la campana no lista filas crudas. Los avisos que hablan del
 * mismo asunto (la misma pevaluación, asignatura o programa educativo) se
 * colapsan en una sola entrada —"3 actividades nuevas en MATEMÁTICAS · QUINTO
 * AÑO · A"— para que un pico de registros no tape el resto. Sin agrupar, solo
 * se leían los primeros `MAX_RECENT` avisos y el resto quedaba invisible.
 */
class NotificationBell extends Component
{
    /** Máximo de entradas (ya agrupadas) mostradas en el dropdown. */
    public const MAX_RECENT = 8;

    /**
     * Avisos leídos de la BD para poder agrupar. Tiene que ser holgado frente
     * a MAX_RECENT: si un mismo asunto acapara los primeros, el agrupado tiene
     * que ver más filas para que "8 entradas" no se convierta en 3.
     */
    private const FETCH_LIMIT = 50;

    /**
     * Tipos que se agrupan: clave del payload que identifica el asunto y
     * sustantivos (singular, plural) para el rótulo del grupo. El plural va
     * explícito porque `Str::plural()` no pluraliza una frase completa
     * ("actividad nueva" → "actividad nuevas").
     *
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    private const GROUPS = [
        'activity_created' => ['pevaluacion_id', 'actividad nueva', 'actividades nuevas'],
        'pevaluacion_observation_updated' => ['pevaluacion_id', 'observación actualizada', 'observaciones actualizadas'],
        'pevaluacion_observation_registered' => ['pevaluacion_id', 'observación registrada', 'observaciones registradas'],
        'peducativo_created' => ['peducativo_id', 'programa educativo nuevo', 'programas educativos nuevos'],
        'peducativo_updated' => ['peducativo_id', 'cambio en el programa educativo', 'cambios en el programa educativo'],
        'peducativo_deleted' => ['peducativo_id', 'programa educativo eliminado', 'programas educativos eliminados'],
        'diag_question_created' => ['asignatura', 'pregunta diagnóstica nueva', 'preguntas diagnósticas nuevas'],
        'diag_question_updated' => ['asignatura', 'pregunta diagnóstica actualizada', 'preguntas diagnósticas actualizadas'],
        'diag_question_deleted' => ['asignatura', 'pregunta diagnóstica eliminada', 'preguntas diagnósticas eliminadas'],
    ];

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

        $items = $this->groupItems(array_merge(
            [$this->normalizeItem(id: $id, data: $data, readAt: null, createdAt: null)],
            $this->notifications
        ));

        $this->notifications = array_slice($items, 0, self::MAX_RECENT);

        $this->unreadCount++;
        app(NotificationService::class)->invalidateUnreadCount(auth()->id());
    }

    public function reconcile(): void
    {
        $user = auth()->user();

        $this->unreadCount = app(NotificationService::class)->unreadCountFor($user->id);

        $items = $user->notifications()
            ->orderByDesc('created_at')
            ->limit(self::FETCH_LIMIT)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->normalizeItem(
                id: $notification->id,
                data: (array) $notification->data,
                readAt: $notification->read_at,
                createdAt: $notification->created_at,
            ))
            ->all();

        $this->notifications = array_slice($this->groupItems($items), 0, self::MAX_RECENT);
    }

    /**
     * Marca como leídas una o varias notificaciones (una entrada puede ser un
     * grupo colapsado, con varios ids detrás).
     *
     * @param  array<int, string>|string  $ids
     */
    public function markAsRead($ids): void
    {
        $user = auth()->user();
        $ids = array_values(array_filter((array) $ids));

        if ($ids === []) {
            return;
        }

        $read = app(NotificationService::class)->markAsReadFor($user, $ids);

        if ($read > 0) {
            $this->dispatch('notification-read');
        }

        $this->reconcile();
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();

        $ids = $user->unreadNotifications()->pluck('id')->all();

        if ($ids !== []) {
            app(NotificationService::class)->markAsReadFor($user, $ids);
            $this->dispatch('notification-read');
        }

        $this->reconcile();
    }

    public function targetUrl(array $data): string
    {
        return app(NotificationTargetResolver::class)->resolveFor(auth()->user(), $data);
    }

    private function containsNotification(string $id): bool
    {
        return collect($this->notifications)->contains(fn (array $item) => in_array($id, (array) ($item['ids'] ?? []), true));
    }

    /**
     * Colapsa en una sola entrada los avisos del mismo asunto, conservando el
     * orden de aparición (el más reciente manda). Las entradas sin tipo
     * agrupable se quedan como están.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function groupItems(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $key = $item['group_key'] ?? null;

            if ($key === null) {
                $result[] = $item + ['ids' => [$item['id']], 'count' => 1];

                continue;
            }

            $position = array_search($key, array_column($result, 'group_key'), true);

            if ($position === false) {
                $result[] = $item + ['ids' => [$item['id']], 'count' => 1];

                continue;
            }

            $result[$position]['ids'][] = $item['id'];
            $result[$position]['count']++;

            // El grupo se sitúa por su aviso más reciente y queda sin leer si
            // alguno de sus avisos lo está.
            if (($item['created_at'] ?? '') > ($result[$position]['created_at'] ?? '')) {
                $result[$position]['created_at'] = $item['created_at'];
            }
            if ($item['read_at'] === null) {
                $result[$position]['read_at'] = null;
            }
        }

        // El rótulo se calcula DESPUÉS de agrupar: durante el bucle el primer
        // aviso del grupo todavía tiene count == 1 y su mensaje es el original.
        return array_map(fn (array $item) => $this->describeGroup($item), $result);
    }

    /**
     * Sustituye el mensaje por el rótulo del grupo cuando hay más de un aviso.
     * Si el grupo llena el cupo de lectura (`FETCH_LIMIT`), se marca con "+"
     * para no prometer un número exacto que no se ha podido contar.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function describeGroup(array $item): array
    {
        $count = (int) ($item['count'] ?? 1);

        if ($count > 1) {
            $type = (string) ($item['type'] ?? '');
            $noun = self::GROUPS[$type][2] ?? 'avisos';
            $subject = (string) ($item['subject'] ?? '');
            $label = $count.($count >= self::FETCH_LIMIT ? '+' : '').' '.$noun;

            $item['message'] = $subject !== '' ? $label.' en '.$subject : $label;
            $item['grouped'] = true;
        }

        return $item;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeItem(string $id, array $data, ?CarbonInterface $readAt, ?CarbonInterface $createdAt): array
    {
        // `type` es la clave canónica; `event_type` la de las filas
        // históricas (TimetableChanged/SubstituteAssigned y anteriores), que
        // sin este fallback se pintarían como "generic".
        $type = (string) ($data['type'] ?? $data['event_type'] ?? 'generic');

        return [
            'id' => $id,
            'type' => $type,
            'message' => (string) ($data['message'] ?? 'Nueva notificación'),
            'url' => $this->targetUrl($data),
            'read_at' => $readAt?->toIso8601String(),
            'created_at' => $createdAt
                ? $createdAt->toIso8601String()
                : (string) ($data['created_at'] ?? now()->toIso8601String()),
            'ids' => [$id],
            'count' => 1,
            'grouped' => false,
            'group_key' => $this->groupKey($type, $data),
            'subject' => $this->subjectLabel($data),
        ];
    }

    /**
     * Clave de agrupación del aviso, o `null` si su tipo no se agrupa.
     *
     * @param  array<string, mixed>  $data
     */
    private function groupKey(string $type, array $data): ?string
    {
        if (! isset(self::GROUPS[$type])) {
            return null;
        }

        $subject = $data[self::GROUPS[$type][0]] ?? null;

        if (! is_scalar($subject) || (string) $subject === '') {
            return null;
        }

        return $type.'|'.$subject;
    }

    /**
     * Etiqueta legible del asunto (P.Estudio · Asignatura · Grado · Sección),
     * tomándola de las claves que usan las notificaciones.
     *
     * @param  array<string, mixed>  $data
     */
    private function subjectLabel(array $data): string
    {
        $parts = array_filter([
            $data['pestudio'] ?? $data['pestudio_name'] ?? null,
            $data['asignatura'] ?? $data['asignatura_name'] ?? null,
            $data['grado'] ?? $data['grado_name'] ?? null,
            $data['seccion'] ?? $data['seccion_name'] ?? null,
        ], fn ($value) => is_string($value) && trim($value) !== '');

        if ($parts !== []) {
            return implode(' · ', $parts);
        }

        foreach (['peducativo_name', 'pensum_label', 'calendar_name', 'asignatura'] as $fallback) {
            if (! empty($data[$fallback]) && is_string($data[$fallback])) {
                return $data[$fallback];
            }
        }

        return '';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.notifications.bell');
    }
}
