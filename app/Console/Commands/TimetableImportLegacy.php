<?php

namespace App\Console\Commands;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Opción 3 (multi-calendario híbrido, PLAN-TIMETABLE-002): importa el horario
 * legacy 2025-2026 (CSVs normalizados por etl_legacy.py) como un calendario
 * BORRADOR con slots locked=true (ADR-TT-007): base editable en el editor
 * drag-and-drop y comparable contra un borrador generado por el solver.
 *
 * Fuentes (blueprint/school-timetable/legacy/csv/):
 *   legacy_horario_secciones.csv  slots por sección (575)
 *   legacy_estructura_horaria.csv franjas/bloques canónicas por nivel/turno
 *   legacy_area_docente.csv       firma área→docente (132) para desambiguar
 *
 * Estrategia de mapeo (ver --dry-run para el audit):
 *   1. Grupo de slots por sección legacy → secciones BD candidatas (mismo
 *      grado+letra; MEDIA 3ER/4TO/5TO existe en MG y CyT → ambas candidatas).
 *   2. Se elige la sección con mejor score: +2 por slot mapeado con firma de
 *      docente, +1 por slot mapeado sin firma, 0 si no mapea.
 *   3. Dentro de la sección: pev por alias de asignatura; si hay varios,
 *      preferencia por grupo_estable que contenga la materia y desempate por
 *      firma de docente del área.
 *
 * Estructura de períodos: franjas legacy (40/80min) descompuestas en períodos
 * de 40min por día L-V, shift M (07:xx) y T (13:xx) existentes.
 *
 * Uso:
 *   php8.2 artisan timetable:import-legacy --lapso=1 --dry-run --force
 *   php8.2 artisan timetable:import-legacy --lapso=1 --strategy=optimized
 *   php8.2 artisan timetable:import-legacy --lapso=1 --strategy=legacy
 */
class TimetableImportLegacy extends Command
{
    protected $signature = 'timetable:import-legacy
        {--lapso=1 : Id del lapso destino}
        {--calendar-name= : Nombre del calendario borrador (default: "Horario 2025-2026 (legacy)")}
        {--csv-dir= : Directorio con los CSVs (default: blueprint/school-timetable/legacy/csv)}
        {--strategy=optimized : Estrategia del calendario generado (optimized|legacy)}
        {--dry-run : Solo audit report, no persiste}
        {--force : Permite ejecutar sin --dry-run aunque haya errores de mapeo}
        {--replace : Elimina los borradores legacy previos del lapso (mismo nombre base) antes de importar}';

    protected $description = 'Importa el horario legacy 2025-2026 (CSVs del ETL) como calendario borrador con slots locked (opción 3)';

