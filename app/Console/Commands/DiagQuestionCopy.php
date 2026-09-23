<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Pensum;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Copia las diag_questions (y sus diag_options) de un pensum origen a un
 * pensum destino, filtrando por diag_main_id.
 *
 * Uso:
 *   php8.2 artisan diag:copy-questions 1 112 113 --dry-run
 *   php8.2 artisan diag:copy-questions 1 112 113 --force
 *   php8.2 artisan diag:copy-questions --from=112 --to=113 --diag-main=1 --dry-run
 */
class DiagQuestionCopy extends Command
{
    protected $signature = 'diag:copy-questions
                            {diag_main? : ID del DiagMain}
                            {from? : Pensum origen}
                            {to? : Pensum destino}
                            {--diag-main= : ID del DiagMain (alternativa a argumento)}
                            {--from= : Pensum origen (alternativa a argumento)}
                            {--to= : Pensum destino (alternativa a argumento)}
                            {--dry-run : Mostrar cambios sin persistir}
                            {--force : Ejecutar sin confirmación}';

    protected $description = 'Copia preguntas (diag_questions + diag_options) de un pensum a otro filtrando por diag_main_id';

    private int $copiedQuestions = 0;

    private int $copiedOptions = 0;

    private int $skippedQuestions = 0;

    public function handle(): int
    {
        $diagMainId = (int) ($this->argument('diag_main') ?: $this->option('diag-main'));
        $fromPensumId = (int) ($this->argument('from') ?: $this->option('from'));
        $toPensumId = (int) ($this->argument('to') ?: $this->option('to'));
        $dryRun = (bool) $this->option('dry-run');

        if (! $diagMainId || ! $fromPensumId || ! $toPensumId) {
            $this->error('Debes indicar diag_main, from y to. Ej: php8.2 artisan diag:copy-questions 1 112 113 --dry-run');
            $this->line('  Argumentos: diag:copy-questions {diag_main} {from} {to}');
            $this->line('  Opciones:   --diag-main=1 --from=112 --to=113');

            return self::FAILURE;
        }

        if ($fromPensumId === $toPensumId) {
            $this->error('El pensum origen y destino no pueden ser el mismo.');

            return self::FAILURE;
        }

        $diagMain = DiagMain::with(['lapso', 'pestudio', 'referent'])->find($diagMainId);
        if (! $diagMain) {
            $this->error("DiagMain {$diagMainId} no encontrado.");

            return self::FAILURE;
        }

        $fromPensum = Pensum::with(['asignatura', 'grado', 'pestudio'])->find($fromPensumId);
        $toPensum = Pensum::with(['asignatura', 'grado', 'pestudio'])->find($toPensumId);

        if (! $fromPensum || ! $toPensum) {
            $this->error('Pensum origen o destino no encontrado.');

            return self::FAILURE;
        }

        $sourceQuestions = DiagQuestion::with('options')
            ->where('diag_main_id', $diagMain->id)
            ->where('pensum_id', $fromPensum->id)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        $this->info('=== DiagMain ===');
        $this->line("DiagMain {$diagMain->id}: {$diagMain->name} (active=" . ($diagMain->active ? '1' : '0') . ')');
        $this->newLine();
        $this->info('=== Origen ===');
        $this->line("Pensum {$fromPensum->id}: " . $this->pensumLabel($fromPensum));
        $this->line("Preguntas encontradas: {$sourceQuestions->count()} | Opciones totales: {$sourceQuestions->sum(fn ($q) => $q->options->count())}");
        $this->newLine();
        $this->info('=== Destino ===');
        $this->line("Pensum {$toPensum->id}: " . $this->pensumLabel($toPensum));
        $destExistingCount = DiagQuestion::where('diag_main_id', $diagMain->id)->where('pensum_id', $toPensum->id)->count();
        $this->line("Preguntas ya existentes en destino (mismo diag_main): {$destExistingCount}");
        $this->newLine();

        if ($sourceQuestions->isEmpty()) {
            $this->warn('El pensum origen no tiene preguntas para ese diag_main. Nada que copiar.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn('MODO DRY-RUN — no se escribirá nada.');
        }

        if (! $dryRun && ! $this->option('force') && ! $this->confirm('¿Copiar las preguntas al destino?', false)) {
            $this->info('Abortado.');

            return self::SUCCESS;
        }

        // Huella de preguntas ya existentes en destino para idempotencia
        $existingFingerprints = DiagQuestion::where('diag_main_id', $diagMain->id)
            ->where('pensum_id', $toPensum->id)
            ->get(['pregunta', 'tipo_pregunta', 'difficulty', 'orden', 'weighing'])
            ->mapWithKeys(fn ($q) => [$this->fingerprint($q) => true]);

        DB::beginTransaction();

        try {
            foreach ($sourceQuestions as $source) {
                $fp = $this->fingerprint($source);

                if ($existingFingerprints->has($fp)) {
                    $this->line("  → q {$source->id}: ya existe en destino, skip — " . $this->shortPregunta($source->pregunta));
                    $this->skippedQuestions++;

                    continue;
                }

                if ($dryRun) {
                    $this->line("  ○ q {$source->id}: se copiaría — " . $this->shortPregunta($source->pregunta) . " ({$source->options->count()} opciones)");
                    $this->copiedQuestions += 1;
                    $this->copiedOptions += $source->options->count();

                    continue;
                }

                $copy = $source->replicate();
                $copy->pensum_id = $toPensum->id;
                // diag_main_id se mantiene igual (mismo instrumento)
                $copy->save();

                foreach ($source->options as $option) {
                    $optionCopy = $option->replicate();
                    $optionCopy->question_id = $copy->id;
                    $optionCopy->save();
                    $this->copiedOptions++;
                }

                $existingFingerprints->put($fp, true);
                $this->copiedQuestions++;
                $this->line("  ✓ q {$source->id} → {$copy->id}: " . $this->shortPregunta($source->pregunta) . " ({$source->options->count()} opciones)");
            }

            if ($dryRun) {
                DB::rollBack();
                $this->newLine();
                $this->warn('DRY-RUN completado — cambios no persistidos.');
            } else {
                DB::commit();
                $this->newLine();
                $this->info('Copia completada.');
            }

            $this->newLine();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Preguntas copiadas', $this->copiedQuestions],
                    ['Opciones copiadas', $this->copiedOptions],
                    ['Preguntas omitidas (ya existían)', $this->skippedQuestions],
                ]
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Error: {$e->getMessage()}");
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        }
    }

