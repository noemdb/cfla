<?php

namespace App\Livewire\Coordinacion\Timetable;

use App\Imports\TimetableLessonsImport;
use App\Jobs\Timetable\GenerateTimetableJob;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetableLessonDraftTrait;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\app\Timetable\TimetableTeacherAvailability;
use App\Services\Timetable\TimetableRoomEligibilityService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use WireUi\Traits\WireUiActions;

/**
 * SPEC-TIMETABLE-001 §5 — Wizard de horario (pasos 1 a 5).
 *
 * Un solo componente Livewire con `currentStep`, layout coordinacion, sin
 * clases de pasos separadas (patrón LessonWizard de LMS).
 */
class TimetableWizard extends Component
{
    use TimetableLessonDraftTrait, WireUiActions, WithFileUploads;

    public int $currentStep = 1;

    protected array $validationAttributes = [
        'calendarId' => 'calendario',
        'shiftId' => 'turno',
    ];

    protected array $messages = [
        'calendarId.required' => 'Debes seleccionar un calendario.',
        'shiftId.required' => 'Debes seleccionar un turno.',
        'shiftId.integer' => 'El turno seleccionado no es válido.',
        'shiftId.gt' => 'Debes seleccionar un turno antes de generar los períodos.',
    ];

    // ─── Paso 1 · Calendario ──────────────────────────────────
    #[Url(as: 'calendarId', history: true)]
    public $calendarId = null;

    /** Abre/cierra el modal de creación de calendario (Paso 1). */
    public bool $showCreateCalendarForm = false;

    /** Abre/cierra el modal de edición del calendario seleccionado. */
    public bool $showEditCalendarForm = false;

    public array $calendars = [];

    public $lapsoId = null;

    public $pescolarId = null;

    public $pestudioId = null;

    public string $calendarName = '';

    public int $periodMinutes = 60;

    public int $maxSubjectsPerPeriod = 2;

    public string $strategy = TimetableCalendar::STRATEGY_OPTIMIZED;

    public int $shiftId = 0;

    public array $periods = [];

    public bool $showShiftForm = false;

    public string $shiftCode = '';

    public string $shiftName = '';

    public string $shiftStart = '07:00';

    public string $shiftEnd = '12:15';

    // ─── Paso 2 · Aulas ────────────────────────────────────────
    public array $rooms = [];

    /** Aulas agrupadas por Peducativo (proyecto educativo). */
    public array $roomsByPeducativo = [];

    /** Aulas generadas (staging) pendientes de guardar en la BD. */
    public array $pendingRooms = [];

    public string $roomCode = '';

    public string $roomName = '';

    public int $roomCapacity = 30;

    public string $roomType = 'aula';

    // Selección reactiva de sección para el alta de aula
    public $roomPestudioId = null;

    public $roomGradoId = null;

    public $roomSeccionId = null;

    public bool $showRoomSectionModal = false;

    public bool $showRoomCreateModal = false;

    // Edición de aula (dialog)
    public bool $roomEditOpen = false;

    public ?int $editingRoomId = null;

    public string $editRoomCode = '';

    public string $editRoomName = '';

    public int $editRoomCapacity = 30;

    public string $editRoomType = 'aula';

    public $editRoomSeccionId = null;

    // ─── Paso 3 · Lecciones ────────────────────────────────────
    public array $selectedPevs = [];

    public array $lessons = [];

    public bool $lessonsDirty = false;

    public ?string $lessonsSavedAt = null;

    public bool $showAddPreviewLessonModal = false;

    public ?int $addPreviewLessonPeriodId = null;

    // Pestañas pestudio → grado → sección (PLAN-ACTIVITIES-001)
    public $activePestudioId = null;

    public $activeGradoId = null;

    public $activeSeccionId = null;

    // Mejoras del paso 3 (PLAN-ACTIVITIES-001)
    public string $step3ViewMode = 'tabs'; // 'tabs' | 'flat'

    public string $step3Search = '';

    public string $step3Sort = 'asignatura'; // asignatura | profesor | blocks

    public string $step3SortDir = 'asc';

    public $bulkShiftId = null;

    public $bulkRoomType = '';

    // Importación masiva (SPEC-TIMETABLE-001g)
    public $importFile = null;

    public ?string $importMessage = null;

    public array $importErrors = [];

    public bool $importing = false;

    // ─── Paso 4 · Disponibilidad ───────────────────────────────
    public array $availability = [];

    /** Búsqueda por apellido/nombre para filtrar el select de profesores. */
    public string $searchProfesor = '';

    /** Profesor seleccionado para editar su disponibilidad puntualmente. */
    public $selectedProfesorId = null;

    // ─── Paso 5 · Generar ───────────────────────────────────────
    public ?string $generationState = null;

    public ?array $preview = null;

    public bool $busy = false;

    public bool $dryRunFirst = true;

    /** Índice de día seleccionado en la vista de bloques (tabs). Persistido por calendario. */
    public int $selectedScheduleDayIndex = 0;

    public const ROOM_TYPES = ['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'];

    public function mount(): void
    {
        $this->loadCalendars();

        if ($this->calendarId) {
            $selectedCalendar = TimetableCalendar::find((int) $this->calendarId);
            if ($selectedCalendar) {
                $this->loadCalendar($selectedCalendar);
            }
        }

        $this->reloadRooms();
    }

    public function refreshWizard(): void
    {
        $this->calendarId = null;
        $this->currentStep = 1;
        $this->calendarName = '';
        $this->pestudioId = null;
        $this->periodMinutes = 60;
        $this->maxSubjectsPerPeriod = 2;
        $this->strategy = TimetableCalendar::STRATEGY_OPTIMIZED;
        $this->shiftId = 0;
        $this->periods = [];
        $this->lessons = [];
        $this->selectedPevs = [];
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = null;
        $this->availability = [];
        $this->generationState = null;
        $this->preview = null;
        $this->activePestudioId = null;
        $this->activeGradoId = null;
        $this->activeSeccionId = null;
        $this->selectedProfesorId = null;
        $this->showCreateCalendarForm = false;
        $this->showEditCalendarForm = false;

        // Rehidrata las colecciones y aulas como en el montaje inicial. La
        // asignación anterior de calendarId se sincroniza con #[Url].
        $this->mount();
    }

    public function updatedLapsoId($value): void
    {
        // PLAN-TIMETABLE-002: al cambiar de lapso se resetea el contexto y se
        // listan los calendarios (alternativas) de ese lapso.
        $this->calendars = [];
        $this->calendarId = null;
        $this->lessons = [];
        $this->availability = [];
        $this->periods = [];
        $this->generationState = null;
        $this->preview = null;
        $this->activePestudioId = null;
        $this->activeGradoId = null;
        $this->activeSeccionId = null;

        if (! $value) {
            return;
        }

        $lapso = Lapso::find($value);
        if ($lapso) {
            $this->calendarName = 'Horario '.$lapso->name;
            $this->pescolarId = $lapso->pescolar_id;
            $this->loadCalendars();

            $active = TimetableCalendar::activeForLapso($lapso->id);
            if ($active) {
                $this->calendarId = $active->id;
                $this->loadCalendar($active);
                $this->loadAvailability();
            }
        }
    }

    public function createCalendar(): void
    {
        $this->validate([
            'lapsoId' => 'required',
            'pestudioId' => 'required|integer|exists:pestudios,id',
            'calendarName' => 'required|string|max:255',
            'periodMinutes' => 'required|integer|min:30|max:120',
            'maxSubjectsPerPeriod' => 'required|integer|min:1|max:10',
            'strategy' => 'required|string|in:optimized,legacy',
        ]);

        // PLAN-TIMETABLE-002 I-1: se permiten N borradores por lapso. Solo se
        // evitan nombres duplicados dentro del lapso (D-3, app-level).
        $nameExists = TimetableCalendar::query()
            ->forLapso($this->lapsoId)
            ->where('name', $this->calendarName)
            ->exists();

        if ($nameExists) {
            session()->flash('error', 'Ya existe un calendario con ese nombre en el lapso.');

            return;
        }

        $calendar = TimetableCalendar::create([
            'lapso_id' => $this->lapsoId,
            'pescolar_id' => $this->pescolarId ?: null,
            'pestudio_id' => (int) $this->pestudioId,
            'name' => $this->calendarName,
            'period_minutes' => $this->periodMinutes,
            'max_subjects_per_period' => $this->maxSubjectsPerPeriod,
            'strategy' => $this->strategy,
            'status' => 'draft',
            'version' => 0,
        ]);

        $this->calendarId = $calendar->id;
        $this->loadCalendar($calendar);
        $this->loadCalendars();
        $this->showCreateCalendarForm = false;
        session()->flash('message', 'Borrador creado. Ahora crea los turnos y períodos.');
    }

    /**
     * Abre el modal de edición del calendario seleccionado, recargando sus
     * datos desde BD (por si cambiaron en otra pestaña/sesión).
     */
    public function openEditCalendarForm(): void
    {
        $calendar = TimetableCalendar::find((int) $this->calendarId);
        if (! $calendar) {
            session()->flash('error', 'Selecciona un calendario para editar.');

            return;
        }

        $this->loadCalendar($calendar);
        $this->showEditCalendarForm = true;
    }

    /**
     * Edita la configuración del calendario. Lapso/pestudio son la identidad
     * del calendario (los períodos heredan el pestudio, SPEC §10.1) y no se
     * tocan en edición.
     */
    public function updateCalendar(): void
    {
        if (! $this->calendarId) {
            return;
        }

        $calendar = TimetableCalendar::find((int) $this->calendarId);
        if (! $calendar) {
            return;
        }

        $this->validate([
            'calendarName' => 'required|string|max:255',
            'periodMinutes' => 'required|integer|min:30|max:120',
            'maxSubjectsPerPeriod' => 'required|integer|min:1|max:10',
            'strategy' => 'required|string|in:optimized,legacy',
        ]);

        // D-3: nombre único dentro del lapso, excluyendo el propio calendario.
        $nameExists = TimetableCalendar::query()
            ->forLapso($calendar->lapso_id)
            ->where('name', $this->calendarName)
            ->where('id', '!=', $calendar->id)
            ->exists();

        if ($nameExists) {
            $this->addError('calendarName', 'Ya existe un calendario con ese nombre en el lapso.');

            return;
        }

        $calendar->update([
            'name' => $this->calendarName,
            'period_minutes' => $this->periodMinutes,
            'max_subjects_per_period' => $this->maxSubjectsPerPeriod,
            'strategy' => $this->strategy,
        ]);

        $this->loadCalendars();
        $this->showEditCalendarForm = false;
        session()->flash('message', 'Calendario actualizado.');
    }

    /**
     * PLAN-TIMETABLE-002 §4.5 — Cambia el calendario en edición (alternativa
     * del lapso) recargando todo el contexto del wizard.
     */
    public function selectCalendar($calendarId): void
    {
        $calendar = TimetableCalendar::find((int) $calendarId);
        if (! $calendar) {
            return;
        }

        $this->calendarId = $calendar->id;
        $this->loadCalendar($calendar);
        // Resetea la navegación de pestañas del Paso 3 (cada calendario es de un
        // pestudio; los activos previos pueden quedar obsoletos y desactivar tabs).
        $this->activePestudioId = null;
        $this->activeGradoId = null;
        $this->activeSeccionId = null;
        $this->periods = [];
        $this->generationState = null;
        $this->preview = null;

        // Restaurar día seleccionado desde la sesión si existe (persistencia por calendario)
        $sessionKey = 'timetable.selected_schedule_day_'.($this->calendarId ?? '');
        $this->selectedScheduleDayIndex = (int) session()->get($sessionKey, 0);

        $this->loadLessons();
        $this->loadAvailability();
    }

    /**
     * PLAN-TIMETABLE-002 I-4 — Activa un borrador que ya tiene horario generado
     * (archiva al activo anterior del mismo lapso).
     */
    public function activateCalendar($calendarId): void
    {
        $calendar = TimetableCalendar::find((int) $calendarId);
        if (! $calendar || $calendar->status === 'active') {
            return;
        }

        if (! $calendar->slots()->exists()) {
            session()->flash('error', 'El borrador no tiene horario generado. Ejecuta el dry-run y confirma la publicación para activarlo.');

            return;
        }

        $calendar->activate();
        $this->loadCalendars();
        if ($this->calendarId === $calendar->id) {
            $this->generationState = 'published';
            $this->preview = null;
        }
        session()->flash('message', 'Calendario «'.$calendar->name.'» activado. El activo anterior quedó archivado.');
    }

    /**
     * PLAN-TIMETABLE-002 §9 — Duplica un borrador (estructura + lecciones + slots).
     */
    public function duplicateCalendar($calendarId): void
    {
        $source = TimetableCalendar::find((int) $calendarId);
        if (! $source || $source->status !== TimetableCalendar::STATUS_DRAFT) {
            session()->flash('error', 'Solo se pueden duplicar calendarios en estado borrador.');

            return;
        }

        $copy = DB::transaction(function () use ($source) {
            $copy = TimetableCalendar::create([
                'lapso_id' => $source->lapso_id,
                'pescolar_id' => $source->pescolar_id,
                'pestudio_id' => $source->pestudio_id,
                'name' => $source->name.' (copia)',
                'period_minutes' => $source->period_minutes,
                'max_subjects_per_period' => $source->max_subjects_per_period,
                'strategy' => $source->strategy ?: TimetableCalendar::STRATEGY_OPTIMIZED,
                'status' => TimetableCalendar::STATUS_DRAFT,
                'version' => 0,
            ]);

            foreach ($source->periods()->get() as $period) {
                TimetablePeriod::create([
                    'calendar_id' => $copy->id, 'shift_id' => $period->shift_id,
                    'day_of_week' => $period->day_of_week, 'order_in_day' => $period->order_in_day,
                    'start_time' => $period->start_time, 'end_time' => $period->end_time, 'is_break' => $period->is_break,
                ]);
            }

            foreach ($source->lessons()->with('slots')->get() as $lesson) {
                $newLesson = TimetableLesson::create([
                    'calendar_id' => $copy->id, 'pevaluacion_id' => $lesson->pevaluacion_id, 'shift_id' => $lesson->shift_id,
                    'weekly_blocks_t' => $lesson->weekly_blocks_t, 'weekly_blocks_p' => $lesson->weekly_blocks_p,
                    'room_type_required' => $lesson->room_type_required, 'is_half_group' => $lesson->is_half_group,
                    'priority' => $lesson->priority, 'locked' => $lesson->locked,
                ]);
                foreach ($lesson->slots as $slot) {
                    TimetableSlot::create([
                        'calendar_id' => $copy->id, 'lesson_id' => $newLesson->id, 'period_id' => $slot->period_id,
                        'profesor_id' => $slot->profesor_id, 'seccion_id' => $slot->seccion_id,
                        'grupo_estable_id' => $slot->grupo_estable_id, 'room_id' => $slot->room_id,
                        'is_half_group' => $slot->is_half_group,
                        'locked' => $slot->locked, 'is_manual_override' => $slot->is_manual_override,
                    ]);
                }
            }

            return $copy;
        });

        $this->loadCalendars();
        session()->flash('message', 'Calendario duplicado: «'.$copy->name.'».');
    }

