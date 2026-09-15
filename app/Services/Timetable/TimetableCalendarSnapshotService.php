<?php

namespace App\Services\Timetable;

use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableConflict;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\app\Timetable\TimetableTeacherAvailability;
use App\Services\Timetable\Solver\TimetableSolverPlaybook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

/**
 * SPEC-TIMETABLE-SNAPSHOT-001 — Snapshot restaurable del calendario.
 *
 * Exporta el estado completo de un calendario (configuración de lessons +
 * horario + disponibilidad + bloqueos de sección + períodos) a un JSON con
 * checksum semántico, y lo restaura con **semántica de reemplazo**: el horario
 * del calendario se sustituye por el del snapshot, no se fusiona.
 *
 * El payload embebe un bloque `playbook` (§5.7) que documenta el flujo y las
 * reglas del solver para análisis por un LLM. Es **salida pura**: `verify()`,
 * `preview()` y `apply()` lo ignoran por completo, y queda **fuera del
 * checksum** (§7) para que editarlo no invalide snapshots ya emitidos.
 *
 * El componente Livewire queda como orquestador (guards, notificaciones,
 * sesión, modal); aquí vive toda la lógica (§13).
 */
final class TimetableCalendarSnapshotService
{
    /** Formato del snapshot restaurable (semántica de reemplazo). */
    public const FORMAT = 'cfla-timetable-calendar-snapshot';

    /** Versión del formato. Se incrementa ante cambios incompatibles (§10). */
    public const VERSION = 1;

    /** Formato legacy: aditivo, NO reemplaza el horario (§10). */
    public const LEGACY_FORMAT = 'cfla-timetable-lessons-backup';

    public const CHECKSUM_ALGO = 'sha256-semantic-v1';

    /**
     * Contenido semántico hasheado, en orden fijo (§7). Todo lo demás
     * —metadatos, `schema` y `playbook`— queda fuera del hash.
     *
     * @var list<string>
     */
    public const CHECKSUM_KEYS = ['calendar', 'periods', 'section_locks', 'availability', 'lessons', 'slots'];

    /** Directorio del auto-backup dentro del disco `local` (§9.3). */
    public const AUTO_BACKUP_DIR = 'timetable-snapshots';

    private const INSERT_CHUNK = 500;

    /** Tope de detalle listado en el preview: el resto se cuenta (§15). */
    private const DETAIL_CAP = 50;

    public function __construct(
        private readonly TimetableSolverPlaybook $playbook,
        private readonly TimetableLessonPersistenceService $persistence,
    ) {}

    // ─── Export ───────────────────────────────────────────────────────────

    /**
     * Arma el payload del snapshot + su checksum (§5.1, §7, §8).
     *
     * @return array<string, mixed>
     */
    public function build(TimetableCalendar $calendar): array
    {
        $calendar->loadMissing(['lapso', 'pestudio', 'pescolar']);

        $semantic = [
            'calendar' => [
                'id' => (int) $calendar->id,
                'name' => $calendar->name,
                'lapso_id' => $calendar->lapso_id,
                'lapso' => $calendar->lapso?->name,
                'pescolar_id' => $calendar->pescolar_id,
                'pestudio_id' => $calendar->pestudio_id,
                'pestudio' => $calendar->pestudio?->name,
                'period_minutes' => (int) $calendar->period_minutes,
                'max_subjects_per_period' => (int) $calendar->max_subjects_per_period,
                'strategy' => $calendar->strategy,
                'status' => (string) $calendar->status,
            ],
            'periods' => $this->periodRows($calendar),
            'section_locks' => $this->sectionLockRows($calendar),
            'availability' => $this->availabilityRows($calendar),
            'lessons' => $this->lessonRows($calendar),
            'slots' => $this->slotRows($calendar),
        ];

        $payload = [
            'format' => self::FORMAT,
            'version' => self::VERSION,
            'scope' => 'calendar',
            'exported_at' => now()->toIso8601String(),
            'exported_by' => auth()->id(),
            'app_version' => (string) (config('app.version') ?? 'dev'),
            'environment' => app()->environment(),
            'checksum_algo' => self::CHECKSUM_ALGO,
            'checksum' => '',
            'schema' => $this->schema(),
        ] + $semantic;

        // El checksum se calcula ANTES de añadir el playbook: doble garantía de
        // que la documentación nunca invalida un snapshot (§5.7, regla 2).
        $payload['checksum'] = $this->checksum($payload);
        $payload['playbook'] = $this->playbook->build();

        return $payload;
    }

