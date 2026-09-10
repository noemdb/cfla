<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Asignatura;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Normaliza `hour_t_week` / `hour_p_week` a partir de la carga legacy
 * documentada en `blueprint/school-timetable/legacy/csv/legacy_carga_docentes.csv`.
 *
 * Objetivo: que cada asignatura deje de depender de valores vacíos o
 * inconsistentes y pase a seguir la distribución legada del colegio.
 *
 * Regla de sincronización:
 * - se toma el total semanal legado por área/asignatura
 * - si el área indica práctica/laboratorio, se reserva parte como práctica
 * - por defecto, la diferencia queda teórica
 * - sin `--force` solo se actualizan registros con carga 0/nula
 * - con `--force` se alinea también a asignaturas ya informadas
 *
 * Uso:
 *   php8.2 artisan timetable:normalize-legacy-hours --dry-run
 *   php8.2 artisan timetable:normalize-legacy-hours
 *   php8.2 artisan timetable:normalize-legacy-hours --force --lapso=1
 */
class TimetableNormalizeLegacyHours extends Command
{
    protected $signature = 'timetable:normalize-legacy-hours
        {--csv= : Ruta al CSV legacy con carga docente (default: blueprint/school-timetable/legacy/csv/legacy_carga_docentes.csv)}
        {--lapso= : Filtrar solo asignaturas que participan en ese lapso}
        {--force : Sobrescribe valores ya definidos}
        {--dry-run : Solo audit, no persiste}
        {--all : Procesa todas las asignaturas (no solo las vacías)}';

    protected $description = 'Normaliza horas teóricas/prácticas de asignaturas usando la carga legacy del horario escolar';

    /**
     * Base normativa legacy por plan de estudio. Se toma como estándar de
     * referencia para las asignaturas con carga nula o inconsistente.
     */
    private const LEGACY_PLAN_MAPS = [
        'EDUCACION PRIMARIA' => [
            'LENGUA Y LITERATURA' => [4, 0],
            'LENGUA' => [4, 0],
            'CASTELLANO' => [4, 0],
            'MATEMATICA' => [4, 0],
            'MATEMATICAS' => [4, 0],
            'CIENCIAS NATURALES' => [2, 0],
            'CIENCIAS SOCIALES' => [2, 0],
            'EDUCACION FISICA' => [2, 0],
            'EDUCACION ESTETICA' => [1, 0],
            'ARTES' => [1, 0],
            'INGLES' => [2, 0],
            'FORMACION HUMANO CRISTIANA' => [1, 0],
            'MUSICA' => [1, 0],
        ],
        'EDUCACION MEDIA GENERAL' => [
            'LENGUA Y LITERATURA' => [4, 0],
            'LENGUA' => [4, 0],
            'CASTELLANO' => [4, 0],
            'MATEMATICA' => [4, 0],
            'MATEMATICAS' => [4, 0],
            'BIOLOGIA' => [3, 1],
            'BIOLOGIA AMBIENTE Y TECNOLOGIA' => [3, 1],
            'FISICA' => [3, 1],
            'QUIMICA' => [3, 1],
            'GEOGRAFIA HISTORIA Y CIUDADANIA' => [3, 0],
            'GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL' => [3, 0],
            'EDUCACION FISICA' => [2, 0],
            'INGLES' => [3, 1],
            'FORMACION HUMANO CRISTIANA' => [1, 0],
            'FORMACION PARA LA SOBERANIA NACIONAL' => [1, 0],
            'ORIENTACION' => [1, 0],
            'ORIENTACION VOCACIONAL' => [1, 0],
            'PARTICIPACION EN GRUPOS' => [2, 1],
            'INNOVACION TECNOLOGICA Y PRODUCTIVA' => [2, 1],
            'ROBOTICA' => [2, 1],
            'INFORMATICA' => [2, 1],
            'MUSICA' => [1, 0],
        ],
    ];

