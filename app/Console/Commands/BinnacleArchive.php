<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Mueve las entradas de la bitácora que superan la retención por categoría
 * (config/binnacle.php#retention_months, Spec §12) a binnacle_entries_archive.
 *
 * La sesión marca @binnacle_archive_process = 1 para que los triggers de
 * inmutabilidad (ADR-004) permitan el DELETE únicamente en este proceso.
 *
 * Uso:
 *   php8.2 artisan binnacle:archive
 *   php8.2 artisan binnacle:archive --older-than=90 --limit=5000
 */
class BinnacleArchive extends Command
{
    protected $signature = 'binnacle:archive
        {--older-than= : Antigüedad mínima en días (reemplaza la retención por categoría)}
        {--limit=10000 : Máximo de filas movidas por categoría en esta ejecución}';

    protected $description = 'Archiva las entradas de la bitácora que superan su política de retención';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $cutoffs = $this->cutoffs($this->option('older-than'));
        $moved = 0;

        DB::transaction(function () use (&$moved, $cutoffs, $limit) {
            // Permite DELETE solo en esta sesión/conexión (ADR-004).
            DB::statement('SET @binnacle_archive_process = 1');

            foreach ($cutoffs as $category => $cutoff) {
                $count = $this->archiveCategory($category, $cutoff, $limit);
                $moved += $count;

                if ($count > 0) {
                    $this->line("  → {$category}: {$count} filas (corte {$cutoff->toDateTimeString()})");
                }
            }

            DB::statement('SET @binnacle_archive_process = NULL');
        });

        $this->info("Bitácora archivada: {$moved} filas movidas.");

        return self::SUCCESS;
    }

    private function cutoffs(string|int|null $days): array
    {
        if ($days !== null) {
            $cutoff = now()->subDays((int) $days);

            return array_fill_keys(array_keys(config('binnacle.retention_months', [])), $cutoff);
        }

        $cutoffs = [];

        foreach (config('binnacle.retention_months', []) as $category => $months) {
            $cutoffs[$category] = now()->subMonths((int) $months);
        }

        return $cutoffs;
    }

    private function archiveCategory(string $category, Carbon $cutoff, int $limit): int
    {
        $columns = ['id', 'uuid', 'event_type', 'event_category', 'event_severity', 'title',
            'description', 'subject_type', 'subject_id', 'subject_identifier',
            'object_type', 'object_id', 'object_identifier', 'ip_address', 'user_agent',
            'request_method', 'request_url', 'request_id', 'session_id', 'country_code', 'city',
            'old_values', 'new_values', 'changed_fields', 'metadata',
            'entry_hash', 'previous_hash', 'created_at', 'created_by'];

        $rows = DB::table('binnacle_entries')
            ->where('event_category', $category)
            ->where('created_at', '<', $cutoff)
            ->orderBy('id')
            ->limit($limit)
            ->get($columns);

        if ($rows->isEmpty()) {
            return 0;
        }

        $now = now();

        DB::table('binnacle_entries_archive')->insert(
            $rows->map(fn ($row) => (array) $row + ['archived_at' => $now])->all()
        );

        DB::table('binnacle_entries')
            ->whereIn('id', $rows->pluck('id'))
            ->delete();

        return $rows->count();
    }
}
