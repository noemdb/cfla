<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Activity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Elimina actividades repetidas con campos idénticos:
 *   pevaluacion_id + topic + thematic + references + learning
 *
 * Conserva 1 registro por grupo (por defecto el más antiguo = ID menor)
 * y elimina los duplicados junto con sus dependencias RESTRICT.
 *
 * Uso:
 *   php8.2 artisan activity:deduplicate --dry-run
 *   php8.2 artisan activity:deduplicate --pevaluacion=215226 --dry-run
 *   php8.2 artisan activity:deduplicate --pevaluacion=215225 --dry-run
 *   php8.2 artisan activity:deduplicate --pevaluacion=215223 --dry-run
 *   php8.2 artisan activity:deduplicate --pevaluacion=215222 --dry-run
 * 
 *   php8.2 artisan activity:deduplicate --pevaluacion=215231 --dry-run
 *   php8.2 artisan activity:deduplicate --pevaluacion=215230 --dry-run
 *   php8.2 artisan activity:deduplicate --pevaluacion=215228 --dry-run
 *   php8.2 artisan activity:deduplicate --pevaluacion=215227 --dry-run
 * 
 *   php8.2 artisan activity:deduplicate --force
 *   php8.2 artisan activity:deduplicate --keep=last --force
 */
class ActivityDeduplicate extends Command
{
    protected $signature = 'activity:deduplicate
                            {--pevaluacion= : Filtrar solo una Pevaluación (ID)}
                            {--dry-run : Simular sin borrar}
                            {--force : Ejecutar sin confirmación}
                            {--keep=first : Qué registro conservar: first (ID menor) o last (ID mayor)}';

    protected $description = 'Elimina activities duplicadas con campos idénticos (pevaluacion_id, topic, thematic, references, learning), conservando una por grupo';