    /** Alias materia legacy → asignaturas BD (se normalizan ambas partes). */
    private const ALIAS_ASIGNATURA = [
        'LENGUA Y LITERATURA' => ['LENGUA Y LITERATURA', 'CASTELLANO'],
        'INGLES' => ['INGLES Y OTRAS LENGUAS EXTRANJERAS', 'IDIOMAS', 'AREAS COMPLEMENTARIA INGLES', 'AREA COMPLEMENTARIA INGLES'],
        'MATEMATICA' => ['MATEMATICAS', 'MATEMATICA'],
        'MATEMATICAS' => ['MATEMATICAS', 'MATEMATICA'],
        'EDUCACION FISICA' => ['EDUCACION FISICA'],
        'BIOLOGIA, AMBIENTE Y TECNOLOGIA' => ['BIOLOGIA, AMBIENTE Y TECNOLOGIA'],
        'BIOLOGIA' => ['BIOLOGIA'],
        'FISICA' => ['FISICA'],
        'QUIMICA' => ['QUIMICA'],
        'GEOGRAFIA, HISTORIA Y SOBERANIA NACIONAL' => [
            'GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL', 'GEOGRAFIA HISTORIA Y CIUDADANIA',
            'GEOGRAFIA  HISTORIA Y CIUDADANIA', 'FORMACION PARA LA SOBERANIA NACIONAL',
        ],
        'GEOGRAFIA, HISTORIA Y CIUDADANIA' => ['GEOGRAFIA HISTORIA Y CIUDADANIA', 'GEOGRAFIA  HISTORIA Y CIUDADANIA', 'GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL'],
        'ORIENTACION VOCACIONAL' => ['ORIENTACION VOCACIONAL', 'ORIENTACION Y CONVIVENCIA'],
        'ORIENTACION Y CONVIVENCIA' => ['ORIENTACION Y CONVIVENCIA', 'ORIENTACION VOCACIONAL'],
        'FORMACION HUMANO CRISTIANA' => ['FORMACION HUMANO CRISTIANA', 'AREA COMPLEMENTARIA FORMACION HUMANO CRISTIANA', 'AREA COMPLEMENTARIA  FORMACION HUMANO CRISTIANA'],
        'INNOVACION TECNOLOGICA Y PRODUCTIVA' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA'],
        'INNOVACION TECNOLOGICA' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA'],
        'INNOVACIONES TECNOLOGICAS' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA'],
        // Los paralelos de ITP (ROBÓTICA/FINANZAS/INFORMÁTICA/SEMINARIO) son
        // grupos de INNOVACION... o PARTICIPACION... en la BD: se desambiguan
        // por grupo_estable.
        'ROBOTICA' => ['ROBOTICA', 'AREAS COMPLEMENTARIA ROBOTICA 1G', 'AREAS COMPLEMENTARIA ROBOTICA 2G', 'AREAS COMPLEMENTARIA ROBOTICA 3G', 'AREAS COMPLEMENTARIA ROBOTICA 4G', 'AREAS COMPLEMENTARIA ROBOTICA 5G', 'AREAS COMPLEMENTARIA ROBOTICA 6G', 'PARTICIPACION EN GRUPOS DE CREACION RECREACION Y PRODUCCION', 'INNOVACION TECNOLOGICA Y PRODUCTIVA'],
        'FINANZAS' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA', 'PARTICIPACION EN GRUPOS DE CREACION RECREACION Y PRODUCCION'],
        'INFORMATICA' => ['INNOVACION TECNOLOGICA Y PRODUCTIVA', 'PARTICIPACION EN GRUPOS DE CREACION RECREACION Y PRODUCCION'],
        'SEMINARIO DE INVESTIGACION' => ['PARTICIPACION EN GRUPOS DE CREACION RECREACION Y PRODUCCION', 'INNOVACION TECNOLOGICA Y PRODUCTIVA'],
        'CIENCIAS DE LA TIERRA' => ['CIENCIAS DE LA TIERRA'],
        // Celda legacy multilínea "CIENCIAS DE LA TIERRA\nFORMACIÓN DE LA
        // SOBERANIA NACIONAL" (block de 80min): la segunda parte equivale a la
        // asignatura BD. El ETL unió ambas líneas en una sola materia.
        'FORMACION DE LA SOBERANIA NACIONAL' => ['FORMACION PARA LA SOBERANIA NACIONAL', 'GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL'],
        'LENGUA' => ['LENGUA'],
        'CIENCIAS NATURALES' => ['CIENCIAS NATURALES'],
        'CIENCIAS SOCIALES' => ['CIENCIAS SOCIALES'],
        'EDUCACION ESTETICA' => ['EDUCACION ESTETICA'],
        'MUSICA' => ['MUSICA', 'AREA COMPLEMENTARIA MUSICA'],
        'SOCIO EMOCIONAL' => ['AREA COMPLEMENTARIA INTEGRAL 1G', 'AREA COMPLEMENTARIA INTEGRAL 2G', 'AREA COMPLEMENTARIA INTEGRAL 3G', 'AREA COMPLEMENTARIA INTEGRAL 4G', 'AREA COMPLEMENTARIA INTEGRAL 5G', 'AREA COMPLEMENTARIA INTEGRAL 6G'],
        'COMUNICACION Y REPRESENTACION' => ['COMUNICACION Y REPRESENTACION'],
        'FORMACION PERSONAL Y SOCIAL' => ['FORMACION PERSONAL Y SOCIAL'],
        'RELACION CON EL AMBIENTE' => ['RELACION CON EL AMBIENTE'],
    ];

    /** Materia legacy → keyword del grupo_estable BD que la aloja. */
    private const MATERIA_GRUPO = [
        'ROBOTICA' => 'ROBOTICA',
        'ROBOTICA 1' => 'ROBOTICA',
        'ROBOTICA 2' => 'ROBOTICA',
        'FINANZAS' => 'FINANZAS',
        'INFORMATICA' => 'INFORMATICA',
        'SEMINARIO DE INVESTIGACION' => 'SEMINARIO',
        'INGLES' => 'INGLES',
    ];

    private array $allPevs = [];

