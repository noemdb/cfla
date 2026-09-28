<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Elimina preguntas diagnósticas duplicadas según criterio exacto:
 *   diag_questions.pregunta idéntico + diag_questions.pensum_id idéntico
 * (comparación con la collation de la columna).
 *
 * Por cada grupo se conserva 1 registro (por defecto el más antiguo = ID
 * menor, o --keep=last) y se eliminan los demás junto con sus opciones de
 * selección (diag_options). Regla de seguridad sobre respuestas:
 *   - un duplicado con filas en diag_answers (question_id) NUNCA se elimina
 *     (además la BD lo impide: FK RESTRICT);
 *   - si TODOS los duplicados del grupo tienen respuestas, no se elimina
 *     nada del grupo;
 *   - si alguno tiene respuestas, se prefiere conservar uno de ellos.
 *
 * Sin usuario autenticado en consola, DiagQuestionObserver no emite
 * notificaciones. El borrado usa query builder (sin eventos) dentro de una
 * transacción por chunks; las opciones se borran explícito (aunque la FK es
 * CASCADE) para contarlas en el reporte.
 *
 * Uso:
 *   php8.2 artisan diag:deduplicate-questions --dry-run
 *   php8.2 artisan diag:deduplicate-questions --pensum=123 --dry-run
 *   php8.2 artisan diag:deduplicate-questions --force
 *   php8.2 artisan diag:deduplicate-questions --keep=last --force
 */
class DiagQuestionDeduplicate extends Command
{
    protected $signature = 'diag:deduplicate-questions
                            {--pensum= : Filtrar solo un Pensum (ID)}
                            {--dry-run : Simular sin borrar}
                            {--force : Ejecutar sin confirmación}
                            {--keep=first : Qué registro conservar: first (ID menor) o last (ID mayor)}';

    protected $description = 'Elimina diag_questions duplicadas (pregunta + pensum_id idénticos) con sus opciones, sin tocar preguntas con respuestas';

