<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Opción 3 (multi-calendario híbrido, PLAN-TIMETABLE-002): importa el horario
 * legacy 2025-2026 (CSVs normalizados por etl_legacy.py) como un calendario
 * BORRADOR con slots locked=true (ADR-TT-007), base editable para el editor
 * drag-and-drop y comparable contra un borrador generado por el solver.
 *
 * Fuentes (blueprint/school-timetable/legacy/csv/):
 *   legacy_horario_secciones.csv  slots por sección (575)
 *   legacy_estructura_horaria.csv franjas/bloques (17)
 *   legacy_area_docente.csv       firma área→docente para desambiguar pevs
 *
 * Reglas de mapeo (supuestos documentados, ver --dry-run):
 *   - PRIMARIA: 'NRO A/B' → grados PRIMARIA 1G..6G (A/B); 'INICIAL N°' →
 *     INICIAL por grado (sección única 'U').
 *   - MEDIA: 'N AÑO X' → pestudio con pevs: 1ER→CyT, 2DO→CyT, 3ER→CyT
 *     (13/14 pevs) ó MG (4 pevs) — se elige por firma de docentes del área;
 *     4TO/5TO→MG si la firma calza, CyT como fallback.
 *   - Materia → pev de la sección cuya asignatura matchea por nombre
 *     normalizado (alias legacy→BD documentados) Y, si hay varias candidatas,
 *     la firma del docente del área desambigua.
 *   - GRUPO 1/2 (paralelos): slot con grupo_estable del pev cuando el pev lo
 *     tiene; sin grupo → slot normal de sección completa.
 *   - Los bloques legacy (80min = 2 franjas) generan periodos de 40min L-V;
 *     el turno T legacy (13:05-14:55) crea períodos del shift T existente.
 *
 * Uso:
 *   php8.2 artisan timetable:import-legacy --lapso=1 --dry-run
 *   php8.2 artisan timetable:import-legacy --lapso=1
 */
class TimetableImportLegacy extends Command
{
    protected $signature = 'timetable:import-legacy
        {--lapso=1 : Id del lapso destino}
        {--calendar-name= : Nombre del calendario borrador (default: "Horario 2025-2026 (legacy)")}
        {--csv-dir= : Directorio con los CSVs (default: blueprint/school-timetable/legacy/csv)}
        {--dry-run : Solo audit report, no persiste}
        {--force : Permite ejecutar sin --dry-run aunque haya errores de mapeo}';

    protected $description = 'Importa el horario legacy 2025-2026 (CSVs del ETL) como calendario borrador con slots locked (opción 3)';

    private const DIA_NOMBRE = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie'];