    private ?array $aliasNorm = null;

    private ?array $firmaIndex = null;

    private ?array $knownSubjects = null;

    public function handle(): int
    {
        $lapsoId = (int) $this->option('lapso');
        $dryRun = (bool) $this->option('dry-run');
        $csvDir = $this->option('csv-dir') ?: (string) config('timetable.legacy_csv_dir');
        $strategy = (string) $this->option('strategy');

        if (! in_array($strategy, TimetableCalendar::STRATEGIES, true)) {
            $this->error('La estrategia debe ser optimized o legacy.');

            return self::FAILURE;
        }

        $slotsFile = "$csvDir/legacy_horario_secciones.csv";
        if (! is_file($slotsFile)) {
            $this->error("No existe $slotsFile. Ejecuta etl_legacy.py primero.");

            return self::FAILURE;
        }

        $estructura = $this->loadEstructura();
        $overlaps = $this->findStructureOverlaps($estructura);
        if ($overlaps !== []) {
            $this->error('La estructura horaria contiene franjas solapadas por nivel y turno.');
            $this->table(
                ['Nivel', 'Turno', 'Franja anterior', 'Franja solapada'],
                $overlaps,
            );

            return self::FAILURE;
        }

        $slots = $this->readCsv($slotsFile);
        $areaDocente = $this->readCsv("$csvDir/legacy_area_docente.csv");
        $this->info('CSVs: '.count($slots).' slots · '.count($areaDocente).' firmas área→docente');

        // Los turnos M/T son catálogo compartido: en producción se garantizan con
        // el seeder. Persistir sin turnos terminaría descartando slots.
        if (! $dryRun && ! TimetableShift::where('code', 'M')->exists()) {
            $this->error('Falta el turno M (catálogo). Ejecuta: php8.2 artisan db:seed --class=TimetableShiftsSeeder --force');

            return self::FAILURE;
        }

        $pevs = $this->loadPevs($lapsoId);
        $this->allPevs = $pevs;
        $this->info('Pevs lapso '.$lapsoId.': '.count($pevs).' en '.count(array_unique(array_column($pevs, 'seccion_id'))).' secciones');

        $audit = $this->resolveMappings($slots, $areaDocente);

        $okCount = count($audit['ok']);
        $errCount = count($audit['errors']);
        $this->info("Mapeo: {$okCount} slots OK · {$errCount} con error");
        $this->table(['Tipo', 'Detalle'], $this->auditTable($audit));

        if ($dryRun) {
            $this->info('--dry-run: no se persiste nada.');

            return $errCount === 0 ? self::SUCCESS : self::FAILURE;
        }

        if ($errCount > 0 && ! $this->option('force')) {
            $this->error("Hay {$errCount} errores de mapeo. Corrige o usa --force.");

            return self::FAILURE;
        }

        // --replace: elimina los borradores legacy previos del lapso antes de
        // importar (idempotencia: re-ejecutar no acumula duplicados).
        if ($this->option('replace')) {
            $baseName = $this->option('calendar-name') ?: 'Horario 2025-2026 (legacy)';
            $previos = TimetableCalendar::query()
                ->forLapso($lapsoId)
                ->draft()
                ->where('name', 'like', $baseName.'%')
                ->get();

            foreach ($previos as $previo) {
                $previo->deleteDraft();
                $this->warn('Borrador previo eliminado (--replace): id '.$previo->id.' · '.$previo->name);
            }

            if ($previos->isNotEmpty()) {
                $this->info('Borradores legacy previos eliminados: '.$previos->count());
            }
        }

        $calendars = $this->persistCalendar($lapsoId, $audit, $strategy);
        foreach ($calendars as $calendar) {
            $this->info('Calendario creado: id '.$calendar->id.' · '.$calendar->name);
            $this->info('  Lecciones: '.TimetableLesson::where('calendar_id', $calendar->id)->count().' · slots: '.TimetableSlot::where('calendar_id', $calendar->id)->count());
        }

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
            if (count($r) !== count($header)) {
                continue; // fila malformada
            }
            $rows[] = array_combine($header, $r);
        }
        fclose($h);

