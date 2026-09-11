<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Console\Command;

/**
 * Normalización de datos de producción: asignaturas sin hour_t_week/hour_p_week
 * definidos (0/null) impiden derivar los bloques del horario (§4: ceil(horas ×
 * 60 / period_minutes)) → T=P=0 y el solver no puede planificar.
 *
 * Este comando recorre TODOS los pestudios activos y rellena hour_t_week (y
 * hour_p_week=0) de las asignaturas con horas ausentes, usando un mapa
 * NORMALIZADO por plan de estudio (determinista, sin dependencia de CSVs del
 * blueprint). Los pestudios de la familia "MEDIA GENERAL" comparten el mapa.
 *
 * Es idempotente: sin `--force` solo escribe cuando las horas actuales son
 * 0/null. Con `--force` alinea todas a la norma del plan.
 *
 * Uso:
 *   php8.2 artisan timetable:backfill-horas --dry-run
 *   php8.2 artisan timetable:backfill-horas
 *   php8.2 artisan timetable:backfill-horas --lapso=1 --force
 */
class TimetableBackfillHoras extends Command
{
    protected $signature = 'timetable:backfill-horas
        {--lapso=1 : Lapso base para localizar las asignaturas}
        {--force : Sobrescribir asignaturas que ya tienen horas definidas}
        {--dry-run : Solo audit, no persiste}';

    protected $description = 'Rellena hour_t_week/hour_p_week de las asignaturas con horas ausentes de los pestudios activos';

    /**
     * Horas semanales normalizadas por plan de estudio. Clave = palabra clave
     * del nombre normalizado de la asignatura → [teóricas, prácticas].
     *
     * Los valores provienen de la carga real del legacy (minutos semanales →
     * horas enteras), fijados como estándar del pensum. Las áreas complementarias
     * de robótica tienen 2 bloques teóricos semanales. Separado por plan para
     * que --force no sobrescriba asignaturas homónimas de otro plan.
     */
    private const PLANES = [
        'EDUCACION PRIMARIA' => [
            'LENGUA' => [3, 0],
            'MATEMATIC' => [3, 0],
            'CIENCIAS NATURALES' => [2, 0],
            'CIENCIAS SOCIALES' => [2, 0],
            'EDUCACION FISICA' => [1, 0],
            'EDUCACION ESTETICA' => [1, 0],
            'INGLES' => [2, 0],
            'FORMACION HUMANO CRISTIANA' => [1, 0],
            'MUSICA' => [1, 0],
            'AREAS COMPLEMENTARIA ROBOTICA' => [2, 0],
            'AREA COMPLEMENTARIA ROBOTICA' => [2, 0],
        ],
        'EDUCACION MEDIA GENERAL' => [
            'GEOGRAFIA HISTORIA Y CIUDADANIA' => [3, 0],
            'ORIENTACION' => [3, 0],
            'FORMACION PARA LA SOBERANIA' => [1, 0],
            'PARTICIPACION EN GRUPOS' => [3, 0],
        ],
    ];

