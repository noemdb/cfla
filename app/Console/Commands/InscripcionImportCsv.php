<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Escolaridad;
use App\Models\app\Academy\Programacion;
use App\Models\app\Academy\Tinscripcion;
use App\Services\Planning\ConformadoCiGenerator;
use App\Services\Planning\InscripcionCsvImporter;
use Illuminate\Console\Command;

/**
 * Importa inscripciones desde los CSV de blueprint/inscripcion/csv usando la
 * misma lógica del modal "Importar CSV" de /app/planning/inscripcions.
 *
 * Particularidad: las filas sin CI (columna ci_estudiant vacía) reciben un CI
 * conformado de 7 dígitos + 3 letras mayúsculas (p. ej. 4829137XKQ),
 * aleatorio no consecutivo y verificado contra la tabla `estudiants`
 * (índice único) para garantizar que no exista. El estudiante se crea como
 * nuevo y se inscribe en la sección indicada por el CSV.
 *
 * Uso:
 *   php8.2 artisan inscripcion:import-csv --dry-run
 *   php8.2 artisan inscripcion:import-csv
 *   php8.2 artisan inscripcion:import-csv --path=blueprint/inscripcion/csv/PRIMER_ANO_A.csv
 *   php8.2 artisan inscripcion:import-csv --path=blueprint/inscripcion/csv/PRIMER_ANO_B.csv
 */
class InscripcionImportCsv extends Command
{
    protected $signature = 'inscripcion:import-csv
        {--path= : Archivo o directorio CSV (default: blueprint/inscripcion/csv)}
        {--pestudio= : ID del plan de estudio para desambiguar el grado}
        {--tipo= : ID del tipo de inscripción (default: el primero de la BD)}
        {--escolaridad= : ID de escolaridad (default: la primera de la BD)}
        {--programacion= : ID de programación (default: la primera de la BD)}
        {--grupo-estable= : ID del grupo estable (opcional)}
        {--plan-pago= : ID del plan de pago para estudiantes nuevos (opcional)}
        {--representante-ci= : CI del representante para estudiantes nuevos (opcional)}
        {--update-academic-data : Actualiza tipo/escolaridad/programación/grupo en inscripciones existentes}
        {--update-student-names : Actualiza nombre/apellido de estudiantes existentes}
        {--dry-run : Muestra la vista previa sin persistir cambios}';

    protected $description = 'Importa los CSV de blueprint/inscripcion/csv generando CI conformados (7 dígitos + 3 letras) para las filas sin cédula';