    /**
     * PLAN-TIMETABLE-002 I-7 — Elimina solo borradores.
     */
    public function deleteCalendar($calendarId): void
    {
        $calendar = TimetableCalendar::find((int) $calendarId);
        if (! $calendar || ! $calendar->deleteDraft()) {
            session()->flash('error', 'Solo se pueden eliminar calendarios en estado borrador.');

            return;
        }

        if ($this->calendarId === $calendar->id) {
            $this->calendarId = null;
            $this->lessons = [];
            $this->availability = [];
            $this->periods = [];
            $this->generationState = null;
            $this->preview = null;
        }
        $this->loadCalendars();
        session()->flash('message', 'Borrador eliminado.');
    }

    public function createShift(): void
    {
        $this->validate([
            'shiftCode' => 'required|string|max:2',
            'shiftName' => 'required|string|max:60',
            'shiftStart' => 'required|date_format:H:i',
            'shiftEnd' => 'required|date_format:H:i',
        ]);

        if (! $this->calendarId) {
            session()->flash('error', 'Primero crea el calendario.');

            return;
        }

        // El turno es un catálogo compartido (code único): se reutiliza si ya
        // existe para evitar colisión con el índice único (timetable_shifts).
        $shift = TimetableShift::firstOrCreate(
            ['code' => $this->shiftCode],
            ['name' => $this->shiftName, 'start_time' => $this->shiftStart, 'end_time' => $this->shiftEnd],
        );

        $this->shiftId = $shift->id;
        $this->showShiftForm = false;
        $this->shiftCode = '';
        $this->shiftName = '';
        session()->flash('message', 'Turno creado. Define los períodos de la semana.');
    }

    public function generatePeriods(): void
    {
        $this->validate([
            'calendarId' => 'required',
            'shiftId' => 'required|integer|gt:0',
        ]);

        if (TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->where('shift_id', $this->shiftId)
            ->exists()) {
            session()->flash('error', 'Los períodos de este turno ya están generados.');

            return;
        }

        // Períodos por PESTUDIO: los bloques de cada plan provienen de la
        // estructura del legacy (tiempos exactos + recreos) para el turno elegido.
        $shift = TimetableShift::find($this->shiftId);
        $shiftCode = $shift?->code;

        $periods = [];
        foreach ($this->calendarPestudios() as $pid => $pestudioName) {
            $nivel = $this->levelForPestudio($pestudioName);
            foreach ($this->estructuraFranjas($nivel, $shiftCode) as $i => $franja) {
                $periods[] = [
                    'pestudio_id' => $pid,
                    'pestudio' => $pestudioName,
                    'order' => $i + 1,
                    'start' => $franja[0],
                    'end' => $franja[1],
                    'is_break' => $franja[2],
                    'label' => $pestudioName.' · bloque '.($i + 1).' · '.$this->fmtMin($franja[0]).'–'.$this->fmtMin($franja[1]).($franja[2] ? ' (recreo)' : ''),
                ];
            }
        }

        if ($periods === []) {
            session()->flash('error', 'El turno seleccionado no tiene bloques en la estructura del legacy.');

            return;
        }

        $this->periods = $periods;
    }

    public function savePeriods(): void
    {
        $this->validate([
            'calendarId' => 'required',
            'shiftId' => 'required|integer|gt:0',
        ]);

        if (TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->where('shift_id', $this->shiftId)
            ->exists()) {
            session()->flash('error', 'Los períodos de este turno ya existen.');

            return;
        }

        $this->persistPeriods();
        $this->periods = [];
        session()->flash('message', 'Períodos creados por plan de estudio (Lun–Vie).');
        $this->goToStep(2);
    }

    /** Regenera los períodos de un turno ya generados (los recrea desde el pestudio). */
    public function regeneratePeriods(): void
    {
        $this->validate([
            'calendarId' => 'required',
            'shiftId' => 'required|integer|gt:0',
        ]);

        if (! TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->where('shift_id', $this->shiftId)
            ->exists()) {
            session()->flash('error', 'No hay períodos que regenerar en este turno.');

            return;
        }

        TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->where('shift_id', $this->shiftId)
            ->delete();

        $this->periods = [];
        $this->generatePeriods();
        if ($this->periods === []) {
            session()->flash('error', 'El turno no tiene bloques en la estructura del legacy.');

            return;
        }
        $this->persistPeriods();
        $this->periods = [];
        session()->flash('message', 'Períodos regenerados para el turno (Lun–Vie).');
    }

    /** Persiste $this->periods (por pestudio) como períodos del calendario. */
    private function persistPeriods(): void
    {
        DB::transaction(function () {
            foreach ($this->periods as $p) {
                foreach (range(1, 5) as $day) {
                    TimetablePeriod::create([
                        'calendar_id' => $this->calendarId,
                        'shift_id' => $this->shiftId,
                        'day_of_week' => $day,
                        'order_in_day' => $p['order'],
                        'start_time' => $this->fmtMin($p['start']),
                        'end_time' => $this->fmtMin($p['end']),
                        'is_break' => $p['is_break'],
                    ]);
                }
            }
        });
    }

    /** Pestudios del calendario: el del calendario (si tiene) o los de sus pevs. */
    private function calendarPestudios(): array
    {
        $calendar = TimetableCalendar::find($this->calendarId);
        if (! $calendar) {
            return [];
        }

        if ($calendar->pestudio_id) {
            $pes = $calendar->pestudio;

            return $pes ? [$pes->id => $pes->name] : [];
        }

        return Pevaluacion::query()
            ->where('lapso_id', $calendar->lapso_id)
            ->with('seccion.grado.pestudio')
            ->get()
            ->map(fn ($pv) => $pv->seccion?->grado?->pestudio)
            ->filter()
            ->unique('id')
            ->pluck('name', 'id')
            ->all();
    }

    /** Nivel de la estructura del legacy para un pestudio. */
    private function levelForPestudio(string $pestudioName): string
    {
        $n = mb_strtoupper($pestudioName);
        if (str_contains($n, 'PRIMARIA') || str_contains($n, 'INICIAL')) {
            return 'PRIMARIA';
        }

        return 'MEDIA GENERAL';
    }

    /** Franjas (order) del nivel+turno desde la estructura del legacy. */
    private function estructuraFranjas(string $nivel, ?string $shiftCode): array
    {
        return $this->legacyEstructura()[$nivel][$shiftCode] ?? [];
    }

