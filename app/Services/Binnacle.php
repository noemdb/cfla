<?php

namespace App\Services;

use App\Contracts\Auditable;
use App\Events\BinnacleEntryRequested;
use App\Listeners\WriteBinnacleEntry;
use App\Models\BinnacleEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Binnacle
{
    /**
     * Único punto de entrada (ADR-001): SIEMPRE despacha el evento.
     * No escribe directo a la tabla bajo ninguna circunstancia.
     *
     * ADR-002: critical/alert se escriben síncrono (misma request/transacción)
     * para no perderse si la cola falla. El resto se encola. Nota: en Laravel 10
     * un listener ShouldQueue cuyo shouldQueue() devuelve false se DESCARTA sin
     * ejecutarse, por lo que la decisión sync/cola se toma aquí, no en el listener.
     */
    public static function log(string $eventType, array $context = []): void
    {
        $event = new BinnacleEntryRequested($eventType, $context);

        $severity = $context['severity'] ?? 'info';
        $isSyncSeverity = in_array($severity, config('binnacle.sync_severities', []), true);
        $isSyncEvent = in_array($eventType, config('binnacle.sync_event_types', []), true);

        if ($isSyncSeverity || $isSyncEvent) {
            // Mismo camino de escritura (WriteBinnacleEntry::handle), sin cola.
            (new WriteBinnacleEntry)->handle($event);

            return;
        }

        event($event);
    }

    public static function logModelEvent(Model $model, string $event, array $context = []): void
    {
        $context['object'] = $model;
        $context['subject'] ??= auth()->user() ?? self::systemSubject();

        if ($model instanceof Auditable) {
            [$old, $new, $changed] = self::extractAuditableDiff($model);
            $context += ['old_values' => $old, 'new_values' => $new, 'changed_fields' => $changed];
        }

        self::log($event, $context);
    }

    public static function logAuthEvent(string $event, array $context = []): void
    {
        self::log($event, $context + ['category' => 'authentication']);
    }

    /**
     * Registra event_type=model_viewed SOLO para modelos de la allowlist
     * config/binnacle.php#viewed_models (Spec §9.2). Sin esa restricción el
     * volumen de este evento superaría al resto de la tabla combinado.
     * Llamada opt-in desde los controllers de show() de modelos sensibles.
     */
    public static function logView(Model $model, array $context = []): void
    {
        $allowlist = config('binnacle.viewed_models', []);

        if (! in_array($model::class, $allowlist, true)) {
            return;
        }

        $context['object'] = $model;
        $context['subject'] ??= auth()->user() ?? self::systemSubject();
        $context += [
            'title' => 'Consulta de registro sensible',
            'category' => 'security',
            'severity' => 'info',
        ];

        self::log('model_viewed', $context);
    }

    public static function getUserActivityTimeline(int $userId, ?string $start = null, ?string $end = null, array $filters = []): Collection
    {
        return BinnacleEntry::where('subject_type', User::class)
            ->where('subject_id', $userId)
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('created_at', '<=', $end))
            ->when($filters['event_type'] ?? null, fn ($q, $v) => $q->where('event_type', $v))
            ->when($filters['category'] ?? null, fn ($q, $v) => $q->where('event_category', $v))
            ->when($filters['severity'] ?? null, fn ($q, $v) => $q->where('event_severity', $v))
            ->when($filters['search'] ?? null, function ($q, $v) {
                $needle = '%'.$v.'%';

                return $q->where(fn ($s) => $s
                    ->where('title', 'like', $needle)
                    ->orWhere('description', 'like', $needle));
            })
            ->orderByDesc('created_at')
            ->limit(500)
            ->get();
    }

    public static function systemSubject(): array
    {
        return ['type' => 'System', 'id' => null, 'identifier' => 'system'];
    }

    /**
     * Verifica la integridad de la cadena de hashes (ADR-003): cada fila
     * critical/alert debe tener entry_hash de 64 hex y previous_hash apuntando
     * al entry_hash de la fila anterior (null en la genesis).
     *
     * Detecta borrado/reordenación/forja de eslabones. NO detecta alteración
     * del contenido de un eslabón (limitación documentada de ADR-003).
     */
    public static function verifyChainIntegrity(): array
    {
        $rows = BinnacleEntry::whereIn('event_severity', ['critical', 'alert'])
            ->orderBy('id')
            ->get(['id', 'entry_hash', 'previous_hash']);

        $broken = 0;
        $prevHash = null;

        foreach ($rows as $row) {
            if ($row->entry_hash === null || strlen((string) $row->entry_hash) !== 64) {
                $broken++;

                continue;
            }

            if ($row->previous_hash !== $prevHash) {
                $broken++;
            }

            $prevHash = $row->entry_hash;
        }

        return [
            'total' => $rows->count(),
            'broken_links' => $broken,
            'valid' => $broken === 0,
        ];
    }

    /**
     * Proyección de crecimiento de binnacle_entries (Spec §9 / mejora #8).
     *
     * Con el ritmo real de los últimos 30 días se estima cuántas filas habrá
     * en `partition_lookahead_months` meses. Si la proyección supera
     * `partition_threshold`, el particionado por rango de fecha está justificado.
     * Compartida por el comando binnacle:check-growth y el dashboard.
     *
     * @return array<string, mixed>
     */
    public static function projectedGrowth(?int $lookaheadMonths = null): array
    {
        $lookaheadMonths ??= (int) config('binnacle.partition_lookahead_months', 12);
        $threshold = (int) config('binnacle.partition_threshold', 1_000_000);

        $total = BinnacleEntry::count();
        $last30 = BinnacleEntry::where('created_at', '>=', now()->subDays(30))->count();
        $daily = $last30 / 30;

        return [
            'total' => $total,
            'last_30_days' => $last30,
            'daily_rate' => round($daily, 1),
            'lookahead_months' => $lookaheadMonths,
            'projected' => (int) round($total + $daily * 30 * $lookaheadMonths),
            'threshold' => $threshold,
            'partition_needed' => (int) round($total + $daily * 30 * $lookaheadMonths) >= $threshold,
        ];
    }

    /**
     * Verifica el ancla externa (Spec §8.3 / mejora #6): el último hash
     * publicado por binnacle:anchor sigue presente en la cadena actual.
     *
     * No detecta alteración del contenido de un eslabón (limitación de ADR-003),
     * pero sí detecta borrado/reordenación que haya retirado la fila anclada
     * (rollback o manipulación con acceso a la BD, recalculando la cadena).
     *
     * @return array<string, mixed>
     */
    public static function verifyAnchorIntegrity(?string $path = null): array
    {
        $path ??= config('binnacle.anchor_path');

        if (! is_file($path)) {
            return ['anchored' => false, 'valid' => false, 'reason' => 'Sin archivo ancla', 'last_anchor' => null];
        }

        $lines = array_values(array_filter(
            file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [],
            fn ($l) => trim($l) !== ''
        ));

        if (empty($lines)) {
            return ['anchored' => false, 'valid' => false, 'reason' => 'Archivo ancla vacío', 'last_anchor' => null];
        }

        $last = trim((string) end($lines));
        $parts = explode('|', $last);

        if (count($parts) < 4) {
            return ['anchored' => false, 'valid' => false, 'reason' => 'Línea ancla malformada', 'last_anchor' => $last];
        }

        [$timestamp, $entryId, $eventType, $hash] = $parts;

        $found = BinnacleEntry::whereIn('event_severity', ['critical', 'alert'])
            ->where('entry_hash', $hash)
            ->exists();

        $tip = BinnacleEntry::whereIn('event_severity', ['critical', 'alert'])
            ->orderByDesc('id')
            ->value('entry_hash');

        return [
            'anchored' => true,
            'valid' => $found,
            'reason' => $found
                ? 'El ancla externa sigue presente en la cadena'
                : 'El hash anclado ya no está en la cadena (posible manipulación o rollback)',
            'last_anchor' => [
                'timestamp' => $timestamp,
                'entry_id' => (int) $entryId,
                'event_type' => $eventType,
                'hash' => $hash,
            ],
            'chain_tip' => $tip,
        ];
    }

    private static function extractAuditableDiff(Model&Auditable $model): array
    {
        $allowed = $model->auditableAttributes();
        $masked = $model->maskedAuditFields();

        $old = collect($model->getOriginal())->only($allowed);
        $new = collect($model->getAttributes())->only($allowed);

        foreach ($masked as $field) {
            if ($old->has($field)) {
                $old[$field] = self::mask($old[$field]);
            }
            if ($new->has($field)) {
                $new[$field] = self::mask($new[$field]);
            }
        }

        $changed = collect($model->getDirty())->keys()->intersect($allowed)->values()->toArray();

        return [$old->toArray(), $new->toArray(), $changed];
    }

    private static function mask(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;

        if (strlen($value) < 4) {
            return $value;
        }

        return substr($value, 0, 2).str_repeat('*', max(strlen($value) - 4, 1)).substr($value, -2);
    }
}