    /** Alias materia legacy → asignatura BD (nombre normalizado). */
    private const ALIAS_ASIGNATURA = [
        'LENGUA Y LITERATURA' => ['LENGUA Y LITERATURA', 'CASTELLANO'],
        'INGLES' => ['INGLES Y OTRAS LENGUAS EXTRANJERAS', 'IDIOMAS', 'AREAS COMPLEMENTARIA INGLES', 'AREA COMPLEMENTARIA INGLES'],
        'MATEMATICA' => ['MATEMATICAS', 'MATEMATICA'],
        'MATEMATICAS' => ['MATEMATICAS', 'MATEMATICA'],
        'EDUCACION FISICA' => ['EDUCACION FISICA', 'EDUCACION FISICA'],
        'BIOLOGIA, AMBIENTE Y TECNOLOGIA' => ['BIOLOGIA, AMBIENTE Y TECNOLOGIA', 'BIOLOGIA, AMBIENTE Y TECNOLOGIA'],
        'BIOLOGIA' => ['BIOLOGIA'],
        'FISICA' => ['FISICA', 'FISICA'],
        'QUIMICA' => ['QUIMICA', 'QUIMICA'],
        'GEOGRAFIA, HISTORIA Y SOBERANIA NACIONAL' => [
            'GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL', 'GEOGRAFIA HISTORIA Y CIUDADANIA',
            'GEOGRAFIA HISTORIA Y CIUDADANIA', 'FORMACION PARA LA SOBERANIA NACIONAL',
        ],
        'GEOGRAFIA, HISTORIA Y CIUDADANIA' => ['GEOGRAFIA HISTORIA Y CIUDADANIA', 'GEOGRAFIA  HISTORIA Y CIUDADANIA'],
        'ORIENTACION VOCACIONAL' => ['ORIENTACION VOCACIONAL', 'ORIENTACION Y CONVIVENCIA'],
        'ORIENTACION Y CONVIVENCIA' => ['ORIENTACION Y CONVIVENCIA', 'ORIENTACION VOCACIONAL'],
        'FORMACION HUMANO CRISTIANA' => ['FORMACION HUMANO CRISTIANA', 'AREA COMPLEMENTARIA FORMACION HUMANO CRISTIANA', 'AREA COMPLEMENTARIA  FORMACION HUMANO CRISTIANA'],
        'INNOVACION TECNOLOGICA Y PRODUCTIVA' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA', 'INNOVACION TECNOLOGICA Y PRODUCTIVA'],
        'INNOVACIONES TECNOLOGICAS' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA'],
        'ROBOTICA' => ['AREAS COMPLEMENTARIA ROBOTICA 1G', 'AREAS COMPLEMENTARIA ROBOTICA 2G', 'AREAS COMPLEMENTARIA ROBOTICA 3G', 'AREAS COMPLEMENTARIA ROBOTICA 4G', 'AREAS COMPLEMENTARIA ROBOTICA 5G', 'AREAS COMPLEMENTARIA ROBOTICA 6G', 'ROBOTICA', 'PARTICIPACION EN GRUPOS DE CREACION RECREACION Y PRODUCCION'],
        'FINANZAS' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA'],
        'INFORMATICA' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA', 'PARTICIPACION EN GRUPOS DE CREACION RECREACION Y PRODUCCION'],
        'SEMINARIO DE INVESTIGACION' => ['PARTICIPACION EN GRUPOS DE CREACION RECREACION Y PRODUCCION'],
        'CIENCIAS DE LA TIERRA' => ['CIENCIAS DE LA TIERRA'],
        'LENGUA' => ['LENGUA'],
        'CIENCIAS NATURALES' => ['CIENCIAS NATURALES'],
        'CIENCIAS SOCIALES' => ['CIENCIAS SOCIALES'],
        'EDUCACION ESTETICA' => ['EDUCACION ESTETICA', 'EDUCACION ESTETICA'],
        'MUSICA' => ['AREA COMPLEMENTARIA MUSICA', 'MUSICA'],
        'SOCIO EMOCIONAL' => ['AREA COMPLEMENTARIA INTEGRAL 1G', 'AREA COMPLEMENTARIA INTEGRAL 2G', 'AREA COMPLEMENTARIA INTEGRAL 3G', 'AREA COMPLEMENTARIA INTEGRAL 4G', 'AREA COMPLEMENTARIA INTEGRAL 5G', 'AREA COMPLEMENTARIA INTEGRAL 6G'],
        'COMUNICACION Y REPRESENTACION' => ['COMUNICACION Y REPRESENTACION'],
        'FORMACION PERSONAL Y SOCIAL' => ['FORMACION PERSONAL Y SOCIAL'],
        'RELACION CON EL AMBIENTE' => ['RELACION CON EL AMBIENTE'],
    ];

    /** Mapeo sección legacy → [pestudio, grado_code] para la consulta de secciones BD. */
    private const SECCION_LEGACY = [
        'MEDIA' => [
            '1ER AÑO' => ['EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA', '1A'],
            '2DO AÑO' => ['EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA', '2DO'],
            '3ER AÑO' => null, // ambiguo MG/CyT: resolver por firma de docentes
            '4TO AÑO' => null, // ambiguo MG/CyT
            '5TO AÑO' => null, // ambiguo MG/CyT
        ],
    ];