    private function fingerprint(DiagQuestion $q): string
    {
        return implode('|', [
            $this->normalizePregunta($q->pregunta),
            trim((string) $q->tipo_pregunta),
            trim((string) $q->difficulty),
        ]);
    }

    private function normalizePregunta(string $pregunta): string
    {
        $t = trim($pregunta);
        $t = preg_replace('/\s+/', ' ', $t);
        // lower para evitar duplicados por mayúsculas/acentos triviales; se mantiene original en BD
        return mb_strtolower($t);
    }

    private function shortPregunta(string $pregunta): string
    {
        $t = trim(preg_replace('/\s+/', ' ', $pregunta));
        if (mb_strlen($t) > 80) {
            return mb_substr($t, 0, 77) . '...';
        }

        return $t;
    }

    private function pensumLabel(Pensum $pensum): string
    {
        $parts = [];
        if ($pensum->asignatura) {
            $parts[] = $pensum->asignatura->full_name ?? $pensum->asignatura->name ?? "asig #{$pensum->asignatura_id}";
        } else {
            $parts[] = "asig #{$pensum->asignatura_id}";
        }
        if ($pensum->grado) {
            $parts[] = $pensum->grado->full_name ?? $pensum->grado->name ?? "grado #{$pensum->grado_id}";
        }
        if ($pensum->pestudio) {
            $parts[] = $pensum->pestudio->name ?? "pestudio #{$pensum->pestudio_id}";
        }

        return implode(' | ', $parts) . " (pensum #{$pensum->id})";
    }
}