    /**
     * Alias legacy -> nombre canónico de asignatura usado en la BD.
     */
    private const LEGACY_AREA_ALIASES = [
        'LENGUA Y LITERATURA' => 'LENGUA Y LITERATURA',
        'CASTELLANO' => 'LENGUA Y LITERATURA',
        'INGLES' => 'INGLES',
        'INGLÉS' => 'INGLES',
        'MATEMATICA' => 'MATEMATICA',
        'MATEMÁTICA' => 'MATEMATICA',
        'MATEMATICAS' => 'MATEMATICA',
        'EDUCACION FISICA' => 'EDUCACION FISICA',
        'EDUCACIÓN FÍSICA' => 'EDUCACION FISICA',
        'BIOLOGIA' => 'BIOLOGIA',
        'BIOLOGÍA' => 'BIOLOGIA',
        'FISICA' => 'FISICA',
        'FÍSICA' => 'FISICA',
        'QUIMICA' => 'QUIMICA',
        'GEOGRAFIA HISTORIA Y CIUDADANIA' => 'GEOGRAFIA HISTORIA Y CIUDADANIA',
        'GEOGRAFÍA HISTORIA Y CIUDADANÍA' => 'GEOGRAFIA HISTORIA Y CIUDADANIA',
        'FORMACION HUMANO CRISTIANA' => 'FORMACION HUMANO CRISTIANA',
        'MUSICA' => 'MUSICA',
        'MÚSICA' => 'MUSICA',
        'INNOVACION TECNOLOGICA Y PRODUCTIVA' => 'INNOVACION TECNOLOGICA Y PRODUCTIVA',
        'INNOVACIÓN TECNOLÓGICA Y PRODUCTIVA' => 'INNOVACION TECNOLOGICA Y PRODUCTIVA',
        'COMUNICACION Y REPRESENTACION' => 'COMUNICACION Y REPRESENTACION',
        'COMUNICACIÓN Y REPRESENTACIÓN' => 'COMUNICACION Y REPRESENTACION',
        'RELACION CON EL AMBIENTE' => 'RELACION CON EL AMBIENTE',
        'CIENCIAS NATURALES' => 'CIENCIAS NATURALES',
        'CIENCIAS SOCIALES' => 'CIENCIAS SOCIALES',
        'EDUCACION ESTETICA' => 'EDUCACION ESTETICA',
        'ARTES' => 'EDUCACION ESTETICA',
        'ORIENTACION' => 'ORIENTACION',
        'ORIENTACION VOCACIONAL' => 'ORIENTACION',
        'PARTICIPACION EN GRUPOS' => 'PARTICIPACION EN GRUPOS',
        'ROBOTICA' => 'ROBOTICA',
    ];

    /**
     * Áreas con fuerte componente práctica/laboratorio: se reserva una fracción
     * como prácticas y el resto queda teórico.
     */
    private const PRACTICE_AREAS = [
        'INGLES',
        'BIOLOGIA',
        'FISICA',
        'QUIMICA',
        'EDUCACION FISICA',
        'MUSICA',
        'INNOVACION TECNOLOGICA Y PRODUCTIVA',
        'ROBOTICA',
    ];

