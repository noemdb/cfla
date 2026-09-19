<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Copia las activities y sus achievements de una Pevaluación origen (base de
 * datos S2526) a una Pevaluación destino (base de datos actual).
 *
 * Uso:
 *   php8.2 artisan activity:copy --from=1920 --to=215268 --dry-run
 *   php8.2 artisan activity:copy --from=1920 --to=215268 --force
 *   php8.2 artisan activity:copy --from=1920 --to=215268 --force
 */
class ActivityCopy extends Command
{
    protected $signature = 'activity:copy
                          {--from= : ID de la Pevaluación origen (en la conexión fuente)}
                          {--to= : ID de la Pevaluación destino (en la conexión destino)}
                          {--source-connection=s2526 : Conexión de la Pevaluación origen}
                          {--target-connection= : Conexión de la Pevaluación destino (por defecto, la default)}
                          {--dry-run : Mostrar cambios sin persistir}
                          {--force : Ejecutar sin confirmación}';

    protected $description = 'Copia las actividades (y sus indicadores) desde la Pevaluación origen (S2526) a la Pevaluación destino';

    private int $copiedActivities = 0;

    private int $copiedAchievements = 0;

    private int $skippedActivities = 0;

    public function handle(): int
    {
        $fromId = (int) $this->option('from');
        $toId = (int) $this->option('to');
        $dryRun = (bool) $this->option('dry-run');

        $sourceConnection = (string) $this->option('source-connection');
        $targetConnection = (string) ($this->option('target-connection') ?: config('database.default'));

        if (! $fromId || ! $toId) {
            $this->error('Debes indicar --from y --to con los IDs de las Pevaluaciones.');

            return self::FAILURE;
        }

        if ($fromId === $toId && $sourceConnection === $targetConnection) {
            $this->error('La Pevaluación origen y destino no pueden ser la misma.');

            return self::FAILURE;
        }

        $from = Pevaluacion::on($sourceConnection)
            ->with('pensum.asignatura', 'pensum.grado', 'seccion', 'lapso')
            ->find($fromId);

        $to = Pevaluacion::on($targetConnection)
            ->with('pensum.asignatura', 'pensum.grado', 'seccion', 'lapso')
            ->find($toId);

        if (! $from || ! $to) {
            $this->error('Pevaluación origen o destino no encontrada.');

            return self::FAILURE;
        }

        $sourceActivities = Activity::on($sourceConnection)
            ->with('achievements')
            ->where('pevaluacion_id', $from->id)
            ->orderBy('finicial')
            ->orderBy('id')
            ->get();

        $this->info('=== Origen ===');
        $this->line("Conexión: {$sourceConnection}");
        $this->line("Pevaluación {$from->id}: {$from->full_name}");
        $this->line("Actividades: {$sourceActivities->count()} | Indicadores: {$sourceActivities->sum(fn ($a) => $a->achievements->count())}");
        $this->newLine();
        $this->info('=== Destino ===');
        $this->line("Conexión: {$targetConnection}");
        $this->line("Pevaluación {$to->id}: {$to->full_name}");
        $this->newLine();

        if ($sourceActivities->isEmpty()) {
            $this->warn('La Pevaluación origen no tiene actividades para copiar.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn('MODO DRY-RUN — no se escribirá nada.');
        }

        if (! $dryRun && ! $this->option('force') && ! $this->confirm('¿Copiar las actividades al destino?', false)) {
            $this->info('Abortado.');

            return self::SUCCESS;
        }

        $existingFingerprints = Activity::on($targetConnection)
            ->where('pevaluacion_id', $to->id)
            ->get(['topic', 'thematic', 'finicial', 'ffinal'])
            ->mapWithKeys(fn ($a) => [$this->fingerprint($a) => true]);

        DB::connection($targetConnection)->beginTransaction();

        try {
            foreach ($sourceActivities as $source) {
                if ($existingFingerprints->has($this->fingerprint($source))) {
                    $this->line("  → act {$source->id}: ya existe en destino, skip — {$source->topic}");
                    $this->skippedActivities++;

                    continue;
                }

                if ($dryRun) {
                    $this->line("  ○ act {$source->id}: se copiaría — {$source->topic}");
                    $this->copiedActivities += 1;
                    $this->copiedAchievements += $source->achievements->count();

                    continue;
                }

                $copy = $source->replicate();
                $copy->setConnection($targetConnection);
                $copy->pevaluacion_id = $to->id;
                $copy->comments = null;
                $copy->save();

                foreach ($source->achievements as $achievement) {
                    $achievementCopy = $achievement->replicate();
                    $achievementCopy->setConnection($targetConnection);
                    $achievementCopy->activity_id = $copy->id;
                    $achievementCopy->save();
                    $this->copiedAchievements++;
                }

                $existingFingerprints->put($this->fingerprint($copy), true);
                $this->copiedActivities++;
                $this->line("  ✓ act {$source->id} → {$copy->id}: {$source->topic}");
            }

            if ($dryRun) {
                DB::connection($targetConnection)->rollBack();
                $this->newLine();
                $this->warn('DRY-RUN completado — cambios no persistidos.');
            } else {
                DB::connection($targetConnection)->commit();
                $this->newLine();
                $this->info('Copia completada.');
            }

            $this->newLine();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Actividades copiadas', $this->copiedActivities],
                    ['Indicadores copiados', $this->copiedAchievements],
                    ['Actividades omitidas (ya existían)', $this->skippedActivities],
                ]
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::connection($targetConnection)->rollBack();
            $this->error("Error: {$e->getMessage()}");
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        }
    }

    /**
     * Huella de una actividad para detectar copias ya existentes en el destino.
     */
    private function fingerprint(Activity $activity): string
    {
        return implode('|', [
            trim((string) $activity->topic),
            trim((string) $activity->thematic),
            $activity->finicial ? Carbon::parse($activity->finicial)->toDateString() : '',
            $activity->ffinal ? Carbon::parse($activity->ffinal)->toDateString() : '',
        ]);
    }
}
