<?php

namespace App\Console\Commands;

use App\Services\Planning\ActivityCopyService;
use Illuminate\Console\Command;

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

    public function handle(ActivityCopyService $service): int
    {
        $fromId = (int) $this->option('from');
        $toId = (int) $this->option('to');
        $dryRun = (bool) $this->option('dry-run');

        $sourceConnection = $service->resolveSourceConnection((string) $this->option('source'));
        if ($sourceConnection === null) {
            $this->error('Fuente de datos inválida. Usa --source=1 (DB_CONNECTION) o --source=2 (DB_CONNECTION_S2526).');

            return self::FAILURE;
        }

        $targetConnection = (string) ($this->option('target-connection') ?: config('database.default'));

        try {
            $preview = $service->preview($fromId, $toId, (string) $this->option('source'), $targetConnection);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $from = $preview['from'];
        $to = $preview['to'];
        $sourceActivities = $preview['toCopy']->concat($preview['skipped'])->sortBy([['finicial', 'asc'], ['id', 'asc']])->values();

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

        if ($dryRun) {
            $this->warn('MODO DRY-RUN — no se escribirá nada.');
            foreach ($preview['toCopy'] as $source) {
                $this->line("  ○ act {$source->id}: se copiaría — {$source->topic}");
            }
            foreach ($preview['skipped'] as $source) {
                $this->line("  → act {$source->id}: ya existe en destino, skip — {$source->topic}");
            }
            $this->newLine();
            $this->warn('DRY-RUN completado — cambios no persistidos.');
            $this->newLine();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Actividades que se copiarían', $preview['toCopy']->count()],
                    ['Indicadores que se copiarían', $preview['achievementsToCopy']],
                    ['Actividades omitidas (ya existían)', $preview['skipped']->count()],
                ]
            );

            return self::SUCCESS;
        }

        try {
            $result = $service->copy($fromId, $toId, (string) $this->option('source'), $targetConnection);

            foreach ($result['details'] as $detail) {
                if ($detail['status'] === 'skipped') {
                    $this->line("  → act {$detail['id']}: ya existe en destino, skip — {$detail['topic']}");
                } else {
                    $this->line("  ✓ act {$detail['id']} → {$detail['new_id']}: {$detail['topic']}");
                }
            }

            $this->newLine();
            $this->info('Copia completada.');
            $this->newLine();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Actividades copiadas', $result['copiedActivities']],
                    ['Indicadores copiados', $result['copiedAchievements']],
                    ['Actividades omitidas (ya existían)', $result['skippedActivities']],
                ]
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error: {$e->getMessage()}");
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        }
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