    public function handle(): int
    {
        $csvPath = $this->option('csv') ?: base_path('blueprint/school-timetable/legacy/csv/legacy_carga_docentes.csv');
        if (! is_file($csvPath)) {
            $this->error('No existe el CSV legacy: '.$csvPath);

            return self::FAILURE;
        }

        $legacyMap = $this->loadLegacyHours($csvPath);
        if ($legacyMap === []) {
            $this->error('No se encontraron horas legadas válidas en el CSV.');

            return self::FAILURE;
        }

        $this->info('Horas legacy cargadas: '.count($legacyMap).' áreas/asignaturas detectadas');

        $query = Asignatura::query()->with('pestudio');
        if ($this->option('lapso')) {
            $lapsoId = (int) $this->option('lapso');
            $query->whereHas('pensums.pevaluacions', function ($sub) use ($lapsoId) {
                $sub->where('lapso_id', $lapsoId);
            });
        }

        if (! $this->option('all') && ! $this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('hour_t_week')->orWhere('hour_t_week', 0)->orWhere('hour_p_week', 0);
            });
        }

        $asignaturas = $query->orderBy('name')->get();
        $updated = 0;
        $matches = 0;
        $unmatched = 0;
        $rows = [];

        foreach ($asignaturas as $asignatura) {
            $candidate = $this->findLegacyHoursForAsignatura($asignatura->name, $legacyMap);
            if ($candidate === null) {
                $unmatched++;
                $rows[] = [$asignatura->id, $asignatura->name, (string) ($asignatura->hour_t_week ?? 0), (string) ($asignatura->hour_p_week ?? 0), '-', '-', 'sin coincidencia'];

                continue;
            }

            [$tLegacy, $pLegacy] = $candidate;
            $tActual = (int) ($asignatura->hour_t_week ?? 0);
            $pActual = (int) ($asignatura->hour_p_week ?? 0);

            if (! $this->option('force') && ($tActual > 0 || $pActual > 0)) {
                $rows[] = [$asignatura->id, $asignatura->name, $tActual, $pActual, $tLegacy, $pLegacy, 'ya informado'];

                continue;
            }

            $matches++;
            $rows[] = [$asignatura->id, $asignatura->name, $tActual, $pActual, $tLegacy, $pLegacy, 'candidato'];

            if (! $this->option('dry-run')) {
                $asignatura->update([
                    'hour_t_week' => $tLegacy,
                    'hour_p_week' => $pLegacy,
                ]);
                $updated++;
            }
        }

        $this->table(['ID', 'Asignatura', 'T actual', 'P actual', 'T legacy', 'P legacy', 'Estado'], $rows);

        if ($this->option('dry-run')) {
            $this->info('Dry-run: '.$matches.' asignaturas coinciden con legado y podrían normalizarse; '.$unmatched.' sin coincidencia.');

            return self::SUCCESS;
        }

        $this->info('Actualizadas: '.$updated.' asignaturas. Coincidencias: '.$matches.'. Sin coincidencia: '.$unmatched.'.');

        return self::SUCCESS;
    }

    private function loadLegacyHours(string $csvPath): array
    {
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle, 0, ',');
        $weeklyTotals = [];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count($row) < 8) {
                continue;
            }

            $area = trim((string) ($row[3] ?? ''));
            $horas = trim((string) ($row[5] ?? ''));
            if ($area === '' || ! is_numeric($horas)) {
                continue;
            }

            $normalizedArea = $this->normalizeText($area);
            if ($normalizedArea === '') {
                continue;
            }

            $total = (int) $horas;
            $legacyAreaKey = $this->aliasArea($normalizedArea);
            if ($legacyAreaKey === null) {
                continue;
            }

            $weeklyTotals[$legacyAreaKey] = max($weeklyTotals[$legacyAreaKey] ?? 0, $total);
        }

        fclose($handle);

        return $weeklyTotals;
    }

    private function findLegacyHoursForAsignatura(string $asignaturaName, array $legacyMap): ?array
    {
        $normalizedName = $this->normalizeText($asignaturaName);
        if ($normalizedName === '') {
            return null;
        }

        $planMapCandidates = [];
        foreach (self::LEGACY_PLAN_MAPS as $planName => $subjects) {
            foreach ($subjects as $subjectName => $hours) {
                $planMapCandidates[$subjectName] = $hours;
            }
        }

        foreach ($planMapCandidates as $subjectName => $hours) {
            $normalizedSubject = $this->normalizeText($subjectName);
            if (str_contains($normalizedName, $normalizedSubject) || str_contains($normalizedSubject, $normalizedName)) {
                return $hours;
            }
        }

        foreach (self::LEGACY_AREA_ALIASES as $alias => $canonical) {
            $normalizedAlias = $this->normalizeText($alias);
            $normalizedCanonical = $this->normalizeText($canonical);
            if (str_contains($normalizedName, $normalizedAlias) || str_contains($normalizedName, $normalizedCanonical)
                || str_contains($normalizedAlias, $normalizedName) || str_contains($normalizedCanonical, $normalizedName)) {
                $total = $legacyMap[$canonical] ?? $legacyMap[$alias] ?? null;
                if ($total !== null) {
                    return $this->splitHours($canonical, (int) $total);
                }
            }
        }

        foreach ($legacyMap as $legacyAreaKey => $total) {
            $normalizedLegacy = $this->normalizeText($legacyAreaKey);
            if (str_contains($normalizedName, $normalizedLegacy) || str_contains($normalizedLegacy, $normalizedName)) {
                return $this->splitHours($legacyAreaKey, $total);
            }
        }

        return null;
    }

    private function splitHours(string $areaKey, int $total): array
    {
        $areaKey = $this->normalizeText($areaKey);
        $practicas = 0;
        $teoricas = max(0, $total);

        foreach (self::PRACTICE_AREAS as $practiceArea) {
            $normalizedPracticeArea = $this->normalizeText($practiceArea);
            if (str_contains($areaKey, $normalizedPracticeArea) || str_contains($normalizedPracticeArea, $areaKey)) {
                $practicas = (int) max(1, round($total * 0.25));
                $teoricas = max(0, $total - $practicas);
                break;
            }
        }

        // Regla conservadora: si el área no aparece como práctica y la carga es
        // alta, se asume teoría pura; solo existe práctica cuando la legacy o el
        // área lo exige explícitamente.
        return [$teoricas, $practicas];
    }

    private function normalizeText(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = preg_replace('/\s+/', ' ', trim($value));
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = strtoupper((string) $value);

        return trim($value);
    }

    private function aliasArea(string $normalizedArea): ?string
    {
        foreach (self::LEGACY_AREA_ALIASES as $alias => $canonical) {
            if ($this->normalizeText($alias) === $normalizedArea || $this->normalizeText($canonical) === $normalizedArea) {
                return $this->normalizeText($canonical);
            }
        }

        return null;
    }
}