    public function handle(InscripcionCsvImporter $importer): int
    {
        $path = (string) ($this->option('path') ?: base_path('blueprint/inscripcion/csv'));
        $files = $this->resolveFiles($path);

        if ($files === []) {
            $this->error("No se encontraron archivos CSV en: {$path}");

            return self::FAILURE;
        }

        $options = $this->buildOptions();

        if (! $options['tipo_id'] || ! $options['escolaridad_id'] || ! $options['programacion_id']) {
            $this->error('Faltan datos base: se requiere al menos un tipo de inscripción, una escolaridad y una programación en la BD (o pasarlos por opción).');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $reserved = [];

        $this->line($dryRun
            ? '<fg=yellow>Modo DRY-RUN: no se persiste ningún cambio.</>'
            : '<fg=green>Importando inscripciones...</>');
        $this->line(sprintf(
            'Archivos: %d · tipo=%s · escolaridad=%s · programación=%s · CI conformado: 7 dígitos + 3 letras',
            count($files),
            $options['tipo_id'],
            $options['escolaridad_id'],
            $options['programacion_id'],
        ));

        $totals = [
            'created' => 0,
            'inscribed' => 0,
            'updated' => 0,
            'ci_updated' => 0,
            'unchanged' => 0,
            'skipped' => 0,
            'discarded' => 0,
            'generated' => 0,
            'errors' => 0,
        ];

        foreach ($files as $file) {
            $this->newLine();
            $this->info('▶ '.$this->relativePath($file));

            try {
                $rows = $importer->parse($file);
            } catch (\Throwable $e) {
                $this->error('  No se pudo leer el archivo: '.$e->getMessage());
                $totals['errors']++;

                continue;
            }

            if ($rows === []) {
                $this->warn('  Sin filas detectadas.');

                continue;
            }

            $generated = $this->fillMissingCi($rows, $reserved);
            $totals['generated'] += $generated;

            $preview = $importer->preview($rows, $options);

            $this->line(sprintf(
                '  %d fila(s) · %d CI generado(s) · %d con error',
                count($preview),
                $generated,
                count(array_filter($preview, fn ($item) => ($item['status'] ?? '') !== 'ok')),
            ));

            if ($dryRun) {
                $this->renderPreview($preview);

                continue;
            }

            $report = $importer->import($preview, $options);

            $totals['created'] += $report['created'];
            $totals['inscribed'] += $report['inscribed'];
            $totals['updated'] += $report['updated'];
            $totals['ci_updated'] += $report['ci_updated'] ?? 0;
            $totals['unchanged'] += $report['unchanged'];
            $totals['skipped'] += $report['skipped'];
            $totals['discarded'] += $report['discarded'] ?? 0;
            $totals['errors'] += count($report['errors']);

            $this->line(sprintf(
                '  creados: %d · inscritos: %d · actualizados: %d · CI actualizadas: %d · sin cambios: %d · omitidos: %d · descartados: %d',
                $report['created'],
                $report['inscribed'],
                $report['updated'],
                $report['ci_updated'] ?? 0,
                $report['unchanged'],
                $report['skipped'],
                $report['discarded'] ?? 0,
            ));

            foreach ($report['errors'] as $error) {
                $this->error(sprintf(
                    '    línea %s (CI %s): %s',
                    $error['line'] ?? '?',
                    $error['ci'] ?? '?',
                    $error['message'],
                ));
            }
        }

        $this->newLine();
        $this->line('<fg=cyan>Resumen '.($dryRun ? '(dry-run)' : '').'</>');
        $this->table(
            ['CI generados', 'Creados', 'Inscritos', 'Actualizados', 'CI actualizadas', 'Sin cambios', 'Omitidos', 'Descartados', 'Errores'],
            [[
                $totals['generated'],
                $totals['created'],
                $totals['inscribed'],
                $totals['updated'],
                $totals['ci_updated'],
                $totals['unchanged'],
                $totals['skipped'],
                $totals['discarded'],
                $totals['errors'],
            ]],
        );

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function resolveFiles(string $path): array
    {
        if (is_file($path)) {
            return [realpath($path) ?: $path];
        }

        if (! is_dir($path)) {
            return [];
        }

        $files = [];
        foreach (scandir($path) ?: [] as $entry) {
            if (strtolower((string) pathinfo($entry, PATHINFO_EXTENSION)) !== 'csv') {
                continue;
            }

            $files[] = $path.DIRECTORY_SEPARATOR.$entry;
        }

        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        return $files;
    }

    /**
     * Rellena el CI de las filas vacías con un CI conformado único
     * (7 dígitos + 3 letras mayúsculas).
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<string, bool>  $reserved
     * @return int  Cantidad de CI generados
     */
    private function fillMissingCi(array &$rows, array &$reserved): int
    {
        $count = 0;
        $generator = app(ConformadoCiGenerator::class);

        foreach ($rows as &$row) {
            if (trim((string) ($row['ci_estudiant'] ?? '')) !== '') {
                continue;
            }

            $row['ci_estudiant'] = $generator->generate($reserved);
            $row['ci_generated'] = true;
            $count++;
        }
        unset($row);

        return $count;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOptions(): array
    {
        return [
            'pestudio_id' => $this->option('pestudio') ?: null,
            'tipo_id' => $this->option('tipo') ?: Tinscripcion::orderBy('id')->value('id'),
            'escolaridad_id' => $this->option('escolaridad') ?: Escolaridad::orderBy('id')->value('id'),
            'programacion_id' => $this->option('programacion') ?: Programacion::orderBy('id')->value('id'),
            'grupo_estable_id' => $this->option('grupo-estable') ?: null,
            'plan_pago_id' => $this->option('plan-pago') ?: null,
            'representant_ci' => $this->option('representante-ci') ?: null,
            'update_academic_data' => (bool) $this->option('update-academic-data'),
            'update_student_names' => (bool) $this->option('update-student-names'),
            'observations_note' => 'Importado desde CSV ('.now()->format('d/m/Y').')',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $preview
     */
    private function renderPreview(array $preview): void
    {
        $rows = [];
        foreach ($preview as $item) {
            $rows[] = [
                $item['line'] ?? '',
                $item['ci'] ?? '',
                trim(($item['name'] ?? '').' '.($item['lastname'] ?? '')),
                $item['seccion_label'] ?? ($item['grado'] ?? '').' · '.($item['seccion'] ?? ''),
                ($item['status'] ?? '') === 'ok' ? ($item['action'] ?? '') : 'Error',
                $item['message'] ?? '',
            ];
        }

        $this->table(['Línea', 'CI', 'Estudiante', 'Sección', 'Acción', 'Mensaje'], $rows);
    }

    private function relativePath(string $path): string
    {
        $base = base_path().DIRECTORY_SEPARATOR;

        return str_starts_with($path, $base) ? substr($path, strlen($base)) : $path;
    }
}
