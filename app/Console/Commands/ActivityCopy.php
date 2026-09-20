<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Copia las activities y sus achievements de una Pevaluación origen a una
 * Pevaluación destino. La fuente de datos es seleccionable:
 *   1 = DB_CONNECTION (base actual)
 *   2 = DB_CONNECTION_S2526 (base del período anterior) — por defecto
 *
 * Uso:
 *   php8.2 artisan activity:copy --from=1920 --to=215268 --dry-run
 *   php8.2 artisan activity:copy --from=1920 --to=215268 --source=2 --force
 *   Fuente: DB_CONNECTION (base actual)
 *   php8.2 artisan activity:copy --from=1927 --to=1896 --source=1 --dry-run
 *   php8.2 artisan activity:copy --from=215176 --to=1893 --source=1 --dry-run
 */
class ActivityCopy extends Command
{
    protected $signature = 'activity:copy
                          {--from= : ID de la Pevaluación origen (en la conexión fuente)}
                          {--to= : ID de la Pevaluación destino (en la conexión destino)}
                          {--source=2 : Fuente de datos (1=DB_CONNECTION, 2=DB_CONNECTION_S2526)}
                          {--target-connection= : Conexión destino (por defecto, DB_CONNECTION)}
                          {--dry-run : Mostrar cambios sin persistir}
                          {--force : Ejecutar sin confirmación}';

    protected $description = 'Copia las actividades (y sus indicadores) entre Pevaluaciones, eligiendo la fuente de datos (DB_CONNECTION o S2526)';

    private int $copiedActivities = 0;

    private int $copiedAchievements = 0;

    private int $skippedActivities = 0;

    public function handle(): int
    {
        $fromId = (int) $this->option('from');
        $toId = (int) $this->option('to');
        $dryRun = (bool) $this->option('dry-run');

        $sourceConnection = $this->resolveSourceConnection((string) $this->option('source'));
        if ($sourceConnection === null) {
            $this->error('Fuente de datos inválida. Usa --source=1 (DB_CONNECTION) o --source=2 (DB_CONNECTION_S2526).');

            return self::FAILURE;
        }

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
        $this->line("Fuente: {$this->option('source')} (conexión: {$sourceConnection})");
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
     * Resuelve la conexión de origen a partir de la opción --source.
     * 1 = DB_CONNECTION, 2 = DB_CONNECTION_S2526.
     */
    private function resolveSourceConnection(string $source): ?string
    {
        return match ($source) {
            '1' => (string) config('database.default'),
            '2' => 's2526',
            default => null,
        };
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

/*
============================================================================
 INSTRUCTIVO PASO A PASO — activity:copy
============================================================================

OBJETIVO
  Copiar las actividades (tabla `activities`) y sus indicadores (tabla
  `achievements`) desde una Pevaluación ORIGEN hacia una Pevaluación DESTINO,
  pudiendo elegir la base de datos de la que se lee.

FUENTES DE DATOS (opción --source)
  1 = DB_CONNECTION        (base actual, p. ej. s2627)
  2 = DB_CONNECTION_S2526  (base del período anterior)  <-- POR DEFECTO

  El DESTINO siempre es la conexión por defecto (DB_CONNECTION). Se puede
  sobrescribir con --target-connection=NOMBRE.

OPCIONES
  --from=ID                ID de la Pevaluación origen (obligatorio).
  --to=ID                  ID de la Pevaluación destino (obligatorio).
  --source=1|2             Fuente de datos (por defecto 2 = S2526).
  --target-connection=     Conexión destino (por defecto la default).
  --dry-run                Simula sin escribir nada en la base.
  --force                  Ejecuta sin pedir confirmación.
  --help                   Muestra la ayuda del comando.

PASO 0 — IDENTIFICAR LOS IDs DE PEVALUACIÓN
  Localiza en el sistema (o en la BD) los IDs de la Pevaluación origen y
  destino. La Pevaluación es el "Plan de Evaluación" (materia + sección +
  momento). Puedes verificarlos con:
    php8.2 artisan tinker
    >>> \App\Models\app\Academy\Pevaluacion::on('s2526')->find(1920);
    >>> \App\Models\app\Academy\Pevaluacion::on('mysql')->find(215268);

PASO 1 — SIMULAR (DRY-RUN) SIEMPRE PRIMERO
  No escribe nada; muestra qué actividades se copiarían y cuáles se omiten.
    php8.2 artisan activity:copy --from=1920 --to=215268 --dry-run
    php8.2 artisan activity:copy --from=1920 --to=215268 --source=1 --dry-run
  Revisa el resumen final:
    - "Actividades copiadas": cuántas se insertarían.
    - "Actividades omitidas (ya existían)": ya presentes en el destino.

PASO 2 — EJECUTAR LA COPIA REAL
  Sin --dry-run. Si no usas --force, el comando pide confirmación interactiva.
    php8.2 artisan activity:copy --from=1920 --to=215268 --force
  Todo ocurre dentro de una transacción en el destino: si algo falla, se
  revierte por completo.

PASO 3 — VERIFICAR EL RESULTADO
  Comprueba que las actividades e indicadores quedaron en el destino:
    php8.2 artisan tinker
    >>> $p = \App\Models\app\Academy\Pevaluacion::on('mysql')->find(215268);
    >>> $p->activities()->withCount('achievements')->get();

NOTAS IMPORTANTES
  - Idempotente: se ejecute las veces que se ejecute, omite una actividad si
    en el destino ya existe otra con la misma huella
    (topic + thematic + finicial + ffinal). No genera duplicados.
  - El campo `comments` de cada actividad copiada se guarda en NULL (los
    comentarios de aprobación pertenecen a la sección de origen).
  - Solo copia `activities` y `achievements`; NO copia relaciones LMS
    (secciones, recursos, enlaces, publicaciones, logs).
  - NO existe rollback automático. Si necesitas deshacer, elimina en el
    destino las actividades con `pevaluacion_id` = --to creadas en la corrida.
  - La base ORIGEN nunca se modifica.

EJEMPLOS
  # Copiar de S2526 (por defecto) hacia la base actual, simulando:
  php8.2 artisan activity:copy --from=1920 --to=215268 --dry-run

  # Copiar de S2526 hacia la base actual, confirmando:
  php8.2 artisan activity:copy --from=1920 --to=215268 --force

  # Copiar desde la propia base actual (mismo DB, otra Pevaluación):
  php8.2 artisan activity:copy --from=1920 --to=215268 --source=1 --force

  # Ver la ayuda y las opciones disponibles:
  php8.2 artisan activity:copy --help

============================================================================
*/