    public function handle(): int
    {
        $this->warn('Este comando está deprecado; usa timetable:normalize-legacy-hours como fuente normativa única.');

        $lapsoId = (int) $this->option('lapso');
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');

        $pestudios = Pestudio::query()->where('status_active', 'true')->orderBy('id')->get(['id', 'name']);
        if ($pestudios->isEmpty()) {
            $this->info('No hay pestudios activos. Nada que hacer.');

            return self::SUCCESS;
        }
        $this->info('Pestudios activos: '.$pestudios->pluck('name')->join(' · '));

        $rows = [];
        $totalPlanned = 0;
        $totalUnmapped = 0;
        $totalSinMapa = 0;

        foreach ($pestudios as $pestudio) {
            $plan = $this->planMap($pestudio->name);
            $asignaturas = $this->asignaturas($lapsoId, $pestudio->name, $force);
            $planned = 0;
            $unmapped = 0;

            foreach ($asignaturas as $asig) {
                $norm = $this->norm($asig->name);
                $horas = $this->horasFor($norm, $plan);
                if ($horas === null) {
                    $rows[] = [$pestudio->name, $asig->id, $asig->name, $asig->hour_t_week, $asig->hour_p_week, '-', 'sin mapeo'];
                    $unmapped++;

                    continue;
                }

                [$tNuevo, $pNuevo] = $horas;
                $esZero = ((int) $asig->hour_t_week + (int) $asig->hour_p_week) <= 0;
                // Sin --force solo se toca lo que está vacío; con --force se alinea a la norma.
                if (! $esZero && ! $force) {
                    continue;
                }

                $rows[] = [$pestudio->name, $asig->id, $asig->name, $asig->hour_t_week, $asig->hour_p_week, $tNuevo, $pNuevo];
                if (! $dryRun) {
                    $asig->update(['hour_t_week' => $tNuevo, 'hour_p_week' => $pNuevo]);
                }
                $planned++;
            }

            if ($plan === []) {
                $totalSinMapa++;
            }
            $totalPlanned += $planned;
            $totalUnmapped += $unmapped;
            $this->line(sprintf(
                '  [%s] %s asignaturas con horas ausentes · %s a actualizar%s',
                $pestudio->name,
                $asignaturas->count(),
                $planned,
                $unmapped ? " · {$unmapped} sin mapeo" : '',
            ));
        }

        $this->table(['Plan', 'Asig ID', 'Asignatura', 't_actual', 'p_actual', 't_nuevo', 'p_nuevo'], $rows);

        if ($dryRun) {
            $this->info("--dry-run: {$totalPlanned} asignaturas se actualizarían".($totalUnmapped ? " · {$totalUnmapped} sin mapeo" : '').'.');

            return self::SUCCESS;
        }

        $this->info("Actualizadas {$totalPlanned} asignaturas".($totalUnmapped ? " · {$totalUnmapped} sin mapeo (revisar)" : '').'.');

        return self::SUCCESS;
    }

    /**
     * Mapa normalizado de un pestudio; la familia MEDIA GENERAL (incl. CIENCIA
     * Y TECNOLOGÍA) comparte el mismo mapa.
     */
    private function planMap(string $pestudioName): array
    {
        $map = self::PLANES[$pestudioName] ?? [];
        if ($map === [] && str_contains($pestudioName, 'MEDIA GENERAL')) {
            return self::PLANES['EDUCACION MEDIA GENERAL'];
        }

        return $map;
    }

    /**
     * Asignaturas del plan usadas por pevaluaciones del lapso. Sin `$force` solo
     * las que tienen horas ausentes (0/null); con `$force`, todas (para alinear
     * a la norma).
     */
    private function asignaturas(int $lapsoId, string $pestudioName, bool $force)
    {
        $ids = Pevaluacion::query()
            ->where('lapso_id', $lapsoId)
            ->whereHas('seccion.grado.pestudio', fn ($q) => $q->where('name', $pestudioName))
            ->with('pensum.asignatura')
            ->get()
            ->map(fn ($p) => $p->pensum?->asignatura)
            ->filter(fn ($a) => $a && ($force || ((int) ($a->hour_t_week ?? 0) + (int) ($a->hour_p_week ?? 0)) <= 0))
            ->pluck('id')
            ->unique()
            ->values();

        return Asignatura::query()->whereIn('id', $ids)->get(['id', 'name', 'hour_t_week', 'hour_p_week']);
    }

    /** @return array{0:int,1:int}|null [teóricas, prácticas] para un nombre normalizado del plan */
    private function horasFor(string $norm, array $plan): ?array
    {
        foreach ($plan as $keyword => $horas) {
            if (str_contains($norm, $keyword)) {
                return $horas;
            }
        }

        return null;
    }

    private function norm(?string $s): string
    {
        if ($s === null) {
            return '';
        }
        $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s;
        $s = preg_replace('/\s+/', ' ', trim($s)) ?? '';

        return mb_strtoupper($s);
    }
}