/*
============================================================================
 INSTRUCTIVO PASO A PASO — diag:copy-questions
============================================================================

OBJETIVO
  Copiar las preguntas (tabla `diag_questions`) y sus opciones (tabla
  `diag_options`) desde un pensum ORIGEN hacia un pensum DESTINO,
  filtrando por `diag_main_id`. Todas las preguntas copiadas mantienen
  el mismo `diag_main_id` pero cambian `pensum_id` al destino.

TABLAS
  - diag_mains     (instrumento/cabecera, ej. id=1 Diagnóstico Educativo)
  - diag_questions (pregunta + pensum_id + diag_main_id)
  - diag_options   (opciones por pregunta, FK question_id)

PARAMETROS
  diag_main   ID del DiagMain (obligatorio)
  from        ID del pensum origen (obligatorio)
  to          ID del pensum destino (obligatorio)

  Se pueden pasar como argumentos posicionales o como opciones:
    php8.2 artisan diag:copy-questions 1 112 113 --dry-run
    php8.2 artisan diag:copy-questions --diag-main=1 --from=112 --to=113 --dry-run

OPCIONES
  --diag-main=ID   Alternativa a argumento diag_main
  --from=ID        Alternativa a argumento from
  --to=ID          Alternativa a argumento to
  --dry-run        Simula sin escribir nada en la base
  --force          Ejecuta sin pedir confirmación
  --help           Muestra la ayuda del comando

PASO 0 — IDENTIFICAR LOS IDs
  Localiza el DiagMain y los dos pensums:
    php8.2 artisan tinker
    >>> \App\Models\app\Instrument\DiagMain::find(1);
    >>> \App\Models\app\Academy\Pensum::with('asignatura','grado')->find(112);
    >>> \App\Models\app\Academy\Pensum::with('asignatura','grado')->find(113);

PASO 1 — SIMULAR (DRY-RUN) SIEMPRE PRIMERO
  No escribe nada; muestra qué preguntas se copiarían y cuáles se omiten.
    php8.2 artisan diag:copy-questions 1 112 113 --dry-run
    php8.2 artisan diag:copy-questions --diag-main=1 --from=112 --to=113 --dry-run
  Revisa el resumen final:
    - "Preguntas copiadas": cuántas se insertarían.
    - "Opciones copiadas": total de diag_options que se insertarían.
    - "Preguntas omitidas (ya existían)": ya presentes en el destino.

PASO 2 — EJECUTAR LA COPIA REAL
  Sin --dry-run. Si no usas --force, el comando pide confirmación interactiva.
    php8.2 artisan diag:copy-questions 1 112 113 --force
  Todo ocurre dentro de una transacción: si algo falla, se revierte por completo.

PASO 3 — VERIFICAR EL RESULTADO
  Comprueba que las preguntas y opciones quedaron en el destino:
    php8.2 artisan tinker
    >>> \App\Models\app\Instrument\DiagQuestion::where('diag_main_id',1)->where('pensum_id',113)->withCount('options')->get();
    >>> \App\Models\app\Instrument\DiagQuestion::where('diag_main_id',1)->where('pensum_id',113)->count();

NOTAS IMPORTANTES
  - Idempotente: se ejecute las veces que se ejecute, omite una pregunta si
    en el destino ya existe otra con la misma huella
    (pregunta normalizada + tipo_pregunta + difficulty). No genera duplicados.
  - Mantiene diag_main_id, tipo_pregunta, orden, weighing, difficulty, activo,
    competency_id, indicator_id. Solo cambia pensum_id al destino.
  - Copia todas las diag_options asociadas (opcion, valor, orden) con el nuevo
    question_id.
  - NO copia respuestas (diag_answers), sesiones ni reportes.
  - Transaccional: ante cualquier error hace rollback completo.
  - Origen nunca se modifica.

EJEMPLOS
  # Copiar de pensum 112 a 113 dentro del diag_main 1, simulando:
  php8.2 artisan diag:copy-questions 1 112 113 --dry-run

  # Copiar real con confirmación automática:
  php8.2 artisan diag:copy-questions 1 112 113 --force

  # Con opciones largas:
  php8.2 artisan diag:copy-questions --diag-main=1 --from=112 --to=113 --dry-run

  # Ver la ayuda y las opciones disponibles:
  php8.2 artisan diag:copy-questions --help

============================================================================
*/