    public function handle(): int
    {
        $pensumFilter = $this->option('pensum') ? (int) $this->option('pensum') : null;
        $dryRun = (bool) $this->option('dry-run');
        $keep = strtolower((string) $this->option('keep'));
        $force = (bool) $this->option('force');

        if (! in_array($keep, ['first', 'last'], true)) {
            $this->error('Opción --keep inválida. Usa first o last.');

            return self::FAILURE;
        }

        $this->info('=== DiagQuestion Deduplicate ===');
        $this->line('Campos clave: pensum_id + pregunta (idénticos)');
        $this->line('Estrategia keep: '.$keep.' (se prefiere conservar una con respuestas si existe)');
        if ($pensumFilter) {
            $this->line("Filtro pensum_id = {$pensumFilter}");
        }
        if ($dryRun) {
            $this->warn('MODO DRY-RUN — no se borrará nada.');
        }
        $this->newLine();

        try {
            DB::statement('SET SESSION group_concat_max_len = 1000000');
        } catch (\Throwable $e) {
            // ignorar si no hay permisos
        }

        // ── 1. Grupos duplicados ──
        $this->line('Buscando grupos duplicados...');

        $groups = DB::table('diag_questions')
            ->selectRaw('pensum_id, `pregunta`, COUNT(*) as cnt, MIN(id) as min_id, MAX(id) as max_id, GROUP_CONCAT(id ORDER BY id SEPARATOR \',\') as all_ids')
            ->when($pensumFilter, fn ($q) => $q->where('pensum_id', $pensumFilter))
            ->groupByRaw('pensum_id, `pregunta`')
            ->havingRaw('COUNT(*) > 1')
            ->orderByRaw('cnt DESC')
            ->get();

        if ($groups->isEmpty()) {
            $this->info('No se encontraron preguntas duplicadas.');

            return self::SUCCESS;
        }

        $this->info("Grupos duplicados encontrados: {$groups->count()}");

        // ── 2. Resolver keep/delete por grupo con regla de respuestas ──
        $idsToDelete = [];
        $skippedGroups = 0;
        $preview = [];

        foreach ($groups as $g) {
            $allIds = array_map('intval', explode(',', $g->all_ids));

            $answeredIds = DB::table('diag_answers')
                ->whereIn('question_id', $allIds)
                ->distinct()
                ->pluck('question_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            // Todos con respuestas → no se toca el grupo.
            if (count($answeredIds) >= count($allIds)) {
                $skippedGroups++;

                continue;
            }

            // Preferir conservar una con respuestas (no se puede borrar);
            // si no, MIN/MAX según --keep.
            if ($answeredIds !== []) {
                sort($answeredIds);
                $keepId = $answeredIds[0];
            } else {
                $keepId = $keep === 'last' ? (int) $g->max_id : (int) $g->min_id;
            }

            $toDelete = array_values(array_filter($allIds, fn ($id) => $id !== $keepId && ! in_array($id, $answeredIds, true)));

            foreach ($toDelete as $id) {
                $idsToDelete[] = $id;
            }

            if (count($preview) < 10) {
                $preview[] = [
                    $g->pensum_id,
                    $g->cnt,
                    $keepId,
                    implode(', ', $toDelete),
                    count($answeredIds) > 0 ? 'sí ('.implode(',', $answeredIds).')' : 'no',
                    mb_strimwidth((string) $g->pregunta, 0, 32, '…'),
                ];
            }
        }

        $idsToDelete = array_values(array_unique($idsToDelete));
        sort($idsToDelete);

        $this->newLine();
        $this->table(
            ['pensum_id', 'duplicados', 'keep_id', 'delete_ids', 'con_respuestas', 'pregunta (preview)'],
            $preview
        );

        if ($groups->count() > 10) {
            $this->line('… y '.($groups->count() - 10).' grupos más (usa -v para ver todos los IDs).');
        }

        // Contar opciones asociadas a lo que se borraría.
        $optionsCount = $idsToDelete === []
            ? 0
            : (int) DB::table('diag_options')->whereIn('question_id', $idsToDelete)->count();

        $this->newLine();
        $this->info('Resumen:');
        $this->line("  Grupos con duplicados       : {$groups->count()}");
        $this->line("  Grupos intactos (todos con respuestas): {$skippedGroups}");
        $this->line('  Preguntas a eliminar        : '.count($idsToDelete));
        $this->line("  Opciones a eliminar         : {$optionsCount}");

        if ($idsToDelete === []) {
            $this->info('Nada para eliminar.');

            return self::SUCCESS;
        }

        if ($this->output->isVerbose()) {
            $this->newLine();
            $this->line('IDs a eliminar: '.implode(', ', $idsToDelete));
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY-RUN completado — no se persistieron cambios.');
            $this->line('Ejecuta sin --dry-run y con --force para borrar.');

            return self::SUCCESS;
        }

        if (! $force && ! $this->confirm('¿Eliminar '.count($idsToDelete)." preguntas duplicadas con sus {$optionsCount} opciones?", false)) {
            $this->info('Abortado.');

            return self::SUCCESS;
        }

        // ── 3. Borrado en chunks dentro de transacción ──
        $chunkSize = 500;
        $deletedQuestions = 0;
        $deletedOptions = 0;
        $progress = $this->output->createProgressBar(count($idsToDelete));
        $progress->start();

        DB::beginTransaction();
        try {
            foreach (array_chunk($idsToDelete, $chunkSize) as $chunk) {
                // Re-verificar regla de respuestas dentro de la transacción.
                $stillAnswered = DB::table('diag_answers')
                    ->whereIn('question_id', $chunk)
                    ->distinct()
                    ->pluck('question_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                $safe = array_values(array_diff($chunk, $stillAnswered));

                if ($safe === []) {
                    $progress->advance(count($chunk));

                    continue;
                }

                // Opciones primero (explícito; la FK además es CASCADE).
                $deletedOptions += DB::table('diag_options')->whereIn('question_id', $safe)->delete();
                // Preguntas (query builder: sin eventos/observers).
                $deletedQuestions += DB::table('diag_questions')->whereIn('id', $safe)->delete();

                $progress->advance(count($chunk));
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $progress->finish();
            $this->newLine(2);
            $this->error('Error durante el borrado: '.$e->getMessage());
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        }

        $progress->finish();
        $this->newLine(2);
        $this->info("✓ Eliminación completada: {$deletedQuestions} preguntas y {$deletedOptions} opciones borradas.");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Grupos procesados', $groups->count()],
                ['Grupos intactos (con respuestas)', $skippedGroups],
                ['Preguntas eliminadas', $deletedQuestions],
                ['Opciones eliminadas', $deletedOptions],
                ['Estrategia keep', $keep],
            ]
        );

        return self::SUCCESS;
    }
}
