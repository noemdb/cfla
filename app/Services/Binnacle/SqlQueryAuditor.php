<?php

namespace App\Services\Binnacle;

use App\Services\Binnacle;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

/**
 * Auditor SQL por request (expansión binnacle).
 *
 * El middleware binnacle.sql llama a start() al inicio de la request y a
 * flush() al final. Mientras está activo, escucha cada QueryExecuted y
 * agrega el conteo por (operación, tabla) SOLO para las tablas de
 * config/binnacle.php#sql_monitored_tables. Con el flush emite una entrada
 * sql_<operación> por (tabla, operación) tocada, sin importar la request
 * (Bindings y literales nunca se registran: solo el SQL parametrizado).
 *
 * El alcance es solo rutas marcadas (no global) para acotar el volumen de
 * SELECTs (Spec BINNACLE-001 §9.2). El listener se registra una única vez y
 * se desactiva con $enabled para no duplicar conteos entre requests.
 */
class SqlQueryAuditor
{
    /** @var array<string, int> conteo por "operacion|tabla" */
    private array $buffer = [];

    /** @var array<string, string> primera consulta por "operacion|tabla" */
    private array $samples = [];

    private bool $enabled = false;

    private bool $registered = false;

    private int $startedAt = 0;

    public function start(): void
    {
        $this->buffer = [];
        $this->samples = [];
        $this->enabled = true;
        $this->startedAt = (int) hrtime(true);

        if (! $this->registered) {
            $this->registered = true;

            DB::listen(fn (QueryExecuted $event) => $this->record($event));
        }
    }

    public function record(QueryExecuted $event): void
    {
        if (! $this->enabled || ! config('binnacle.sql_audit_enabled', true)) {
            return;
        }

        $operation = $this->operationOf($event->sql);

        if ($operation === null) {
            return;
        }

        foreach ($this->tablesOf($event->sql, $operation) as $table) {
            if (! in_array($table, config('binnacle.sql_monitored_tables', []), true)) {
                continue;
            }

            $key = $operation.'|'.$table;
            $this->buffer[$key] = ($this->buffer[$key] ?? 0) + 1;
            $this->samples[$key] ??= $event->sql;
        }
    }

    public function flush(): void
    {
        $this->enabled = false;

        if ($this->buffer === []) {
            return;
        }

        $durationMs = (hrtime(true) - $this->startedAt) / 1e6;

        foreach ($this->buffer as $key => $count) {
            [$operation, $table] = explode('|', $key, 2);

            Binnacle::log('sql_'.$operation, [
                'title' => "Consulta {$operation} sobre {$table}",
                'category' => 'user_action',
                'severity' => 'info',
                'subject' => auth()->user() ?? Binnacle::systemSubject(),
                'metadata' => [
                    'table' => $table,
                    'operation' => $operation,
                    'count' => $count,
                    'response_ms' => round($durationMs, 1),
                    'sql' => $this->sample($key),
                ],
            ]);
        }

        $this->buffer = [];
        $this->samples = [];
    }

    /**
     * Clasifica la operación a partir de la primera palabra clave del SQL.
     * REPLACE INTO se trata como INSERT; DDL y consultas sin tabla se ignoran.
     */
    private function operationOf(string $sql): ?string
    {
        if (preg_match('/^\s*(?:select|with)\b/i', $sql)) {
            return 'select';
        }

        if (preg_match('/^\s*(?:insert|replace)\b/i', $sql)) {
            return 'insert';
        }

        if (preg_match('/^\s*update\b/i', $sql)) {
            return 'update';
        }

        if (preg_match('/^\s*delete\b/i', $sql)) {
            return 'delete';
        }

        return null;
    }

    /**
     * Extrae las tablas tocadas. Se elimina previamente la cláusula
     * `ON DUPLICATE KEY UPDATE` para que sus asignaciones no produzcan
     * falsos positivos de tabla en los INSERT.
     *
     * @return array<int, string>
     */
    private function tablesOf(string $sql, string $operation): array
    {
        $sql = preg_replace('/\bon duplicate key update\b.*$/i', '', $sql) ?? $sql;

        $clauses = match ($operation) {
            'insert' => 'INTO',
            'update' => 'UPDATE|JOIN',
            'delete' => 'FROM',
            default => 'FROM|JOIN',
        };

        preg_match_all(
            '/(?:'.$clauses.')\s+(?:`?[a-zA-Z0-9_]+`?\.)?`?([a-zA-Z0-9_]+)`?/i',
            $sql,
            $matches
        );

        $tables = array_map('strtolower', $matches[1]);

        return array_values(array_unique($tables));
    }

    private function sample(string $key): ?string
    {
        $sql = $this->samples[$key] ?? null;

        if ($sql === null) {
            return null;
        }

        $sql = trim(preg_replace('/\s+/', ' ', $sql) ?? $sql);
        $max = (int) config('binnacle.sql_audit_max_samples', 3);
        $length = (int) config('binnacle.sql_audit_sample_length', 200);

        if ($max <= 0) {
            return null;
        }

        return mb_strlen($sql) > $length ? mb_substr($sql, 0, $length).'…' : $sql;
    }
}