    /**
     * Filas de lesson en el formato del respaldo vigente (§5.1, §8). Lo usan
     * tanto el snapshot como los respaldos legacy.
     *
     * @return list<array<string, mixed>>
     */
    public function lessonRows(TimetableCalendar $calendar): array
    {
        return TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->with([
                'pevaluacion.pensum.asignatura',
                'pevaluacion.seccion.grado.pestudio',
                'pevaluacion.profesor',
                'pevaluacion.grupoEstable',
            ])
            ->orderBy('id')
            ->get()
            ->map(function (TimetableLesson $lesson): ?array {
                $pevaluacion = $lesson->pevaluacion;

                if (! $pevaluacion) {
                    return null;
                }

                $asignatura = $pevaluacion->pensum?->asignatura;
                $seccion = $pevaluacion->seccion;
                $grado = $seccion?->grado;
                $pestudio = $grado?->pestudio;
                $profesor = $pevaluacion->profesor;

                return [
                    'pevaluacion_id' => (int) $pevaluacion->id,
                    'academic_identity' => [
                        'lapso_id' => $pevaluacion->lapso_id,
                        'pestudio_id' => $pestudio?->id,
                        'grado_id' => $grado?->id,
                        'seccion_id' => $seccion?->id,
                        'pensum_id' => $pevaluacion->pensum_id,
                        'profesor_id' => $pevaluacion->profesor_id,
                        'grupo_estable_id' => $pevaluacion->grupo_estable_id,
                        'asignatura_id' => $asignatura?->id,
                    ],
                    'labels' => [
                        'pestudio' => $pestudio?->name,
                        'grado' => $grado?->name,
                        'seccion' => $seccion?->name,
                        'asignatura' => $asignatura?->name,
                        'profesor' => $profesor
                            ? trim(($profesor->lastname ?? '').', '.($profesor->name ?? ''))
                            : null,
                        'lapso' => $pevaluacion->lapso?->name,
                    ],
                    'configuration' => [
                        'shift_id' => (int) $lesson->shift_id,
                        'weekly_blocks_t' => max(0, (int) $lesson->weekly_blocks_t),
                        'weekly_blocks_p' => max(0, (int) $lesson->weekly_blocks_p),
                        'room_type_required' => $lesson->room_type_required ?: null,
                        'is_half_group' => (bool) $lesson->is_half_group,
                        'allow_shared_teacher' => (bool) $lesson->allow_shared_teacher,
                        'priority' => max(0, (int) $lesson->priority),
                        'locked' => (bool) $lesson->locked,
                    ],
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Documentación autocontenida del formato (§5.1). No entra en el checksum.
     *
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        return [
            'format' => [
                'type' => 'string',
                'enum' => [self::FORMAT, self::LEGACY_FORMAT],
                'desc' => 'Contrato del archivo. El snapshot ('.self::FORMAT.') reemplaza el horario; el legacy ('.self::LEGACY_FORMAT.') es aditivo.',
            ],
            'version' => [
                'type' => 'int',
                'current' => self::VERSION,
                'desc' => 'Versión del formato. El restore exige version===1 para el snapshot.',
            ],
            'scope' => [
                'type' => 'string',
                'enum' => ['calendar'],
                'desc' => 'Alcance del snapshot. Siempre un calendario completo.',
            ],
            'exported_at' => 'string ISO8601. Informativo; no se usa en el restore.',
            'exported_by' => 'int|null. Usuario que exportó. Informativo.',
            'app_version' => 'string. Versión de la aplicación al exportar. Informativo.',
            'environment' => 'string. Entorno de origen (local/production). Informativo.',
            'checksum_algo' => [
                'type' => 'string',
                'value' => self::CHECKSUM_ALGO,
                'desc' => 'sha256 sobre el re-encode canónico de las claves semánticas: '.implode(', ', self::CHECKSUM_KEYS).'. Se excluyen metadatos, schema y playbook.',
            ],
            'checksum' => 'string "sha256:<hex>". El restore lo recalcula y rechaza si no coincide.',
            'schema' => 'object. Este documento. No entra en el checksum.',
            'playbook' => 'object. Flujo y reglas del solver para análisis por un LLM. Salida pura: el restore NUNCA lo lee. No entra en el checksum.',
            'calendar' => [
                'type' => 'object',
                'desc' => 'Identidad del calendario. El restore empareja el destino por lapso_id+pestudio_id, NO por id (los ids no son portables).',
                'fields' => [
                    'id' => 'int. PK local. Informativo.',
                    'name' => 'string. Etiqueta.',
                    'lapso_id' => 'int. FK lapso. Clave de emparejamiento.',
                    'lapso' => 'string. Etiqueta.',
                    'pescolar_id' => 'int. FK periodo escolar.',
                    'pestudio_id' => 'int. FK pestudio. Clave de emparejamiento.',
                    'pestudio' => 'string. Etiqueta.',
                    'period_minutes' => 'int. Duración del bloque en minutos.',
                    'max_subjects_per_period' => 'int. Tope de medio-grupos por celda (D-6).',
                    'strategy' => 'string. optimized|legacy.',
                    'status' => 'string. Estado del calendario: draft, active o archived.',
                ],
            ],
            'periods' => [
                'type' => 'array',
                'desc' => 'Bloques del día. El restore los usa como referencia y crea solo los faltantes por (shift_code, day_of_week, order_in_day). NO borra períodos: los slots caen por cascade.',
                'item' => ['shift_code' => 'string M|T', 'day_of_week' => 'int 1..5', 'order_in_day' => 'int', 'start_time' => 'HH:MM', 'end_time' => 'HH:MM', 'is_break' => 'bool'],
            ],
            'section_locks' => [
                'type' => 'array',
                'desc' => 'Bloqueo del horario por sección (seccions.timetable_locked). Toca una tabla fuera de horario y es por sección, no por calendario: el preview lista los cambios y el usuario confirma (§18.1).',
                'item' => ['seccion_id' => 'int', 'seccion' => 'string', 'grado' => 'string|null', 'pestudio' => 'string|null', 'locked' => 'bool'],
            ],
            'availability' => [
                'type' => 'array',
                'desc' => 'Disponibilidad docente por TURNO·DÍA·BLOQUE (no por period_id). El restore la re-vincula por (profesor_id, shift_code, day_of_week, order_in_day) y reemplaza todas las filas del calendario.',
                'item' => ['profesor_id' => 'int', 'profesor' => 'string', 'shift_code' => 'string', 'day_of_week' => 'int', 'order_in_day' => 'int', 'start_time' => 'HH:MM', 'end_time' => 'HH:MM', 'is_available' => 'bool'],
            ],
            'lessons' => [
                'type' => 'array',
                'desc' => 'Configuración de lecciones (mismo formato del respaldo vigente). Se re-vinculan por pevaluacion_id con respaldo en academic_identity.',
                'lesson_row' => [
                    'pevaluacion_id' => 'int. PK local de pevaluaciones.',
                    'academic_identity' => 'object. FKs para re-vincular (lapso_id, pestudio_id, grado_id, seccion_id, pensum_id, profesor_id, grupo_estable_id, asignatura_id).',
                    'labels' => 'object. Etiquetas. Solo informativas.',
                    'configuration' => [
                        'shift_id' => 'int. Turno preferido.',
                        'weekly_blocks_t' => 'int>=0. Bloques teóricos.',
                        'weekly_blocks_p' => 'int>=0. Bloques prácticos.',
                        'room_type_required' => 'string|null.',
                        'is_half_group' => 'bool. Medio-grupo: la celda de su sección admite hasta max_subjects_per_period mitades.',
                        'allow_shared_teacher' => 'bool. Relaja la unicidad de docente (D-1).',
                        'priority' => 'int>=0.',
                        'locked' => 'bool. Bloqueo a nivel de lección.',
                    ],
                ],
            ],
            'slots' => [
                'type' => 'array',
                'desc' => 'Horario asignado. Ausente = no se toca el horario (modo aditivo/solo configuración); presente y vacío = el horario queda vacío (§5.4).',
                'item' => [
                    'pevaluacion_id' => 'int. Enlaza con la lesson (los lesson_id se regeneran).',
                    'period' => 'object {shift_code, day_of_week, order_in_day}. Re-vinculación estable del período.',
                    'room_id' => 'int|null.',
                    'profesor_id' => 'int. Validación: prevalece el valor de la pevaluación resuelta (§5.3).',
                    'seccion_id' => 'int. Validación: prevalece el de la pevaluación.',
                    'grupo_estable_id' => 'int|null. Validación: prevalece el de la pevaluación.',
                    'is_manual_override' => 'bool',
                    'locked' => 'bool',
                    'is_practical' => 'bool. Solo si la columna existe en el destino (§5.5).',
                    'is_half_group' => 'bool. Afecta la clave generada slot_section_key.',
                    'allow_shared_teacher' => 'bool. Afecta la clave generada slot_teacher_key.',
                ],
            ],
        ];
    }

    // ─── Integridad (§7) ──────────────────────────────────────────────────

    /** Re-encode canónico de las claves semánticas, para el hash. */
    public function canonicalize(array $payload): string
    {
        $semantic = [];
        foreach (self::CHECKSUM_KEYS as $key) {
            if (array_key_exists($key, $payload)) {
                $semantic[$key] = $payload[$key];
            }
        }

        return json_encode(
            $this->sortRecursive($semantic),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );
    }

    /** `sha256:<hex>` del contenido semántico (§7). */
    public function checksum(array $payload): string
    {
        return 'sha256:'.hash('sha256', $this->canonicalize($payload));
    }

    /**
     * Valida formato, versión y checksum. No toca la base de datos (§9.1).
     *
     * @return array{format: string, version: int, legacy: bool, checksum_verified: bool}
     *
     * @throws InvalidArgumentException cuando el payload no es restaurable.
     */
    public function verify(array $payload): array
    {
        $format = $payload['format'] ?? null;

        if (! is_string($format) || $format === '') {
            throw new InvalidArgumentException('El archivo no declara un formato de snapshot.');
        }

        if ($format === self::LEGACY_FORMAT) {
            return [
                'format' => self::LEGACY_FORMAT,
                'version' => (int) ($payload['version'] ?? 0),
                'legacy' => true,
                'checksum_verified' => false,
            ];
        }

        if ($format !== self::FORMAT) {
            throw new InvalidArgumentException("Formato de snapshot no reconocido: «{$format}».");
        }

        $version = (int) ($payload['version'] ?? 0);
        if ($version !== self::VERSION) {
            throw new InvalidArgumentException("Versión de snapshot no soportada: {$version} (se espera ".self::VERSION.').');
        }

        if (! is_array($payload['lessons'] ?? null)) {
            throw new InvalidArgumentException('El snapshot no contiene un bloque de lessons válido.');
        }

        $algo = $payload['checksum_algo'] ?? null;
        if ($algo !== null && $algo !== self::CHECKSUM_ALGO) {
            throw new InvalidArgumentException("Algoritmo de checksum no soportado: «{$algo}».");
        }

        $checksumVerified = false;
        $checksum = $payload['checksum'] ?? null;
        if (is_string($checksum) && $checksum !== '') {
            if (! hash_equals($this->checksum($payload), $checksum)) {
                throw new InvalidArgumentException('El checksum no coincide: el archivo está corrupto o fue alterado.');
            }
            $checksumVerified = true;
        }

        return [
            'format' => self::FORMAT,
            'version' => $version,
            'legacy' => false,
            'checksum_verified' => $checksumVerified,
        ];
    }

    // ─── Restore (§9) ─────────────────────────────────────────────────────

    /**
     * Diff contra la base de datos, sin escribir nada (§9.2). Incluye las
     * colisiones de unicidad y el sello de concurrencia (`version` +
     * `state_hash`) que `apply()` revalidará.
     *
     * @return array<string, mixed>
     */
    public function preview(TimetableCalendar $calendar, array $payload): array
    {
        $mode = array_key_exists('slots', $payload) ? 'replace' : 'additive';
        $resolved = $this->resolveLessonRows($calendar, $payload['lessons'] ?? []);
        $snapshotPevIds = array_map('intval', array_keys($resolved));

        $currentLessonPevIds = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->pluck('pevaluacion_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $shiftCodes = $this->shiftCodes();
        $periodIndex = $this->existingPeriodIndex($calendar, $shiftCodes);
        $periodDefs = $this->snapshotPeriodDefs($payload);

        $plan = $this->planSlots($payload, $resolved, $shiftCodes, $periodIndex, $periodDefs);

        $snapshotRows = is_array($payload['lessons'] ?? null) ? count($payload['lessons']) : 0;
        $unresolvable = $snapshotRows - count($resolved);

        $lostLessonIds = array_values(array_diff($currentLessonPevIds, $snapshotPevIds));

        $currentSlots = TimetableSlot::query()->where('calendar_id', $calendar->id)->count();

        $warnings = $this->previewWarnings($payload, $plan, $mode, $resolved);
        $lockChanges = $this->sectionLockChanges($payload);

        $availability = $payload['availability'] ?? [];

        // Sello de concurrencia contra el estado REAL de la BD, no contra el
        // modelo en memoria (que puede traer atributos no persistidos): apply()
        // revalida con `fresh()` y debe comparar manzanas con manzanas (D5).
        $fresh = $calendar->fresh() ?? $calendar;

        return [
            'mode' => $mode,
            'version' => (int) $fresh->version,
            'state_hash' => $this->checksum($this->build($fresh)),
            'lessons' => [
                'snapshot' => $snapshotRows,
                'resolvable' => count($resolved),
                'unresolvable' => max(0, $unresolvable),
                'lost' => count($lostLessonIds),
                'lost_ids' => array_slice($lostLessonIds, 0, self::DETAIL_CAP),
                'skipped_detail' => $plan['lesson_skips'],
            ],
            'slots' => [
                'present' => $mode === 'replace',
                'current' => $currentSlots,
                'snapshot' => is_array($payload['slots'] ?? null) ? count($payload['slots']) : 0,
                'insertable' => count($plan['rows']),
                'skipped' => count($plan['skipped']),
                'deduplicated' => $plan['deduplicated'],
                'periods_to_create' => count($plan['periods_to_create']),
                'skipped_detail' => array_slice($plan['skipped'], 0, self::DETAIL_CAP),
            ],
            'availability' => [
                'current' => TimetableTeacherAvailability::query()->where('calendar_id', $calendar->id)->count(),
                'snapshot' => is_array($availability) ? count($availability) : 0,
            ],
            'section_locks' => ['changes' => $lockChanges],
            'collisions' => $plan['collisions'],
            'warnings' => $warnings,
            'truncated' => count($plan['skipped']) > self::DETAIL_CAP || count($lostLessonIds) > self::DETAIL_CAP,
        ];
    }

    /**
     * Aplica el snapshot: revalida concurrencia, escribe el auto-backup y
     * reemplaza dentro de una transacción (§9.3). Devuelve el reporte.
     *
     * @param  array<string, mixed>  $preview  Salida de `preview()` (concurrencia D5).
     * @return array<string, mixed>
     *
     * @throws RuntimeException cuando el calendario cambió desde el preview o falla el auto-backup.
     * @throws \Illuminate\Validation\ValidationException cuando las lessons no son válidas.
     */
    public function apply(TimetableCalendar $calendar, array $payload, array $preview = []): array
    {
        $calendar = $this->revalidate($calendar, $preview);

        // 1) Red de seguridad: si el auto-backup no se puede escribir, NO se aplica nada.
        $autoBackup = $this->writeAutoBackup($calendar);

        $mode = array_key_exists('slots', $payload) ? 'replace' : 'additive';

        $report = DB::transaction(function () use ($calendar, $payload, $mode): array {
            $resolved = $this->resolveLessonRows($calendar, $payload['lessons'] ?? []);

            if ($resolved === []) {
                throw new RuntimeException('No se encontraron Pevaluaciones compatibles para restaurar.');
            }

            if ($mode === 'replace') {
                // Orden explícito: slots primero, luego lessons. No dependemos del
                // cascade (puede faltar en entornos reconstruidos, §11/§15).
                TimetableSlot::query()->where('calendar_id', $calendar->id)->delete();
                TimetableLesson::query()->where('calendar_id', $calendar->id)->delete();
            }

            $this->persistence->persist((int) $calendar->id, $resolved);

            $lessonIdByPev = TimetableLesson::query()
                ->where('calendar_id', $calendar->id)
                ->pluck('id', 'pevaluacion_id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            $shiftCodes = $this->shiftCodes();
            $periodIndex = $this->existingPeriodIndex($calendar, $shiftCodes);
            $periodDefs = $this->snapshotPeriodDefs($payload);
            $plan = $this->planSlots($payload, $resolved, $shiftCodes, $periodIndex, $periodDefs);

            $periodIdsByKey = $this->ensurePeriods($calendar, $plan, $shiftCodes, $periodDefs, $periodIndex);

            $slotsInserted = $mode === 'replace'
                ? $this->insertSlots($calendar, $plan['rows'], $lessonIdByPev, $periodIdsByKey)
                : 0;

            // Limpia los conflictos del calendario: describían el horario reemplazado.
            TimetableConflict::query()->where('calendar_id', $calendar->id)->delete();

            $availability = $this->replaceAvailability($calendar, $payload['availability'] ?? [], $shiftCodes);
            $lockChanges = $this->applySectionLocks($payload);

            return [
                'mode' => $mode,
                'lessons' => count($resolved),
                'slots' => $slotsInserted,
                'slots_skipped' => count($plan['skipped']),
                'slots_deduplicated' => $plan['deduplicated'],
                'collisions_resolved' => array_sum(array_column($plan['collisions'], 'dropped')),
                'periods_created' => count($plan['periods_to_create']),
                'availability' => $availability,
                'section_locks' => $lockChanges,
            ];
        });

        $report['auto_backup'] = $autoBackup;

        return $report;
    }

    /**
     * Escribe el snapshot del estado ACTUAL del calendario antes de tocarlo
     * (§9.3). Es un snapshot válido y re-aplicable → "Deshacer último restore".
     *
     * @param  string  $prefix  Prefijo del archivo: `auto` (restore) o
     *                          `pre-reset` (borrado total del módulo).
     * @return string Ruta relativa dentro del disco `local`.
     */
    public function writeAutoBackup(TimetableCalendar $calendar, string $prefix = 'auto'): string
    {
        $json = json_encode(
            $this->build($calendar),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );

        $file = sprintf(
            '%s/%s-%d-%s-%s.json',
            self::AUTO_BACKUP_DIR,
            $prefix,
            (int) $calendar->id,
            now()->format('Ymd_His'),
            substr(hash('sha256', $json), 0, 8),
        );

        if (! Storage::disk('local')->put($file, $json)) {
            throw new RuntimeException('No se pudo escribir el auto-backup; la operación se abortó sin tocar el horario.');
        }

        return $file;
    }

    // ─── Concurrencia (D5) ────────────────────────────────────────────────

    /** Revalida `version` + hash de estado y devuelve el calendario fresco. */
    private function revalidate(TimetableCalendar $calendar, array $preview): TimetableCalendar
    {
        if ($preview === []) {
            return $calendar;
        }

        $fresh = $calendar->fresh();
        if ($fresh === null) {
            throw new RuntimeException('El calendario ya no existe.');
        }

        $expectedVersion = $preview['version'] ?? null;
        if ($expectedVersion !== null && (int) $fresh->version !== (int) $expectedVersion) {
            throw new RuntimeException('El calendario cambió desde el preview; vuelve a previsualizar.');
        }

        $expectedHash = $preview['state_hash'] ?? null;
        if (is_string($expectedHash) && $expectedHash !== ''
            && ! hash_equals($expectedHash, $this->checksum($this->build($fresh)))
        ) {
            throw new RuntimeException('El calendario cambió desde el preview; vuelve a previsualizar.');
        }

        return $fresh;
    }

    // ─── Resolución de lessons ────────────────────────────────────────────

    /**
     * Re-vincula cada fila del snapshot a su Pevaluacion del calendario destino,
     * por `pevaluacion_id` con respaldo en `academic_identity` (§6).
     *
     * @return array<int, array<string, mixed>> pev_id => configuración + denormalizados
     */
    private function resolveLessonRows(TimetableCalendar $calendar, array $rows): array
    {
        $pevaluaciones = Pevaluacion::query()
            ->with(['seccion.grado.pestudio', 'pensum.asignatura', 'profesor'])
            ->where('lapso_id', $calendar->lapso_id)
            ->whereHas('seccion.grado', fn ($query) => $query->where('pestudio_id', $calendar->pestudio_id))
            ->get();

        $byId = $pevaluaciones->keyBy('id');
        $byIdentity = $pevaluaciones->keyBy(fn ($pev) => implode(':', [
            $pev->seccion_id,
            $pev->pensum_id,
            $pev->profesor_id,
            $pev->grupo_estable_id ?? 0,
        ]));

        $resolved = [];
        foreach ($rows as $row) {
            if (! is_array($row) || ! is_array($row['configuration'] ?? null)) {
                continue;
            }

            $identity = is_array($row['academic_identity'] ?? null) ? $row['academic_identity'] : [];
            $pev = $byId->get((int) ($row['pevaluacion_id'] ?? 0));

            if (! $pev || (int) $pev->lapso_id !== (int) $calendar->lapso_id
                || (int) $pev->seccion?->grado?->pestudio_id !== (int) $calendar->pestudio_id
            ) {
                $pev = $byIdentity->get(implode(':', [
                    (int) ($identity['seccion_id'] ?? 0),
                    (int) ($identity['pensum_id'] ?? 0),
                    (int) ($identity['profesor_id'] ?? 0),
                    (int) ($identity['grupo_estable_id'] ?? 0),
                ]));
            }

            if (! $pev) {
                continue;
            }

            $configuration = $row['configuration'];
            $shiftId = $this->resolveShiftId($configuration['shift_id'] ?? 0);
            if ($shiftId <= 0 || ! TimetableShift::query()->whereKey($shiftId)->exists()) {
                $shiftId = $this->defaultShiftId();
            }

            $resolved[(int) $pev->id] = [
                'pev_id' => (int) $pev->id,
                'seccion_id' => (int) $pev->seccion_id,
                'profesor_id' => (int) $pev->profesor_id,
                'grupo_estable_id' => $pev->grupo_estable_id !== null ? (int) $pev->grupo_estable_id : null,
                'shift_id' => $shiftId,
                'weekly_blocks_t' => max(0, (int) ($configuration['weekly_blocks_t'] ?? 0)),
                'weekly_blocks_p' => max(0, (int) ($configuration['weekly_blocks_p'] ?? 0)),
                'room_type_required' => $configuration['room_type_required'] ?? null,
                'is_half_group' => (bool) ($configuration['is_half_group'] ?? false),
                'allow_shared_teacher' => (bool) ($configuration['allow_shared_teacher'] ?? false),
                'priority' => max(0, (int) ($configuration['priority'] ?? 0)),
                'locked' => (bool) ($configuration['locked'] ?? false),
            ];
        }

        return $resolved;
    }

    // ─── Planificación de slots ───────────────────────────────────────────

    /**
     * Resuelve cada slot del snapshot a una fila insertable, deduplicando por
     * identidad completa y descartando lo que violaría los índices únicos
     * generados (`uq_slot_lesson`, `uq_slot_section`, `uq_slot_teacher`,
     * `uq_slot_room`) — §9.2, §9.6.
     *
     * @param  array<int, array<string, mixed>>  $resolved
     * @param  array<int, string>  $shiftCodes
     * @param  array<string, int>  $periodIndex
     * @param  array<string, array<string, mixed>>  $periodDefs
     * @return array{rows: list<array<string, mixed>>, skipped: list<array<string, mixed>>, lesson_skips: list<array<string, mixed>>, collisions: list<array<string, mixed>>, deduplicated: int, periods_to_create: list<string>}
     */
    private function planSlots(
        array $payload,
        array $resolved,
        array $shiftCodes,
        array $periodIndex,
        array $periodDefs,
    ): array {
        $rows = [];
        $skipped = [];
        $collisions = [];
        $lessonSkips = [];
        $deduplicated = 0;
        $periodsToCreate = [];
        $seenIdentity = [];
        $seenUnique = [];
        $hasPractical = Schema::hasColumn('timetable_slots', 'is_practical');

        $slots = is_array($payload['slots'] ?? null) ? $payload['slots'] : [];
        $validRoomIds = $this->validRoomIds($slots);

        // Filas del snapshot cuya pevaluación no resolvió (§9.2).
        foreach (is_array($payload['lessons'] ?? null) ? $payload['lessons'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $pevId = (int) ($row['pevaluacion_id'] ?? 0);
            if ($pevId > 0 && ! isset($resolved[$pevId])) {
                $lessonSkips[] = ['pevaluacion_id' => $pevId, 'reason' => 'pevaluacion_not_found'];
                if (count($lessonSkips) >= self::DETAIL_CAP) {
                    break;
                }
            }
        }

        foreach ($slots as $index => $slot) {
            if (! is_array($slot)) {
                $skipped[] = ['index' => $index, 'reason' => 'invalid_row'];

                continue;
            }

            $pevId = (int) ($slot['pevaluacion_id'] ?? 0);
            $lesson = $resolved[$pevId] ?? null;
            if (! $lesson) {
                $skipped[] = ['index' => $index, 'pevaluacion_id' => $pevId, 'reason' => 'pevaluacion_not_found'];

                continue;
            }

            $period = is_array($slot['period'] ?? null) ? $slot['period'] : [];
            $shiftCode = strtoupper(trim((string) ($period['shift_code'] ?? '')));
            $day = (int) ($period['day_of_week'] ?? 0);
            $order = (int) ($period['order_in_day'] ?? 0);

            if ($shiftCode === '' || ! in_array($shiftCode, $shiftCodes, true)) {
                $skipped[] = ['index' => $index, 'pevaluacion_id' => $pevId, 'reason' => 'unknown_shift', 'shift_code' => $shiftCode];

                continue;
            }

            if ($day <= 0 || $order <= 0) {
                $skipped[] = ['index' => $index, 'pevaluacion_id' => $pevId, 'reason' => 'invalid_period'];

                continue;
            }

            $periodKey = $this->periodKey($shiftCode, $day, $order);
            $periodId = $periodIndex[$periodKey] ?? null;

            if ($periodId === null) {
                if (! isset($periodDefs[$periodKey])) {
                    $skipped[] = ['index' => $index, 'pevaluacion_id' => $pevId, 'reason' => 'period_not_found', 'period' => $periodKey];

                    continue;
                }
                if (! in_array($periodKey, $periodsToCreate, true)) {
                    $periodsToCreate[] = $periodKey;
                }
            }

            $roomId = $slot['room_id'] ?? null;
            if ($roomId !== null && $roomId !== '') {
                $roomId = (int) $roomId;
                if (! isset($validRoomIds[$roomId])) {
                    $skipped[] = ['index' => $index, 'pevaluacion_id' => $pevId, 'reason' => 'room_not_found', 'room_id' => $roomId];

                    continue;
                }
            } else {
                $roomId = null;
            }

            $isHalfGroup = (bool) ($slot['is_half_group'] ?? false);
            $allowShared = (bool) ($slot['allow_shared_teacher'] ?? false);
            $isPractical = $hasPractical ? (bool) ($slot['is_practical'] ?? false) : false;

            // §5.3: los denormalizados se derivan SIEMPRE de la pevaluación
            // resuelta; los del snapshot solo se validan (drift = warning).
            $seccionId = (int) $lesson['seccion_id'];
            $profesorId = (int) $lesson['profesor_id'];
            $grupoEstableId = $lesson['grupo_estable_id'] ?? null;

            $row = [
                'pev_id' => $pevId,
                'period_key' => $periodKey,
                'room_id' => $roomId,
                'seccion_id' => $seccionId,
                'profesor_id' => $profesorId,
                'grupo_estable_id' => $grupoEstableId,
                'is_manual_override' => (bool) ($slot['is_manual_override'] ?? false),
                'locked' => (bool) ($slot['locked'] ?? false),
                'is_practical' => $isPractical,
                'is_half_group' => $isHalfGroup,
                'allow_shared_teacher' => $allowShared,
            ];

            // Dedup por identidad completa (§9.6).
            $identity = implode('|', [
                $pevId, $periodKey, $seccionId, $grupoEstableId ?? 0, $isPractical ? 1 : 0,
                $isHalfGroup ? 1 : 0, $roomId ?? 0,
            ]);
            if (isset($seenIdentity[$identity])) {
                $deduplicated++;

                continue;
            }
            $seenIdentity[$identity] = true;

            // Colisiones residuales contra los índices únicos (claves generadas).
            $uniqueKeys = [
                'uq_slot_lesson' => 'L|'.$periodKey.'|'.$pevId,
                'uq_slot_section' => 'S|'.$periodKey.'|'.$this->slotSectionKey($seccionId, $grupoEstableId, $isHalfGroup, $pevId),
                'uq_slot_teacher' => 'T|'.$periodKey.'|'.$this->slotTeacherKey($profesorId, $isHalfGroup, $allowShared, $pevId),
            ];
            if ($roomId !== null) {
                $uniqueKeys['uq_slot_room'] = 'R|'.$periodKey.'|'.$roomId;
            }

            $collided = null;
            foreach ($uniqueKeys as $type => $key) {
                if (isset($seenUnique[$key])) {
                    $collided = $type;
                    break;
                }
            }

            if ($collided !== null) {
                $collisions[] = [
                    'index' => $index,
                    'pevaluacion_id' => $pevId,
                    'period' => $periodKey,
                    'type' => $collided,
                    'dropped' => 1,
                ];

                continue;
            }

            foreach ($uniqueKeys as $key) {
                $seenUnique[$key] = true;
            }

            $rows[] = $row;
        }

        return [
            'rows' => $rows,
            'skipped' => $skipped,
            'lesson_skips' => $lessonSkips,
            'collisions' => $collisions,
            'deduplicated' => $deduplicated,
            'periods_to_create' => $periodsToCreate,
        ];
    }

    /** Clave de la terna estable de período (§6). */
    private function periodKey(string $shiftCode, int $day, int $order): string
    {
        return $shiftCode.'-'.$day.'-'.$order;
    }

    /** Replica la columna generada `slot_section_key` (§9.6, migración 2026_09_09_000001). */
    private function slotSectionKey(int $seccionId, ?int $grupoEstableId, bool $isHalfGroup, int $lessonProxy): string
    {
        if ($isHalfGroup) {
            return 'S'.$seccionId.':H'.$lessonProxy;
        }

        return $grupoEstableId === null
            ? 'S'.$seccionId.':0'
            : 'S'.$seccionId.':G'.$grupoEstableId;
    }

    /** Replica la columna generada `slot_teacher_key` (§9.6, migración 2026_09_13_000002). */
    private function slotTeacherKey(int $profesorId, bool $isHalfGroup, bool $allowShared, int $lessonProxy): string
    {
        return ($isHalfGroup || $allowShared)
            ? 'T'.$profesorId.':S'.$lessonProxy
            : 'T'.$profesorId;
    }

    // ─── Escritura ────────────────────────────────────────────────────────

    /**
     * Crea los períodos faltantes por (shift_code, day_of_week, order_in_day) y
     * devuelve el índice completo `period_key => period_id`. Nunca borra ni
     * modifica períodos existentes (§9.3, §11).
     *
     * @param  array<string, mixed>  $plan
     * @param  array<int, string>  $shiftCodes
     * @param  array<string, array<string, mixed>>  $periodDefs
     * @param  array<string, int>  $periodIndex
     * @return array<string, int>
     */
    private function ensurePeriods(
        TimetableCalendar $calendar,
        array $plan,
        array $shiftCodes,
        array $periodDefs,
        array $periodIndex,
    ): array {
        $shiftIdsByCode = array_flip($shiftCodes);

        foreach ($plan['periods_to_create'] as $key) {
            $def = $periodDefs[$key] ?? null;
            if ($def === null) {
                continue;
            }

            [$shiftCode, $day, $order] = explode('-', $key);
            $shiftId = (int) ($shiftIdsByCode[$shiftCode] ?? 0);
            if ($shiftId <= 0) {
                continue;
            }

            $period = TimetablePeriod::query()->firstOrCreate(
                [
                    'calendar_id' => $calendar->id,
                    'shift_id' => $shiftId,
                    'day_of_week' => (int) $day,
                    'order_in_day' => (int) $order,
                ],
                [
                    'start_time' => $this->normalizeTime($def['start_time'] ?? null),
                    'end_time' => $this->normalizeTime($def['end_time'] ?? null),
                    'is_break' => (bool) ($def['is_break'] ?? false),
                ],
            );

            $periodIndex[$key] = (int) $period->id;
        }

        return $periodIndex;
    }

    /**
     * Inserta los slots por chunks (§9.4). Los campos desnormalizados ya vienen
     * derivados de la pevaluación (§5.3); `is_practical` solo si la columna
     * existe (§5.5). Las claves generadas las calcula MySQL: nunca se escriben.
     *
     * @param  list<array<string, mixed>>  $rows
     * @param  array<int, int>  $lessonIdByPev
     * @param  array<string, int>  $periodIdsByKey
     */
    private function insertSlots(
        TimetableCalendar $calendar,
        array $rows,
        array $lessonIdByPev,
        array $periodIdsByKey,
    ): int {
        $hasPractical = Schema::hasColumn('timetable_slots', 'is_practical');
        $now = now();
        $payload = [];

        foreach ($rows as $row) {
            $lessonId = $lessonIdByPev[(int) $row['pev_id']] ?? null;
            $periodId = $periodIdsByKey[$row['period_key']] ?? null;

            if ($lessonId === null || $periodId === null) {
                continue;
            }

            $attributes = [
                'calendar_id' => (int) $calendar->id,
                'lesson_id' => (int) $lessonId,
                'period_id' => (int) $periodId,
                'profesor_id' => (int) $row['profesor_id'],
                'seccion_id' => (int) $row['seccion_id'],
                'grupo_estable_id' => $row['grupo_estable_id'],
                'room_id' => $row['room_id'],
                'is_manual_override' => (bool) $row['is_manual_override'],
                'locked' => (bool) $row['locked'],
                'is_half_group' => (bool) $row['is_half_group'],
                'allow_shared_teacher' => (bool) $row['allow_shared_teacher'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($hasPractical) {
                $attributes['is_practical'] = (bool) $row['is_practical'];
            }

            $payload[] = $attributes;
        }

        foreach (array_chunk($payload, self::INSERT_CHUNK) as $chunk) {
            TimetableSlot::query()->insert($chunk);
        }

        return count($payload);
    }

    /**
     * Reemplaza la disponibilidad del calendario, re-vinculando por la terna
     * turno·día·bloque (§6, §9.3).
     *
     * @param  array<int, mixed>  $rows
     * @param  array<int, string>  $shiftCodes
     * @return int Filas escritas.
     */
    private function replaceAvailability(TimetableCalendar $calendar, array $rows, array $shiftCodes): int
    {
        TimetableTeacherAvailability::query()->where('calendar_id', $calendar->id)->delete();

        if ($rows === []) {
            return 0;
        }

        $shiftIdsByCode = array_flip($shiftCodes);
        $profesorIds = $this->validProfesorIds($rows);
        $payload = [];
        $seen = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $shiftCode = strtoupper(trim((string) ($row['shift_code'] ?? '')));
            $shiftId = (int) ($shiftIdsByCode[$shiftCode] ?? 0);
            $profesorId = (int) ($row['profesor_id'] ?? 0);
            $day = (int) ($row['day_of_week'] ?? 0);
            $order = (int) ($row['order_in_day'] ?? 0);

            if ($shiftId <= 0 || $profesorId <= 0 || $day <= 0 || $order <= 0 || ! isset($profesorIds[$profesorId])) {
                continue;
            }

            // uq_avail (calendar_id, profesor_id, shift_id, day_of_week, order_in_day)
            $key = $profesorId.'-'.$shiftId.'-'.$day.'-'.$order;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $payload[] = [
                'calendar_id' => (int) $calendar->id,
                'profesor_id' => $profesorId,
                'shift_id' => $shiftId,
                'day_of_week' => $day,
                'order_in_day' => $order,
                'start_time' => $this->normalizeTime($row['start_time'] ?? null),
                'end_time' => $this->normalizeTime($row['end_time'] ?? null),
                'is_available' => (bool) ($row['is_available'] ?? true),
            ];
        }

        foreach (array_chunk($payload, self::INSERT_CHUNK) as $chunk) {
            TimetableTeacherAvailability::query()->insert($chunk);
        }

        return count($payload);
    }

    /**
     * Aplica `seccions.timetable_locked`. Devuelve el número de secciones
     * cuyo estado cambió.
     */
    private function applySectionLocks(array $payload): int
    {
        $rows = is_array($payload['section_locks'] ?? null) ? $payload['section_locks'] : [];
        $changes = 0;

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $seccionId = (int) ($row['seccion_id'] ?? 0);
            if ($seccionId <= 0) {
                continue;
            }

            $seccion = Seccion::query()->find($seccionId);
            if (! $seccion) {
                continue;
            }

            $target = (bool) ($row['locked'] ?? false);
            if ((bool) $seccion->timetable_locked !== $target) {
                $seccion->update(['timetable_locked' => $target]);
                $changes++;
            }
        }

        return $changes;
    }

    // ─── Lectura auxiliar ─────────────────────────────────────────────────

    /**
     * @return list<array<string, mixed>>
     */
    private function periodRows(TimetableCalendar $calendar): array
    {
        $shiftCodes = $this->shiftCodes();

        return TimetablePeriod::query()
            ->where('calendar_id', $calendar->id)
            ->orderBy('shift_id')
            ->orderBy('day_of_week')
            ->orderBy('order_in_day')
            ->get()
            ->map(fn (TimetablePeriod $period): array => [
                'shift_code' => $shiftCodes[(int) $period->shift_id] ?? null,
                'day_of_week' => (int) $period->day_of_week,
                'order_in_day' => (int) $period->order_in_day,
                'start_time' => $this->clock((string) $period->start_time),
                'end_time' => $this->clock((string) $period->end_time),
                'is_break' => (bool) $period->is_break,
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function availabilityRows(TimetableCalendar $calendar): array
    {
        $shiftCodes = $this->shiftCodes();

        return TimetableTeacherAvailability::query()
            ->where('calendar_id', $calendar->id)
            ->with('profesor')
            ->orderBy('profesor_id')
            ->orderBy('shift_id')
            ->orderBy('day_of_week')
            ->orderBy('order_in_day')
            ->get()
            ->map(function (TimetableTeacherAvailability $row) use ($shiftCodes): array {
                $profesor = $row->profesor;

                return [
                    'profesor_id' => (int) $row->profesor_id,
                    'profesor' => $profesor
                        ? trim(($profesor->lastname ?? '').', '.($profesor->name ?? ''))
                        : null,
                    'shift_code' => $shiftCodes[(int) $row->shift_id] ?? null,
                    'day_of_week' => (int) $row->day_of_week,
                    'order_in_day' => (int) $row->order_in_day,
                    'start_time' => $this->clock((string) $row->start_time),
                    'end_time' => $this->clock((string) $row->end_time),
                    'is_available' => (bool) $row->is_available,
                ];
            })
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function slotRows(TimetableCalendar $calendar): array
    {
        $shiftCodes = $this->shiftCodes();
        $hasPractical = Schema::hasColumn('timetable_slots', 'is_practical');

        $persisted = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->with(['period', 'lesson:id,pevaluacion_id'])
            ->orderBy('id')
            ->get();

        if ($persisted->isNotEmpty()) {
            return $persisted->map(function (TimetableSlot $slot) use ($shiftCodes, $hasPractical): array {
                $period = $slot->period;

                $row = [
                    'pevaluacion_id' => (int) ($slot->lesson?->pevaluacion_id ?? 0),
                    'period' => [
                        'shift_code' => $period ? ($shiftCodes[(int) $period->shift_id] ?? null) : null,
                        'day_of_week' => $period ? (int) $period->day_of_week : null,
                        'order_in_day' => $period ? (int) $period->order_in_day : null,
                    ],
                    'room_id' => $slot->room_id !== null ? (int) $slot->room_id : null,
                    'profesor_id' => (int) $slot->profesor_id,
                    'seccion_id' => (int) $slot->seccion_id,
                    'grupo_estable_id' => $slot->grupo_estable_id !== null ? (int) $slot->grupo_estable_id : null,
                    'is_manual_override' => (bool) $slot->is_manual_override,
                    'locked' => (bool) $slot->locked,
                    'is_half_group' => (bool) $slot->is_half_group,
                    'allow_shared_teacher' => (bool) $slot->allow_shared_teacher,
                ];

                if ($hasPractical) {
                    $row['is_practical'] = (bool) $slot->is_practical;
                }

                return $row;
            })->all();
        }

        // A dry-run stores its assignment in preview_payload until publication.
        // Preserve that schedule too; otherwise a complete backup would contain
        // all lessons but silently lose the visible draft timetable.
        $assignment = is_array($calendar->preview_payload)
            ? ($calendar->preview_payload['assignment'] ?? [])
            : [];
        if (! is_array($assignment) || $assignment === []) {
            return [];
        }

        $lessons = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->with('pevaluacion:id,profesor_id,seccion_id,grupo_estable_id')
            ->get(['id', 'pevaluacion_id']);
        $lessons = $lessons->keyBy('id');
        $periods = TimetablePeriod::query()
            ->where('calendar_id', $calendar->id)
            ->get()
            ->keyBy('id');
        $rows = [];

        foreach ($assignment as $lessonId => $slots) {
            $lesson = $lessons->get((int) $lessonId);
            if (! $lesson || ! is_array($slots)) {
                continue;
            }

            foreach ($slots as $slot) {
                if (! is_array($slot)) {
                    continue;
                }
                $period = $periods->get((int) ($slot['period_id'] ?? 0));
                if (! $period) {
                    continue;
                }
                $pevaluacion = $lesson->pevaluacion;
                if (! $pevaluacion) {
                    continue;
                }

                $rows[] = [
                    'pevaluacion_id' => (int) $lesson->pevaluacion_id,
                    'period' => [
                        'shift_code' => $shiftCodes[(int) $period->shift_id] ?? null,
                        'day_of_week' => (int) $period->day_of_week,
                        'order_in_day' => (int) $period->order_in_day,
                    ],
                    'room_id' => isset($slot['room_id']) && $slot['room_id'] !== null
                        ? (int) $slot['room_id']
                        : null,
                    'profesor_id' => (int) $pevaluacion->profesor_id,
                    'seccion_id' => (int) $pevaluacion->seccion_id,
                    'grupo_estable_id' => $pevaluacion->grupo_estable_id !== null
                        ? (int) $pevaluacion->grupo_estable_id
                        : null,
                    'is_manual_override' => (bool) ($slot['is_manual_override'] ?? false),
                    'locked' => (bool) ($slot['locked'] ?? false),
                    'is_half_group' => (bool) ($slot['is_half_group'] ?? false),
                    'allow_shared_teacher' => (bool) ($slot['allow_shared_teacher'] ?? false),
                ];
            }
        }

        return $rows;
    }

    /**
     * Bloqueos de sección de las secciones presentes en el calendario (§5.1).
     *
     * @return list<array<string, mixed>>
     */
    private function sectionLockRows(TimetableCalendar $calendar): array
    {
        $seccionIds = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->with('pevaluacion:id,seccion_id')
            ->get()
            ->pluck('pevaluacion.seccion_id')
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($seccionIds === []) {
            return [];
        }

        return Seccion::query()
            ->whereIn('id', $seccionIds)
            ->with('grado.pestudio')
            ->orderBy('id')
            ->get()
            ->map(fn (Seccion $seccion): array => [
                'seccion_id' => (int) $seccion->id,
                'seccion' => $seccion->name,
                'grado' => $seccion->grado?->name,
                'pestudio' => $seccion->grado?->pestudio?->name,
                'locked' => (bool) $seccion->timetable_locked,
            ])
            ->all();
    }

    /**
     * Cambios de bloqueo que aplicaría el restore (§9.2).
     *
     * @return list<array<string, mixed>>
     */
    private function sectionLockChanges(array $payload): array
    {
        $rows = is_array($payload['section_locks'] ?? null) ? $payload['section_locks'] : [];
        if ($rows === []) {
            return [];
        }

        $ids = collect($rows)
            ->map(fn ($row): int => (int) (is_array($row) ? ($row['seccion_id'] ?? 0) : 0))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $current = Seccion::query()
            ->whereIn('id', $ids)
            ->pluck('timetable_locked', 'id');

        $changes = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $seccionId = (int) ($row['seccion_id'] ?? 0);
            if ($seccionId <= 0 || ! $current->has($seccionId)) {
                continue;
            }

            $from = (bool) $current[$seccionId];
            $to = (bool) ($row['locked'] ?? false);

            if ($from !== $to) {
                $changes[] = [
                    'seccion_id' => $seccionId,
                    'seccion' => $row['seccion'] ?? null,
                    'from' => $from,
                    'to' => $to,
                ];
            }
        }

        return $changes;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    /**
     * @return array<int, string> shift_id => code
     */
    private function shiftCodes(): array
    {
        return TimetableShift::query()->pluck('code', 'id')
            ->map(fn ($code): string => (string) $code)
            ->all();
    }

    /**
     * Períodos ya existentes del calendario, indexados por la terna estable.
     *
     * @param  array<int, string>  $shiftCodes
     * @return array<string, int>
     */
    private function existingPeriodIndex(TimetableCalendar $calendar, array $shiftCodes): array
    {
        $index = [];
        foreach (TimetablePeriod::query()->where('calendar_id', $calendar->id)->get() as $period) {
            $code = $shiftCodes[(int) $period->shift_id] ?? null;
            if ($code === null) {
                continue;
            }
            $index[$this->periodKey($code, (int) $period->day_of_week, (int) $period->order_in_day)] = (int) $period->id;
        }

        return $index;
    }

    /**
     * Definiciones de período declaradas en el snapshot (para crear faltantes).
     *
     * @return array<string, array<string, mixed>>
     */
    private function snapshotPeriodDefs(array $payload): array
    {
        $defs = [];
        $rows = is_array($payload['periods'] ?? null) ? $payload['periods'] : [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $code = strtoupper(trim((string) ($row['shift_code'] ?? '')));
            $day = (int) ($row['day_of_week'] ?? 0);
            $order = (int) ($row['order_in_day'] ?? 0);

            if ($code === '' || $day <= 0 || $order <= 0) {
                continue;
            }

            $defs[$this->periodKey($code, $day, $order)] = [
                'start_time' => $row['start_time'] ?? null,
                'end_time' => $row['end_time'] ?? null,
                'is_break' => (bool) ($row['is_break'] ?? false),
            ];
        }

        return $defs;
    }

    /**
     * Advertencias del preview (§9.2).
     *
     * @param  array<string, mixed>  $plan
     * @param  array<int, array<string, mixed>>  $resolved
     * @return list<array<string, mixed>>
     */
    private function previewWarnings(
        array $payload,
        array $plan,
        string $mode,
        array $resolved,
    ): array {
        $warnings = [];

        if ($mode === 'replace' && ($payload['slots'] ?? null) === []) {
            $warnings[] = [
                'type' => 'empty_schedule',
                'message' => 'El snapshot trae «slots» vacío: el horario del calendario quedará SIN clases tras el restore.',
            ];
        }

        if ($mode === 'additive') {
            $warnings[] = [
                'type' => 'additive_mode',
                'message' => 'El snapshot no incluye el bloque «slots»: solo se aplicará la configuración; el horario actual NO se toca.',
            ];
        }

        if ($plan['skipped'] !== []) {
            $warnings[] = [
                'type' => 'slots_skipped',
                'count' => count($plan['skipped']),
                'message' => count($plan['skipped']).' slot(s) no se pudieron resolver y serán omitidos.',
            ];
        }

        if ($plan['periods_to_create'] !== []) {
            $warnings[] = [
                'type' => 'periods_created',
                'count' => count($plan['periods_to_create']),
                'message' => count($plan['periods_to_create']).' período(s) no existen en el destino y se crearán.',
            ];
        }

        if ($plan['collisions'] !== []) {
            $warnings[] = [
                'type' => 'unique_collisions',
                'count' => count($plan['collisions']),
                'message' => count($plan['collisions']).' slot(s) colisionarían con los índices únicos y se descartarán.',
            ];
        }

        if ($plan['deduplicated'] > 0) {
            $warnings[] = [
                'type' => 'deduplicated',
                'count' => $plan['deduplicated'],
                'message' => $plan['deduplicated'].' slot(s) duplicados exactos fueron deduplicados.',
            ];
        }

        // Drift en campos desnormalizados (§5.3): prevalece la lección.
        $drift = $this->denormalizedDrift($payload, $resolved);
        if ($drift > 0) {
            $warnings[] = [
                'type' => 'denormalized_drift',
                'count' => $drift,
                'message' => "{$drift} slot(s) traen profesor/sección/grupo distintos a su Pevaluacion; prevalece la Pevaluacion.",
            ];
        }

        return $warnings;
    }

    /**
     * Cuenta los slots cuyo `profesor_id`/`seccion_id`/`grupo_estable_id` del
     * snapshot difiere del derivado de la pevaluación resuelta (§5.3).
     *
     * @param  array<int, array<string, mixed>>  $resolved
     */
    private function denormalizedDrift(array $payload, array $resolved): int
    {
        $slots = is_array($payload['slots'] ?? null) ? $payload['slots'] : [];
        $drift = 0;

        foreach ($slots as $slot) {
            if (! is_array($slot)) {
                continue;
            }

            $lesson = $resolved[(int) ($slot['pevaluacion_id'] ?? 0)] ?? null;
            if (! $lesson) {
                continue;
            }

            $snapshotSeccion = (int) ($slot['seccion_id'] ?? 0);
            if ($snapshotSeccion > 0 && $snapshotSeccion !== (int) $lesson['seccion_id']) {
                $drift++;

                continue;
            }

            $snapshotTeacher = (int) ($slot['profesor_id'] ?? 0);
            if ($snapshotTeacher > 0 && $snapshotTeacher !== (int) $lesson['profesor_id']) {
                $drift++;

                continue;
            }

            $snapshotGroup = $slot['grupo_estable_id'] ?? null;
            $derivedGroup = $lesson['grupo_estable_id'] ?? null;
            if ($snapshotGroup !== null && (int) $snapshotGroup !== (int) ($derivedGroup ?? 0)) {
                $drift++;
            }
        }

        return $drift;
    }

    /**
     * IDs de aulas referenciadas que existen.
     *
     * @param  array<int, mixed>  $slots
     * @return array<int, bool>
     */
    private function validRoomIds(array $slots): array
    {
        $ids = [];
        foreach ($slots as $slot) {
            if (! is_array($slot)) {
                continue;
            }
            $roomId = $slot['room_id'] ?? null;
            if ($roomId !== null && $roomId !== '') {
                $ids[(int) $roomId] = true;
            }
        }

        if ($ids === []) {
            return [];
        }

        return DB::table('timetable_rooms')
            ->whereIn('id', array_keys($ids))
            ->pluck('id')
            ->mapWithKeys(fn ($id): array => [(int) $id => true])
            ->all();
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, bool>
     */
    private function validProfesorIds(array $rows): array
    {
        $ids = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $id = (int) ($row['profesor_id'] ?? 0);
                if ($id > 0) {
                    $ids[$id] = true;
                }
            }
        }

        if ($ids === []) {
            return [];
        }

        return Profesor::query()
            ->whereIn('id', array_keys($ids))
            ->pluck('id')
            ->mapWithKeys(fn ($id): array => [(int) $id => true])
            ->all();
    }

    /** Resuelve la columna "turno" a un shift_id (acepta id numérico o código M/T). */
    private function resolveShiftId(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $code = strtoupper(trim((string) $value));

        return (int) (TimetableShift::query()->where('code', $code)->value('id') ?? 0);
    }

    private function defaultShiftId(): int
    {
        return (int) (TimetableShift::query()->orderBy('id')->value('id') ?? 0);
    }

    /** Recorta a HH:MM para el snapshot (estable entre entornos). */
    private function clock(string $time): string
    {
        if ($time === '') {
            return '';
        }

        return substr($time, 0, 5);
    }

    /** Normaliza HH:MM a un valor TIME aceptable por MySQL. */
    private function normalizeTime(mixed $time): ?string
    {
        if (! is_string($time) || trim($time) === '') {
            return null;
        }

        $time = trim($time);

        return strlen($time) === 5 ? $time.':00' : $time;
    }

    /** ksort recursivo: los mapas se ordenan, los arrays-lista conservan su orden (§7). */
    private function sortRecursive(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $isList = array_is_list($value);
        foreach ($value as $key => $item) {
            $value[$key] = $this->sortRecursive($item);
        }

        if (! $isList) {
            ksort($value);
        }

        return $value;
    }
}