        return $rows;
    }

    private function loadPevs(int $lapsoId): array
    {
        $rows = DB::select(
            "SELECT pe.id, pe.seccion_id, pe.profesor_id, pe.grupo_estable_id,
                    s.name sec_nombre, g.code_sm grado, p2.name pestudio, p2.id pestudio_id,
                    a.name asig, ge.name grupo_estable,
                    a.hour_t_week hour_t_week, a.hour_p_week hour_p_week,
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

    private function resolveMappings(array $slots, array $areaDocente): array
    {
        $firma = [];
        foreach ($areaDocente as $ad) {
            $firma[$this->norm($ad['grado']).'|'.$this->norm($ad['seccion']).'|'.$this->norm($ad['area'])] = $this->norm($ad['docente']);
        }
        $this->firmaIndex = $firma;

        // agrupar slots por sección legacy
        $groups = [];
        foreach ($slots as $i => $slot) {
            $groups[$slot['nivel'].'|'.$slot['seccion']][] = $i;
        }

        $audit = ['ok' => [], 'errors' => [], 'seccion_map' => []];
        foreach ($groups as $key => $idxs) {
            $first = $slots[$idxs[0]];
            $candidates = $this->candidateSecciones($first);
            if (! $candidates) {
                foreach ($idxs as $i) {
                    $audit['errors'][] = "slot#{$i}: sección legacy '{$first['seccion']}' ({$first['nivel']}) sin sección BD";
                }

                continue;
            }

            // elegir mejor sección por score de firma sobre TODOS los slots
            $best = null;
            $bestScore = -1;
            $tied = false;
            foreach ($candidates as $sid) {
                $score = 0;
                foreach ($idxs as $i) {
                    $score += $this->slotScore($slots[$i], $sid);
                }
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = $sid;
                    $tied = false;
                } elseif ($score === $bestScore) {
                    $tied = true;
                }
            }
            $audit['seccion_map'][$key] = $best.($tied ? ' (empate!)' : '');

            foreach ($idxs as $i) {
                $slot = $slots[$i];
                $mapped = $this->resolvePevsForSlot($slot, $best);
                if ($mapped === []) {
                    $audit['errors'][] = "slot#{$i}: materia '{$slot['materia']}' sin pev en {$slot['seccion']} (sección BD {$best})";

                    continue;
                }
                $periodo = $this->resolvePeriodo($slot);
                foreach ($mapped as $m) {
                    $audit['ok'][] = [
                        'slot' => $slot,
                        'seccion_id' => $best,
                        'pev' => $m['pev'],
                        'periodo' => $periodo,
                        'sub_index' => $m['sub_index'],
                        'compound' => $m['compound'],
                    ];
                }
            }
        }

        return $audit;
    }

    private function slotScore(array $slot, int $seccionId): int
    {
        [$pev, $firmaUsed] = $this->resolvePevWithFirma($slot, $seccionId);
        if ($pev === null) {
            return 0;
        }

        return $firmaUsed ? 2 : 1;
    }

    /** Secciones BD candidatas (grado+letra) para una sección legacy. */
    private function candidateSecciones(array $slot): array
    {
        $nivel = $slot['nivel'];
        $seccion = $this->norm($slot['seccion']);

        if ($nivel === 'PRIMARIA') {
            if (preg_match('/^INICIAL (\d)/', $seccion, $m)) {
                $gradoCode = ['1' => '1GR', '2' => '2GP', '3' => '3GP'][$m[1]] ?? null;
                $sid = $this->findSeccionIdBy('EDUCACION INICIAL', $gradoCode, 'U');

                return $sid ? [$sid] : [];
            }
            if (preg_match('/^(\d+)(RO|DO|TO) ([A-C])$/', $seccion, $m)) {
                $sid = $this->findSeccionIdBy('EDUCACION PRIMARIA', ((int) $m[1]).'G', $m[3]);

                return $sid ? [$sid] : [];
            }

            return [];
        }

        // MEDIA: todas las secciones (pevs) de ese año+letra, cualquier pestudio
        $gradoLegacy = $this->norm($slot['grado']);
        if (! preg_match('/^(\d+)/', $gradoLegacy, $m)) {
            return [];
        }
        $num = (int) $m[1];
        $letra = $this->secLetter($slot);
        $out = [];
        foreach ($this->allPevs as $p) {
            if ($this->gradoNum($p->grado) === $num && $p->sec_nombre === $letra) {
                $out[(int) $p->seccion_id] = true;
            }
        }

        return array_keys($out);
    }

    private function gradoNum(string $codeSm): ?int
    {
        if (preg_match('/^(\d+)/', trim($codeSm), $m)) {
            return (int) $m[1];
        }

        return null;
    }

    private function findSeccionIdBy(string $pestudio, ?string $gradoCode, string $letra): ?int
    {
        if (! $gradoCode) {
            return null;
        }
        foreach ($this->allPevs as $p) {
            if ($p->pestudio === $pestudio && $p->grado === $gradoCode && $p->sec_nombre === $letra) {
                return (int) $p->seccion_id;
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

    private function resolvePev(array $slot, int $seccionId): ?object
    {
        [$pev] = $this->resolvePevWithFirma($slot, $seccionId);

        return $pev;
    }

    /** @return array{0: ?object, 1: bool} [pev, usó_firma] */
    private function resolvePevWithFirma(array $slot, int $seccionId): array
    {
        return $this->resolvePevForMateria(
            $this->norm($slot['materia']),
            $seccionId,
            $this->norm($slot['grado']),
            $this->norm($this->secLetter($slot)),
        );
    }

    /** @return array{0: ?object, 1: bool} [pev, usó_firma] para una materia normalizada */
    private function resolvePevForMateria(string $materia, int $seccionId, string $grado, string $letra): array
    {
        $aliases = $this->aliasesFor($materia);

        $candidatos = [];
        foreach ($this->pevsForSeccion($seccionId) as $p) {
            if (in_array($p->asig_norm, $aliases, true)) {
                $candidatos[] = $p;
            }
        }
        if (! $candidatos) {
            return [null, false];
        }

        // preferencia por grupo_estable (paralelos ITP)
        $needle = self::MATERIA_GRUPO[$materia] ?? null;
        if ($needle) {
            $porGrupo = array_filter($candidatos, fn ($p) => $p->grupo_norm !== '' && str_contains($p->grupo_norm, $needle));
            if ($porGrupo) {
                $candidatos = array_values($porGrupo);
            }
        }

        if (count($candidatos) === 1) {
            return [$candidatos[0], false];
        }

        $docenteFirma = $this->firmaIndex[$grado.'|'.$letra.'|'.$materia] ?? null;
        if ($docenteFirma) {
            foreach ($candidatos as $p) {
                if ($p->docente_norm === $docenteFirma) {
                    return [$p, true];
                }
            }
        }

        return [$candidatos[0], false];
    }

    /**
     * Resuelve un slot legacy a UNA o VARIAS lecciones. Las celdas legacy con
     * dos asignaturas sin etiqueta de grupo (p. ej. "CIENCIAS DE LA TIERRA\n
     * FORMACIÓN DE LA SOBERANIA NACIONAL" en un bloque de 80min) se dividen en
     * sus componentes, cada uno con su pev y sub-período del bloque.
     *
     * @return array<int, array{pev: object, sub_index: int, compound: bool}>
     */
    private function resolvePevsForSlot(array $slot, int $seccionId): array
    {
        $whole = $this->norm($slot['materia']);
        $grado = $this->norm($slot['grado']);
        $letra = $this->norm($this->secLetter($slot));

        $single = $this->resolvePevForMateria($whole, $seccionId, $grado, $letra);
        if ($single[0]) {
            return [['pev' => $single[0], 'sub_index' => 0, 'compound' => false]];
        }

        $parts = $this->splitCompoundMateria($whole);
        if (count($parts) < 2) {
            return [];
        }

        $out = [];
        foreach ($parts as $idx => $part) {
            $r = $this->resolvePevForMateria($part, $seccionId, $grado, $letra);
            if ($r[0]) {
                $out[] = ['pev' => $r[0], 'sub_index' => $idx, 'compound' => true];
            }
        }

        return $out;
    }

    /**
     * Divide una materia que es la concatenación de dos asignaturas conocidas
     * (unidas por el ETL a partir de un bloque de celda combinado). Devuelve
     * las partes si se reconocen >= 2 sujetos consecutivos; [] si no encaja.
     *
     * @return list<string>
     */
    private function splitCompoundMateria(string $materiaNorm): array
    {
        $known = $this->allKnownSubjects();
        $parts = [];
        $rest = $materiaNorm;

        while ($rest !== '') {
            $matched = null;
            $matchedLen = 0;
            foreach ($known as $subject) {
                $len = strlen($subject);
                if ($len <= $matchedLen) {
                    continue;
                }
                if ($subject === $rest || strncmp($rest, $subject, $len) === 0) {
                    $matched = $subject;
                    $matchedLen = $len;
                }
            }
            if ($matchedLen === 0) {
                return []; // no es una concatenación reconocible
            }
            $parts[] = $matched;
            $rest = trim(substr($rest, $matchedLen));
        }

        return count($parts) >= 2 ? $parts : [];
    }

    /** @return list<string> todas las asignaturas conocidas (alias), ordenadas por longitud desc */
    private function allKnownSubjects(): array
    {
        if ($this->knownSubjects === null) {
            $set = [];
            foreach (self::ALIAS_ASIGNATURA as $key => $values) {
                $set[] = $this->norm($key);
                foreach ($values as $v) {
                    $set[] = $this->norm($v);
                }
            }
            $set = array_values(array_unique(array_filter($set)));
            usort($set, fn ($a, $b) => strlen($b) <=> strlen($a));
            $this->knownSubjects = $set;
        }

        return $this->knownSubjects;
    }

    private function aliasesFor(string $materiaNorm): array
    {
        if ($this->aliasNorm === null) {
            $map = [];
            foreach (self::ALIAS_ASIGNATURA as $key => $values) {
                $map[$this->norm($key)] = array_map(fn ($v) => $this->norm($v), $values);
            }
            $this->aliasNorm = $map;
        }
        $aliases = $this->aliasNorm[$materiaNorm] ?? [];

        return array_unique(array_merge([$materiaNorm], $aliases));
    }

    private function pevsForSeccion(int $seccionId): array
    {
        static $idx = null;
        if ($idx === null) {
            $idx = [];
            foreach ($this->allPevs as $p) {
                $idx[(int) $p->seccion_id][] = $p;
            }
        }

        return $idx[$seccionId] ?? [];
    }

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
        foreach (array_slice($audit['errors'], 0, 30) as $e) {
            $rows[] = ['ERROR', $e];
        }
        if (count($audit['errors']) > 30) {
            $rows[] = ['ERROR', '... '.(count($audit['errors']) - 30).' más'];
        }
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

    /**
     * Crea UN calendario POR PESTUDIO (el pestudio se asocia al calendario, no al
     * período). Cada calendario genera los períodos de la estructura de su nivel.
     *
     * @return array<int, TimetableCalendar>
     */
    private function persistCalendar(int $lapsoId, array $audit, string $strategy): array
    {
        $baseName = $this->option('calendar-name') ?: 'Horario 2025-2026 (legacy)';
        $shiftM = TimetableShift::where('code', 'M')->first();
        $shiftT = TimetableShift::where('code', 'T')->first();

        return DB::transaction(function () use ($lapsoId, $baseName, $audit, $shiftM, $shiftT, $strategy) {
            $calendars = [];

            $byPestudio = [];
            foreach ($audit['ok'] as $m) {
                $pid = (int) $m['pev']->pestudio_id;
                $byPestudio[$pid]['name'] = (string) $m['pev']->pestudio;
                $byPestudio[$pid]['items'][] = $m;
            }
            foreach ($this->allPevs as $pev) {
                $pid = (int) $pev->pestudio_id;
                if (! isset($byPestudio[$pid])) {
                    $byPestudio[$pid] = [
                        'name' => (string) $pev->pestudio,
                        'items' => [],
                    ];
                }
            }

            foreach ($byPestudio as $pid => $group) {
                $calendar = TimetableCalendar::create([
                    'lapso_id' => $lapsoId,
                    'pestudio_id' => $pid,
                    'name' => $baseName.' · '.$group['name'],
                    'period_minutes' => 60,
                    'strategy' => $strategy,
                    'status' => TimetableCalendar::STATUS_DRAFT,
                    'version' => 0,
                ]);

                $nivel = $this->levelForPestudio($group['name']);
                $periods = $this->buildPeriods($calendar, $shiftM, $shiftT, $nivel);

                $lessonByPev = [];
                $skipped = 0;
                $conflicts = [];

                // También se crean las evaluaciones sin slot legacy. Quedan
                // disponibles como "no asignadas" para completarlas en el
                // preview o el editor manual.
                $turnoPorPev = [];
                foreach ($group['items'] as $m) {
                    $turnoPorPev[(int) $m['pev']->id] = $m['periodo']['turno'];
                }
                foreach ($this->allPevs as $pev) {
                    if ((int) $pev->pestudio_id !== (int) $pid) {
                        continue;
                    }
                    $turno = $turnoPorPev[(int) $pev->id] ?? 'M';
                    $shift = $turno === 'T' ? $shiftT : $shiftM;
                    if (! $shift) {
                        $skipped++;

                        continue;
                    }
                    $lessonByPev[$pev->id] = TimetableLesson::create([
                        'calendar_id' => $calendar->id,
                        'pevaluacion_id' => $pev->id,
                        'shift_id' => $shift->id,
                        'weekly_blocks_t' => (int) ceil(((int) ($pev->hour_t_week ?? 0)) * 60 / 60),
                        'weekly_blocks_p' => (int) ceil(((int) ($pev->hour_p_week ?? 0)) * 60 / 60),
                        'room_type_required' => null,
                        'priority' => 0,
                        'locked' => isset($turnoPorPev[(int) $pev->id]),
                    ]);
                }

                foreach ($group['items'] as $m) {
                    $pev = $m['pev'];
                    $turno = $m['periodo']['turno'];
                    $dia = $m['periodo']['dia'];
                    $subStartMin = $this->toMin($m['periodo']['inicio'])
                        + (! empty($m['compound']) ? (($m['sub_index'] ?? 0) * 40) : 0);
                    $periodId = $this->periodIdFor($periods, $turno, $dia, $subStartMin);
                    if (! $periodId) {
                        $this->warn('slot sin período: '.$m['slot']['seccion'].' '.$m['slot']['materia'].' '.$m['periodo']['inicio']);
                        $skipped++;

                        continue;
                    }
                    try {
                        TimetableSlot::create([
                            'calendar_id' => $calendar->id,
                            'lesson_id' => $lessonByPev[$pev->id]->id,
                            'period_id' => $periodId,
                            'profesor_id' => $pev->profesor_id,
                            'seccion_id' => $pev->seccion_id,
                            'grupo_estable_id' => $pev->grupo_estable_id,
                            'room_id' => null,
                            'locked' => true,
                            'is_manual_override' => true,
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        $skipped++;
                        $conflictKey = str_contains((string) $e->getMessage(), 'uq_slot_teacher')
                            ? 'docente doble'
                            : (str_contains((string) $e->getMessage(), 'uq_slot_room')
                                ? 'aula doble'
                                : (str_contains((string) $e->getMessage(), 'uq_slot_section') ? 'sección doble' : 'conflicto'));
                        $conflicts[$conflictKey] = ($conflicts[$conflictKey] ?? 0) + 1;
                        $this->warn("slot omitido ($conflictKey): {$m['slot']['seccion']} {$m['slot']['materia']} {$m['periodo']['inicio']}");
                    }
                }
                if ($skipped) {
                    $detalle = array_map(fn ($k, $v) => "{$k}×{$v}", array_keys($conflicts), $conflicts);
                    $this->warn("{$skipped} slots omitidos en {$group['name']} (".(implode(', ', $detalle) ?: 'sin shift/período').'). Revisa en el editor.');
                }

                $calendars[] = $calendar;
            }

            return $calendars;
        });
    }

    /**
     * Períodos del calendario (que es DE UN pestudio): los bloques del nivel de
     * ese pestudio (del legacy legacy_estructura_horaria.csv), con tiempos
     * EXACTOS y recreos is_break=true.
     *
     * @return array<string, list<array{start:int, end:int, is_break:bool, ids:array<int,int>}>> turno => frames
     */
    private function buildPeriods(TimetableCalendar $calendar, ?TimetableShift $shiftM, ?TimetableShift $shiftT, string $nivel): array
    {
        $estructura = $this->loadEstructura();
        $periods = [];

        foreach (['M' => $shiftM, 'T' => $shiftT] as $turno => $shift) {
            if (! $shift) {
                continue;
            }
            $franjas = $estructura[$nivel][$turno] ?? [];
            if ($franjas === []) {
                continue;
            }
            $order = 1;
            foreach ($franjas as [$start, $end, $isBreak]) {
                $ids = [];
                foreach (range(1, 5) as $dia) {
                    $ids[$dia] = TimetablePeriod::create([
                        'calendar_id' => $calendar->id,
                        'shift_id' => $shift->id,
                        'day_of_week' => $dia,
                        'order_in_day' => $order,
                        'start_time' => $this->fmt($start),
                        'end_time' => $this->fmt($end),
                        'is_break' => $isBreak,
                    ])->id;
                }
                $periods[$turno][] = ['start' => $start, 'end' => $end, 'is_break' => $isBreak, 'ids' => $ids];
                $order++;
            }
        }

        return $periods;
    }

    /** Id del período NO-recreo (turno + día) cuyo bloque contiene el minuto dado. */
    private function periodIdFor(array $periods, string $turno, int $dia, int $minute): ?int
    {
        foreach ($periods[$turno] ?? [] as $frame) {
            if ($frame['is_break']) {
                continue;
            }
            if ($minute >= $frame['start'] && $minute < $frame['end']) {
                return $frame['ids'][$dia] ?? null;
            }
        }

        return null;
    }

    /** Nivel del legacy (estructura) para un pestudio. */
    private function levelForPestudio(string $pestudioName): string
    {
        $n = $this->norm($pestudioName);
        if (str_contains($n, 'PRIMARIA') || str_contains($n, 'INICIAL')) {
            return 'PRIMARIA';
        }

        return 'MEDIA GENERAL';
    }

    /**
     * Estructura horaria del legacy por nivel+turno: lista de franjas ordenadas
     * [startMin, endMin, esReceso]. De legacy_estructura_horaria.csv.
     *
     * @return array<string, array<string, list<array{0:int,1:int,2:bool}>>>
     */
    private function loadEstructura(): array
    {
        $path = rtrim((string) ($this->option('csv-dir') ?: config('timetable.legacy_csv_dir')), '/').'/legacy_estructura_horaria.csv';
        if (! is_file($path)) {
            return [];
        }

        $rows = [];
        $h = fopen($path, 'r');
        $header = fgetcsv($h);
        while (($r = fgetcsv($h)) !== false) {
            if (count($r) !== count($header)) {
                continue;
            }
            $rows[] = array_combine($header, $r);
        }
        fclose($h);

        $out = [];
        foreach ($rows as $r) {
            $out[$r['nivel']][$r['turno']][] = [
                $this->toMin($r['hora_inicio']),
                $this->toMin($r['hora_fin']),
                $r['es_receso'] === 'true',
            ];
        }
        foreach ($out as $nivel => $turnos) {
            foreach ($turnos as $turno => $franjas) {
                usort($franjas, fn ($a, $b) => $a[0] <=> $b[0]);
                $out[$nivel][$turno] = $franjas;
            }
        }

        return $out;
    }

    /**
     * Detecta franjas que se cruzan dentro de un mismo nivel y turno.
     *
     * @param  array<string, array<string, list<array{0:int,1:int,2:bool}>>>  $estructura
     * @return list<array{0:string,1:string,2:string,3:string}>
     */
    private function findStructureOverlaps(array $estructura): array
    {
        $overlaps = [];

        foreach ($estructura as $nivel => $turnos) {
            foreach ($turnos as $turno => $franjas) {
                $previous = null;

                foreach ($franjas as $franja) {
                    [$start, $end] = $franja;

                    if ($previous !== null && $start < $previous[1]) {
                        $overlaps[] = [
                            $nivel,
                            $turno,
                            $this->formatFrame($previous),
                            $this->formatFrame($franja),
                        ];
                    }

                    if ($previous === null || $end > $previous[1]) {
                        $previous = $franja;
                    }
                }
            }
        }

        return $overlaps;
    }

    /** @param array{0:int,1:int,2:bool} $franja */
    private function formatFrame(array $franja): string
    {
        return $this->fmt($franja[0]).'–'.$this->fmt($franja[1]).($franja[2] ? ' (recreo)' : ' (clase)');
    }

    private function toMin(string $h): int
    {
        [$hh, $mm] = explode(':', $h);

        return ((int) $hh) * 60 + ((int) $mm);
    }

    private function fmt(int $min): string
    {
        return sprintf('%02d:%02d:00', intdiv($min, 60), $min % 60);
    }

    private function norm(?string $s): string
    {
        if ($s === null) {
            return '';
        }

        // Los CSV legacy pueden venir en Windows-1252 o contener bytes
        // inválidos; iconv emite un warning que Laravel convierte en excepción.
        if (! mb_check_encoding($s, 'UTF-8')) {
            $s = mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
        }

        // `//IGNORE` handles bad bytes in most libc builds; `@` is required
        // because some production iconv implementations still emit a warning.
        $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        $s = $normalized !== false ? $normalized : $s;
        $s = preg_replace('/\s+/', ' ', trim($s)) ?? '';
        $s = str_replace("'", '', $s);

        return mb_strtoupper($s);
    }
}