    public function handle(): int
    {
        $lapsoId = (int) $this->option('lapso');
        $dryRun = (bool) $this->option('dry-run');
        $csvDir = $this->option('csv-dir') ?: base_path('blueprint/school-timetable/legacy/csv');

        $slotsFile = "$csvDir/legacy_horario_secciones.csv";
        if (! is_file($slotsFile)) {
            $this->error("No existe $slotsFile. Ejecuta etl_legacy.py primero.");

            return self::FAILURE;
        }

        // 1. Cargar CSVs
        $slots = $this->readCsv($slotsFile);
        $areaDocente = $this->readCsv("$csvDir/legacy_area_docente.csv");
        $this->info('CSVs: '.count($slots).' slots · '.count($areaDocente).' firmas área→docente');

        // 2. Preparar consultas BD
        $pevs = $this->loadPevs($lapsoId);
        $this->allPevs = $pevs;
        $this->info('Pevs lapso '.$lapsoId.': '.count($pevs).' en '.count(array_unique(array_column($pevs, 'seccion_id'))).' secciones');

        // 3. Resolver mapeos
        $audit = $this->resolveMappings($slots, $pevs, $areaDocente);

        $okCount = count($audit['ok']);
        $errCount = count($audit['errors']);
        $this->info("Mapeo: {$okCount} slots OK · {$errCount} con error");

        // 4. Reporte audit
        $this->table(
            ['Tipo', 'Detalle'],
            $this->auditTable($audit)
        );

        if ($dryRun) {
            $this->info('--dry-run: no se persiste nada.');

            return $errCount === 0 ? self::SUCCESS : self::FAILURE;
        }

        if ($errCount > 0 && ! $this->option('force')) {
            $this->error("Hay {$errCount} errores de mapeo. Corrige o usa --force.");

            return self::FAILURE;
        }

        // 5. Persistir
        $calendar = $this->persistCalendar($lapsoId, $audit, $pevs);
        $this->info('Calendario creado: id '.$calendar->id.' · '.$calendar->name);

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────
    // Carga
    // ─────────────────────────────────────────────────────────────

    private function readCsv(string $path): array
    {
        $rows = [];
        $h = fopen($path, 'r');
        $header = fgetcsv($h);
        while (($r = fgetcsv($h)) !== false) {
            $rows[] = array_combine($header, $r);
        }
        fclose($h);

        return $rows;
    }

    /** Pevs del lapso con todo el contexto (sección, grado, pestudio, asignatura, docente). */
    private function loadPevs(int $lapsoId): array
    {
        $rows = DB::select(
            "SELECT pe.id, pe.seccion_id, pe.profesor_id, pe.grupo_estable_id,
                    s.name sec_nombre, g.code_sm grado, p2.name pestudio,
                    a.name asig, ge.name grupo_estable,
                    CONCAT(pr.name,' ',pr.lastname) docente
             FROM pevaluacions pe
             JOIN seccions s ON s.id = pe.seccion_id
             JOIN grados g ON g.id = s.grado_id
             JOIN pestudios p2 ON p2.id = g.pestudio_id
             JOIN pensums ps ON ps.id = pe.pensum_id
             JOIN asignaturas a ON a.id = ps.asignatura_id
             LEFT JOIN profesors pr ON pr.id = pe.profesor_id
             LEFT JOIN grupo_estables ge ON ge.id = pe.grupo_estable_id
             WHERE pe.lapso_id = ?
             ORDER BY s.id, a.name",
            [$lapsoId]
        );
        $out = [];
        foreach ($rows as $r) {
            $r->asig_norm = $this->norm($r->asig);
            $r->docente_norm = $this->norm($r->docente);
            $r->grupo_norm = $this->norm($r->grupo_estable);
            $out[] = $r;
        }

        return $out;
    }

    // ─────────────────────────────────────────────────────────────
    // Mapeo
    // ─────────────────────────────────────────────────────────────

    private function resolveMappings(array $slots, array $pevs, array $areaDocente): array
    {
        // índices: por sección BD, por asignatura normalizada
        $bySeccion = [];
        foreach ($pevs as $p) {
            $bySeccion[$p->seccion_id][] = $p;
        }
        // firma: (grado_legacy, sec, area_norm) => docente legacy norm
        $firma = [];
        foreach ($areaDocente as $ad) {
            $firma[$this->norm($ad['grado']).'|'.$this->norm($ad['seccion']).'|'.$this->norm($ad['area'])] = $this->norm($ad['docente']);
        }

        $audit = ['ok' => [], 'errors' => [], 'stats' => []];
        foreach ($slots as $i => $slot) {
            $seccionBD = $this->resolveSeccion($slot, $bySeccion, $pevs);
            if (! $seccionBD) {
                $audit['errors'][] = "slot#{$i}: sección legacy '{$slot['seccion']}' ({$slot['nivel']}) sin sección BD";
                continue;
            }
            $pev = $this->resolvePev($slot, $seccionBD, $firma);
            if (! $pev) {
                $audit['errors'][] = "slot#{$i}: materia '{$slot['materia']}' sin pev en {$slot['seccion']} (docente firma: ".($firma[$this->norm($slot['grado']).'|'.$this->norm($this->secLetter($slot)).'|'.$this->norm($slot['materia'])] ?? '?').')';
                continue;
            }
            $audit['ok'][] = [
                'slot' => $slot,
                'seccion_id' => $seccionBD,
                'pev' => $pev,
                'periodo' => $this->resolvePeriodo($slot),
            ];
        }

        return $audit;
    }

    /** Resuelve la sección BD para un slot legacy. Devuelve seccion_id o null. */
    private function resolveSeccion(array $slot, array $bySeccion, array $pevs)
    {
        $nivel = $slot['nivel'];
        $seccion = $this->norm($slot['seccion']);

        if ($nivel === 'PRIMARIA') {
            // '1RO A' → PRIMARIA 1G A · 'INICIAL N°' → INICIAL grado N
            if (preg_match('/^INICIAL (\d)/', $seccion, $m)) {
                $gradoCode = $m[1] === '1' ? '1GR' : ($m[1] === '2' ? '2GP' : '3GP');

                return $this->findSeccionId($pevs, 'EDUCACION INICIAL', $gradoCode, 'U');
            }
            if (preg_match('/^(\d+)(RO|DO|TO) ([A-C])$/', $seccion, $m)) {
                $num = (int) $m[1];

                return $this->findSeccionId($pevs, 'EDUCACION PRIMARIA', $num.'G', $m[3]);
            }

            return null;
        }

        // MEDIA
        $gradoLegacy = $this->norm($slot['grado']);   // '1ER AÑO'
        $letra = $this->secLetter($slot);              // 'A' | 'B'
        if (preg_match('/^(\d+)(ER|DO|TO)/', $gradoLegacy, $m)) {
            $num = (int) $m[1];
            $mapping = self::SECCION_LEGACY['MEDIA'][$gradoLegacy] ?? null;
            if ($mapping) {
                return $this->findSeccionId($pevs, $mapping[0], $mapping[1], $letra);
            }
            // 3ER/4TO/5TO ambigüos MG vs CyT: elegir el pestudio con pevs que
            // calce la firma del docente del área; fallback: el que tenga pevs.
            $candidatos = [
                ['EDUCACION MEDIA GENERAL', $num === 3 ? '3A' : ($num === 4 ? '4A' : '5A')],
                ['EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA', $num === 3 ? '3ER' : ($num === 4 ? '4TO' : '5TO')],
            ];
            $conPevs = [];
            foreach ($candidatos as $c) {
                $sid = $this->findSeccionId($pevs, $c[0], $c[1], $letra);
                if ($sid && isset($bySeccion[$sid])) {
                    $conPevs[] = [$sid, $c[0]];
                }
            }
            if (count($conPevs) === 1) {
                return $conPevs[0][0];
            }
            // ambos con pevs: desempatar por firma (se hace en resolvePev vía
            // candidato múltiple); devolvemos el primero (MG) y el pev null
            // forzará el error de firma.
            if ($conPevs) {
                return $conPevs[0][0];
            }
        }

        return null;
    }

    private function secLetter(array $slot): string
    {
        if (preg_match('/([A-C])$/', $this->norm($slot['seccion']), $m)) {
            return $m[1];
        }

        return 'A';
    }

    private function findSeccionId(array $pevs, string $pestudio, string $gradoCode, string $letra): ?int
    {
        foreach ($pevs as $p) {
            if ($p->pestudio === $pestudio && $p->grado === $gradoCode && $p->sec_nombre === $letra) {
                return (int) $p->seccion_id;
            }
        }

        return null;
    }

    /** Resuelve el pev para un slot: por alias de asignatura + firma de docente. */
    private function resolvePev(array $slot, int $seccionId, array $firma): ?object
    {
        $materia = $this->norm($slot['materia']);
        $aliases = array_map(fn ($a) => $this->norm($a), self::ALIAS_ASIGNATURA[$materia] ?? [$materia]);
        $grado = $this->norm($slot['grado']);
        $letra = $this->norm($this->secLetter($slot));

        // candidatos por alias (comparación en espacio normalizado)
        $candidatos = [];
        foreach ($this->pevsForSeccion($seccionId) as $p) {
            if (in_array($p->asig_norm, $aliases, true)) {
                $candidatos[] = $p;
            }
        }
        if (! $candidatos) {
            return null;
        }
        if (count($candidatos) === 1) {
            return $candidatos[0];
        }

        // varios: desambiguar por firma de docente
        $docenteFirma = $firma[$grado.'|'.$letra.'|'.$materia] ?? null;
        if ($docenteFirma) {
            foreach ($candidatos as $p) {
                if ($p->docente_norm === $docenteFirma) {
                    return $p;
                }
            }
        }

        return $candidatos[0]; // primera coincidencia
    }

    private $pevsIndex = [];

    private function pevsForSeccion(int $seccionId): array
    {
        if (! isset($this->pevsIndex[$seccionId])) {
            $this->pevsIndex[$seccionId] = array_values(array_filter(
                $this->allPevs,
                fn ($p) => (int) $p->seccion_id === $seccionId
            ));
        }

        return $this->pevsIndex[$seccionId];
    }

    private $allPevs = [];

    /** Turno y bloque horario del slot. */
    private function resolvePeriodo(array $slot): array
    {
        $h1 = $slot['hora_inicio'];
        $turno = $h1 >= '13:00' ? 'T' : 'M';

        return ['turno' => $turno, 'inicio' => $h1, 'fin' => $slot['hora_fin'], 'dia' => (int) $slot['dia']];
    }

    // ─────────────────────────────────────────────────────────────
    // Audit
    // ─────────────────────────────────────────────────────────────

    private function auditTable(array $audit): array
    {
        $rows = [];
        $errores = array_slice($audit['errors'], 0, 30);
        foreach ($errores as $e) {
            $rows[] = ['ERROR', $e];
        }
        if (count($audit['errors']) > 30) {
            $rows[] = ['ERROR', '... '.(count($audit['errors']) - 30).' más'];
        }
        // resumen por sección
        $porSeccion = [];
        foreach ($audit['ok'] as $m) {
            $porSeccion[$m['slot']['seccion']] = ($porSeccion[$m['slot']['seccion']] ?? 0) + 1;
        }
        ksort($porSeccion);
        foreach ($porSeccion as $sec => $n) {
            $rows[] = ['OK', "$sec: $n slots mapeados"];
        }

        return $rows;
    }

    // ─────────────────────────────────────────────────────────────
    // Persistencia
    // ─────────────────────────────────────────────────────────────

    private function persistCalendar(int $lapsoId, array $audit, array $pevs): TimetableCalendar
    {
        $name = $this->option('calendar-name') ?: 'Horario 2025-2026 (legacy)';

        return DB::transaction(function () use ($lapsoId, $name, $audit) {
            // 1. Calendario borrador
            $calendar = TimetableCalendar::create([
                'lapso_id' => $lapsoId,
                'name' => $name,
                'period_minutes' => 40,
                'status' => TimetableCalendar::STATUS_DRAFT,
                'version' => 0,
            ]);

            // 2. Períodos: franjas legacy de 40min por día y turno
            $shiftM = TimetableShift::where('code', 'M')->first();
            $shiftT = TimetableShift::where('code', 'T')->first();
            $periods = $this->buildPeriods($calendar, $shiftM, $shiftT, $audit);

            // 3. Lecciones (1 por pev distinto) + slots locked
            $lessonByPev = [];
            foreach ($audit['ok'] as $m) {
                $pev = $m['pev'];
                if (! isset($lessonByPev[$pev->id])) {
                    $lessonByPev[$pev->id] = TimetableLesson::create([
                        'calendar_id' => $calendar->id,
                        'pevaluacion_id' => $pev->id,
                        'shift_id' => ($m['periodo']['turno'] === 'T' ? $shiftT : $shiftM)->id,
                        'weekly_blocks_t' => 0,
                        'weekly_blocks_p' => 0,
                        'room_type_required' => null,
                        'priority' => 0,
                        'locked' => true,
                    ]);
                }
                $periodKey = $m['periodo']['turno'].'|'.$m['periodo']['inicio'].'|'.$m['periodo']['fin'];
                $periodId = $periods[$periodKey]['ids'][$m['periodo']['dia']] ?? null;
                if (! $periodId) {
                    $this->warn('slot sin período: '.$m['slot']['seccion'].' '.$m['slot']['materia'].' '.$m['periodo']['inicio']);
                    continue;
                }
                TimetableSlot::create([
                    'calendar_id' => $calendar->id,
                    'lesson_id' => $lessonByPev[$pev->id]->id,
                    'period_id' => $periodId,
                    'profesor_id' => $pev->profesor_id,
                    'seccion_id' => $pev->seccion_id,
                    'grupo_estable_id' => $pev->grupo_estable_id,
                    'room_id' => $this->roomForSeccion($pev->seccion_id),
                    'locked' => true,
                    'is_manual_override' => true,
                ]);
            }

            return $calendar;
        });
    }

    /**
     * Franjas legacy agrupadas: cada franja de 40min es un período; las de
     * 80min se descomponen en dos períodos.
     */
    private function buildPeriods(TimetableCalendar $calendar, ?TimetableShift $shiftM, ?TimetableShift $shiftT, array $audit): array
    {
        // franjas únicas por turno desde los slots mapeados
        $franjas = [];
        foreach ($audit['ok'] as $m) {
            $p = $m['periodo'];
            $franjas[$p['turno']][$p['inicio'].'-'.$p['fin']] = true;
        }
        // descomponer 80→2×40 dentro del turno
        $periods = [];
        foreach (['M' => $shiftM, 'T' => $shiftT] as $turno => $shift) {
            if (! $shift) {
                continue;
            }
            $list = array_keys($franjas[$turno] ?? []);
            sort($list);
            foreach ($list as $rango) {
                [$h1, $h2] = explode('-', $rango);
                $periods[$turno.'|'.$h1.'-'.$h2] = ['ids' => $this->createPeriodRows($calendar, $shift, $h1, $h2)];
            }
        }

        return $periods;
    }

    private function createPeriodRows(TimetableCalendar $calendar, TimetableShift $shift, string $h1, string $h2): array
    {
        $ids = [];
        [$i1, $i2] = [explode(':', $h1), explode(':', $h2)];
        $min1 = (int) $i1[0] * 60 + (int) $i1[1];
        $min2 = (int) $i2[0] * 60 + (int) $i2[1];
        $orden = 1;
        // cada período legacy (40/80min) se materializa en franjas de 40
        for ($start = $min1; $start < $min2; $start += 40) {
            $end = min($start + 40, $min2);
            foreach (range(1, 5) as $dia) {
                $ids[$dia] = TimetablePeriod::create([
                    'calendar_id' => $calendar->id,
                    'shift_id' => $shift->id,
                    'day_of_week' => $dia,
                    'order_in_day' => $orden,
                    'start_time' => sprintf('%02d:%02d', intdiv($start, 60), $start % 60),
                    'end_time' => sprintf('%02d:%02d', intdiv($end, 60), $end % 60),
                    'is_break' => false,
                ])->id;
            }
            $orden++;
        }

        return $ids;
    }

    private function roomForSeccion(int $seccionId): ?int
    {
        $room = TimetableRoom::where('seccion_id', $seccionId)->first();

        return $room?->id;
    }

    // ─────────────────────────────────────────────────────────────
    // Utilidades
    // ─────────────────────────────────────────────────────────────

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
