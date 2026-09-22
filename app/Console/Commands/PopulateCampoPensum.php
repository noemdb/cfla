<?php

namespace App\Console\Commands;

use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\AreaConocimiento;
use Illuminate\Console\Command;

/**
 * Pobla campo_conocimientos.pensum_id según la cadena
 * pevaluacion → pensum → asignatura.
 *
 * Para cada adscripción (área ↔ asignatura) resuelve el pensum con:
 *   1) pensum cuyo pestudio coincide con el del área (si hay exactamente 1),
 *   2) si la asignatura tiene un único pensum,
 *   3) el pensum con más pevaluaciones (el realmente impartido).
 *
 * Es idempotente y reutiliza CampoConocimiento::resolvePensumId(). Sin
 * `--force` solo escribe los registros con pensum_id NULL; con `--force`
 * recalcula y sobrescribe todos (incluidos los ya poblados).
 *
 * Uso:
 *   php8.2 artisan campo:poblar-pensum --dry-run
 *   php8.2 artisan campo:poblar-pensum
 *   php8.2 artisan campo:poblar-pensum --force
 */
class PopulateCampoPensum extends Command
{
    protected $signature = 'campo:poblar-pensum
        {--force : Recalcular y sobrescribir pensum_id aunque ya esté poblado}
        {--dry-run : Solo audit, no persiste}';

    protected $description = 'Pobla campo_conocimientos.pensum_id según la cadena pevaluacion → pensum → asignatura';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');

        $query = CampoConocimiento::query()->with('area_conocimiento');

        if (! $force) {
            $query->whereNull('pensum_id');
        }

        $campos = $query->get();

        if ($campos->isEmpty()) {
            $this->info('No hay adscripciones por procesar. Nada que hacer.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Procesando %s adscripciones%s…', $campos->count(), $dryRun ? ' (dry-run)' : ''));

        $rows = [];
        $updated = 0;
        $unresolved = 0;

        foreach ($campos as $campo) {
            $areaPestudioId = $campo->area_conocimiento?->pestudio_id;
            $pensumId = CampoConocimiento::resolvePensumId($campo->asignatura_id, $areaPestudioId);

            if ($pensumId === null) {
                $rows[] = [$campo->id, $campo->asignatura_id, $campo->asignatura?->code ?? '?', $areaPestudioId ?? '-', '-', 'sin pensum'];
                $unresolved++;

                continue;
            }

            $rows[] = [$campo->id, $campo->asignatura_id, $campo->asignatura?->code ?? '?', $areaPestudioId ?? '-', $campo->pensum_id ?? '-', $pensumId];

            if (! $dryRun) {
                $campo->update(['pensum_id' => $pensumId]);
            }
            $updated++;
        }

        $this->table(['Campo ID', 'Asignatura ID', 'Código', 'Área Pestudio', 'Pensum previo', 'Pensum nuevo'], $rows);

        if ($dryRun) {
            $this->info("--dry-run: {$updated} se actualizarían".($unresolved ? " · {$unresolved} sin resolver" : '').'.');

            return self::SUCCESS;
        }

        $this->info("Actualizadas {$updated} adscripciones".($unresolved ? " · {$unresolved} sin resolver (revisar)" : '').'.');

        return self::SUCCESS;
    }
}