    public function handle(): int
    {
        $pevaluacionFilter = $this->option('pevaluacion') ? (int) $this->option('pevaluacion') : null;
        $dryRun = (bool) $this->option('dry-run');
        $keep = strtolower((string) $this->option('keep'));
        $force = (bool) $this->option('force');

        if (! in_array($keep, ['first', 'last'], true)) {
            $this->error('Opción --keep inválida. Usa first o last.');

            return self::FAILURE;
        }

        $keepAgg = $keep === 'last' ? 'MAX(id)' : 'MIN(id)';

        $this->info('=== Activity Deduplicate ===');
        $this->line('Campos clave: pevaluacion_id, topic, thematic, references, learning');
        $this->line('Estrategia keep: ' . $keep . ' (' . $keepAgg . ')');
        if ($pevaluacionFilter) {
            $this->line("Filtro pevaluacion_id = {$pevaluacionFilter}");
        }
        if ($dryRun) {
            $this->warn('MODO DRY-RUN — no se borrará nada.');
        }
        $this->newLine();

        // Evitar truncamiento de GROUP_CONCAT si hay muchos duplicados por grupo
        try {
            DB::statement('SET SESSION group_concat_max_len = 1000000');
        } catch (\Throwable $e) {
            // ignorar si no hay permisos
        }

        // ── 1. Buscar grupos duplicados ──
        $this->line('Buscando grupos duplicados...');

        $groupsQuery = DB::table('activities')
            ->selectRaw("pevaluacion_id, `topic`, `thematic`, `references`, `learning`, COUNT(*) as cnt, {$keepAgg} as keep_id, GROUP_CONCAT(id ORDER BY id SEPARATOR ',') as all_ids")
            ->when($pevaluacionFilter, fn ($q) => $q->where('pevaluacion_id', $pevaluacionFilter))
            ->groupByRaw('pevaluacion_id, `topic`, `thematic`, `references`, `learning`')
            ->havingRaw('COUNT(*) > 1')
            ->orderByRaw('cnt DESC');

        $groups = $groupsQuery->get();

        if ($groups->isEmpty()) {
            $this->info('No se encontraron actividades duplicadas.');

            return self::SUCCESS;
        }

        $this->info("Grupos duplicados encontrados: {$groups->count()}");

        // Construir lista de IDs a eliminar
        $idsToDelete = [];
        $preview = [];

        foreach ($groups as $g) {
            $allIds = explode(',', $g->all_ids);
            $keepId = (int) $g->keep_id;
            $toDelete = array_values(array_filter(array_map('intval', $allIds), fn ($id) => $id !== $keepId));

            foreach ($toDelete as $id) {
                $idsToDelete[] = $id;
            }

            // Para preview: solo primeros 10 grupos
            if (count($preview) < 10) {
                $preview[] = [
                    $g->pevaluacion_id,
                    $g->cnt,
                    $keepId,
                    implode(', ', $toDelete),
                    mb_strimwidth((string) $g->topic, 0, 32, '…'),
                ];
            }
        }

        $idsToDelete = array_values(array_unique($idsToDelete));
        sort($idsToDelete);

        $this->newLine();
        $this->table(
            ['pevaluacion_id', 'duplicados', 'keep_id', 'delete_ids', 'topic (preview)'],
            $preview
        );

        if ($groups->count() > 10) {
            $this->line('… y ' . ($groups->count() - 10) . ' grupos más (ver --dry-run para detalle completo con -v).');
        }

        $this->newLine();
        $this->info('Resumen:');
        $this->line("  Grupos con duplicados : {$groups->count()}");
        $this->line("  Registros a conservar: {$groups->count()} (1 por grupo)");
        $this->line("  Registros a eliminar : " . count($idsToDelete));

        if ($idsToDelete === []) {
            $this->info('Nada para eliminar.');

            return self::SUCCESS;
        }

        // Detalle verboso si -v
        if ($this->output->isVerbose()) {
            $this->newLine();
            $this->line('IDs a eliminar: ' . implode(', ', $idsToDelete));
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY-RUN completado — no se persistieron cambios.');
            $this->line('Ejecuta sin --dry-run y con --force para borrar.');

            return self::SUCCESS;
        }

        if (! $force && ! $this->confirm('¿Eliminar ' . count($idsToDelete) . ' actividades duplicadas? Se conservará 1 por grupo (' . $keep . ').', false)) {
            $this->info('Abortado.');

            return self::SUCCESS;
        }

        // ── 2. Borrado en chunks con manejo de FK RESTRICT ──
        $chunkSize = 500;
        $chunks = array_chunk($idsToDelete, $chunkSize);
        $deleted = 0;
        $progress = $this->output->createProgressBar(count($idsToDelete));
        $progress->start();

        // Tablas con FK RESTRICT que impiden DELETE directo de activities
        $restrictTables = [
            'lms_activity_logs' => 'activity_id',
            'lms_activity_attendances' => 'activity_id',
        ];

        DB::beginTransaction();
        try {
            foreach ($chunks as $chunk) {
                // 2a. Limpiar hijos con RESTRICT primero
                foreach ($restrictTables as $table => $fk) {
                    DB::table($table)->whereIn($fk, $chunk)->delete();
                }

                // 2b. Borrar activities (el resto es CASCADE: achievements, lms_sections, etc.)
                // Usar query builder para evitar eventos si se quiere, pero Eloquent dispara cascadas DB.
                // Para respetar SoftDeletes inexistente, delete directo es suficiente.
                // Si se prefiere respetar observers, usar Activity::whereIn()->delete().
                $affected = DB::table('activities')->whereIn('id', $chunk)->delete();
                $deleted += $affected;
                $progress->advance(count($chunk));
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $progress->finish();
            $this->newLine(2);
            $this->error('Error durante el borrado: ' . $e->getMessage());
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        }

        $progress->finish();
        $this->newLine(2);
        $this->info("✓ Eliminación completada: {$deleted} actividades borradas.");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Grupos procesados', $groups->count()],
                ['Registros eliminados', $deleted],
                ['Registros conservados', $groups->count()],
                ['Estrategia keep', $keep],
            ]
        );

        return self::SUCCESS;
    }
}
