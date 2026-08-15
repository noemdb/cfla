<?php

namespace App\Listeners;

use App\Events\BinnacleEntryRequested;
use App\Models\BinnacleEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WriteBinnacleEntry implements ShouldQueue
{
    /**
     * Cola dedicada: un backlog de bitácora nunca bloquea jobs de negocio.
     * Las severidades critical/alert NUNCA llegan aquí por cola: Binnacle::log
     * las escribe síncrono llamando handle() directamente (ADR-002), porque en
     * Laravel 10 un shouldQueue()=false descarta el evento sin ejecutarlo.
     */
    public $queue = 'binnacle';

    public function handle(BinnacleEntryRequested $event): void
    {
        $request = $event->requestContext();
        $severity = $event->context['severity'] ?? 'info';

        $data = [
            'uuid' => (string) Str::uuid(),
            'event_type' => $event->eventType,
            'event_category' => $event->context['category'] ?? 'system',
            'event_severity' => $severity,
            'title' => $event->context['title'] ?? $event->eventType,
            'description' => $event->context['description'] ?? null,
            'subject_type' => $event->subjectType(),
            'subject_id' => $event->subjectId(),
            'subject_identifier' => $event->subjectIdentifier(),
            'object_type' => $event->objectType(),
            'object_id' => $event->objectId(),
            'object_identifier' => $event->objectIdentifier(),
            'ip_address' => $request['ip'] ?? null,
            'user_agent' => $request['user_agent'] ?? null,
            'request_method' => $request['method'] ?? null,
            'request_url' => $request['url'] ?? null,
            'request_id' => $request['request_id'] ?? null,
            'session_id' => $request['session_id'] ?? null,
            'old_values' => $event->context['old_values'] ?? null,
            'new_values' => $event->context['new_values'] ?? null,
            'changed_fields' => $event->context['changed_fields'] ?? null,
            'metadata' => $event->context['metadata'] ?? null,
            'created_by' => $event->context['created_by'] ?? null,
        ];

        // Hash-chain (ADR-003, Fase 4): solo para critical/alert, que se
        // escriben síncrono en orden. Mitigación parcial, no absoluta.
        if (in_array($severity, config('binnacle.sync_severities', ['critical', 'alert']), true)) {
            [$data['entry_hash'], $data['previous_hash']] = $this->hashChain($data);
        }

        try {
            // forceCreate: único writer interno de la bitácora (ADR-001). El
            // modelo tiene $guarded = ['*']; el guard bloquea UPDATE/DELETE.
            BinnacleEntry::forceCreate($data);
        } catch (\Throwable $e) {
            // Fail-open: un fallo de auditoría jamás debe romper el flujo de negocio.
            Log::error('No se pudo escribir entrada de bitácora', [
                'event_type' => $event->eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Encadena la fila con la última fila critical/alert. La cadena solo
     * cubre esa minoría del volumen (ADR-003), nunca los eventos de cola.
     *
     * @return array{0: string, 1: ?string} [entry_hash, previous_hash]
     */
    private function hashChain(array $data): array
    {
        $previous = BinnacleEntry::whereIn('event_severity', ['critical', 'alert'])
            ->orderByDesc('id')
            ->value('entry_hash');

        $payload = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'|'.($previous ?? 'genesis');

        return [hash('sha256', $payload), $previous];
    }
}