    /** @return array<string, array<string, list<array{0:int,1:int,2:bool}>>> nivel => turno => franjas */
    private function legacyEstructura(): array
    {
        $path = base_path('blueprint/school-timetable/legacy/csv/legacy_estructura_horaria.csv');
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
                $this->minOfDay($r['hora_inicio']),
                $this->minOfDay($r['hora_fin']),
                (string) ($r['es_receso'] ?? '') === 'true',
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

    // ─── Paso 2 · Aulas ────────────────────────────────────────

    public function saveRoom(): void
    {
        $this->validate([
            'roomCode' => 'required|string|max:20',
            'roomName' => 'required|string|max:80',
            'roomCapacity' => 'required|integer|min:1',
            'roomType' => 'required|in:'.implode(',', self::ROOM_TYPES),
            'roomSeccionId' => 'nullable|integer|exists:seccions,id',
        ]);

        if (TimetableRoom::query()
            ->whereRaw('LOWER(code) = ?', [mb_strtolower(trim($this->roomCode))])
            ->exists()) {
            $this->addError('roomCode', 'Ya existe un aula con ese código.');

            return;
        }

        TimetableRoom::create([
            'code' => trim($this->roomCode),
            'name' => $this->roomName,
            'capacity' => $this->roomCapacity,
            'type' => $this->roomType,
            'seccion_id' => $this->roomSeccionId ? (int) $this->roomSeccionId : null,
            'status_active' => true,
        ]);

        $this->roomCode = '';
        $this->roomName = '';
        $this->roomCapacity = 30;
        $this->roomType = 'aula';
        $this->roomPestudioId = null;
        $this->roomGradoId = null;
        $this->roomSeccionId = null;
        $this->closeRoomCreateModal();
        $this->reloadRooms();
    }

    public function updatedRoomPestudioId($value): void
    {
        $this->roomGradoId = null;
        $this->roomSeccionId = null;
    }

    public function updatedRoomGradoId($value): void
    {
        $this->roomSeccionId = null;
    }

    public function openRoomCreateModal(): void
    {
        $this->showRoomCreateModal = true;
    }

    public function closeRoomCreateModal(): void
    {
        $this->showRoomCreateModal = false;
    }

    public function openRoomSectionModal(): void
    {
        $this->showRoomSectionModal = true;
    }

    public function closeRoomSectionModal(): void
    {
        $this->showRoomSectionModal = false;
    }

    /**
     * Etiqueta legible del vínculo Pestudio → Grado → Sección seleccionado.
     */
    public function getRoomSectionLinkLabelProperty(): ?string
    {
        if (! $this->roomSeccionId) {
            return null;
        }

        $seccion = \App\Models\app\Academy\Seccion::query()
            ->with('grado.pestudio')
            ->find($this->roomSeccionId);

        if (! $seccion) {
            return null;
        }

        $pestudio = $seccion->grado?->pestudio;

        return trim(implode(' · ', array_filter([
            $pestudio?->code.' '.$pestudio?->name,
            $seccion->grado?->name,
            'Sección '.$seccion->name,
        ])));
    }

    /**
     * Quita el vínculo de sección seleccionado.
     */
    public function clearRoomSection(): void
    {
        $this->roomSeccionId = null;
        $this->roomGradoId = null;
        $this->roomPestudioId = null;
    }

    /**
     * Aulas ya vinculadas a la sección elegida en el modal de alta
     * (aviso informativo no bloqueante: una sección puede tener varias aulas).
     */
    public function getRoomSectionLinkedRoomsProperty()
    {
        if (! $this->roomSeccionId) {
            return collect();
        }

        return TimetableRoom::query()
            ->where('seccion_id', (int) $this->roomSeccionId)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'type']);
    }

    /**
     * Otras aulas (excluyendo la que se edita) ya vinculadas a la sección
     * elegida en el dialog de edición — mismo aviso no bloqueante.
     */
    public function getEditRoomSectionLinkedRoomsProperty()
    {
        if (! $this->editRoomSeccionId) {
            return collect();
        }

        return TimetableRoom::query()
            ->where('seccion_id', (int) $this->editRoomSeccionId)
            ->when($this->editingRoomId, fn ($query, $id) => $query->where('id', '!=', $id))
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'type']);
    }

    /**
     * Pestudios activos para la cascada de selección de sección.
     */
    private function loadRoomPestudios()
    {
        return \App\Models\app\Academy\Pestudio::query()
            ->where('status_active', 'true')
            ->orderBy('code')
            ->get();
    }

    /**
     * Grados del pestudio seleccionado (activos).
     */
    private function loadRoomGrados()
    {
        if (! $this->roomPestudioId) {
            return collect();
        }

        return \App\Models\app\Academy\Grado::query()
            ->where('pestudio_id', $this->roomPestudioId)
            ->where('status_active', 'true')
            ->orderBy('code_sm')
            ->get();
    }

    /**
     * Secciones del grado seleccionado (activas).
     */
    private function loadRoomSecciones()
    {
        if (! $this->roomGradoId) {
            return collect();
        }

        return \App\Models\app\Academy\Seccion::query()
            ->where('grado_id', $this->roomGradoId)
            ->where('status_active', 'true')
            ->orderBy('name')
            ->get();
    }

    /**
     * Todas las secciones activas con etiqueta legible, para el selector del
     * dialog de edición (Plan · Grado · Sección).
     */
    private function loadAllRoomSecciones(): array
    {
        return \App\Models\app\Academy\Seccion::query()
            ->with('grado.pestudio')
            ->where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->map(fn ($seccion) => [
                'id' => $seccion->id,
                'label' => trim(implode(' · ', array_filter([
                    $seccion->grado?->pestudio?->code.' '.$seccion->grado?->pestudio?->name,
                    $seccion->grado?->name,
                    'Sección '.$seccion->name,
                ]))),
            ])
            ->all();
    }

    public function deleteRoom($roomId): void
    {
        try {
            // Desasignar slots que referencian este aula antes de eliminar (FK).
            \App\Models\app\Timetable\TimetableSlot::query()
                ->where('room_id', $roomId)
                ->update(['room_id' => null]);

            TimetableRoom::query()->where('id', $roomId)->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            $this->notification()->error('Aula en uso', 'No se pudo eliminar el aula: '.$e->getMessage());

            return;
        }
        $this->reloadRooms();
    }

    /**
     * Abre el dialog para editar un aula registrada.
     */
    public function editRoom(int $roomId): void
    {
        $room = TimetableRoom::query()->find($roomId);
        if (! $room) {
            return;
        }

        $this->editingRoomId = $room->id;
        $this->editRoomCode = $room->code;
        $this->editRoomName = $room->name;
        $this->editRoomCapacity = (int) ($room->capacity ?? 30);
        $this->editRoomType = $room->type;
        $this->editRoomSeccionId = $room->seccion_id ? (string) $room->seccion_id : null;
        $this->roomEditOpen = true;
    }

    /**
     * Actualiza un aula registrada desde el dialog.
     */
    public function updateRoom(): void
    {
        $this->validate([
            'editRoomCode' => 'required|string|max:20|unique:timetable_rooms,code,'.$this->editingRoomId,
            'editRoomName' => 'required|string|max:80',
            'editRoomCapacity' => 'required|integer|min:1',
            'editRoomType' => 'required|in:'.implode(',', self::ROOM_TYPES),
            'editRoomSeccionId' => 'nullable|integer|exists:seccions,id',
        ]);

        $room = TimetableRoom::query()->find($this->editingRoomId);
        if (! $room) {
            return;
        }

        $room->update([
            'code' => $this->editRoomCode,
            'name' => $this->editRoomName,
            'capacity' => $this->editRoomCapacity,
            'type' => $this->editRoomType,
            'seccion_id' => $this->editRoomSeccionId ? (int) $this->editRoomSeccionId : null,
        ]);

        $this->notification()->success('Aula actualizada', "Se actualizó el aula «{$room->name}».");
        $this->closeRoomEdit();
        $this->reloadRooms();
    }

    public function closeRoomEdit(): void
    {
        $this->roomEditOpen = false;
        $this->editingRoomId = null;
        $this->editRoomCode = '';
        $this->editRoomName = '';
        $this->editRoomCapacity = 30;
        $this->editRoomType = 'aula';
        $this->editRoomSeccionId = null;
    }

    /**
     * Muestra el dialog de confirmación antes de registrar las aulas.
     */
    public function confirmBulkCreateRooms(): void
    {
        if (! $this->calendarId) {
            $this->notification()->warning('Calendario requerido', 'Crea el calendario primero.');

            return;
        }

        $this->dialog()->confirm([
            'title' => 'Registrar aulas por grado/sección',
            'description' => 'Se registrará un aula/salón/ambiente por cada grado/sección de los pestudios activos, con nombre asociado al grado y la sección. Las secciones que ya tienen aula se omitirán.',
            'icon' => 'question',
            'accept' => [
                'label' => 'Registrar',
                'method' => 'bulkCreateRooms',
                'color' => 'primary',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    /**
     * Genera (staging) un aula por cada grado/sección de los pestudios activos,
     * con nombre asociado al grado y la sección (ej. "Aula 1er Grado A"). No
     * persiste: las aulas quedan pendientes en `pendingRooms` hasta pulsar
     * "Guardar todas". Omite las secciones que ya tienen aula asociada
     * (por seccion_id): el modo bulk no duplica vínculos existentes.
     */
    public function bulkCreateRooms(): void
    {
        if (! $this->calendarId) {
            $this->notification()->warning('Calendario requerido', 'Crea el calendario primero.');

            return;
        }

        $calendar = TimetableCalendar::find($this->calendarId);
        $pestudioId = $calendar?->pestudio_id;

        $seccions = \App\Models\app\Academy\Seccion::query()
            ->where('seccions.status_active', 'true')
            ->when($pestudioId, fn ($q, $id) => $q->whereHas('grado', fn ($grade) => $grade
                ->where('grados.status_active', 'true')
                ->where('grados.pestudio_id', $id)
                ->whereHas('pestudio', fn ($p) => $p->where('pestudios.status_active', 'true'))))
            ->when(! $pestudioId, fn ($q) => $q->whereHas('grado', fn ($grade) => $grade
                ->where('grados.status_active', 'true')
                ->whereHas('pestudio', fn ($p) => $p->where('pestudios.status_active', 'true'))))
            ->with('grado', 'grado.pestudio.peducativo')
            ->get()
            ->sortBy(fn ($s) => ($s->grado?->code_sm ?? '').'-'.$s->name)
            ->values();

        if ($seccions->isEmpty()) {
            $this->notification()->error('Sin secciones', 'No hay grados/secciones activas en los pestudios.');

            return;
        }

        $generated = [];
        $skipped = 0;

        foreach ($seccions as $seccion) {
            // El modo bulk no duplica vínculos: se omite si la sección ya
            // tiene aula asociada (por seccion_id). No se deduce por nombre
            // porque dos secciones de pestudios distintos pueden compartir el
            // mismo nombre de grado+sección.
            if (TimetableRoom::query()->where('seccion_id', $seccion->id)->exists()) {
                $skipped++;

                continue;
            }

            $peducativo = $seccion->grado?->pestudio?->peducativo;

            $generated[] = [
                'id' => 'tmp-'.$seccion->id,
                'code' => $this->roomCodeForSection($seccion),
                'name' => $this->roomNameForSection($seccion),
                'capacity' => $seccion->amount_student ?: null,
                'type' => 'aula',
                'seccion_id' => $seccion->id,
                'peducativo_id' => $peducativo ? (int) $peducativo->id : null,
                'peducativo_name' => $peducativo ? $peducativo->name : 'General',
            ];
        }

        $this->pendingRooms = $generated;
        $this->rebuildRoomGroups();

        $message = count($generated).' aula(s) generada(s)'.($skipped ? ", {$skipped} ya existían." : '.');
        $this->notification()->success('Aulas generadas', $message.' Revisa y guarda.');
    }

    /**
     * Quita una aula generada (pendiente) de la lista sin guardar.
     */
    public function removePendingRoom(string $roomId): void
    {
        $this->pendingRooms = array_values(array_filter(
            $this->pendingRooms,
            fn ($room) => ($room['id'] ?? null) !== $roomId,
        ));

        $this->rebuildRoomGroups();
    }

    /**
     * Persiste todas las aulas generadas (staging) en la BD.
     */
    public function saveAllRooms(): void
    {
        if ($this->pendingRooms === []) {
            $this->notification()->info('Sin cambios', 'No hay aulas pendientes por guardar.');

            return;
        }

        $created = 0;
        $errors = [];

        foreach ($this->pendingRooms as $room) {
            try {
                TimetableRoom::create([
                    'code' => $room['code'],
                    'name' => $room['name'],
                    'capacity' => $room['capacity'],
                    'type' => $room['type'],
                    'seccion_id' => $room['seccion_id'],
                    'status_active' => true,
                ]);
                $created++;
            } catch (\Throwable $e) {
                $errors[] = "No se pudo guardar «{$room['name']}»: {$e->getMessage()}";
            }
        }

        $this->pendingRooms = [];
        $this->reloadRooms();

        $message = "{$created} aula(s) guardada(s).";
        if ($errors) {
            $message .= ' '.count($errors).' con error.';
            $this->notification()->error('Guardar aulas', $message);
        } else {
            $this->notification()->success('Guardar aulas', $message);
        }
    }

    /**
     * Muestra el dialog de confirmación antes de eliminar todas las aulas.
     */
    public function confirmDeleteAllRooms(): void
    {
        $count = TimetableRoom::query()->count();

        if ($count === 0 && $this->pendingRooms === []) {
            $this->notification()->info('Sin aulas', 'No hay aulas registradas para eliminar.');

            return;
        }

        $this->dialog()->confirm([
            'title' => 'Eliminar todas las aulas',
            'description' => 'Se eliminarán permanentemente todas las aulas registradas'.($this->pendingRooms ? ' (incluidas las pendientes)' : '').'. Esta acción no se puede deshacer.',
            'icon' => 'error',
            'accept' => [
                'label' => 'Eliminar todas',
                'method' => 'deleteAllRooms',
                'color' => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    /**
     * Elimina todas las aulas registradas (y las pendientes de guardar).
     */
    public function deleteAllRooms(): void
    {
        try {
            // Los slots pueden referenciar aulas (FK room_id): se desasignan
            // antes de eliminar para no chocar con la integridad referencial.
            \App\Models\app\Timetable\TimetableSlot::query()->whereNotNull('room_id')->update(['room_id' => null]);

            TimetableRoom::query()->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            $this->notification()->error('Aulas en uso', 'No se pudieron eliminar las aulas: '.$e->getMessage());

            return;
        }

        $this->pendingRooms = [];
        $this->reloadRooms();

        $this->notification()->success('Aulas eliminadas', 'Se eliminaron todas las aulas registradas (los slots quedaron sin aula asignada).');
    }

    /**
     * Nombre del aula asociado al grado y la sección.
     */
    private function roomNameForSection(\App\Models\app\Academy\Seccion $seccion): string
    {
        $gradoName = $seccion->grado?->name ?? 'Grado';

        return "Aula {$gradoName} {$seccion->name}";
    }

    /**
     * Código único del aula: slug legible del grado+sección; si colisiona con
     * un código existente, cae a un código basado en el id de la sección.
     */
    private function roomCodeForSection(\App\Models\app\Academy\Seccion $seccion): string
    {
        $gradoName = $seccion->grado?->name ?? 'Grado';
        $slug = substr(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($gradoName.' '.$seccion->name, '-')), 0, 18);

        if (! TimetableRoom::query()->where('code', $slug)->exists()) {
            return $slug;
        }

        return 'AULA-'.str_pad((string) $seccion->id, 3, '0', STR_PAD_LEFT);
    }

    private function reloadRooms(): void
    {
        $rooms = TimetableRoom::query()
            ->active()
            ->with(['seccion.grado.pestudio.peducativo'])
            ->when($this->calendarId, function ($query) {
                $calendar = TimetableCalendar::find($this->calendarId);
                if (! $calendar || ! $calendar->pestudio_id) {
                    return;
                }

                $query->where(function ($q) use ($calendar) {
                    $q->whereNull('seccion_id')
                        ->orWhereHas('seccion.grado', fn ($grado) => $grado->where('pestudio_id', $calendar->pestudio_id))
                        ->orWhereDoesntHave('seccion.grado.pestudio');
                });
            })
            ->orderBy('code')
            ->get();

        $this->rooms = $rooms->map(function (TimetableRoom $r) {
            $peducativo = $r->seccion?->grado?->pestudio?->peducativo;

            return array_merge($r->toArray(), [
                'peducativo_id' => $peducativo ? (int) $peducativo->id : null,
                'peducativo_name' => $peducativo ? $peducativo->name : 'General',
            ]);
        })->values()->all();

        $this->rebuildRoomGroups();
    }

    /**
     * Construye la agrupación por Peducativo combinando las aulas persistidas
     * ($this->rooms) y las generadas pendientes ($this->pendingRooms).
     */
    private function rebuildRoomGroups(): void
    {
        $groups = [];

        foreach ($this->rooms as $room) {
            $key = $room['peducativo_id'] ? (int) $room['peducativo_id'] : 'general';
            $groups[$key]['name'] = $room['peducativo_name'] ?? 'General';
            $groups[$key]['rooms'][] = $room;
        }

        foreach ($this->pendingRooms as $room) {
            $key = ! empty($room['peducativo_id']) ? (int) $room['peducativo_id'] : 'general';
            $groups[$key]['name'] = $room['peducativo_name'] ?? 'General';
            $groups[$key]['rooms'][] = $room + ['pending' => true];
        }

        $this->roomsByPeducativo = collect($groups)
            ->map(fn ($g, $key) => [
                'id' => $key,
                'name' => $g['name'],
                'rooms' => array_values($g['rooms']),
            ])
            ->values()
            ->all();
    }

    // ─── Paso 3 · Lecciones ────────────────────────────────────

    public function updatedSelectedPevs(): void
    {
        $this->lessonsDirty = true;
        $this->loadLessons();
    }

    public function updatedLessons(): void
    {
        $this->lessonsDirty = true;
    }

    /**
     * Refreshes the academic load behind the selected lessons. The lesson
     * settings remain intact while the current pevaluacion teacher/name are
     * rebuilt from the academic module.
     */
    public function syncAcademicLoad(): void
    {
        if (! $this->calendarId) {
            $this->notification()->warning(
                'Calendario requerido',
                'Selecciona un calendario antes de sincronizar la carga académica.',
            );

            return;
        }

        $availableIds = $this->allPevaluaciones()->pluck('id')->map(fn ($id) => (int) $id);
        $selectedIds = collect($this->selectedPevIds())
            ->filter(fn ($id) => $availableIds->contains((int) $id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $this->selectedPevs = array_fill_keys($selectedIds, true);
        $this->loadLessons();
        $this->notification()->success(
            'Carga académica sincronizada',
            count($selectedIds).' lección(es) actualizada(s) con la asignatura, sección y profesor vigentes.',
        );
    }

    public function updatedActivePestudioId($value): void
    {
        $this->activeGradoId = null;
        $this->activeSeccionId = null;
    }

    public function updatedActiveGradoId($value): void
    {
        $this->activeSeccionId = null;
    }

    public function setStep3ViewMode($mode): void
    {
        $this->step3ViewMode = $mode === 'flat' ? 'flat' : 'tabs';
    }

    /** Establece el día seleccionado (índice) en la vista de bloques y lo persiste por calendario. */
    public function setSelectedScheduleDay($index): void
    {
        $this->selectedScheduleDayIndex = (int) $index;

        if ($this->calendarId) {
            $sessionKey = 'timetable.selected_schedule_day_'.($this->calendarId ?? '');
            session()->put($sessionKey, $this->selectedScheduleDayIndex);
        }
    }

    /**
     * Marca/desmarca todas las lecciones visibles (pestaña activa o modo plano).
     */
    public function toggleSelectAll(): void
    {
        $visible = $this->visiblePevaluaciones();

        if ($visible->isEmpty()) {
            return;
        }

        $allSelected = $visible->every(fn ($pev) => ! empty($this->selectedPevs[$pev->id]));

        foreach ($visible as $pev) {
            if ($allSelected) {
                unset($this->selectedPevs[$pev->id]);
            } else {
                $this->selectedPevs[$pev->id] = true;
            }
        }

        $this->loadLessons();
    }

    /**
     * Aplica el turno seleccionado (bulk) a todas las lecciones cargadas.
     */
    public function bulkAssignShift(): void
    {
        if (! $this->bulkShiftId) {
            return;
        }

        foreach ($this->lessons as $pevId => &$lesson) {
            $lesson['shift_id'] = (int) $this->bulkShiftId;
        }
        unset($lesson);

        $this->autosaveLessons();
        $this->notification()->success('Turno aplicado', 'Se asignó el turno a todas las lecciones.');
    }

    /**
     * Aplica el tipo de aula requerido (bulk) a todas las lecciones cargadas.
     */
    public function bulkAssignRoomType(): void
    {
        if ($this->lessons === []) {
            $this->notification()->warning(
                'Sin lecciones seleccionadas',
                'Selecciona al menos una lección antes de aplicar un tipo de aula.',
            );

            return;
        }

        if (! in_array($this->bulkRoomType, self::ROOM_TYPES, true)) {
            $this->notification()->warning(
                'Tipo de aula requerido',
                'Selecciona un tipo de aula en el selector «Aula masiva…».',
            );

            return;
        }

        $count = count($this->lessons);
        $roomType = ucfirst($this->bulkRoomType);

        foreach ($this->lessons as $pevId => &$lesson) {
            $lesson['room_type_required'] = $this->bulkRoomType;
        }
        unset($lesson);

        $this->autosaveLessons();
        $this->notification()->success(
            'Aula aplicada',
            "Se asignó «{$roomType}» a {$count} lección(es) y se guardó el borrador.",
        );
    }

    /**
     * Autoguardado (borrador): persiste las lecciones sin navegar de paso.
     */
    public function autosaveLessons(): void
    {
        if (! $this->calendarId) {
            return;
        }

        $this->persistTimetableLessonDraft((int) $this->calendarId, $this->lessons);
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = now()->format('H:i:s');
        session()->flash('message', 'Borrador de lecciones guardado.');
    }

    /**
     * Recalcula únicamente los bloques de las lecciones desde las horas
     * actuales de sus asignaturas. No modifica la tabla asignaturas.
     */
    public function recalculateLessonBlocks(): void
    {
        if (! $this->calendarId || $this->lessons === []) {
            $this->notification()->warning(
                'Sin lecciones',
                'Selecciona al menos una lección antes de recalcular los bloques.',
            );

            return;
        }

        if (! TimetableCalendar::query()->whereKey($this->calendarId)->exists()) {
            $this->notification()->error('Calendario no encontrado', 'No se pudo recalcular el borrador.');

            return;
        }

        $this->lessons = $this->recalculateTimetableLessonBlocks((int) $this->calendarId, $this->lessons);
        $this->persistTimetableLessonDraft((int) $this->calendarId, $this->lessons);
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = now()->format('H:i:s');
        $this->notification()->success(
            'Bloques recalculados',
            'Se actualizaron los bloques según las horas actuales de cada asignatura.',
        );
    }

    /**
     * Todas las pevaluaciones del lapso del calendario (grados activos), con
     * búsqueda (asignatura/profesor) y orden aplicados.
     */
    private function allPevaluaciones()
    {
        if (! $this->calendarId) {
            return collect();
        }

        $calendar = TimetableCalendar::find($this->calendarId);
        if (! $calendar) {
            return collect();
        }

        $query = Pevaluacion::query()
            ->with(['pensum.asignatura', 'seccion.grado.pestudio', 'profesor', 'grupoEstable'])
            ->where('lapso_id', $calendar->lapso_id)
            ->whereHas('seccion.grado', fn ($q) => $q->where('grados.status_active', 'true'));

        // Paso 3: solo las lecciones del plan de estudio (pestudio) del calendario.
        if ($calendar->pestudio_id) {
            $query->whereHas('seccion.grado.pestudio', fn ($q) => $q->where('pestudios.id', $calendar->pestudio_id));
        }

        if ($this->step3Search !== '') {
            $search = '%'.$this->step3Search.'%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('pensum.asignatura', fn ($a) => $a->where('asignaturas.name', 'like', $search))
                    ->orWhereHas('profesor', fn ($p) => $p->where(function ($pp) use ($search) {
                        $pp->where('profesors.lastname', 'like', $search)
                            ->orWhere('profesors.name', 'like', $search);
                    }));
            });
        }

        $pevaluaciones = $query->get();

        $sortFn = function ($pev) {
            if ($this->step3Sort === 'profesor') {
                return ($pev->profesor?->lastname ?? '').' '.($pev->profesor?->name ?? '');
            }

            if ($this->step3Sort === 'blocks') {
                return (int) ($pev->pensum?->asignatura?->hour_t_week ?? 0) + (int) ($pev->pensum?->asignatura?->hour_p_week ?? 0);
            }

            return $pev->pensum?->asignatura?->name ?? '';
        };

        return $this->step3SortDir === 'desc'
            ? $pevaluaciones->sortByDesc($sortFn)
            : $pevaluaciones->sortBy($sortFn);
    }

    /**
     * Lecciones visibles según el modo (pestaña activa o todas en plano).
     */
    private function visiblePevaluaciones()
    {
        $pevaluaciones = $this->allPevaluaciones();

        if ($this->step3ViewMode === 'flat') {
            return $pevaluaciones;
        }

        $data = $this->resolveTabData($pevaluaciones);

        return $data['tabActivePevaluaciones'];
    }

    public function loadLessons(): void
    {
        if (! $this->calendarId) {
            $this->lessons = [];
            $this->selectedPevs = [];

            return;
        }

        $calendar = TimetableCalendar::find($this->calendarId);
        if (! $calendar) {
            $this->lessons = [];
            $this->selectedPevs = [];

            return;
        }

        $savedLessons = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->get([
                'pevaluacion_id',
                'shift_id',
                'weekly_blocks_t',
                'weekly_blocks_p',
                'room_type_required',
                'is_half_group',
                'priority',
                'locked',
            ])
            ->keyBy(fn ($lesson) => (int) $lesson->pevaluacion_id);
        $periodMinutes = max(1, (int) $calendar->period_minutes);
        $ids = $this->selectedPevIds();
        $pevs = $ids === []
            ? collect()
            : Pevaluacion::query()
                ->with(['pensum.asignatura', 'seccion', 'profesor', 'grupoEstable'])
                ->where('lapso_id', $calendar->lapso_id)
                ->whereIn('id', $ids)
                ->get();

        // Preserva los valores que el usuario ya ajustó (turno/aula/prioridad/
        // locked) al reconstruir tras marcar/desmarcar otra lección.
        $existing = $this->lessons;

        $this->lessons = $pevs->mapWithKeys(function ($pev) use ($periodMinutes, $existing, $savedLessons) {
            $lesson = $this->buildLesson($pev, $periodMinutes);

            $savedLesson = $savedLessons->get((int) $pev->id);

            if (isset($existing[$pev->id])) {
                $lesson['shift_id'] = (int) ($existing[$pev->id]['shift_id'] ?? $lesson['shift_id']);
                $lesson['room_type_required'] = $existing[$pev->id]['room_type_required'] ?? null;
                $lesson['is_half_group'] = (bool) ($existing[$pev->id]['is_half_group'] ?? false);
                $lesson['weekly_blocks_t'] = (int) ($existing[$pev->id]['weekly_blocks_t'] ?? $lesson['weekly_blocks_t']);
                $lesson['weekly_blocks_p'] = (int) ($existing[$pev->id]['weekly_blocks_p'] ?? $lesson['weekly_blocks_p']);
                $lesson['priority'] = (int) ($existing[$pev->id]['priority'] ?? 0);
                $lesson['locked'] = (bool) ($existing[$pev->id]['locked'] ?? false);
            } elseif ($savedLesson) {
                $lesson['shift_id'] = (int) ($savedLesson->shift_id ?: $lesson['shift_id']);
                $lesson['room_type_required'] = $savedLesson->room_type_required ?? null;
                $lesson['is_half_group'] = (bool) ($savedLesson->is_half_group ?? false);
                $lesson['priority'] = (int) ($savedLesson->priority ?? 0);
                $lesson['locked'] = (bool) ($savedLesson->locked ?? false);
                $savedBlocksT = (int) $savedLesson->weekly_blocks_t;
                $savedBlocksP = (int) $savedLesson->weekly_blocks_p;
                if ($savedBlocksT > 0 || $savedBlocksP > 0) {
                    $lesson['weekly_blocks_t'] = $savedBlocksT;
                    $lesson['weekly_blocks_p'] = $savedBlocksP;
                }
            }

            return [$pev->id => $lesson];
        })->all();

        $this->lessonsDirty = false;
    }

    /**
     * Ids de pevaluaciones seleccionadas. Normaliza tanto el formato asociativo
     * (checkboxes: [pevId => bool]) como el de lista numérica ([pevId]).
     *
     * @return array<int, int>
     */
    private function selectedPevIds(): array
    {
        $selected = $this->selectedPevs;

        if (! is_array($selected) || $selected === []) {
            return [];
        }

        $hasBoolValues = collect($selected)->contains(fn ($v) => is_bool($v));

        if ($hasBoolValues) {
            return array_map('intval', array_keys(array_filter($selected)));
        }

        return array_values(array_filter($selected, fn ($v) => is_numeric($v) && $v > 0));
    }

    /**
     * Construye la entrada de lección para una Pevaluacion.
     *
     * @return array<string, mixed>
     */
    private function buildLesson(Pevaluacion $pev, int $periodMinutes): array
    {
        $asignatura = $pev->pensum?->asignatura;
        $grupo = $pev->grupoEstable?->name;

        return [
            'pev_id' => $pev->id,
            'name' => $asignatura?->name.' · '.($pev->seccion?->name ?? '').($grupo ? ' · '.$grupo : ''),
            'seccion_id' => $pev->seccion_id,
            'profesor_id' => $pev->profesor_id,
            'grupo_estable_id' => $pev->grupo_estable_id,
            'weekly_blocks_t' => (int) ceil(((int) ($asignatura?->hour_t_week ?? 0)) * 60 / $periodMinutes),
            'weekly_blocks_p' => (int) ceil(((int) ($asignatura?->hour_p_week ?? 0)) * 60 / $periodMinutes),
            'shift_id' => $this->defaultShiftId(),
            'room_type_required' => null,
            'is_half_group' => false,
            'priority' => 0,
            'locked' => false,
        ];
    }

    public function saveLessons(): void
    {
        if (! $this->calendarId) {
            session()->flash('error', 'Crea el calendario primero.');

            return;
        }

        if (! $this->validateLessons()) {
            return;
        }

        $this->persistLessons();
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = now()->format('H:i:s');
        session()->flash('message', count($this->lessons).' lecciones registradas.');
        $this->goToStep(4);
    }

    private function validateLessons(): bool
    {
        foreach ($this->lessons as $lessonKey => $lesson) {
            if (($lesson['weekly_blocks_t'] ?? 0) <= 0 && ($lesson['weekly_blocks_p'] ?? 0) <= 0) {
                $pevId = (int) ($lesson['pev_id'] ?? $lessonKey);
                $pev = $pevId > 0
                    ? Pevaluacion::query()
                        ->with(['pensum.asignatura', 'seccion'])
                        ->find($pevId)
                    : null;
                $subject = trim((string) ($pev?->pensum?->asignatura?->name ?? ''));
                $section = trim((string) ($pev?->seccion?->name ?? ''));
                $label = trim((string) ($lesson['name'] ?? ''));
                $label = $label !== ''
                    ? $label
                    : collect([$subject, $section])->filter()->implode(' · ');
                $label = $label !== '' ? $label : 'Lección sin identificar';
                $identifier = $pevId > 0 ? "PEV #{$pevId}" : 'sin PEV asociado';
                $blocks = sprintf(
                    'Bloques configurados: T=%d, P=%d.',
                    (int) ($lesson['weekly_blocks_t'] ?? 0),
                    (int) ($lesson['weekly_blocks_p'] ?? 0),
                );

                $this->notification()->error(
                    'Lección incompleta',
                    "«{$label}» ({$identifier}) debe tener al menos un bloque. {$blocks} "
                    .'Selecciona nuevamente la lección o configura bloques T/P mayores que cero.',
                );

                return false;
            }

        }

        return true;
    }

    private function persistLessons(): void
    {
        $this->persistTimetableLessonDraft((int) $this->calendarId, $this->lessons);
    }

    /**
     * SPEC-TIMETABLE-001g — Importación masiva de lecciones desde CSV/Excel.
     *
     * Columnas (primera fila = cabecera): pevaluacion_id (requerido), turno
     * ('M'/'T' o id de turno), bloques_t, bloques_p, aula (tipo), prioridad.
     * Resuelve cada Pevaluacion del lapso, aplica los valores del archivo y
     * falla limpiamente ante filas duplicadas o inválidas (sin persistir nada
     * hasta que el usuario confirme con saveLessons()).
     */
    public function importLessons(): void
    {
        $this->importing = true;
        $this->importErrors = [];
        $this->importMessage = null;

        try {
            $this->validate([
                'importFile' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
            ]);

            if (! $this->calendarId) {
                $this->importMessage = 'Crea el calendario primero.';

                return;
            }

            $calendar = TimetableCalendar::find($this->calendarId);
            if (! $calendar) {
                $this->importMessage = 'Calendario no encontrado.';

                return;
            }

            $import = new TimetableLessonsImport;
            Excel::import($import, $this->importFile->getRealPath());

            if ($import->rows === []) {
                $this->importMessage = 'El archivo no contiene filas de lecciones.';

                return;
            }

            $periodMinutes = max(1, (int) $calendar->period_minutes);

            // Resolución de filas: por pevaluacion_id directo, o por
            // seccion_id + asignatura (nombre) dentro del lapso del calendario.
            $pevIds = array_filter(array_map('intval', array_column($import->rows, 'pevaluacion_id')));

            $seccionIds = array_filter(array_map('intval', array_column($import->rows, 'seccion_id')));
            $asignaturas = array_values(array_filter(array_map(
                fn ($r) => trim((string) ($r['asignatura'] ?? '')),
                $import->rows,
            )));

            $pevs = Pevaluacion::query()
                ->with(['pensum.asignatura', 'seccion', 'profesor', 'grupoEstable'])
                ->where('lapso_id', $calendar->lapso_id)
                ->when($pevIds !== [], fn ($q) => $q->orWhereIn('id', $pevIds))
                ->when($seccionIds !== [] && $asignaturas !== [], function ($q) use ($seccionIds) {
                    $q->orWhereIn('seccion_id', $seccionIds);
                })
                ->get();

            // Índice (seccion_id|ASIGNATURA_NORMALIZADA) => pev para lookup.
            $pevBySectionSubject = [];
            foreach ($pevs as $p) {
                $key = ((int) $p->seccion_id).'|'.$this->normPevSubject($p->pensum?->asignatura?->name ?? '');
                $pevBySectionSubject[$key] = $p;
            }
            $pevById = $pevs->keyBy('id');

            $imported = [];
            $errors = [];
            $seen = [];

            foreach ($import->rows as $i => $row) {
                $rowNum = $i + 1;
                $pevId = (int) ($row['pevaluacion_id'] ?? 0);
                $pev = null;

                if ($pevId) {
                    $pev = $pevById->get($pevId);
                    if (! $pev) {
                        $errors[] = "Fila {$rowNum}: la lección {$pevId} no existe en el lapso.";

                        continue;
                    }
                } elseif (! empty($row['seccion_id']) && ! empty($row['asignatura'])) {
                    $key = ((int) $row['seccion_id']).'|'.$this->normPevSubject((string) $row['asignatura']);
                    $pev = $pevBySectionSubject[$key] ?? null;
                    if (! $pev) {
                        $errors[] = "Fila {$rowNum}: no hay lección de «{$row['asignatura']}» en la sección {$row['seccion_id']} del lapso.";

                        continue;
                    }
                    $pevId = (int) $pev->id;
                } else {
                    $errors[] = "Fila {$rowNum}: falta pevaluacion_id o (seccion_id + asignatura).";

                    continue;
                }

                if (isset($seen[$pevId])) {
                    $errors[] = "Fila {$rowNum}: la lección {$pevId} está duplicada en el archivo.";

                    continue;
                }
                $seen[$pevId] = true;

                $shiftId = $this->resolveShiftId($row['turno'] ?? null);
                if (! $shiftId) {
                    $errors[] = "Fila {$rowNum}: turno inválido para la lección {$pevId}.";

                    continue;
                }

                $lesson = $this->buildLesson($pev, $periodMinutes);
                $lesson['shift_id'] = $shiftId;
                $lesson['weekly_blocks_t'] = $this->blocksFromRow($row, 'bloques_t', $pev, $periodMinutes, 'hour_t_week');
                $lesson['weekly_blocks_p'] = $this->blocksFromRow($row, 'bloques_p', $pev, $periodMinutes, 'hour_p_week');
                $lesson['room_type_required'] = ! empty($row['aula']) ? (string) $row['aula'] : null;
                $lesson['priority'] = (int) ($row['prioridad'] ?? 0);

                $imported[] = $lesson;
            }

            $this->selectedPevs = array_fill_keys(array_column($imported, 'pev_id'), true);
            $this->lessons = collect($imported)->keyBy('pev_id')->all();
            $this->importErrors = $errors;

            $summary = count($imported).' lección(es) importada(s).';
            if ($errors) {
                $summary .= ' '.count($errors).' fila(s) con error (revisar la lista).';
            }
            $this->importMessage = $summary;
        } catch (\Throwable $e) {
            $this->importMessage = 'Error al importar: '.$e->getMessage();
        } finally {
            $this->importing = false;
        }
    }

    /**
     * Resuelve la columna "turno" a un shift_id: acepta el id numérico o el
     * código 'M'/'T' del turno.
     */
    private function resolveShiftId($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $code = strtoupper(trim((string) $value));
        $shift = TimetableShift::query()->where('code', $code)->first();

        return $shift?->id ?? 0;
    }

    /**
     * Bloques semanales desde la columna del archivo; si viene vacía, se
     * derivan de las horas de la asignatura (regla de redondeo del §4).
     */
    private function blocksFromRow(array $row, string $column, Pevaluacion $pev, int $periodMinutes, string $hourField): int
    {
        $value = $row[$column] ?? null;

        if ($value !== null && $value !== '' && is_numeric($value)) {
            return (int) $value;
        }

        $hours = (int) ($pev->pensum?->asignatura?->{$hourField} ?? 0);

        return (int) ceil($hours * 60 / $periodMinutes);
    }

    /**
     * Descarga la plantilla CSV de importación de lecciones.
     */
    public function downloadTemplate()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
            fputcsv($out, ['pevaluacion_id', 'seccion_id', 'asignatura', 'turno', 'bloques_t', 'bloques_p', 'aula', 'prioridad']);
            fputcsv($out, ['', '21', 'MATEMÁTICAS', 'M', '', '', 'aula', '0']);
            fclose($out);
        }, 'plantilla-lecciones.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Normaliza el nombre de una asignatura para el índice de importación. */
    private function normPevSubject(string $subject): string
    {
        $s = iconv('UTF-8', 'ASCII//TRANSLIT', $subject) ?: $subject;
        $s = preg_replace('/\s+/', ' ', trim($s)) ?? '';
        $s = str_replace(['(', ')', ','], ' ', $s);

        return mb_strtoupper(preg_replace('/\s+/', ' ', $s) ?? '');
    }

    // ─── Paso 4 · Disponibilidad ───────────────────────────────

    public function setAllAvailable(): void
    {
        if (! $this->calendarId) {
            return;
        }

        $grid = $this->availabilityGrid();

        $profesorIds = \App\Models\app\Academy\Profesor::query()
            ->whereIn('id', TimetableLesson::query()
                ->where('calendar_id', $this->calendarId)
                ->with('pevaluacion')
                ->get()
                ->pluck('pevaluacion.profesor_id')
                ->unique()
                ->values())
            ->where('status_active', 'true')
            ->pluck('id')
            ->all();

        foreach ($profesorIds as $profesorId) {
            $this->upsertAvailabilityRows($profesorId, $grid, true);
        }

        $this->loadAvailability();

        session()->flash('message', 'Disponibilidad 100% marcada para '.count($profesorIds).' profesor(es) activo(s).');
    }

    /** Al filtrar por apellido/nombre se limpia la selección previa. */
    public function updatedSearchProfesor(): void
    {
        $this->selectedProfesorId = null;
    }

    /** Hook Livewire: al cambiar el select, se carga la disponibilidad del profesor. */
    public function updatedSelectedProfesorId($value): void
    {
        $this->selectedProfesorId = $value ? (int) $value : null;
        $this->loadAvailability();
    }

    /** Marca toda la disponibilidad del profesor seleccionado (se guarda aparte). */
    public function markAllAvailable(): void
    {
        if (! $this->calendarId || ! $this->selectedProfesorId) {
            return;
        }

        $this->setGridValue($this->selectedProfesorId, true);
        session()->flash('message', 'Disponibilidad marcada para el profesor seleccionado. Usa «Guardar disponibilidad» para persistir.');
    }

    /** Desmarca toda la disponibilidad del profesor seleccionado (se guarda aparte). */
    public function uncheckAllAvailability(): void
    {
        if (! $this->calendarId || ! $this->selectedProfesorId) {
            return;
        }

        $this->setGridValue($this->selectedProfesorId, false);
        session()->flash('message', 'Disponibilidad desmarcada para el profesor seleccionado. Usa «Guardar disponibilidad» para persistir.');
    }

    /** Prellena la disponibilidad del profesor según los bloques donde dicta en el calendario. */
    public function fillAvailabilityFromLessons(): void
    {
        if (! $this->calendarId || ! $this->selectedProfesorId) {
            return;
        }

        $grid = $this->availabilityGrid();
        $this->setGridValue($this->selectedProfesorId, false);

        $periodIds = TimetableSlot::query()
            ->where('calendar_id', $this->calendarId)
            ->where('profesor_id', $this->selectedProfesorId)
            ->pluck('period_id');

        $periods = TimetablePeriod::query()
            ->whereIn('id', $periodIds)
            ->get(['shift_id', 'day_of_week', 'start_time']);

        foreach ($periods as $p) {
            $pid = (int) $p->shift_id;
            if (! isset($grid[$pid])) {
                continue;
            }
            $day = (int) $p->day_of_week;
            $min = $this->minOfDay((string) $p->start_time);
            foreach ($grid[$pid]['blocks'] as $order => $block) {
                if ($min >= $block['start'] && $min < $block['end']) {
                    $this->availability[$this->selectedProfesorId][$pid][$day][$order] = true;
                    break;
                }
            }
        }

        session()->flash('message', 'Disponibilidad prellenada con los bloques donde dicta el profesor. Usa «Guardar disponibilidad» para persistir.');
    }

    /** Copia la disponibilidad guardada de OTRO profesor al seleccionado. */
    public function copyAvailabilityFrom($sourceProfesorId): void
    {
        if (! $this->calendarId || ! $this->selectedProfesorId || ! $sourceProfesorId) {
            return;
        }
        if ((int) $sourceProfesorId === (int) $this->selectedProfesorId) {
            return;
        }

        // Grilla por defecto disponible=true y aplico SOLO las filas guardadas
        // del docente origen (coherente con loadAvailability).
        $this->loadAvailability();

        $rows = TimetableTeacherAvailability::query()
            ->where('calendar_id', $this->calendarId)
            ->where('profesor_id', (int) $sourceProfesorId)
            ->get();

        foreach ($rows as $r) {
            $this->availability[$this->selectedProfesorId][(int) $r->shift_id][(int) $r->day_of_week][(int) $r->order_in_day] = (bool) $r->is_available;
        }

        session()->flash('message', 'Disponibilidad copiada del docente seleccionado. Usa «Guardar disponibilidad» para persistir.');
    }

    public function saveAvailability(): void
    {
        if (! $this->calendarId) {
            session()->flash('error', 'Crea el calendario primero.');

            return;
        }

        $grid = $this->availabilityGrid();
        foreach ($this->availability as $profesorId => $shifts) {
            foreach ($shifts as $shiftId => $days) {
                foreach ($days as $day => $orders) {
                    foreach ($orders as $order => $isAvailable) {
                        $block = $grid[(int) $shiftId]['blocks'][(int) $order] ?? null;
                        TimetableTeacherAvailability::updateOrCreate(
                            [
                                'calendar_id' => $this->calendarId,
                                'profesor_id' => (int) $profesorId,
                                'shift_id' => (int) $shiftId,
                                'day_of_week' => (int) $day,
                                'order_in_day' => (int) $order,
                            ],
                            [
                                'start_time' => $block ? $this->fmtMin($block['start']) : null,
                                'end_time' => $block ? $this->fmtMin($block['end']) : null,
                                'is_available' => (bool) $isAvailable,
                            ],
                        );
                    }
                }
            }
        }

        session()->flash('message', 'Disponibilidad guardada.');
        $this->goToStep(5);
    }

    public function updatedCalendarId($value): void
    {
        if (! $this->calendarId) {
            $this->currentStep = 1;
            $this->lessons = [];
            $this->selectedPevs = [];
            $this->availability = [];
            $this->periods = [];
            $this->generationState = null;
            $this->preview = null;
            $this->lessonsDirty = false;
            $this->lessonsSavedAt = null;
            $this->activePestudioId = null;
            $this->activeGradoId = null;
            $this->activeSeccionId = null;
            $this->selectedProfesorId = null;
            $this->reloadRooms();

            return;
        }

        if ($this->lessonsDirty) {
            session()->flash('warning', 'Había cambios de lecciones sin guardar. Se conservaron en el calendario anterior; verifica el nuevo borrador.');
        }

        // Switcher global: al cambiar el calendario se recarga todo el contexto.
        $this->selectCalendar($this->calendarId);
    }

    public function loadAvailability(): void
    {
        $this->availability = [];
        if (! $this->calendarId || ! $this->selectedProfesorId) {
            return;
        }

        // Grilla por turno · día · bloque, disponible por defecto (solver: ausencia
        // de fila = disponible), con los overrides guardados.
        $rows = TimetableTeacherAvailability::query()
            ->where('calendar_id', $this->calendarId)
            ->where('profesor_id', $this->selectedProfesorId)
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $map["{$r->shift_id}-{$r->day_of_week}-{$r->order_in_day}"] = (bool) $r->is_available;
        }

        foreach ($this->availabilityGrid() as $shiftId => $g) {
            foreach (range(1, 5) as $day) {
                foreach ($g['blocks'] as $order => $block) {
                    $this->availability[$this->selectedProfesorId][$shiftId][$day][$order] = $map["{$shiftId}-{$day}-{$order}"] ?? true;
                }
            }
        }
    }

    /**
     * Rejilla de disponibilidad por turno (según el seeder): bloques de 60 min
     * desde la hora de inicio, cubriendo hasta la hora completa de la ventana.
     *
     * @return array<int, array{code:string, name:string, start:string, end:string, blocks:array<int, array{start:int, end:int}>}>
     */
    public function availabilityGrid(): array
    {
        $grid = [];
        foreach (TimetableShift::query()->orderBy('start_time')->get() as $shift) {
            $startMin = intdiv($this->minOfDay((string) $shift->start_time), 60) * 60;
            $endMin = $this->minOfDay((string) $shift->end_time);
            $hours = max(1, (int) ceil(($endMin - $startMin) / 60));
            $blocks = [];
            for ($i = 1; $i <= $hours; $i++) {
                $s = $startMin + ($i - 1) * 60;
                $e = $startMin + $i * 60;
                $blocks[$i] = [
                    'start' => $s, 'end' => $e,
                    'start_time' => $this->fmtMin($s), 'end_time' => $this->fmtMin($e),
                ];
            }
            $grid[$shift->id] = [
                'code' => $shift->code,
                'name' => $shift->name,
                'start' => $shift->start_time,
                'end' => $shift->end_time,
                'blocks' => $blocks,
            ];
        }

        return $grid;
    }

    /** Fija todas las celdas de la rejilla del profesor (en memoria). */
    private function setGridValue(int $profesorId, bool $value): void
    {
        foreach ($this->availabilityGrid() as $shiftId => $g) {
            foreach (range(1, 5) as $day) {
                foreach ($g['blocks'] as $order => $block) {
                    $this->availability[$profesorId][$shiftId][$day][$order] = $value;
                }
            }
        }
    }

    /** upsert de las filas de disponibilidad de un profesor (todas disponibles). */
    private function upsertAvailabilityRows(int $profesorId, array $grid, bool $value = true): void
    {
        foreach ($grid as $shiftId => $g) {
            foreach (range(1, 5) as $day) {
                foreach ($g['blocks'] as $order => $block) {
                    TimetableTeacherAvailability::updateOrCreate(
                        [
                            'calendar_id' => $this->calendarId,
                            'profesor_id' => $profesorId,
                            'shift_id' => (int) $shiftId,
                            'day_of_week' => $day,
                            'order_in_day' => (int) $order,
                        ],
                        [
                            'start_time' => $this->fmtMin($block['start']),
                            'end_time' => $this->fmtMin($block['end']),
                            'is_available' => $value,
                        ],
                    );
                }
            }
        }
    }

    // ─── Paso 5 · Generar ───────────────────────────────────────

    /**
     * Grilla del horario previsualizado de una sección: del `preview_payload`
     * (assignment) coloca cada asignatura en su período (turno + orden + día).
     *
     * @return array<int, array<int, array<int, list<array{lesson_id:int, period_id:int, asignatura:string, profesor:string, grupo:?string}>>>>
     */
    private function previewSectionGrid(?int $seccionId): array
    {
        if (! $this->preview || ! $seccionId) {
            return [];
        }

        $assignment = $this->preview['assignment'] ?? [];

        $lessons = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereHas('pevaluacion', fn ($q) => $q->where('seccion_id', $seccionId))
            ->with('pevaluacion.pensum.asignatura', 'pevaluacion.profesor', 'pevaluacion.grupoEstable')
            ->get();

        $periodMap = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->get()
            ->keyBy('id');

        $maxSubjectsPerPeriod = max(1, (int) (TimetableCalendar::find($this->calendarId)?->max_subjects_per_period ?? 2));
        $cellLoad = [];

        $grid = [];
        foreach ($lessons as $lesson) {
            $slots = $assignment[(string) $lesson->id] ?? [];

            foreach ($slots as $slot) {
                $period = $periodMap->get($slot['period_id']);
                if (! $period) {
                    continue;
                }
                $cellKey = $period->shift_id.':'.$period->order_in_day.':'.$period->day_of_week;
                if (($cellLoad[$cellKey] ?? 0) >= $maxSubjectsPerPeriod) {
                    continue;
                }
                $pev = $lesson->pevaluacion;
                $grid[$period->shift_id][$period->order_in_day][$period->day_of_week][] = [
                    'lesson_id' => (int) $lesson->id,
                    'period_id' => (int) $period->id,
                    'asignatura' => $pev?->pensum?->asignatura?->name ?? '—',
                    'profesor' => trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')),
                    'grupo' => $pev?->grupoEstable?->name,
                ];
                $cellLoad[$cellKey] = ($cellLoad[$cellKey] ?? 0) + 1;
            }
        }

        return $grid;
    }

    public function movePreviewLesson(int $lessonId, int $fromPeriodId, int $newPeriodId): void
    {
        if (! $this->preview || $lessonId <= 0 || $fromPeriodId <= 0 || $newPeriodId <= 0) {
            return;
        }

        $targetPeriod = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->whereKey($newPeriodId)
            ->first();
        $lesson = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion')
            ->find($lessonId);

        if (! $targetPeriod || $targetPeriod->is_break || ! $lesson?->pevaluacion) {
            $this->notification()->error(
                'Movimiento no válido',
                'Solo puedes mover una lección a un período de clase válido.',
            );

            return;
        }

        $assignment = $this->preview['assignment'] ?? [];
        $lessonKey = (string) $lessonId;
        $slots = $assignment[$lessonKey] ?? $assignment[$lessonId] ?? [];
        $slotIndex = collect($slots)->search(
            fn (array $slot): bool => (int) ($slot['period_id'] ?? 0) === $fromPeriodId,
        );

        if ($slotIndex === false) {
            return;
        }

        $alreadyInTarget = collect($slots)->contains(
            fn (array $slot, int $index): bool => $index !== $slotIndex
                && (int) ($slot['period_id'] ?? 0) === $newPeriodId,
        );
        if ($alreadyInTarget) {
            $this->notification()->warning(
                'Lección ya ubicada',
                'La lección ya tiene otro bloque en el período destino; no se creó un duplicado en la celda.',
            );

            return;
        }

        $sourcePeriod = TimetablePeriod::query()->find($fromPeriodId);
        if (! $sourcePeriod || $sourcePeriod->shift_id !== $targetPeriod->shift_id
            || $lesson->shift_id !== $targetPeriod->shift_id) {
            $this->notification()->error(
                'Turno incompatible',
                'El período destino debe pertenecer al mismo turno de la lección.',
            );

            return;
        }

        $swapLessonId = null;
        $swapSlotIndex = null;
        foreach ($assignment as $otherLessonId => $otherSlots) {
            foreach ($otherSlots as $otherSlot) {
                if ((int) ($otherSlot['period_id'] ?? 0) !== $newPeriodId
                    || (int) $otherLessonId === $lessonId) {
                    continue;
                }

                $otherLesson = TimetableLesson::query()
                    ->whereKey((int) $otherLessonId)
                    ->with('pevaluacion')
                    ->first();
                if (! $otherLesson?->pevaluacion) {
                    continue;
                }

                $sameSection = (int) $otherLesson->pevaluacion->seccion_id
                    === (int) $lesson->pevaluacion->seccion_id;
                $bothAllowHalfGroup = (bool) $lesson->is_half_group
                    && (bool) $otherLesson->is_half_group;
                $isSectionSwap = $sameSection && ! $bothAllowHalfGroup;

                if ((int) $otherLesson->pevaluacion->profesor_id === (int) $lesson->pevaluacion->profesor_id
                    && ! $isSectionSwap) {
                    $this->notification()->error(
                        'Conflicto de docente',
                        'El docente ya tiene una lección en ese período.',
                    );

                    return;
                }

                if ($isSectionSwap) {
                    $swapLessonId = (int) $otherLessonId;
                    $swapSlotIndex = collect($otherSlots)->search(
                        fn (array $slot): bool => (int) ($slot['period_id'] ?? 0) === $newPeriodId,
                    );
                }
            }
        }

        if ($swapLessonId !== null && $swapSlotIndex !== false && $swapSlotIndex !== null) {
            $swapKey = (string) $swapLessonId;
            $swapSlots = $assignment[$swapKey] ?? $assignment[$swapLessonId] ?? [];

            $slots[$slotIndex]['period_id'] = $newPeriodId;
            $swapSlots[$swapSlotIndex]['period_id'] = $fromPeriodId;
            $assignment[$lessonKey] = array_values($slots);
            $assignment[$swapKey] = array_values($swapSlots);
            $this->preview['assignment'] = $assignment;
            $this->preview['manual_override'] = true;
            $this->preview['assignment_source'] = 'manual_preview';
            $this->notification()->success(
                'Lecciones intercambiadas',
                'La lección arrastrada y la lección de la sección intercambiaron sus períodos.',
            );

            return;
        }

        $slots[$slotIndex]['period_id'] = $newPeriodId;
        $assignment[$lessonKey] = array_values($slots);
        $this->preview['assignment'] = $assignment;
        $this->preview['manual_override'] = true;
        $this->preview['assignment_source'] = 'manual_preview';
        $this->notification()->success(
            'Lección reubicada',
            'La lección se movió correctamente en el preview.',
        );
    }

    public function openAddPreviewLessonModal(int $periodId): void
    {
        $period = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->whereKey($periodId)
            ->first();

        if (! $period || $period->is_break) {
            $this->notification()->error('Período no válido', 'Solo puedes agregar lecciones en períodos de clase.');

            return;
        }

        $this->addPreviewLessonPeriodId = $periodId;
        $this->showAddPreviewLessonModal = true;
    }

    public function closeAddPreviewLessonModal(): void
    {
        $this->showAddPreviewLessonModal = false;
        $this->addPreviewLessonPeriodId = null;
    }

    public function availablePreviewLessons()
    {
        $ids = collect($this->preview['unassigned'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        return TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereIn('id', $ids)
            ->with('pevaluacion.pensum.asignatura', 'pevaluacion.seccion')
            ->get();
    }

    public function addPreviewLesson(int $lessonId): void
    {
        $period = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->whereKey($this->addPreviewLessonPeriodId)
            ->first();
        $lesson = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion')
            ->find($lessonId);

        if (! $period || $period->is_break || ! $lesson?->pevaluacion) {
            $this->notification()->error('Lección no agregada', 'El período o la lección seleccionada no es válido.');

            return;
        }

        if ($lesson->shift_id !== $period->shift_id) {
            $this->notification()->error('Turno incompatible', 'La lección no pertenece al turno del período seleccionado.');

            return;
        }

        $assignment = $this->preview['assignment'] ?? [];
        $targetLessonIds = collect($assignment)
            ->filter(fn ($slots) => collect($slots)->contains(
                fn (array $slot): bool => (int) ($slot['period_id'] ?? 0) === $period->id
            ))
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($targetLessonIds as $targetLessonId) {
            $targetLesson = TimetableLesson::query()
                ->where('calendar_id', $this->calendarId)
                ->with('pevaluacion')
                ->find($targetLessonId);
            if (! $targetLesson?->pevaluacion) {
                continue;
            }

            if ((int) $targetLesson->pevaluacion->profesor_id === (int) $lesson->pevaluacion->profesor_id) {
                $this->notification()->error('Conflicto de docente', 'El docente ya tiene una lección en ese período.');

                return;
            }

            if ((int) $targetLesson->pevaluacion->seccion_id === (int) $lesson->pevaluacion->seccion_id
                && (! $lesson->is_half_group || ! $targetLesson->is_half_group)) {
                $this->notification()->error(
                    'Conflicto de sección',
                    'La sección ya tiene una lección en ese período. Usa Drag and Drop para intercambiarla.',
                );

                return;
            }
        }

        $lessonKey = (string) $lesson->id;
        $lessonSlots = $assignment[$lessonKey] ?? $assignment[$lesson->id] ?? [];
        if (collect($lessonSlots)->contains(
            fn (array $slot): bool => (int) ($slot['period_id'] ?? 0) === $period->id
        )) {
            $this->notification()->warning('Lección ya ubicada', 'La lección ya está asignada a ese período.');

            return;
        }

        $lessonSlots[] = ['period_id' => $period->id, 'room_id' => null, 'is_practical' => false];
        $assignment[$lessonKey] = array_values($lessonSlots);
        $this->preview['assignment'] = $assignment;
        $this->preview['unassigned'] = array_values(array_filter(
            $this->preview['unassigned'] ?? [],
            fn ($id): bool => (int) $id !== $lesson->id,
        ));
        $this->preview['manual_override'] = true;
        $this->preview['assignment_source'] = 'manual_preview';
        $this->closeAddPreviewLessonModal();
        $this->notification()->success('Lección agregada', 'La lección se agregó al período seleccionado.');
    }

    public function runDryRun(): void
    {
        if (! $this->calendarId) {
            session()->flash('error', 'Crea el calendario primero.');

            return;
        }

        $this->busy = true;
        $this->generationState = 'generating';

        try {
            GenerateTimetableJob::dispatchSync($this->calendarId, dryRun: true);
            $calendar = TimetableCalendar::find($this->calendarId);
            $this->preview = $calendar?->preview_payload;
            $this->generationState = 'preview_ready';
        } finally {
            $this->busy = false;
        }
    }

    /**
     * Persiste el preview actual como borrador sin publicar slots ni cambiar
     * el estado del calendario a activo.
     */
    public function updateDraftPreview(): void
    {
        if (! $this->calendarId || ! $this->preview) {
            $this->notification()->warning(
                'Preview requerido',
                'Genera una previsualización antes de actualizar el borrador.',
            );

            return;
        }

        $calendar = TimetableCalendar::query()->find($this->calendarId);
        if (! $calendar) {
            $this->notification()->error('Calendario no encontrado', 'No se pudo actualizar el borrador.');

            return;
        }

        $calendar->update([
            'preview_payload' => $this->preview,
            'status' => TimetableCalendar::STATUS_DRAFT,
        ]);
        $this->generationState = 'preview_ready';
        $this->notification()->success(
            'Borrador actualizado',
            'Se guardó la previsualización actual sin publicar el horario.',
        );
    }

    /**
     * Checklist pre-publicación (Paso 5): resume los problemas del preview
     * (lecciones sin asignar, docentes con bloqueos, secciones vacías) para
     * revisarlos ANTES de «Confirmar y publicar».
     *
     * @return array<string, int>
     */
    public function publishChecklist(): array
    {
        if (! $this->preview) {
            return [];
        }

        $unassigned = collect($this->preview['unassigned'] ?? []);
        $assignment = collect($this->preview['assignment'] ?? []);

        // Docentes con lecciones sin asignar (necesitan ajuste manual).
        $unassignedLessonIds = $unassigned->map(fn ($v) => (int) $v)->all();
        $teachersAffected = TimetableLesson::query()
            ->whereIn('id', $unassignedLessonIds)
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion.profesor')
            ->get()
            ->pluck('pevaluacion.profesor.lastname')
            ->unique()
            ->values();

        // Secciones con horario (al menos 1 slot asignado).
        $seccionesConHorario = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereIn('id', $assignment->keys()->map(fn ($v) => (int) $v))
            ->with('pevaluacion')
            ->get()
            ->pluck('pevaluacion.seccion_id')
            ->unique()
            ->count();

        return [
            'asignadas' => $assignment->count(),
            'sin_asignar' => $unassigned->count(),
            'docentes_afectados' => $teachersAffected->count(),
            'docentes' => $teachersAffected->take(5)->implode(', '),
            'secciones_con_horario' => $seccionesConHorario,
            'calidad' => $assignment->count() + $unassigned->count() > 0
                ? (int) round($assignment->count() * 100 / ($assignment->count() + $unassigned->count()))
                : 100,
        ];
    }

    /**
     * Builds actionable diagnostics for lessons that the solver could not place.
     *
     * @return array<string, array{count:int, items:list<array<string, mixed>>}>
     */
    public function generationConflictGroups(): array
    {
        if (! $this->preview) {
            return [];
        }

        $ids = collect($this->preview['unassigned'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $lessons = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereIn('id', $ids)
            ->with('shift', 'pevaluacion.pensum.asignatura', 'pevaluacion.seccion', 'pevaluacion.profesor')
            ->get();

        $periods = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->where('is_break', false)
            ->get(['id', 'shift_id', 'day_of_week', 'order_in_day']);
        $periodsByShift = $periods
            ->groupBy('shift_id');
        $roomsByType = app(TimetableRoomEligibilityService::class)
            ->idsByType(TimetableCalendar::findOrFail($this->calendarId));
        $blockedAvailability = TimetableTeacherAvailability::query()
            ->where('calendar_id', $this->calendarId)
            ->where('is_available', false)
            ->get(['profesor_id', 'shift_id', 'day_of_week', 'order_in_day'])
            ->groupBy('profesor_id');

        // Reconstruye las ocupaciones que el solver ya tomó en el preview para
        // explicar por qué un dominio nominalmente disponible quedó sin espacio.
        $previewAssignment = $this->preview['assignment'] ?? [];
        $allLessons = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion')
            ->get()
            ->keyBy('id');
        $teacherBusy = [];
        $sectionBusy = [];
        $sectionGroupBusy = [];
        $roomBusy = [];

        foreach ($previewAssignment as $lessonId => $assignedSlots) {
            $assignedLesson = $allLessons->get((int) $lessonId);
            $assignedPev = $assignedLesson?->pevaluacion;
            if (! $assignedLesson || ! $assignedPev) {
                continue;
            }

            foreach ($assignedSlots as $assignedSlot) {
                $periodId = (int) ($assignedSlot['period_id'] ?? 0);
                if (! $periodId) {
                    continue;
                }
                $teacherBusy[$periodId.':'.(int) $assignedPev->profesor_id] = true;
                $sectionKey = $periodId.':'.(int) $assignedPev->seccion_id;
                if ($assignedPev->grupo_estable_id) {
                    $sectionGroupBusy[$sectionKey.':'.(int) $assignedPev->grupo_estable_id] = true;
                } else {
                    $sectionBusy[$sectionKey] = true;
                }

                $roomId = (int) ($assignedSlot['room_id'] ?? 0);
                if ($roomId) {
                    $roomBusy[$periodId.':'.$roomId] = true;
                }
            }
        }

        return $lessons
            ->map(function (TimetableLesson $lesson) use (
                $periodsByShift,
                $roomsByType,
                $blockedAvailability,
                $teacherBusy,
                $sectionBusy,
                $sectionGroupBusy,
                $roomBusy
            ): array {
                $pev = $lesson->pevaluacion;
                $subject = $pev?->pensum?->asignatura?->name ?? 'Sin asignatura';
                $section = $pev?->seccion?->name ?? 'Sin sección';
                $teacher = trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')) ?: 'Sin docente';
                $blocks = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
                $shiftPeriods = $periodsByShift->get($lesson->shift_id, collect());
                $teacherBlocks = $blockedAvailability->get($pev?->profesor_id, collect())
                    ->where('shift_id', $lesson->shift_id);
                $blockedKeys = $teacherBlocks
                    ->mapWithKeys(fn ($blocked) => [
                        "{$blocked->shift_id}-{$blocked->day_of_week}-{$blocked->order_in_day}" => true,
                    ]);
                $candidatePeriods = $shiftPeriods->filter(fn ($period) => ! $blockedKeys->has(
                    "{$period->shift_id}-{$period->day_of_week}-{$period->order_in_day}"
                ));
                $sectionId = (int) ($pev?->seccion_id ?? 0);
                $groupId = $pev?->grupo_estable_id ? (int) $pev->grupo_estable_id : null;
                $sectionBlocked = 0;
                $teacherOccupied = 0;
                $roomUnavailable = 0;
                $freePeriods = 0;

                foreach ($candidatePeriods as $period) {
                    $periodId = (int) $period->id;
                    $teacherConflict = isset($teacherBusy[$periodId.':'.(int) ($pev?->profesor_id)]);
                    $sectionConflict = isset($sectionBusy[$periodId.':'.$sectionId])
                        || ($groupId === null
                            ? collect(array_keys($sectionGroupBusy))
                                ->contains(fn ($key) => str_starts_with($key, $periodId.':'.$sectionId.':'))
                            : isset($sectionGroupBusy[$periodId.':'.$sectionId.':'.$groupId]));

                    if ($teacherConflict) {
                        $teacherOccupied++;
                    }
                    if ($sectionConflict) {
                        $sectionBlocked++;
                    }

                    $roomConflict = false;
                    if ((int) $lesson->weekly_blocks_p > 0 && $lesson->room_type_required) {
                        $roomIds = $roomsByType[$lesson->room_type_required] ?? [];
                        $hasFreeRoom = collect($roomIds)->contains(
                            fn ($roomId) => ! isset($roomBusy[$periodId.':'.(int) $roomId])
                        );
                        if (! $hasFreeRoom) {
                            $roomUnavailable++;
                            $roomConflict = true;
                        }
                    }

                    if (! $teacherConflict && ! $sectionConflict && ! $roomConflict) {
                        $freePeriods++;
                    }
                }

                $availablePeriods = $candidatePeriods->count();
                $reason = 'Combinación no viable por restricciones simultáneas.';
                $actions = [
                    'Revisa el turno, la disponibilidad del docente y los bloques de la sección.',
                    'Prueba ejecutar nuevamente después de liberar o redistribuir períodos.',
                ];

                if (! $lesson->shift_id) {
                    $reason = 'La lección no tiene un turno válido.';
                    $actions = ['Selecciona un turno con períodos guardados en el paso 3.'];
                } elseif ($shiftPeriods->isEmpty()) {
                    $reason = 'El turno seleccionado no tiene períodos disponibles.';
                    $actions = ['Genera y guarda los períodos del turno en el paso 1.'];
                } elseif ((int) $lesson->weekly_blocks_p > 0 && empty($lesson->room_type_required)) {
                    $reason = 'La lección tiene bloques prácticos sin tipo de aula requerido.';
                    $actions = ['Selecciona un tipo de aula compatible en el paso 3.'];
                } elseif ((int) $lesson->weekly_blocks_p > 0
                    && empty($roomsByType[$lesson->room_type_required] ?? [])) {
                    $reason = "No hay aulas activas compatibles de tipo «{$lesson->room_type_required}».";
                    $actions = [
                        'Registra o activa un aula de ese tipo en el paso 2.',
                        'Revisa que el aula pertenezca al pestudio actual o esté sin asociación.',
                    ];
                } elseif ($availablePeriods < $blocks) {
                    $reason = "El docente dispone de {$availablePeriods} bloque(s), pero la lección necesita {$blocks}.";
                    $actions = [
                        'Aumenta los períodos del turno o marca más disponibilidad docente.',
                        'Reduce los bloques semanales o cambia la lección de turno.',
                    ];
                } elseif ($freePeriods < $blocks && $sectionBlocked >= $teacherOccupied && $sectionBlocked > 0) {
                    $reason = "La sección {$section} tiene {$sectionBlocked} período(s) ocupado(s) por otras asignaturas.";
                    $actions = [
                        'Revisa las clases paralelas o bloqueadas de la sección.',
                        'Cambia el turno o redistribuye los bloques de la sección.',
                    ];
                } elseif ($freePeriods < $blocks && $teacherOccupied > 0) {
                    $reason = "El docente tiene {$teacherOccupied} período(s) ocupado(s) por otras lecciones.";
                    $actions = [
                        'Revisa las asignaciones del docente en este turno.',
                        'Amplía su disponibilidad o cambia la lección de turno.',
                    ];
                } elseif ((int) $lesson->weekly_blocks_p > 0 && $roomUnavailable >= $blocks) {
                    $reason = "No hay aulas {$lesson->room_type_required} libres en los períodos candidatos.";
                    $actions = [
                        'Libera aulas ocupadas en ese turno o registra aulas adicionales.',
                        'Cambia el tipo de aula requerido si la actividad lo permite.',
                    ];
                } elseif ($teacherBlocks->count() > 0) {
                    $reason = "La disponibilidad del docente bloquea {$teacherBlocks->count()} período(s) del turno.";
                    $actions = [
                        'Abre el paso 4 y habilita más períodos para el docente.',
                        'Cambia el turno de la lección si la disponibilidad no puede ampliarse.',
                    ];
                }

                return [
                    'subject' => $subject,
                    'section' => $section,
                    'teacher' => $teacher,
                    'shift' => $lesson->shift?->code ?? '—',
                    'blocks_t' => (int) $lesson->weekly_blocks_t,
                    'blocks_p' => (int) $lesson->weekly_blocks_p,
                    'room_type' => $lesson->room_type_required ?: 'No requerido',
                    'available_periods' => $availablePeriods,
                    'free_periods' => $freePeriods,
                    'section_blocked_periods' => $sectionBlocked,
                    'teacher_occupied_periods' => $teacherOccupied,
                    'room_unavailable_periods' => $roomUnavailable,
                    'required_periods' => $blocks,
                    'reason' => $reason,
                    'actions' => $actions,
                ];
            })
            ->groupBy(fn (array $item) => $item['teacher'])
            ->map(fn ($group) => [
                'count' => $group->count(),
                'items' => $group->values()->all(),
            ])
            ->sortByDesc('count')
            ->all();
    }

    public function confirmAndPublish(): void
    {
        if (! $this->calendarId || ! $this->preview) {
            session()->flash('error', 'Primero ejecuta una previsualización (dry-run).');

            return;
        }

        $this->busy = true;
        try {
            GenerateTimetableJob::dispatchSync(
                $this->calendarId,
                dryRun: false,
                previewPayload: $this->preview,
            );
            $this->generationState = 'published';
            $this->preview = null;
            session()->flash('message', 'Horario publicado.');
        } finally {
            $this->busy = false;
        }
    }

    // ─── Helpers ───────────────────────────────────────────────

    private function loadCalendar(TimetableCalendar $calendar): void
    {
        $this->lapsoId = $calendar->lapso_id;
        $this->pescolarId = $calendar->pescolar_id;
        $this->pestudioId = $calendar->pestudio_id;
        $this->calendarName = $calendar->name;
        $this->periodMinutes = (int) $calendar->period_minutes;
        $this->maxSubjectsPerPeriod = max(1, (int) ($calendar->max_subjects_per_period ?? 2));
        $this->strategy = in_array($calendar->strategy, TimetableCalendar::STRATEGIES, true)
            ? $calendar->strategy
            : TimetableCalendar::STRATEGY_OPTIMIZED;
        $this->reloadRooms();
    }

    /**
     * PLAN-TIMETABLE-002 §4.5 — Alternativas (calendarios) del lapso en edición,
     * ordenadas activo → borrador → generando → archivado.
     */
    private function loadCalendars(): void
    {
        $query = TimetableCalendar::query();

        if ($this->lapsoId) {
            $query->forLapso($this->lapsoId);
        }

        $this->calendars = $query
            ->orderByRaw("FIELD(status, 'active', 'draft', 'generating', 'archived'), id DESC")
            ->get()
            ->map(fn ($c) => $c->toArray())
            ->values()
            ->all();
    }

    private function defaultShiftId(): int
    {
        return TimetableShift::query()->orderBy('id')->value('id') ?? 0;
    }

    /**
     * Bloques completos de `$minutes` que caben desde la hora de inicio del
     * turno seleccionado (redondeada a la hora) hasta el fin de su ventana.
     */
    private function shiftCompleteBlocks(int $minutes): int
    {
        $startMin = $this->shiftStartMin();
        $endMin = $this->minOfDay((string) TimetableShift::find($this->shiftId)?->end_time);

        return intdiv($endMin - $startMin, $minutes);
    }

    /** Minuto-a-hora (align a la hora) del inicio del turno seleccionado. */
    private function shiftStartMin(): int
    {
        $shift = TimetableShift::find($this->shiftId);

        return intdiv($this->minOfDay((string) ($shift?->start_time ?? $this->shiftStart)), 60) * 60;
    }

    private function minOfDay(string $h): int
    {
        [$h, $m] = array_pad(explode(':', (string) $h), 2, '0');

        return ((int) $h) * 60 + (int) $m;
    }

    private function fmtMin(int $min): string
    {
        return sprintf('%02d:%02d', intdiv($min, 60), $min % 60);
    }

    public function goToStep(int $step): void
    {
        if ($step > 1 && ! $this->calendarId) {
            $this->currentStep = 1;

            return;
        }

        $this->currentStep = max(1, min(5, $step));
    }

    /**
     * Layout de render. Los submódulos (p. ej. planning) lo sobreescriben.
     */
    protected function getLayout(): string
    {
        return 'coordinacion.layouts.app';
    }

    /**
     * Prefijo de rutas del módulo que instancia el wizard. La vista lo usa
     * para rutas que existen por módulo (p. ej. el PDF del preview del
     * paso 5); las subclases lo sobreescriben.
     */
    public function moduleRoutePrefix(): string
    {
        return 'app.coordinacion';
    }

    public function render(): \Illuminate\View\View
    {
        $lapsos = Lapso::orderBy('finicial', 'desc')->get();

        $shifts = TimetableShift::query()->orderBy('start_time')->get();

        // Los pasos 3, 4 y 5 necesitan las pevaluaciones/tabs; los pasos 1 y 2
        // no, y así se evita la consulta (rendimiento).
        $pevaluaciones = in_array($this->currentStep, [3, 4, 5], true)
            ? $this->allPevaluaciones()
            : collect();

        $profesores = collect();
        if ($this->calendarId && $pevaluaciones->isNotEmpty()) {
            $step3PevIds = $this->selectedPevIds();
            $step3Pevaluaciones = $step3PevIds !== []
                ? $pevaluaciones->whereIn('id', $step3PevIds)
                : $pevaluaciones;

            $profesores = \App\Models\app\Academy\Profesor::query()
                ->whereIn('id', $step3Pevaluaciones->pluck('profesor_id')->unique())
                ->where('status_active', 'true')
                ->orderBy('lastname')
                ->get();
        }

        // Paso 4: profesores activos filtrados por búsqueda (para el select) y el
        // profesor seleccionado para editar su disponibilidad puntualmente.
        $search = mb_strtolower(trim($this->searchProfesor));
        $profesoresFiltrados = $profesores->when($search !== '', fn ($c) => $c->filter(
            fn ($p) => str_contains(mb_strtolower($p->lastname.' '.$p->name), $search)
        ));
        $selectedProfesor = $profesores->firstWhere('id', (int) $this->selectedProfesorId);
        if (! $selectedProfesor && $this->selectedProfesorId) {
            $this->selectedProfesorId = null;
            $this->availability = [];
        }

        $periodsList = $this->calendarId
            ? TimetablePeriod::query()
                ->where('calendar_id', $this->calendarId)
                ->with('shift:id,code,name')
                ->orderBy('day_of_week')
                ->orderBy('order_in_day')
                ->get()
            : collect();

        $calendarPeriodMinutes = $this->calendarId
            ? (int) (TimetableCalendar::find($this->calendarId)?->period_minutes ?? 60)
            : 60;

        $tabData = $this->resolveTabData($pevaluaciones);

        // Lecciones ya persistidas (guardadas) para el calendario.
        $savedPevIds = $this->calendarId
            ? TimetableLesson::query()->where('calendar_id', $this->calendarId)->pluck('pevaluacion_id')->flip()->all()
            : [];

        // Períodos disponibles por turno (factibilidad).
        $periodsByShift = $periodsList->where('is_break', false)->groupBy('shift_id')->map->count()->all();

        $step3Warnings = $this->buildLessonWarnings($periodsByShift);
        $generationConflictGroups = $this->generationConflictGroups();

        // Capacidad del turno por día: bloques de clase (sin recreos) distintos
        // por día — la rejilla de una sección. Se usa en el resumen del Paso 3.
        $shiftCapacityPerDay = $periodsList->where('is_break', false)
            ->groupBy('shift_id')
            ->map(fn ($group) => $group->groupBy('order_in_day')->count())
            ->all();

        $selectedCount = count(array_filter($this->selectedPevs));

        // Paso 5: grilla del horario previsualizado de la sección activa.
        $sectionPreviewGrid = ($this->currentStep === 5 && $this->preview)
            ? $this->previewSectionGrid((int) ($this->activeSeccionId ?? 0))
            : [];

        // Detalle del calendario seleccionado (tarjeta del Paso 1).
        $selectedCalendar = $this->calendarId ? TimetableCalendar::with('pestudio')->find((int) $this->calendarId) : null;
        $selectedCalendarDetail = null;
        if ($selectedCalendar) {
            $periodsOfCalendar = $periodsList;
            $slotsCount = TimetableSlot::query()->where('calendar_id', $selectedCalendar->id)->count();
            $conflictsCount = \App\Models\app\Timetable\TimetableConflict::query()
                ->where('calendar_id', $selectedCalendar->id)
                ->count();
            $selectedCalendarDetail = [
                'id' => $selectedCalendar->id,
                'name' => $selectedCalendar->name,
                'status' => $selectedCalendar->status,
                'version' => (int) $selectedCalendar->version,
                'quality_score' => $selectedCalendar->quality_score !== null ? (float) $selectedCalendar->quality_score : null,
                'period_minutes' => (int) $selectedCalendar->period_minutes,
                'max_subjects_per_period' => (int) ($selectedCalendar->max_subjects_per_period ?? 2),
                'strategy' => $selectedCalendar->strategy ?: TimetableCalendar::STRATEGY_OPTIMIZED,
                'lapso_name' => $selectedCalendar->lapso?->name,
                'pestudio_name' => $selectedCalendar->pestudio?->name,
                'is_editable' => $selectedCalendar->is_editable,
                'created_at' => optional($selectedCalendar->created_at)->format('d/m/Y H:i'),
                'updated_at' => optional($selectedCalendar->updated_at)->format('d/m/Y H:i'),
                'shifts_count' => $periodsOfCalendar->groupBy('shift_id')->count(),
                'periods_count' => $periodsOfCalendar->count(),
                'class_periods_count' => $periodsOfCalendar->where('is_break', false)->count(),
                'break_periods_count' => $periodsOfCalendar->where('is_break', true)->count(),
                'lessons_count' => TimetableLesson::query()->where('calendar_id', $selectedCalendar->id)->count(),
                'slots_count' => $slotsCount,
                'conflicts_count' => $conflictsCount,
                'schedule_blocks' => $periodsOfCalendar
                    ->groupBy('day_of_week')
                    ->map(function ($dayPeriods) {
                        $blocks = $dayPeriods
                            ->sortBy(fn ($period) => [
                                $period->start_time ?? '99:99:99',
                                $period->end_time ?? '99:99:99',
                                (int) $period->order_in_day,
                            ])
                            ->map(fn ($period) => [
                                'order' => $period->order_in_day,
                                'start' => $period->start_time ? substr($period->start_time, 0, 5) : '—',
                                'end' => $period->end_time ? substr($period->end_time, 0, 5) : '—',
                                'type' => $period->is_break ? 'Recreo' : 'Clase',
                                'shift' => $period->shift?->name ?? 'Turno '.$period->shift_id,
                                'shift_code' => $period->shift?->code ?: 'SIN CÓDIGO',
                            ])->values();

                        return [
                            'day' => $dayPeriods->first()->day_label,
                            'blocks' => $blocks->all(),
                            'shift_groups' => $blocks
                                ->groupBy('shift_code')
                                ->map(fn ($shiftBlocks, $shiftCode) => [
                                    'code' => $shiftCode,
                                    'name' => $shiftBlocks->first()['shift'],
                                    'blocks' => $shiftBlocks->values()->all(),
                                ])
                                ->values()
                                ->all(),
                        ];
                    })->values()->all(),
            ];
        }

        return view('livewire.coordinacion.timetable.timetable-wizard', [
            'lapsos' => $lapsos,
            'pestudios' => \App\Models\app\Academy\Pestudio::query()->where('status_active', 'true')->orderBy('name')->get(),
            'shifts' => $shifts,
            'pevaluaciones' => $pevaluaciones,
            'pevaluacionesGrouped' => $tabData['pevaluacionesGrouped'],
            'tabPestudioOptions' => $tabData['tabPestudioOptions'],
            'tabGradoOptions' => $tabData['tabGradoOptions'],
            'tabSeccionOptions' => $tabData['tabSeccionOptions'],
            'tabActivePevaluaciones' => $tabData['tabActivePevaluaciones'],
            'savedPevIds' => $savedPevIds,
            'step3Warnings' => $step3Warnings,
            'generationConflictGroups' => $generationConflictGroups,
            'shiftCapacityPerDay' => $shiftCapacityPerDay,
            'step3SelectedCount' => $selectedCount,
            'profesores' => $profesores,
            'profesoresFiltrados' => $profesoresFiltrados,
            'selectedProfesor' => $selectedProfesor,
            'periodsList' => $periodsList,
            'availabilityGrid' => $this->availabilityGrid(),
            'sectionPreviewGrid' => $sectionPreviewGrid,
            'calendarPeriodMinutes' => $calendarPeriodMinutes,
            'roomPestudios' => $this->loadRoomPestudios(),
            'roomGrados' => $this->loadRoomGrados(),
            'roomSecciones' => $this->loadRoomSecciones(),
            'allRoomSecciones' => $this->loadAllRoomSecciones(),
            'roomSectionLinkLabel' => $this->roomSectionLinkLabel,
            'roomSectionLinkedRooms' => $this->roomSectionLinkedRooms,
            'editRoomSectionLinkedRooms' => $this->editRoomSectionLinkedRooms,
            'moduleRoutePrefix' => $this->moduleRoutePrefix(),
            'selectedCalendarDetail' => $selectedCalendarDetail,
        ])->layout($this->getLayout());
    }

    /**
     * Advertencias por lección (validación previa + factibilidad).
     *
     * @return array<int, list<string>>
     */
    private function buildLessonWarnings(array $periodsByShift): array
    {
        $warnings = [];
        $lessonIds = array_map('intval', array_keys($this->lessons));
        $pevaluaciones = $lessonIds === []
            ? collect()
            : Pevaluacion::query()
                ->with('seccion.grado')
                ->whereIn('id', $lessonIds)
                ->get()
                ->keyBy('id');

        $calendar = $this->calendarId ? TimetableCalendar::find($this->calendarId) : null;
        $rooms = $calendar
            ? app(TimetableRoomEligibilityService::class)->forCalendar($calendar)
            : collect();
        $roomsByType = $rooms->groupBy('type');

        foreach ($this->lessons as $pevId => $lesson) {
            $list = [];
            $pev = $pevaluaciones->get((int) $pevId);
            $practicalBlocks = (int) ($lesson['weekly_blocks_p'] ?? 0);

            if (((int) ($lesson['weekly_blocks_t'] ?? 0)) + ((int) ($lesson['weekly_blocks_p'] ?? 0)) <= 0) {
                $list[] = 'Sin bloques';
            }

            if (empty($lesson['shift_id'])) {
                $list[] = 'Sin turno';
            } else {
                $available = $periodsByShift[$lesson['shift_id']] ?? 0;
                $needed = ((int) ($lesson['weekly_blocks_t'] ?? 0)) + ((int) ($lesson['weekly_blocks_p'] ?? 0));
                if ($available > 0 && $needed > $available) {
                    $list[] = 'Excede los períodos del turno';
                }
            }

            if ($practicalBlocks > 0 && empty($lesson['room_type_required'])) {
                $list[] = 'Los bloques prácticos requieren tipo de aula';
            } elseif ($practicalBlocks > 0) {
                $compatibleRooms = $roomsByType->get($lesson['room_type_required'], collect());
                if ($compatibleRooms->isEmpty()) {
                    $list[] = 'No hay aulas activas de tipo '.$lesson['room_type_required'];
                } elseif (($pev?->seccion?->amount_student ?? 0) > 0
                    && $compatibleRooms->max('capacity') < (int) $pev->seccion->amount_student) {
                    $list[] = 'La capacidad máxima del aula es menor que la matrícula de la sección';
                }
            }

            if ($list !== []) {
                $warnings[(int) $pevId] = $list;
            }
        }

        return $warnings;
    }

    /**
     * Resuelve la navegación por pestañas (peducativo → grado → sección) del
     * paso 3 y las pevaluaciones visibles según la pestaña activa.
     *
     * @return array{pevaluacionesGrouped: array, tabPestudioOptions: array, tabGradoOptions: array, tabSeccionOptions: array, tabActivePevaluaciones: \Illuminate\Support\Collection}
     */
    private function resolveTabData($pevaluaciones): array
    {
        $grouped = $this->buildPevsGrouped($pevaluaciones);

        $tabPestudioOptions = collect($grouped)->map(fn ($g) => ['id' => $g['pestudio_id'], 'name' => $g['pestudio_name']])->values()->all();

        if ($tabPestudioOptions !== [] && $this->activePestudioId === null) {
            $this->activePestudioId = $tabPestudioOptions[0]['id'];
        }

        $activePestudio = collect($grouped)->firstWhere('pestudio_id', $this->activePestudioId) ?? collect($grouped)->first();

        $tabGradoOptions = collect($activePestudio['grados'] ?? [])->map(fn ($g) => ['id' => $g['grado_id'], 'name' => $g['grado_name']])->values()->all();

        if ($tabGradoOptions !== [] && $this->activeGradoId === null) {
            $this->activeGradoId = $tabGradoOptions[0]['id'];
        }

        $activeGrado = collect($activePestudio['grados'] ?? [])->firstWhere('grado_id', $this->activeGradoId)
            ?? collect($activePestudio['grados'] ?? [])->first();

        $tabSeccionOptions = collect($activeGrado['secciones'] ?? [])->map(fn ($s) => [
            'id' => $s['seccion_id'],
            'name' => $s['seccion_name'],
            'label' => 'Sección '.$s['seccion_name'].' · '.($activeGrado['grado_name'] ?? 'Grado').' · #'.$s['seccion_id'],
        ])->values()->all();

        if ($tabSeccionOptions !== [] && $this->activeSeccionId === null) {
            $this->activeSeccionId = $tabSeccionOptions[0]['id'];
        }

        $activeSeccion = collect($activeGrado['secciones'] ?? [])->firstWhere('seccion_id', $this->activeSeccionId)
            ?? collect($activeGrado['secciones'] ?? [])->first();

        return [
            'pevaluacionesGrouped' => $grouped,
            'tabPestudioOptions' => $tabPestudioOptions,
            'tabGradoOptions' => $tabGradoOptions,
            'tabSeccionOptions' => $tabSeccionOptions,
            'tabActivePevaluaciones' => $activeSeccion['pevaluaciones'] ?? collect(),
        ];
    }

    /**
     * Agrupa las pevaluaciones por Pestudio → Grado → Sección, para presentarlas
     * en el paso 3 de forma jerárquica.
     *
     * @return array<int, array{pestudio_id: int|string, pestudio_name: string, grados: array<int, array{grado_id: int|string, grado_name: string, secciones: array<int, array{seccion_id: int|string, seccion_name: string, pevaluaciones: \Illuminate\Support\Collection}>}>}>
     */
    private function buildPevsGrouped($pevaluaciones): array
    {
        return $pevaluaciones
            ->groupBy(fn ($pev) => $pev->seccion?->grado?->pestudio?->id ?? 'general')
            ->map(function ($pestudioGroup) {
                $pestudio = $pestudioGroup->first()->seccion?->grado?->pestudio;

                return [
                    'pestudio_id' => $pestudio ? (int) $pestudio->id : 'general',
                    'pestudio_name' => $pestudio ? $pestudio->name : 'General',
                    'pestudio_code' => $pestudio ? (string) $pestudio->code : '',
                    'grados' => $pestudioGroup
                        ->groupBy(fn ($pev) => $pev->seccion?->grado?->id ?? 'general')
                        ->map(function ($gradoGroup) {
                            $grado = $gradoGroup->first()->seccion?->grado;

                            return [
                                'grado_id' => $grado ? (int) $grado->id : 'general',
                                'grado_name' => $grado ? $grado->name : 'General',
                                'secciones' => $gradoGroup
                                    ->groupBy(fn ($pev) => $pev->seccion?->id ?? 'general')
                                    ->map(function ($seccionGroup) {
                                        $seccion = $seccionGroup->first()->seccion;

                                        return [
                                            'seccion_id' => $seccion ? (int) $seccion->id : 'general',
                                            'seccion_name' => $seccion ? $seccion->name : 'General',
                                            'pevaluaciones' => $seccionGroup->values(),
                                        ];
                                    })->values()->all(),
                            ];
                        })->values()->all(),
                ];
            })
            ->sortBy('pestudio_code')
            ->values()
            ->all();
    }
}
