<?php

namespace App\Livewire\Coordinacion\Timetable;

use App\Imports\TimetableLessonsImport;
use App\Jobs\Timetable\GenerateTimetableJob;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableCalendarVersion;
use App\Models\app\Timetable\TimetableChangeLog;
use App\Models\app\Timetable\TimetableConflict;
use App\Models\app\Timetable\TimetableAbsence;
use App\Models\app\Timetable\TimetableSubstituteAssignment;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetableLessonDraftTrait;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\app\Timetable\TimetableTeacherAvailability;
use App\Services\Timetable\TimetablePublicationReadinessService;
use App\Services\Timetable\TimetableRoomEligibilityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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

    public string $strategy = TimetableCalendar::DEFAULT_STRATEGY;

    public int $shiftId = 0;

    public array $periods = [];

    public int $periodPestudioId = 0;

    public int $periodDayOfWeek = 1;

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

    /** Fuerza la recreación de los checkboxes al reiniciar el wizard. */
    public int $selectionResetToken = 0;

    public array $lessons = [];

    public bool $lessonsDirty = false;

    public ?string $lessonsSavedAt = null;

    public bool $showAddPreviewLessonModal = false;

    public ?int $addPreviewLessonPeriodId = null;

    /** Búsqueda por asignatura/docente/sección en el modal de agregar lección. */
    public string $addPreviewLessonSearch = '';

    public string $addPreviewLessonSource = 'grade';

    // Pestañas pestudio → grado → sección (PLAN-ACTIVITIES-001)
    public $activePestudioId = null;

    public $activeGradoId = null;

    public $activeSeccionId = null;

    public string $step5GradeTab = 'grade';

    public string $step5PestudioTab = 'pestudio';

    public string $step5SectionTab = 'section';

    public function showSectionFormats(): void
    {
        $this->step5SectionTab = 'formats';
    }

    public function selectStep5Pestudio(int|string $pestudioId): void
    {
        $this->activePestudioId = $pestudioId;
        $this->activeGradoId = null;
        $this->activeSeccionId = null;
        $this->step5GradeTab = 'grade';
        $this->step5PestudioTab = 'pestudio';
        $this->step5SectionTab = 'section';
    }

    public function selectStep5Grade(int|string $gradoId): void
    {
        $this->activeGradoId = $gradoId;
        $this->activeSeccionId = null;
        $this->step5GradeTab = 'grade';
        $this->step5SectionTab = 'section';
    }

    public function selectStep5Section(int|string $seccionId): void
    {
        $this->activeSeccionId = $seccionId;
        $this->step5SectionTab = 'section';
    }

    public function showPestudioFormats(): void
    {
        $this->step5GradeTab = 'pestudio';
        $this->step5PestudioTab = 'formats';
        $this->step5SectionTab = 'section';
    }

    /** Sección del grado actual que recibirá una copia de las lecciones. */
    public $replicateToSeccionId = null;

    // Mejoras del paso 3 (PLAN-ACTIVITIES-001)
    public string $step3ViewMode = 'tabs'; // 'tabs' | 'flat'

    public string $step3Search = '';

    public string $step3Sort = 'asignatura'; // asignatura | profesor | blocks

    public string $step3SortDir = 'asc';

    public $bulkShiftId = null;

    public $bulkRoomType = '';

    // Importación masiva (SPEC-TIMETABLE-001g)
    public $importFile = null;

    /** Archivo JSON de respaldo de las lessons del Paso 3. */
    public $lessonsBackupFile = null;

    /** Archivo JSON de respaldo de slots de la sección activa. */
    public $slotsBackupFile = null;

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

    public ?string $aiDryRunAnalysis = null;

    public ?string $aiDryRunAnalysisModel = null;

    public bool $aiDryRunAnalysisBusy = false;

    public bool $showAiAnalysisModal = false;

    public bool $showTeacherScheduleDialog = false;

    public ?int $teacherScheduleProfesorId = null;

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
                $this->loadLessons();
                $this->loadPublishedPreview($selectedCalendar);

                // Horario ACTIVO/publicado: mostrar su grilla por defecto (Paso 5).
                if ($this->generationState === 'published') {
                    $this->currentStep = 5;
                }
            }
        }

        $this->reloadRooms();
    }

    public function refreshWizard(): void
    {
        $selectedCalendarId = $this->calendarId ? (int) $this->calendarId : null;
        $this->currentStep = 1;
        $this->calendarName = '';
        $this->pestudioId = null;
        $this->lapsoId = null;
        $this->pescolarId = null;
        $this->periodMinutes = 60;
        $this->maxSubjectsPerPeriod = 2;
        $this->strategy = TimetableCalendar::DEFAULT_STRATEGY;
        $this->shiftId = 0;
        $this->periods = [];
        $this->lessons = [];
        $this->selectedPevs = [];
        $this->selectionResetToken++;
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = null;
        $this->availability = [];
        $this->searchProfesor = '';
        $this->step3Search = '';
        $this->bulkShiftId = null;
        $this->bulkRoomType = '';
        $this->generationState = null;
        $this->preview = null;
        $this->busy = false;
        $this->activePestudioId = null;
        $this->activeGradoId = null;
        $this->activeSeccionId = null;
        $this->replicateToSeccionId = null;
        $this->selectedProfesorId = null;
        $this->showCreateCalendarForm = false;
        $this->showEditCalendarForm = false;

        // Conserva el calendario contextual (incluido el `calendarId` de la
        // URL) y reconstruye el estado persistido como una recarga del
        // componente, sin solicitar otra página al navegador.
        $this->calendarId = $selectedCalendarId;
        $this->loadCalendars();

        $selectedCalendar = $selectedCalendarId
            ? TimetableCalendar::find($selectedCalendarId)
            : null;
        if (! $selectedCalendar) {
            $this->calendarId = null;
            $this->reloadRooms();

            return;
        }

        $this->loadCalendar($selectedCalendar);
        $this->loadLessons(true);
        $this->loadAvailability();
        $this->loadPublishedPreview($selectedCalendar);

        if ($this->generationState === 'published') {
            $this->currentStep = 5;
        }
        $this->selectedScheduleDayIndex = (int) session()->get(
            'timetable.selected_schedule_day_'.$selectedCalendar->id,
            0,
        );
        $this->reloadRooms();
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

            $active = TimetableCalendar::activeForLapso($lapso->id, $this->pestudioId ? (int) $this->pestudioId : null);
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
    public function selectCalendar($calendarId, bool $hydrateSavedSelection = true): void
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

        $this->loadLessons($hydrateSavedSelection);
        $this->loadAvailability();
        $this->loadPublishedPreview($calendar);
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
     * PLAN-TIMETABLE-002 §9 — Duplica un calendario como nuevo borrador
     * independiente, conservando la trazabilidad del calendario origen.
     */
    public function duplicateCalendar($calendarId): void
    {
        $source = TimetableCalendar::find((int) $calendarId);
        if (! $source || ! in_array($source->status, [
            TimetableCalendar::STATUS_DRAFT,
            TimetableCalendar::STATUS_ACTIVE,
            TimetableCalendar::STATUS_ARCHIVED,
        ], true)) {
            session()->flash('error', 'El calendario seleccionado no se puede duplicar.');

            return;
        }

        $copy = DB::transaction(function () use ($source) {
            $copyNumber = TimetableCalendar::query()
                ->where('lapso_id', $source->lapso_id)
                ->where('name', 'like', $source->name.' (copia%')
                ->count() + 1;
            $copyName = $source->name.' (copia v'.((int) $source->version).' · '.$copyNumber.')';

            $copy = TimetableCalendar::create([
                'lapso_id' => $source->lapso_id,
                'pescolar_id' => $source->pescolar_id,
                'pestudio_id' => $source->pestudio_id,
                'name' => $copyName,
                'period_minutes' => $source->period_minutes,
                'max_subjects_per_period' => $source->max_subjects_per_period,
                'strategy' => $source->strategy ?: TimetableCalendar::DEFAULT_STRATEGY,
                'status' => TimetableCalendar::STATUS_DRAFT,
                'version' => 0,
                'quality_score' => $source->quality_score,
                'preview_payload' => [
                    'duplicated_from' => [
                        'calendar_id' => (int) $source->id,
                        'name' => $source->name,
                        'status' => $source->status,
                        'version' => (int) $source->version,
                        'duplicated_at' => now()->toIso8601String(),
                    ],
                ],
            ]);

            $periodMap = [];
            foreach ($source->periods()->get() as $period) {
                $newPeriod = TimetablePeriod::create([
                    'calendar_id' => $copy->id, 'shift_id' => $period->shift_id,
                    'day_of_week' => $period->day_of_week, 'order_in_day' => $period->order_in_day,
                    'start_time' => $period->start_time, 'end_time' => $period->end_time, 'is_break' => $period->is_break,
                ]);
                $periodMap[$period->id] = $newPeriod->id;
            }

            foreach ($source->lessons()->with('slots')->get() as $lesson) {
                $newLesson = TimetableLesson::create([
                    'calendar_id' => $copy->id, 'pevaluacion_id' => $lesson->pevaluacion_id, 'shift_id' => $lesson->shift_id,
                    'weekly_blocks_t' => $lesson->weekly_blocks_t, 'weekly_blocks_p' => $lesson->weekly_blocks_p,
                    'room_type_required' => $lesson->room_type_required, 'is_half_group' => $lesson->is_half_group,
                    'priority' => $lesson->priority, 'locked' => $lesson->locked,
                ]);
                foreach ($lesson->slots as $slot) {
                    $newPeriodId = $periodMap[$slot->period_id] ?? null;
                    if (! $newPeriodId) {
                        continue;
                    }

                    TimetableSlot::create([
                        'calendar_id' => $copy->id, 'lesson_id' => $newLesson->id, 'period_id' => $newPeriodId,
                        'profesor_id' => $slot->profesor_id, 'seccion_id' => $slot->seccion_id,
                        'grupo_estable_id' => $slot->grupo_estable_id, 'room_id' => $slot->room_id,
                        'is_half_group' => $slot->is_half_group,
                        'locked' => $slot->locked, 'is_manual_override' => $slot->is_manual_override,
                    ]);
                }

            }

            foreach ($source->availabilities()->get() as $availability) {
                TimetableTeacherAvailability::create([
                    'calendar_id' => $copy->id,
                    'profesor_id' => $availability->profesor_id,
                    'shift_id' => $availability->shift_id,
                    'day_of_week' => $availability->day_of_week,
                    'order_in_day' => $availability->order_in_day,
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'is_available' => $availability->is_available,
                ]);
            }

            return $copy;
        });

        $this->loadCalendars();
        $this->selectCalendar($copy->id);
        session()->flash('message', 'Calendario duplicado como borrador: «'.$copy->name.'». Origen: «'.$source->name.'» v'.$source->version.'.');
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
            foreach (range(1, 5) as $day) {
                foreach ($this->estructuraFranjas($nivel, $shiftCode) as $i => $franja) {
                    $periods[] = [
                        'pestudio_id' => $pid,
                        'pestudio' => $pestudioName,
                        'day_of_week' => $day,
                        'day_label' => $this->dayLabel($day),
                        'order' => $i + 1,
                        'start' => $franja[0],
                        'end' => $franja[1],
                        'is_break' => $franja[2],
                        'label' => $pestudioName.' · '.$this->dayLabel($day).' · bloque '.($i + 1).' · '.$this->fmtMin($franja[0]).'–'.$this->fmtMin($franja[1]).($franja[2] ? ' (recreo)' : ''),
                    ];
                }
            }
        }

        if ($periods === []) {
            session()->flash('error', 'El turno seleccionado no tiene bloques en la estructura del legacy.');

            return;
        }

        $this->periods = $periods;
        $this->periodPestudioId = (int) array_key_first($this->calendarPestudios());
    }

    public function savePeriods(): void
    {
        $this->validate([
            'calendarId' => 'required',
            'shiftId' => 'required|integer|gt:0',
        ]);

        $calendar = TimetableCalendar::find($this->calendarId);
        if (! $calendar || $calendar->status !== TimetableCalendar::STATUS_DRAFT) {
            $this->notification()->error('Períodos no guardados', 'Solo puedes editar bloques de un calendario borrador.');

            return;
        }
        if ($this->periods === []) {
            $this->notification()->warning('Períodos no guardados', 'Debes conservar al menos un bloque antes de guardar.');

            return;
        }
        foreach ($this->periods as $period) {
            if ((int) ($period['end'] ?? 0) <= (int) ($period['start'] ?? 0)) {
                $this->notification()->warning('Horario inválido', 'Cada bloque debe tener una hora final posterior a la inicial.');

                return;
            }
        }
        foreach (collect($this->periods)->groupBy(fn (array $period): string => ($period['pestudio_id'] ?? 0).':'.($period['day_of_week'] ?? 0)) as $pestudioPeriods) {
            $ordered = $pestudioPeriods->sortBy('start')->values();
            for ($index = 1; $index < $ordered->count(); $index++) {
                if ((int) $ordered[$index]['start'] < (int) $ordered[$index - 1]['end']) {
                    $this->notification()->warning('Períodos solapados', 'Los bloques del mismo plan de estudio no pueden solaparse en el mismo día.');

                    return;
                }
            }
        }
        $this->normalizePeriodOrders();

        $existingPeriodIds = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->where('shift_id', $this->shiftId)
            ->pluck('id');
        if ($existingPeriodIds->isNotEmpty() && TimetableSlot::query()->whereIn('period_id', $existingPeriodIds)->exists()) {
            $this->notification()->warning('Períodos protegidos', 'No se pueden modificar estos bloques porque ya tienen asignaciones de horario.');

            return;
        }

        $this->persistPeriods();
        $this->periods = [];
        $this->notification()->success('Períodos guardados', 'Los períodos se crearon, actualizaron o eliminaron correctamente por día.');
        $this->goToStep(2);
    }

    /** Regenera los períodos de un turno ya generados (los recrea desde el pestudio). */
    public function regeneratePeriods(): void
    {
        $this->validate([
            'calendarId' => 'required',
            'shiftId' => 'required|integer|gt:0',
        ]);

        if (! TimetablePeriod::query()->where('calendar_id', $this->calendarId)->where('shift_id', $this->shiftId)->exists()) {
            session()->flash('error', 'No hay períodos que regenerar en este turno.');

            return;
        }
        $existingIds = TimetablePeriod::query()->where('calendar_id', $this->calendarId)->where('shift_id', $this->shiftId)->pluck('id');
        if (TimetableSlot::query()->whereIn('period_id', $existingIds)->exists()) {
            session()->flash('error', 'No se pueden regenerar bloques con asignaciones de horario.');

            return;
        }

        $this->loadEditablePeriods();
        if ($this->periods === []) {
            return;
        }
        $this->periods = [];
        $this->generatePeriods();
        $this->persistPeriods();
        $this->periods = [];
        session()->flash('message', 'Períodos regenerados para el turno (Lun–Vie).');
    }

    public function loadEditablePeriods(): void
    {
            $this->validate([
                'calendarId' => 'required',
                'shiftId' => 'required|integer|gt:0',
            ]);

            $hasPestudioColumn = Schema::hasColumn('timetable_periods', 'pestudio_id');
            $query = TimetablePeriod::query()
                ->where('calendar_id', $this->calendarId)
                ->where('shift_id', $this->shiftId);
            $query->orderBy('day_of_week');
            if ($hasPestudioColumn) {
                $query->orderBy('pestudio_id');
            }
            $rows = $query->orderBy('order_in_day')->get();

            if ($rows->isEmpty()) {
                session()->flash('error', 'No hay bloques guardados para este turno.');

                return;
            }

            $names = $this->calendarPestudios();
            $this->periods = $rows->map(function (TimetablePeriod $period) use ($names, $hasPestudioColumn): array {
                    $pestudioId = (int) ($period->pestudio_id ?? 0);
                    $start = $this->minutesFromTime($period->start_time);
                    $end = $this->minutesFromTime($period->end_time);

                    return [
                        'id' => (int) $period->id,
                        'pestudio_id' => $hasPestudioColumn ? $pestudioId : 0,
                        'pestudio' => $names[$pestudioId] ?? 'Plan de estudio',
                        'day_of_week' => (int) $period->day_of_week,
                        'day_label' => $this->dayLabel((int) $period->day_of_week),
                        'order' => (int) $period->order_in_day,
                        'start' => $start,
                        'end' => $end,
                        'is_break' => (bool) $period->is_break,
                        'label' => '',
                    ];
                })->values()->all();
            session()->flash('message', 'Bloques cargados para edición.');
            $this->periodPestudioId = (int) ($this->periods[0]['pestudio_id'] ?? 0);
            $this->periodDayOfWeek = (int) ($this->periods[0]['day_of_week'] ?? 1);
    }

    public function addPeriodBlock(?int $pestudioId = null): void
    {
            $pestudioId = $pestudioId ?: $this->periodPestudioId;
            $pestudios = $this->calendarPestudios();
            if (! isset($pestudios[$pestudioId])) {
                session()->flash('error', 'Selecciona un plan de estudio válido para crear el bloque.');

                return;
            }

            $pestudioName = $pestudios[$pestudioId];
            $samePestudio = array_values(array_filter(
                $this->periods,
                fn (array $period): bool => (int) ($period['pestudio_id'] ?? 0) === $pestudioId
                    && (int) ($period['day_of_week'] ?? 0) === $this->periodDayOfWeek,
            ));
            $order = count($samePestudio) + 1;
            $selectedShift = TimetableShift::find($this->shiftId);
            $shiftStart = $selectedShift?->start_time ?? $this->shiftStart;
            $start = $samePestudio !== []
                ? (int) $samePestudio[array_key_last($samePestudio)]['end']
                : $this->minutesFromTime($shiftStart);

            $this->periods[] = [
                'id' => null,
                'pestudio_id' => $pestudioId,
                'pestudio' => $pestudioName,
                'day_of_week' => $this->periodDayOfWeek,
                'day_label' => $this->dayLabel($this->periodDayOfWeek),
                'order' => $order,
                'start' => $start,
                'end' => min($start + 45, 23 * 60 + 59),
                'is_break' => false,
                'label' => '',
            ];
            $this->periodPestudioId = $pestudioId;
    }

    public function removePeriodBlock(int $index): void
    {
        if (! isset($this->periods[$index])) {
            return;
        }

        $periodId = (int) ($this->periods[$index]['id'] ?? 0);
        if ($periodId > 0 && TimetableSlot::query()->where('period_id', $periodId)->exists()) {
            session()->flash('error', 'No se puede eliminar este bloque porque tiene asignaciones de horario.');

            return;
        }

        unset($this->periods[$index]);
        $this->periods = array_values($this->periods);
        $orders = [];
        foreach ($this->periods as &$period) {
            $key = ($period['pestudio_id'] ?? 0).':'.($period['day_of_week'] ?? 0);
            $orders[$key] = ($orders[$key] ?? 0) + 1;
            $period['order'] = $orders[$key];
        }
        unset($period);
    }

    public function confirmRemovePeriodBlock(int $index): void
    {
        if (! isset($this->periods[$index])) {
            return;
        }

        $period = $this->periods[$index];
        $this->dialog()->confirm([
            'title' => 'Eliminar bloque de horario',
            'description' => 'Se eliminará «'.$period['pestudio'].' · '.$this->dayLabel((int) ($period['day_of_week'] ?? 1)).' · Bloque '.$period['order'].'» al guardar.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'removePeriodBlock',
                'params' => $index,
                'color' => 'negative',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    public function updatePeriodTime(int $index, string $field, string $value): void
    {
        if (! isset($this->periods[$index]) || ! in_array($field, ['start', 'end'], true)) {
            return;
        }

        $this->periods[$index][$field] = $this->minutesFromTime($value);
    }

    /** Persiste $this->periods (por pestudio) como períodos del calendario. */
    private function persistPeriods(): void
    {
        DB::transaction(function () {
            TimetablePeriod::query()
                ->where('calendar_id', $this->calendarId)
                ->where('shift_id', $this->shiftId)
                ->delete();

            foreach ($this->periods as $p) {
                $attributes = [
                    'calendar_id' => $this->calendarId,
                    'shift_id' => $this->shiftId,
                    'day_of_week' => (int) ($p['day_of_week'] ?? 1),
                    'order_in_day' => $p['order'],
                    'start_time' => $this->fmtMin($p['start']),
                    'end_time' => $this->fmtMin($p['end']),
                    'is_break' => $p['is_break'],
                ];
                if (Schema::hasColumn('timetable_periods', 'pestudio_id')) {
                    $attributes['pestudio_id'] = $p['pestudio_id'] ?? null;
                }

                TimetablePeriod::create($attributes);
            }
        });
    }

    private function normalizePeriodOrders(): void
    {
        $orders = [];
        foreach (collect($this->periods)->map(fn (array $period, int $index): array => [
            'index' => $index,
            'period' => $period,
        ])->groupBy(
            fn (array $item): string => ($item['period']['pestudio_id'] ?? 0).':'.($item['period']['day_of_week'] ?? 0)
        ) as $periods) {
            foreach ($periods->sortBy(fn (array $item): int => (int) ($item['period']['start'] ?? 0))->values() as $order => $item) {
                $orders[$item['index']] = $order + 1;
            }
        }

        foreach ($this->periods as $index => &$period) {
            if (isset($orders[$index])) {
                $period['order'] = $orders[$index];
            }
        }
        unset($period);
    }

    private function dayLabel(int $day): string
    {
        return [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'][$day] ?? 'Día '.$day;
    }

    private function minutesFromTime(?string $time): int
    {
        [$hours, $minutes] = array_pad(explode(':', (string) $time), 2, 0);

        return ((int) $hours * 60) + (int) $minutes;
    }

    /** Pestudios del calendario: el del calendario (si tiene) o los de sus pevs. */
    public function calendarPestudios(): array
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
        $this->loadLessons(false);
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

    /**
     * Replica las lecciones configuradas de la sección activa a otra sección
     * del mismo grado. Las nuevas lecciones empiezan sin slots asignados.
     */
    public function replicateLessonsToSection(): void
    {
        $sourceSectionId = is_numeric($this->activeSeccionId) ? (int) $this->activeSeccionId : 0;
        $targetSectionId = is_numeric($this->replicateToSeccionId) ? (int) $this->replicateToSeccionId : 0;

        if (! $this->calendarId || $sourceSectionId <= 0 || $targetSectionId <= 0 || $sourceSectionId === $targetSectionId) {
            $this->notification()->warning(
                'Sección destino requerida',
                'Selecciona una sección destino distinta de la sección activa.',
            );

            return;
        }

        $sourceSection = \App\Models\app\Academy\Seccion::query()
            ->with('grado')
            ->find($sourceSectionId);
        $targetSection = \App\Models\app\Academy\Seccion::query()
            ->with('grado')
            ->find($targetSectionId);

        if (! $sourceSection || ! $targetSection || (int) $sourceSection->grado_id !== (int) $targetSection->grado_id) {
            $this->notification()->warning(
                'Sección no compatible',
                'La sección destino debe pertenecer al mismo grado que la sección activa.',
            );

            return;
        }

        $calendar = TimetableCalendar::query()->find($this->calendarId);
        $pevaluaciones = Pevaluacion::query()
            ->with(['pensum.asignatura', 'seccion', 'profesor', 'grupoEstable'])
            ->where('lapso_id', $calendar?->lapso_id)
            ->whereIn('seccion_id', [$sourceSectionId, $targetSectionId])
            ->when($calendar?->pestudio_id, fn ($query, $pestudioId) => $query->whereHas(
                'seccion.grado',
                fn ($gradoQuery) => $gradoQuery->where('pestudio_id', $pestudioId)
            ))
            ->get()
            ->groupBy('seccion_id');
        $sourcePevaluaciones = $pevaluaciones->get($sourceSectionId, collect());
        $targetPevaluaciones = $pevaluaciones->get($targetSectionId, collect())
            ->keyBy(fn ($pev) => (int) $pev->pensum_id.'|'.(int) ($pev->grupo_estable_id ?? 0));

        $sourcePevIds = collect($this->selectedPevIds())
            ->map(fn ($id) => (int) $id)
            ->intersect($sourcePevaluaciones->pluck('id')->map(fn ($id) => (int) $id))
            ->values();
        $persistedLessons = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereIn('pevaluacion_id', $sourcePevIds)
            ->get()
            ->keyBy('pevaluacion_id');
        $sourceLessons = $sourcePevIds->mapWithKeys(function (int $pevId) use ($persistedLessons) {
            $lesson = $this->lessons[$pevId] ?? $persistedLessons->get($pevId);

            return $lesson ? [$pevId => $lesson] : [];
        });

        if ($sourceLessons->isEmpty()) {
            $this->notification()->warning(
                'Sin lecciones para replicar',
                'Selecciona las asignaturas de la sección activa antes de replicar.',
            );

            return;
        }

        $created = 0;
        $updated = 0;
        $missing = [];

        DB::transaction(function () use (
            $sourceLessons,
            $sourcePevaluaciones,
            $targetPevaluaciones,
            &$created,
            &$updated,
            &$missing
        ): void {
            foreach ($sourceLessons as $sourcePevId => $sourceLesson) {
                $sourcePev = $sourcePevaluaciones->firstWhere('id', (int) $sourcePevId);
                $targetPev = $targetPevaluaciones->get(
                    (int) ($sourcePev?->pensum_id).'|'.(int) ($sourcePev?->grupo_estable_id ?? 0)
                );

                if (! $targetPev) {
                    $missing[] = $sourcePev?->pensum?->asignatura?->name ?? 'Asignatura sin nombre';

                    continue;
                }

                $targetLesson = TimetableLesson::query()->firstOrNew([
                    'calendar_id' => $this->calendarId,
                    'pevaluacion_id' => $targetPev->id,
                ]);
                $wasExisting = $targetLesson->exists;
                $targetLesson->fill([
                    'shift_id' => data_get($sourceLesson, 'shift_id'),
                    'weekly_blocks_t' => (int) data_get($sourceLesson, 'weekly_blocks_t', 0),
                    'weekly_blocks_p' => (int) data_get($sourceLesson, 'weekly_blocks_p', 0),
                    'room_type_required' => filled(data_get($sourceLesson, 'room_type_required'))
                        ? (string) data_get($sourceLesson, 'room_type_required')
                        : null,
                    'is_half_group' => filter_var(data_get($sourceLesson, 'is_half_group', false), FILTER_VALIDATE_BOOLEAN),
                    'priority' => (int) data_get($sourceLesson, 'priority', 0),
                    'locked' => (bool) data_get($sourceLesson, 'locked', false),
                ])->save();

                $wasExisting ? $updated++ : $created++;
            }
        });

        $targetIds = $targetPevaluaciones
            ->values()
            ->filter(fn ($pev) => TimetableLesson::query()
                ->where('calendar_id', $this->calendarId)
                ->where('pevaluacion_id', $pev->id)
                ->exists())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $this->selectedPevs = array_replace($this->selectedPevs, array_fill_keys($targetIds, true));
        $this->loadLessons(false);
        $this->replicateToSeccionId = null;

        $message = "{$created} lección(es) creada(s), {$updated} actualizada(s).";
        if ($missing !== []) {
            $message .= ' Sin equivalente: '.implode(', ', array_unique($missing)).'.';
        }
        $this->notification()->success('Lecciones replicadas', $message);
    }

    public function updatedActivePestudioId($value): void
    {
        $this->activeGradoId = null;
        $this->activeSeccionId = null;
        $this->replicateToSeccionId = null;
    }

    public function updatedActiveGradoId($value): void
    {
        $this->activeSeccionId = null;
        $this->replicateToSeccionId = null;
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
     * Marca o desmarca únicamente las lecciones de una sección concreta.
     */
    public function toggleSelectAllForSection(int $seccionId): void
    {
        if ($seccionId <= 0) {
            return;
        }

        $sectionLessons = $this->allPevaluaciones()
            ->where('seccion_id', $seccionId)
            ->values();

        if ($sectionLessons->isEmpty()) {
            return;
        }

        $selectedIds = array_flip($this->selectedPevIds());
        $allSelected = $sectionLessons->every(
            fn ($pev) => isset($selectedIds[(int) $pev->id])
        );

        foreach ($sectionLessons as $pev) {
            if ($allSelected) {
                unset($this->selectedPevs[$pev->id]);
            } else {
                $this->selectedPevs[$pev->id] = true;
            }
        }

        $this->lessonsDirty = true;
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
     * Restablece a cero los bloques prácticos de todas las lessons cargadas.
     */
    public function resetPracticalBlocks(): void
    {
        if ($this->lessons === []) {
            $this->notification()->warning(
                'Sin lecciones seleccionadas',
                'Selecciona al menos una lección antes de restablecer los bloques prácticos.',
            );

            return;
        }

        foreach ($this->lessons as &$lesson) {
            $lesson['weekly_blocks_p'] = 0;
        }
        unset($lesson);

        $this->autosaveLessons();
        $this->notification()->success(
            'Bloques prácticos reiniciados',
            'Los bloques prácticos de las lecciones seleccionadas se establecieron en cero.',
        );
    }

    /**
     * Desmarca todas las lecciones del Paso 3 sin eliminar las lessons
     * persistidas del calendario.
     */
    public function resetLessonCheckboxes(): void
    {
        $this->selectedPevs = [];
        $this->lessons = [];
        $this->selectionResetToken++;
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = null;
        $this->notification()->success(
            'Selección reiniciada',
            'Se desmarcaron todas las lecciones del Paso 3. Las lessons guardadas no fueron eliminadas.',
        );
    }

    /**
     * Autoguardado (borrador): persiste las lecciones sin navegar de paso.
     */
    public function autosaveLessons(): void
    {
        if (! $this->calendarId || $this->lessonScopePevIds() === []) {
            $this->notification()->warning(
                'Sin asignaturas seleccionadas',
                'Selecciona al menos una asignatura en el paso 3.',
            );

            return;
        }

        try {
            $this->persistTimetableLessonDraft((int) $this->calendarId, $this->lessons);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->notification()->error(
                'Referencia académica inválida',
                $exception->validator->errors()->first('lessons'),
            );

            return;
        }
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
     * Lessons que pertenecen al alcance académico vigente del calendario.
     *
     * El estado de selectedPevs puede sobrevivir a un cambio de grado o a la
     * desactivación de una sección. Por eso la publicación no debe confiar
     * únicamente en esos IDs enviados por el navegador.
     */
    private function eligibleCalendarLessons(TimetableCalendar $calendar)
    {
        return $calendar->lessons()
            ->with('pevaluacion.seccion.grado', 'pevaluacion.pensum.asignatura', 'pevaluacion.profesor')
            ->whereHas('pevaluacion', function ($query) use ($calendar): void {
                $query
                    ->where('lapso_id', $calendar->lapso_id)
                    ->whereHas('seccion', function ($sectionQuery) use ($calendar): void {
                        $sectionQuery
                            ->where('status_active', 'true')
                            ->whereHas('grado', function ($gradeQuery) use ($calendar): void {
                                $gradeQuery->where('grados.status_active', 'true');

                                if ($calendar->pestudio_id) {
                                    $gradeQuery->where('pestudio_id', $calendar->pestudio_id);
                                }
                            });
                    });
            })
            ->get()
            ->keyBy('id');
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

    public function loadLessons(bool $hydrateSavedSelection = false): void
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
        $slottedPevIds = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->whereHas('slots')
            ->pluck('pevaluacion_id')
            ->map(fn ($pevId): int => (int) $pevId);
        if ($hydrateSavedSelection || $slottedPevIds->isNotEmpty()) {
            $selectedIds = ($hydrateSavedSelection
                ? $savedLessons->keys()
                : collect($this->selectedPevIds()))
                ->merge($slottedPevIds)
                ->map(fn ($pevId): int => (int) $pevId)
                ->unique()
                ->values();
            $this->selectedPevs = $selectedIds
                ->mapWithKeys(fn ($pevId): array => [$pevId => true])
                ->all();
        }
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
     * Current lesson payload is the scope for legacy callers that set lesson
     * values directly; the checkbox selection remains the normal source.
     *
     * @return array<int, int>
     */
    private function lessonScopePevIds(): array
    {
        $selected = $this->selectedPevIds();
        if ($selected !== []) {
            return $selected;
        }

        return collect($this->lessons)
            ->map(fn ($lesson, $key) => (int) ($lesson['pev_id'] ?? $key))
            ->filter()
            ->unique()
            ->values()
            ->all();
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
            $pevId = (int) ($lesson['pev_id'] ?? $lessonKey);
            $pev = $pevId > 0
                ? Pevaluacion::query()
                    ->with(['pensum.asignatura', 'seccion'])
                    ->find($pevId)
                : null;

            if (! $pev) {
                $this->notification()->error(
                    'Referencia académica inválida',
                    "La lesson intenta registrar la Pevaluacion #{$pevId}, pero ese registro no existe. "
                    .'Corrige la selección antes de guardar.',
                );

                return false;
            }

            $lessonSectionId = (int) ($lesson['seccion_id'] ?? 0);
            if ($lessonSectionId > 0 && $lessonSectionId !== (int) $pev->seccion_id) {
                $this->notification()->error(
                    'Sección académica inconsistente',
                    "La lesson de la fila {$lessonKey} apunta a la sección #{$lessonSectionId}, "
                    ."pero la Pevaluacion #{$pevId} pertenece a la sección #{$pev->seccion_id}. "
                    .'Recarga la sección antes de guardar.',
                );

                return false;
            }

            if (($lesson['weekly_blocks_t'] ?? 0) <= 0 && ($lesson['weekly_blocks_p'] ?? 0) <= 0) {
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

    /**
     * Lessons persistidas cuya Pevaluacion fue eliminada o nunca existió.
     *
     * @return list<array{id:int, pevaluacion_id:int, blocks:int}>
     */
    public function timetableOrphanLessons(): array
    {
        if (! $this->calendarId) {
            return [];
        }

        return TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereDoesntHave('pevaluacion')
            ->orderBy('id')
            ->get(['id', 'pevaluacion_id', 'weekly_blocks_t', 'weekly_blocks_p'])
            ->map(fn (TimetableLesson $lesson) => [
                'id' => (int) $lesson->id,
                'pevaluacion_id' => (int) $lesson->pevaluacion_id,
                'blocks' => (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p,
            ])
            ->values()
            ->all();
    }

    public function confirmRemoveOrphanLessons(): void
    {
        $orphans = $this->timetableOrphanLessons();

        if ($orphans === []) {
            $this->notification()->warning(
                'Sin lessons huérfanas',
                'No se encontraron referencias académicas huérfanas en este calendario.',
            );

            return;
        }

        $ids = collect($orphans)->pluck('id')->map(fn ($id) => '#'.$id)->implode(', ');
        $this->dialog()->confirm([
            'title' => '¿Retirar lessons huérfanas?',
            'description' => "Se eliminarán del calendario las lessons {$ids} y sus slots asociados. Esta acción no afecta las Pevaluaciones existentes.",
            'icon' => 'warning',
            'accept' => [
                'label' => 'Sí, retirar lessons',
                'method' => 'removeOrphanLessons',
                'color' => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
                'color' => 'secondary',
            ],
        ]);
    }

    public function removeOrphanLessons(): void
    {
        if (! $this->calendarId) {
            return;
        }

        $orphans = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereDoesntHave('pevaluacion')
            ->pluck('id');

        if ($orphans->isEmpty()) {
            $this->notification()->warning(
                'Sin cambios',
                'Las lessons huérfanas ya fueron retiradas o corregidas.',
            );

            return;
        }

        DB::transaction(function () use ($orphans): void {
            TimetableSlot::query()
                ->whereIn('lesson_id', $orphans)
                ->delete();

            TimetableLesson::query()
                ->whereIn('id', $orphans)
                ->delete();
        });

        $this->preview = null;
        $this->generationState = null;
        $this->loadLessons(true);
        $this->notification()->success(
            'Lessons huérfanas retiradas',
            $orphans->count().' lesson(s) inválida(s) fueron retiradas del calendario.',
        );
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

    /**
     * Descarga una copia autocontenida de la configuración actual de lessons.
     */
    public function downloadLessonsBackup(?int $seccionId = null)
    {
        if (! $this->calendarId || ! $seccionId || $this->lessons === []) {
            $this->notification()->warning(
                'Sin lessons para respaldar',
                'Selecciona una sección con lessons en el Paso 3 antes de descargar el respaldo.',
            );

            return null;
        }

        $calendar = TimetableCalendar::query()
            ->with(['lapso', 'pestudio', 'pescolar'])
            ->find($this->calendarId);
        if (! $calendar) {
            $this->notification()->error('Calendario no encontrado', 'No se pudo generar el respaldo.');

            return null;
        }

        $pevIds = collect($this->lessons)
            ->map(fn ($lesson, $key) => (int) ($lesson['pev_id'] ?? $key))
            ->filter()
            ->unique()
            ->values();
        $pevaluaciones = Pevaluacion::query()
            ->with(['pensum.asignatura', 'seccion.grado.pestudio', 'profesor', 'grupoEstable'])
            ->whereIn('id', $pevIds)
            ->where('seccion_id', $seccionId)
            ->where('lapso_id', $calendar->lapso_id)
            ->whereHas('seccion.grado', fn ($query) => $query->where('pestudio_id', $calendar->pestudio_id))
            ->get()
            ->keyBy('id');

        $lessonRows = collect($this->lessons)->map(function (array $lesson, $key) use ($pevaluaciones): ?array {
            $pevId = (int) ($lesson['pev_id'] ?? $key);
            $pev = $pevaluaciones->get($pevId);
            if (! $pev) {
                return null;
            }
            $asignatura = $pev?->pensum?->asignatura;
            $seccion = $pev?->seccion;
            $grado = $seccion?->grado;
            $pestudio = $grado?->pestudio;
            $profesor = $pev?->profesor;

            return [
                'pevaluacion_id' => $pevId,
                'academic_identity' => [
                    'lapso_id' => $pev?->lapso_id,
                    'pestudio_id' => $pestudio?->id,
                    'grado_id' => $grado?->id,
                    'seccion_id' => $seccion?->id,
                    'pensum_id' => $pev?->pensum_id,
                    'profesor_id' => $pev?->profesor_id,
                    'grupo_estable_id' => $pev?->grupo_estable_id,
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
                    'lapso' => $pev?->lapso?->name,
                ],
                'configuration' => [
                    'shift_id' => (int) ($lesson['shift_id'] ?? 0),
                    'weekly_blocks_t' => max(0, (int) ($lesson['weekly_blocks_t'] ?? 0)),
                    'weekly_blocks_p' => max(0, (int) ($lesson['weekly_blocks_p'] ?? 0)),
                    'room_type_required' => $lesson['room_type_required'] ?: null,
                    'is_half_group' => (bool) ($lesson['is_half_group'] ?? false),
                    'priority' => max(0, (int) ($lesson['priority'] ?? 0)),
                    'locked' => (bool) ($lesson['locked'] ?? false),
                ],
            ];
        })->filter()->values()->all();

        if ($lessonRows === []) {
            $this->notification()->warning(
                'Sin lessons para la sección',
                'La sección seleccionada no tiene lessons compatibles para respaldar.',
            );

            return null;
        }

        $backup = [
            'format' => 'cfla-timetable-lessons-backup',
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
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
            ],
            'section' => [
                'id' => $seccionId,
                'grado_id' => $lessonRows[0]['academic_identity']['grado_id'] ?? null,
                'grado' => $lessonRows[0]['labels']['grado'] ?? null,
                'name' => $lessonRows[0]['labels']['seccion'] ?? null,
                'lesson_count' => count($lessonRows),
            ],
            'lessons' => $lessonRows,
        ];

        $gradeName = Str::slug((string) ($lessonRows[0]['labels']['grado'] ?? 'grado'));
        $sectionName = Str::slug((string) ($lessonRows[0]['labels']['seccion'] ?? 'seccion'));
        $filename = 'respaldo-lessons-calendario-'.(int) $calendar->id
            .'-'.$gradeName.'-'.$sectionName.'-'.now()->format('Ymd_His').'.json';

        return response()->streamDownload(function () use ($backup): void {
            echo json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }, $filename, ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    /**
     * Valida y restaura la configuración del respaldo en el calendario activo.
     */
    public function restoreLessonsBackup(): void
    {
        if (! $this->calendarId || ! $this->lessonsBackupFile) {
            $this->notification()->warning('Respaldo requerido', 'Selecciona un archivo JSON de lessons para restaurar.');

            return;
        }

        if (strtolower((string) $this->lessonsBackupFile->getClientOriginalExtension()) !== 'json'
            || (int) $this->lessonsBackupFile->getSize() > 5 * 1024 * 1024
        ) {
            $this->notification()->error(
                'Archivo no permitido',
                'El respaldo debe ser un archivo JSON de hasta 5 MB.',
            );

            return;
        }

        try {
            $payload = json_decode($this->lessonsBackupFile->get(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->notification()->error('JSON inválido', 'El archivo no tiene un formato de respaldo válido.');

            return;
        }

        if (($payload['format'] ?? null) !== 'cfla-timetable-lessons-backup'
            || (int) ($payload['version'] ?? 0) !== 1
            || ! is_array($payload['lessons'] ?? null)
        ) {
            $this->notification()->error('Respaldo incompatible', 'El archivo no corresponde a un respaldo de lessons de CFlat.');

            return;
        }

        $calendar = TimetableCalendar::query()->find($this->calendarId);
        $backupCalendar = $payload['calendar'] ?? [];
        if (! $calendar || (int) ($backupCalendar['lapso_id'] ?? 0) !== (int) $calendar->lapso_id
            || (int) ($backupCalendar['pestudio_id'] ?? 0) !== (int) $calendar->pestudio_id
        ) {
            $this->notification()->error(
                'Calendario incompatible',
                'El respaldo pertenece a otro lapso o plan de estudio.',
            );

            return;
        }

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

        $restored = [];
        $skipped = 0;
        foreach ($payload['lessons'] as $row) {
            if (! is_array($row) || ! is_array($row['configuration'] ?? null)) {
                $skipped++;

                continue;
            }

            $identity = $row['academic_identity'] ?? [];
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
                $skipped++;

                continue;
            }

            $configuration = $row['configuration'];
            $shiftId = $this->resolveShiftId($configuration['shift_id'] ?? 0);
            if ($shiftId <= 0 || ! TimetableShift::query()->whereKey($shiftId)->exists()) {
                $shiftId = $this->defaultShiftId();
            }
            $restored[$pev->id] = [
                'pev_id' => (int) $pev->id,
                'shift_id' => $shiftId,
                'weekly_blocks_t' => max(0, (int) ($configuration['weekly_blocks_t'] ?? 0)),
                'weekly_blocks_p' => max(0, (int) ($configuration['weekly_blocks_p'] ?? 0)),
                'room_type_required' => $configuration['room_type_required'] ?? null,
                'is_half_group' => (bool) ($configuration['is_half_group'] ?? false),
                'priority' => max(0, (int) ($configuration['priority'] ?? 0)),
                'locked' => (bool) ($configuration['locked'] ?? false),
            ];
        }

        if ($restored === []) {
            $this->notification()->error('Sin coincidencias', 'No se encontraron Pevaluaciones compatibles para restaurar.');

            return;
        }

        $this->persistTimetableLessonDraft((int) $calendar->id, $restored);
        $this->selectedPevs = collect($restored)->keys()->mapWithKeys(fn ($id) => [(int) $id => true])->all();
        $this->lessons = $restored;
        $this->lessonsBackupFile = null;
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = now()->format('H:i:s');
        $message = count($restored).' lesson(s) restaurada(s).';
        if ($skipped > 0) {
            $message .= " {$skipped} fila(s) no coincidieron y fueron omitidas.";
        }
        $this->notification()->success('Respaldo restaurado', $message);
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
        $selectedPevIds = $this->selectedPevIds();
        if ($selectedPevIds === []) {
            $this->notification()->warning(
                'Sin asignaturas seleccionadas',
                'Selecciona al menos una asignatura en el paso 3.',
            );

            return;
        }

        $profesorIds = \App\Models\app\Academy\Profesor::query()
            ->whereIn('id', TimetableLesson::query()
                ->where('calendar_id', $this->calendarId)
                ->whereIn('pevaluacion_id', $selectedPevIds)
                ->with('pevaluacion:id,profesor_id')
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
        $selectedProfessorIds = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereIn('pevaluacion_id', $this->selectedPevIds())
            ->with('pevaluacion:id,profesor_id')
            ->get()
            ->pluck('pevaluacion.profesor_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->all();
        foreach ($this->availability as $profesorId => $shifts) {
            if (! in_array((int) $profesorId, $selectedProfessorIds, true)) {
                continue;
            }
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
        // Un cambio explícito del selector siempre inicia el Paso 3 sin
        // checkboxes heredados del calendario anterior.
        $this->selectedPevs = [];
        $this->lessons = [];
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = null;

        if (! $this->calendarId) {
            $this->currentStep = 1;
            $this->availability = [];
            $this->periods = [];
            $this->generationState = null;
            $this->preview = null;
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
        $this->selectCalendar($this->calendarId, false);
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
            $slots = $assignment[(string) $lesson->id]
                ?? $assignment[(int) $lesson->id]
                ?? [];

            foreach ($slots as $slot) {
                $periodId = is_array($slot) ? ($slot['period_id'] ?? null) : null;
                $period = $periodMap->get((int) $periodId);
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
                    'is_half_group' => (bool) $lesson->is_half_group,
                ];
                $cellLoad[$cellKey] = ($cellLoad[$cellKey] ?? 0) + 1;
            }
        }

        return $grid;
    }

    /**
     * Resume los huecos reales de la sección activa y las lessons que no
     * alcanzaron la cantidad de bloques configurada.
     *
     * @return array{empty_cells:int, empty_periods:list<array{shift_id:int, day:string, time:string}>, incomplete_lessons:list<array{id:int, subject:string, required:int, assigned:int, missing:int}>}
     */
    public function previewSectionGapSummary(?int $seccionId): array
    {
        if (! $this->preview || ! $seccionId || ! $this->calendarId) {
            return [
                'empty_cells' => 0,
                'empty_periods' => [],
                'incomplete_lessons' => [],
            ];
        }

        $assignment = $this->preview['assignment'] ?? [];
        $lessons = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereHas('pevaluacion', fn ($query) => $query->where('seccion_id', $seccionId))
            ->with('pevaluacion.pensum.asignatura')
            ->get();
        $sectionShiftIds = $lessons->pluck('shift_id')->filter()->unique()->map(fn ($id) => (int) $id);
        $assignedPeriods = [];
        $incompleteLessons = [];

        foreach ($lessons as $lesson) {
            $slots = $assignment[(string) $lesson->id]
                ?? $assignment[(int) $lesson->id]
                ?? [];
            $periodIds = collect($slots)->pluck('period_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
            foreach ($periodIds as $periodId) {
                $assignedPeriods[$periodId] = true;
            }

            $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
            $assigned = $periodIds->count();
            if ($required > 0 && $assigned < $required) {
                $incompleteLessons[] = [
                    'id' => (int) $lesson->id,
                    'subject' => $lesson->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura sin nombre',
                    'required' => $required,
                    'assigned' => $assigned,
                    'missing' => $required - $assigned,
                ];
            }
        }

        $periods = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->whereIn('shift_id', $sectionShiftIds->all())
            ->where('is_break', false)
            ->orderBy('shift_id')
            ->orderBy('day_of_week')
            ->orderBy('order_in_day')
            ->get();
        $dayNames = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'];
        $emptyPeriods = $periods
            ->filter(fn (TimetablePeriod $period): bool => ! isset($assignedPeriods[(int) $period->id]))
            ->map(fn (TimetablePeriod $period): array => [
                'shift_id' => (int) $period->shift_id,
                'day' => $dayNames[(int) $period->day_of_week] ?? 'Día '.$period->day_of_week,
                'time' => substr((string) $period->start_time, 0, 5).'–'.substr((string) $period->end_time, 0, 5),
            ])
            ->values()
            ->all();

        return [
            'empty_cells' => count($emptyPeriods),
            'empty_periods' => $emptyPeriods,
            'incomplete_lessons' => $incompleteLessons,
        ];
    }

    /**
     * Compara los slots llenos de las secciones que pertenecen al grado activo.
     *
     * @return array{grade:string, sections:list<array{id:int,name:string,filled_slots:int,assigned_lessons:int}>, subjects:list<array{key:string,name:string,sections:array<int,int>,min:int,max:int,delta:int,balanced:bool}>, min:int, max:int, delta:int, balanced:bool}|null
     */
    public function sectionSlotParity(): ?array
    {
        if (! $this->preview || ! is_numeric($this->activeSeccionId)) {
            return null;
        }

        $lessons = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion.seccion.grado', 'pevaluacion.pensum.asignatura')
            ->get();

        $activeLesson = $lessons->first(
            fn (TimetableLesson $lesson): bool => (int) ($lesson->pevaluacion?->seccion_id ?? 0) === (int) $this->activeSeccionId
        );
        $gradeId = $activeLesson?->pevaluacion?->seccion?->grado_id;

        if (! $gradeId) {
            return null;
        }

        $gradeSections = \App\Models\app\Academy\Seccion::query()
            ->where('grado_id', $gradeId)
            ->where('status_active', 'true')
            ->orderBy('name')
            ->get(['id', 'name']);
        $gradeLessons = $lessons->filter(
            fn (TimetableLesson $lesson): bool => (int) ($lesson->pevaluacion?->seccion?->grado_id ?? 0) === (int) $gradeId
        );
        $assignment = $this->preview['assignment'] ?? [];

        $sections = $gradeSections
            ->map(function ($section) use ($gradeLessons, $assignment): array {
                $sectionLessons = $gradeLessons->filter(
                    fn (TimetableLesson $lesson): bool => (int) ($lesson->pevaluacion?->seccion_id ?? 0) === (int) $section->id
                );
                $filledSlots = 0;
                $assignedLessons = 0;

                foreach ($sectionLessons as $lesson) {
                    $slots = $assignment[(string) $lesson->id]
                        ?? $assignment[(int) $lesson->id]
                        ?? [];
                    $slotCount = count($slots);
                    $filledSlots += $slotCount;
                    $assignedLessons += $slotCount > 0 ? 1 : 0;
                }

                return [
                    'id' => (int) $section->id,
                    'name' => $section->name ?? 'Sección '.$section->id,
                    'filled_slots' => $filledSlots,
                    'assigned_lessons' => $assignedLessons,
                ];
            })
            ->values();

        if ($sections->isEmpty()) {
            return null;
        }

        $subjectGroups = $gradeLessons->groupBy(
            fn (TimetableLesson $lesson): string => (string) (
                $lesson->pevaluacion?->pensum_id
                ?? 'subject-'.strtolower(trim((string) ($lesson->pevaluacion?->pensum?->asignatura?->name ?? 'sin-asignatura')))
            )
        );
        $subjects = $subjectGroups->map(function ($subjectLessons, string $subjectKey) use ($sections, $assignment): array {
            $sectionSlots = $sections->mapWithKeys(function (array $section) use ($subjectLessons, $assignment): array {
                $slots = 0;
                foreach ($subjectLessons as $lesson) {
                    if ((int) ($lesson->pevaluacion?->seccion_id ?? 0) !== $section['id']) {
                        continue;
                    }

                    $slots += count(
                        $assignment[(string) $lesson->id]
                            ?? $assignment[(int) $lesson->id]
                            ?? []
                    );
                }

                return [$section['id'] => $slots];
            })->all();
            $counts = collect($sectionSlots);
            $min = (int) $counts->min();
            $max = (int) $counts->max();

            return [
                'key' => $subjectKey,
                'name' => $subjectLessons->first()?->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura sin nombre',
                'sections' => $sectionSlots,
                'min' => $min,
                'max' => $max,
                'delta' => $max - $min,
                'balanced' => $max === $min,
            ];
        })->sortBy('name')->values()->all();

        $counts = $sections->pluck('filled_slots');
        $min = (int) $counts->min();
        $max = (int) $counts->max();

        return [
            'grade' => $activeLesson->pevaluacion?->seccion?->grado?->name ?? 'Grado activo',
            'sections' => $sections->all(),
            'subjects' => $subjects,
            'min' => $min,
            'max' => $max,
            'delta' => $max - $min,
            'balanced' => $max === $min,
        ];
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
        $originalSlots = $slots;
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
        if (! $sourcePeriod) {
            $this->notification()->error(
                'Movimiento no válido',
                'El período de origen no existe.',
            );

            return;
        }
        $hasShiftMismatch = $sourcePeriod->shift_id !== $targetPeriod->shift_id
            || $lesson->shift_id !== $targetPeriod->shift_id;

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
                    ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado', 'pevaluacion.profesor'])
                    ->first();
                if (! $otherLesson?->pevaluacion) {
                    continue;
                }

                $sameSection = (int) $otherLesson->pevaluacion->seccion_id
                    === (int) $lesson->pevaluacion->seccion_id;
                $bothAllowHalfGroup = (bool) $lesson->is_half_group
                    && (bool) $otherLesson->is_half_group;
                $isSectionSwap = $sameSection && ! $bothAllowHalfGroup;
                $sameTeacher = (int) $otherLesson->pevaluacion->profesor_id
                    === (int) $lesson->pevaluacion->profesor_id;

                // Una celda compartible tiene prioridad sobre el intercambio:
                // dos lessons de medio grupo deben convivir en el destino,
                // incluso si pertenecen al mismo docente.
                if ($bothAllowHalfGroup) {
                    continue;
                }

                // Cuando se intercambian las celdas, dos lessons del mismo
                // docente no generan doble reserva: cada una ocupa el período
                // que la otra deja libre.
                if ($sameTeacher || $isSectionSwap) {
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
            $this->recordPreviewChange('swap_preview_lessons', $lessonId, $originalSlots, [
                'lesson' => $slots,
                'swap_lesson_id' => $swapLessonId,
                'swap_slots' => $swapSlots,
            ]);
            if ($hasShiftMismatch) {
                $this->notification()->warning(
                    'Lecciones intercambiadas con advertencia',
                    'El intercambio se realizó, pero una o ambas lecciones quedaron en un turno distinto al configurado.',
                );
            } else {
                $this->notification()->success(
                    'Lecciones intercambiadas',
                    'La lección arrastrada y la lección de la sección intercambiaron sus períodos.',
                );
            }

            return;
        }

        $slots[$slotIndex]['period_id'] = $newPeriodId;
        $assignment[$lessonKey] = array_values($slots);
        $this->preview['assignment'] = $assignment;
        $this->preview['manual_override'] = true;
        $this->preview['assignment_source'] = 'manual_preview';
        $this->recordPreviewChange('move_preview_lesson', $lessonId, $originalSlots, $slots);
        if ($hasShiftMismatch) {
            $this->notification()->warning(
                'Lección reubicada con advertencia',
                'La lección se movió a un período de otro turno distinto al configurado.',
            );
        } else {
            $this->notification()->success(
                'Lección reubicada',
                'La lección se movió correctamente en el preview.',
            );
        }
    }

    /**
     * Quita una lección completa del preview sin eliminar su configuración
     * persistida. Sus celdas quedan disponibles para agregar otra lección.
     */
    public function removePreviewLesson(int $lessonId): void
    {
        if (! $this->preview || $lessonId <= 0) {
            return;
        }

        $lesson = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion.pensum.asignatura')
            ->find($lessonId);

        if (! $lesson) {
            $this->notification()->error(
                'Lección no encontrada',
                'La lección ya no está disponible en este calendario.',
            );

            return;
        }

        $assignment = $this->preview['assignment'] ?? [];
        $lessonKey = (string) $lessonId;
        $lessonSlots = $assignment[$lessonKey] ?? $assignment[$lessonId] ?? [];
        $hadAssignment = array_key_exists($lessonKey, $assignment)
            || array_key_exists($lessonId, $assignment);

        unset($assignment[$lessonKey], $assignment[$lessonId]);

        if (! $hadAssignment) {
            $this->notification()->warning(
                'Lección ya retirada',
                'La lección no tenía asignaciones en el preview.',
            );

            return;
        }

        $unassigned = collect($this->preview['unassigned'] ?? [])
            ->push($lessonId)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->preview['assignment'] = $assignment;
        $this->preview['unassigned'] = $unassigned;
        $this->preview['manual_override'] = true;
        $this->preview['assignment_source'] = 'manual_preview';

        $subject = $lesson->pevaluacion?->pensum?->asignatura?->name ?? 'La lección';
        $this->notification()->success(
            'Lección retirada',
            $subject.' quedó sin asignar en el preview.',
        );
    }

    public function confirmRemovePreviewLesson(int $lessonId): void
    {
        $lesson = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion.pensum.asignatura')
            ->find($lessonId);

        if (! $lesson) {
            $this->notification()->error(
                'Lección no encontrada',
                'La lección ya no está disponible en este calendario.',
            );

            return;
        }

        $subject = $lesson->pevaluacion?->pensum?->asignatura?->name ?? 'esta lección';
        $this->dialog()->confirm([
            'title' => '¿Retirar lección del preview?',
            'description' => '«'.$subject.'» quedará sin asignar y sus celdas volverán a mostrar el botón +. La configuración guardada no se eliminará.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Sí, retirar',
                'method' => 'removePreviewLesson',
                'params' => $lessonId,
                'color' => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
                'color' => 'secondary',
            ],
        ]);
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
        $this->addPreviewLessonSearch = '';
        $this->addPreviewLessonSource = 'grade';
        $this->showAddPreviewLessonModal = true;
    }

    public function closeAddPreviewLessonModal(): void
    {
        $this->showAddPreviewLessonModal = false;
        $this->addPreviewLessonPeriodId = null;
        $this->addPreviewLessonSearch = '';
        $this->addPreviewLessonSource = 'grade';
    }

    /** Período destino del modal de «agregar lección» (para mostrar su contexto). */
    public function addPreviewPeriod(): ?TimetablePeriod
    {
        if (! $this->addPreviewLessonPeriodId || ! $this->calendarId) {
            return null;
        }

        return TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->with('shift')
            ->find($this->addPreviewLessonPeriodId);
    }

    /**
     * Lecciones sin asignar del preview, filtradas por búsqueda y ordenadas de
     * modo que primero aparezcan las del MISMO turno del período destino.
     */
    public function availablePreviewLessons()
    {
        $period = $this->addPreviewPeriod();
        $search = mb_strtolower(trim($this->addPreviewLessonSearch));

        $assignment = collect($this->preview['assignment'] ?? []);
        $unassignedIds = collect($this->preview['unassigned'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();
        $query = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with([
                'pevaluacion.pensum.asignatura',
                'pevaluacion.seccion.grado',
                'pevaluacion.profesor',
            ]);

        if ($this->addPreviewLessonSource === 'current') {
            $lessons = $query->get();
        } elseif ($this->addPreviewLessonSource === 'grade' && is_numeric($this->activeSeccionId)) {
            $sectionId = (int) $this->activeSeccionId;
            $registeredPevIds = $query->get()->pluck('pevaluacion_id');
            $lessons = $query
                ->whereHas('pevaluacion', fn ($pevaluacion) => $pevaluacion->where('seccion_id', $sectionId))
                ->get();
            $lessons = $lessons->concat(
                Pevaluacion::query()
                    ->where('lapso_id', TimetableCalendar::find($this->calendarId)?->lapso_id)
                    ->where('seccion_id', $sectionId)
                    ->whereNotIn('id', $registeredPevIds)
                    ->with('pensum.asignatura', 'seccion.grado', 'profesor')
                    ->get()
                    ->map(fn ($pev) => (object) [
                        'id' => null,
                        'pevaluacion_id' => $pev->id,
                        'is_grade_candidate' => true,
                        'pevaluacion' => $pev,
                        'shift_id' => $period?->shift_id,
                    ]),
            );
        } else {
            $lessons = $query->whereIn('id', $unassignedIds)->get();
        }

        return $lessons
            ->when($search !== '', fn ($c) => $c->filter(function ($l) use ($search) {
                $haystack = mb_strtolower(implode(' ', [
                    $l->pevaluacion?->pensum?->asignatura?->name ?? '',
                    $l->pevaluacion?->profesor?->lastname ?? '',
                    $l->pevaluacion?->profesor?->name ?? '',
                    $l->pevaluacion?->seccion?->grado?->name ?? '',
                    $l->pevaluacion?->seccion?->name ?? '',
                ]));

                return str_contains($haystack, $search);
            }))
            ->sortBy(fn ($l) => sprintf(
                '%d|%s|%s',
                ($period && (int) $l->shift_id === (int) $period->shift_id) ? 0 : 1,
                $l->pevaluacion?->seccion?->grado?->name ?? '',
                $l->pevaluacion?->pensum?->asignatura?->name ?? '',
            ))
            ->values();
    }

    public function addPreviewPevaluacion(int $pevaluacionId): void
    {
        if (! $this->calendarId || ! $this->addPreviewLessonPeriodId) {
            return;
        }

        $calendar = TimetableCalendar::find($this->calendarId);
        $period = $this->addPreviewPeriod();
        $pev = Pevaluacion::query()
            ->with('pensum.asignatura')
            ->where('lapso_id', $calendar?->lapso_id)
            ->find($pevaluacionId);

        if (! $calendar || ! $period || ! $pev) {
            $this->notification()->error('Asignatura no agregada', 'La Pevaluacion seleccionada no pertenece al calendario actual.');

            return;
        }

        $minutes = max(1, (int) $calendar->period_minutes);
        $asignatura = $pev->pensum?->asignatura;
        $lesson = TimetableLesson::firstOrCreate(
            ['calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id],
            [
                'shift_id' => $period->shift_id,
                'weekly_blocks_t' => (int) ceil(((int) ($asignatura?->hour_t_week ?? 0)) * 60 / $minutes),
                'weekly_blocks_p' => (int) ceil(((int) ($asignatura?->hour_p_week ?? 0)) * 60 / $minutes),
                'room_type_required' => null,
                'is_half_group' => false,
                'priority' => 0,
                'locked' => false,
            ],
        );

        $this->addPreviewLesson((int) $lesson->id);
    }

    public function addPreviewLesson(int $lessonId): void
    {
        $period = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->whereKey($this->addPreviewLessonPeriodId)
            ->first();
        $lesson = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado', 'pevaluacion.profesor'])
            ->find($lessonId);

        if (! $period || $period->is_break || ! $lesson?->pevaluacion) {
            $this->notification()->error('Lección no agregada', 'El período o la lección seleccionada no es válido.');

            return;
        }

        $hasShiftMismatch = (int) $lesson->shift_id !== (int) $period->shift_id;

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
                ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado', 'pevaluacion.profesor'])
                ->find($targetLessonId);
            if (! $targetLesson?->pevaluacion) {
                continue;
            }

            $sameTeacher = (int) $targetLesson->pevaluacion->profesor_id === (int) $lesson->pevaluacion->profesor_id;
            $bothAllowHalfGroup = (bool) $lesson->is_half_group && (bool) $targetLesson->is_half_group;
            if ($sameTeacher && ! $bothAllowHalfGroup) {
                $this->notification()->error(
                    'Conflicto de docente',
                    $this->teacherConflictMessage($lesson, $targetLesson, $period),
                );

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
        $this->recordPreviewChange('add_preview_lesson', $lesson->id, [], $lessonSlots);
        $this->closeAddPreviewLessonModal();
        if ($hasShiftMismatch) {
            $this->notification()->warning(
                'Lección agregada con advertencia',
                'La lección se agregó a un período de otro turno distinto al configurado.',
            );
        } else {
            $this->notification()->success('Lección agregada', 'La lección se agregó al período seleccionado.');
        }
    }

    private function teacherConflictMessage(
        TimetableLesson $lesson,
        TimetableLesson $occupiedLesson,
        TimetablePeriod $period,
    ): string {
        $teacher = trim(($lesson->pevaluacion?->profesor?->lastname ?? '').' '.($lesson->pevaluacion?->profesor?->name ?? ''));
        $subject = $lesson->pevaluacion?->pensum?->asignatura?->name ?? 'La nueva lección';
        $occupiedSubject = $occupiedLesson->pevaluacion?->pensum?->asignatura?->name ?? 'otra lección';
        $occupiedSection = $occupiedLesson->pevaluacion?->seccion?->name ?? 'otra sección';
        $occupiedGrade = $occupiedLesson->pevaluacion?->seccion?->grado?->name ?? 'otro grado';

        return sprintf(
            '%s ya tiene asignada «%s» en %s, sección %s, para %s. Mueve la nueva lección «%s» a otro período o cambia una de las dos asignaciones.',
            $teacher !== '' ? $teacher : 'El docente',
            $occupiedSubject,
            $occupiedGrade,
            $occupiedSection,
            $period->period_label,
            $subject,
        );
    }

    public function openAiDryRunDialog(): void
        {
                $calendar = $this->calendarId ? TimetableCalendar::query()->find($this->calendarId) : null;
                if (! $calendar || $calendar->status !== TimetableCalendar::STATUS_ACTIVE) {
                    $this->notification()->warning(
                        'Horario no publicado',
                        'El análisis IA solo está disponible para horarios publicados.',
                    );

                return;
            }

            $this->dialog()->confirm([
                'title' => 'Analizar resultado con IA',
                'description' => 'Se enviará a OpenRouter el contexto auditable del calendario seleccionado, incluyendo asignaciones, conflictos, disponibilidad y estructura real de las tablas. La IA solo podrá proponer mejoras sustentadas en esos datos.',
                'icon' => 'question',
                'accept' => [
                    'label' => 'Analizar resultado',
                    'method' => 'analyzeDryRunWithAi',
                ],
                'reject' => [
                    'label' => 'Cancelar',
                ],
            ]);
        }

        public function analyzeDryRunWithAi(): void
        {
                $this->aiDryRunAnalysisBusy = true;

                try {
                    $calendar = TimetableCalendar::query()
                        ->with(['lapso', 'pestudio', 'pescolar'])
                        ->find($this->calendarId);
                    if (! $calendar || $calendar->status !== TimetableCalendar::STATUS_ACTIVE) {
                        $this->notification()->warning('Horario no publicado', 'El análisis IA solo está disponible para horarios publicados.');

                        return;
                    }
                $lessons = $calendar->lessons()
                    ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado.pestudio', 'pevaluacion.profesor', 'shift'])
                    ->get();
                $slotColumns = Schema::getColumnListing('timetable_slots');
                $assignmentColumns = array_values(array_intersect(
                    ['lesson_id', 'period_id', 'room_id', 'is_practical'],
                    $slotColumns,
                ));
                $assignment = TimetableSlot::query()
                    ->where('calendar_id', $calendar->id)
                    ->get($assignmentColumns)
                    ->groupBy('lesson_id')
                    ->map(fn ($slots) => $slots->map(fn (TimetableSlot $slot): array => [
                        'period_id' => (int) $slot->period_id,
                        'room_id' => $slot->room_id ? (int) $slot->room_id : null,
                        'is_practical' => in_array('is_practical', $assignmentColumns, true)
                            && (bool) ($slot->is_practical ?? false),
                    ])->values()->all())
                    ->all();
                $readiness = app(TimetablePublicationReadinessService::class)->evaluate($calendar, [
                    'assignment' => $assignment,
                ]);
                $lessonContext = $lessons->map(function (TimetableLesson $lesson) use ($assignment): array {
                    $pev = $lesson->pevaluacion;

                    return [
                        'lesson_id' => (int) $lesson->id,
                        'pevaluacion_id' => (int) $lesson->pevaluacion_id,
                        'weekly_blocks_t' => (int) $lesson->weekly_blocks_t,
                        'weekly_blocks_p' => (int) $lesson->weekly_blocks_p,
                        'shift_id' => $lesson->shift_id ? (int) $lesson->shift_id : null,
                        'room_type_required' => $lesson->room_type_required,
                        'is_half_group' => (bool) $lesson->is_half_group,
                        'assignment' => $assignment[(string) $lesson->id] ?? [],
                        'subject' => $pev?->pensum?->asignatura?->name,
                        'teacher' => $pev?->profesor
                            ? trim(($pev->profesor->lastname ?? '').', '.($pev->profesor->name ?? ''))
                            : null,
                        'section_id' => $pev?->seccion_id ? (int) $pev->seccion_id : null,
                        'section' => $pev?->seccion?->name,
                        'grade_id' => $pev?->seccion?->grado_id ? (int) $pev->seccion->grado_id : null,
                        'grade' => $pev?->seccion?->grado?->name,
                        'pestudio' => $pev?->seccion?->grado?->pestudio?->name,
                    ];
                })->all();

                $compactTable = function (string $tableName, array $preferredColumns, int $limit = 100) use ($calendar): array {
                    if (! Schema::hasTable($tableName)) {
                        return ['exists' => false, 'columns' => [], 'rows' => []];
                    }

                    $columns = Schema::getColumnListing($tableName);
                    $selectedColumns = array_values(array_intersect($preferredColumns, $columns));
                    if ($selectedColumns === []) {
                        return ['exists' => true, 'columns' => $columns, 'rows' => []];
                    }

                    $query = DB::table($tableName)->select($selectedColumns);
                    if (in_array('calendar_id', $columns, true)) {
                        $query->where('calendar_id', $calendar->id);
                    }
                    if (in_array('created_at', $columns, true)) {
                        $query->latest('created_at');
                    } elseif (in_array('id', $columns, true)) {
                        $query->latest('id');
                    }

                    return [
                        'exists' => true,
                        'columns' => $columns,
                        'rows' => $query->limit($limit)->get()->map(fn ($row): array => (array) $row)->all(),
                    ];
                };

                $tableContext = [
                    'timetable_periods' => $compactTable('timetable_periods', [
                        'id', 'calendar_id', 'shift_id', 'day_of_week', 'order_in_day',
                        'start_time', 'end_time', 'period_label', 'is_break',
                    ], 300),
                    'timetable_conflicts' => $compactTable('timetable_conflicts', [
                        'id', 'calendar_id', 'lesson_id', 'period_id', 'type', 'severity',
                        'message', 'resolved_at', 'created_at',
                    ], 200),
                    'timetable_teacher_availability' => $compactTable('timetable_teacher_availability', [
                        'id', 'calendar_id', 'profesor_id', 'shift_id', 'day_of_week',
                        'period_id', 'is_available',
                    ], 300),
                    'timetable_calendar_versions' => $compactTable('timetable_calendar_versions', [
                        'id', 'calendar_id', 'version', 'status', 'quality_score', 'created_at',
                    ], 10),
                    'timetable_change_logs' => $compactTable('timetable_change_logs', [
                        'id', 'calendar_id', 'user_id', 'action', 'description', 'created_at',
                    ], 30),
                    'timetable_absences' => $compactTable('timetable_absences', [
                        'id', 'calendar_id', 'profesor_id', 'starts_at', 'ends_at', 'status',
                    ], 100),
                    'timetable_substitute_assignments' => $compactTable('timetable_substitute_assignments', [
                        'id', 'absence_id', 'substitute_profesor_id', 'starts_at', 'ends_at', 'status',
                    ], 100),
                ];

                $sectionMetrics = collect($lessonContext)->groupBy('section_id')->map(
                    fn ($items, $sectionId): array => [
                        'section_id' => $sectionId ? (int) $sectionId : null,
                        'section' => $items->first()['section'] ?? null,
                        'grade' => $items->first()['grade'] ?? null,
                        'lessons' => $items->count(),
                        'required_blocks' => $items->sum(fn (array $item): int => $item['weekly_blocks_t'] + $item['weekly_blocks_p']),
                        'assigned_blocks' => $items->sum(fn (array $item): int => count(collect($item['assignment'])->pluck('period_id')->filter()->unique())),
                    ],
                )->values()->all();

                $context = [
                    'context_policy' => [
                        'source' => 'published timetable_slots and deterministic Laravel aggregates',
                        'raw_historical_rows_excluded' => true,
                        'table_row_limits' => ['periods' => 300, 'conflicts' => 200, 'availability' => 300, 'versions' => 10, 'logs' => 30],
                    ],
                    'calendar' => [
                        'id' => (int) $calendar->id,
                        'name' => $calendar->name,
                        'status' => $calendar->status,
                        'period_minutes' => (int) $calendar->period_minutes,
                        'max_subjects_per_period' => (int) $calendar->max_subjects_per_period,
                        'quality_score' => $calendar->quality_score,
                    ],
                    'academic_context' => [
                        'lapso' => $calendar->lapso ? ['id' => (int) $calendar->lapso->id, 'name' => $calendar->lapso->name] : null,
                        'pestudio' => $calendar->pestudio ? ['id' => (int) $calendar->pestudio->id, 'name' => $calendar->pestudio->name] : null,
                        'pescolar' => $calendar->pescolar ? ['id' => (int) $calendar->pescolar->id, 'name' => $calendar->pescolar->name] : null,
                    ],
                    'published_schedule' => true,
                    'persisted_assignment_source' => 'timetable_slots',
                    'assignment' => $assignment,
                    'readiness' => $readiness,
                    'lessons_with_context' => $lessonContext,
                    'section_metrics' => $sectionMetrics,
                    'tables' => $tableContext,
                    'model_definitions' => [
                        'TimetableCalendar' => ['table' => 'timetable_calendars', 'purpose' => 'Calendario y configuración global del horario.'],
                        'TimetablePeriod' => ['table' => 'timetable_periods', 'purpose' => 'Períodos por día, turno, hora y receso.'],
                        'TimetableLesson' => ['table' => 'timetable_lessons', 'purpose' => 'Carga semanal, turno, aula requerida y restricciones de cada lección.'],
                        'TimetableSlot' => ['table' => 'timetable_slots', 'purpose' => 'Asignación persistida de lección, período, docente, sección y aula.'],
                        'TimetableTeacherAvailability' => ['table' => 'timetable_teacher_availability', 'purpose' => 'Disponibilidad declarada de docentes.'],
                        'TimetableConflict' => ['table' => 'timetable_conflicts', 'purpose' => 'Conflictos detectados durante generación o publicación.'],
                    ],
                ];

                $systemPrompt = <<<'PROMPT'
Eres un auditor experto de horarios escolares y optimización de restricciones.
Analiza exclusivamente el JSON del horario publicado entregado por el sistema.
REGLAS OBLIGATORIAS:
1. No inventes docentes, asignaturas, secciones, períodos, aulas, horas, conflictos, restricciones ni estadísticas.
2. Si un dato no está presente, indica literalmente "dato no disponible" y no lo completes por inferencia.
3. Distingue hechos observados, riesgos derivados directamente de los datos y propuestas.
4. Cada propuesta debe citar los IDs y valores concretos del contexto que la justifican.
5. No recomiendes cambios de publicación; el calendario ya está publicado. Propón únicamente mejoras operativas verificables.
6. Respeta las reglas del sistema: recesos no son períodos asignables, los docentes no pueden duplicarse en el mismo período, las secciones no pueden duplicarse salvo compatibilidad explícita de medio grupo y las aulas deben respetar disponibilidad/capacidad/tipo.
7. No cambies datos ni simules una nueva solución; propone acciones manuales verificables.

RESPONDE EN ESPAÑOL con esta estructura:
1. Diagnóstico ejecutivo.
2. Hechos comprobables con IDs.
3. Conflictos bloqueantes y causa.
4. Lecciones incompletas y déficit de bloques.
5. Opciones de mejora priorizadas, cada una con impacto, riesgo y datos que la sustentan.
6. Orden recomendado de acciones.
7. Datos faltantes o límites del diagnóstico.
PROMPT;

                $userMessage = "Analiza este horario publicado del calendario {$calendar->id}. No uses conocimiento externo:\n\n"
                    .json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                $maxContextCharacters = 180000;
                if (strlen($userMessage) > $maxContextCharacters) {
                    $this->notification()->warning(
                        'Contexto demasiado grande',
                        'El calendario supera el límite seguro de análisis. Usa un análisis por sección o reduce el alcance.',
                    );

                    return;
                }

                $result = app(\App\Services\OpenRouterService::class)->ask(
                    $systemPrompt,
                    $userMessage,
                    [
                        'max_tokens' => 6000,
                        'temperature' => 0.1,
                        'timeout' => 180,
                    ],
                );

                if (! $result['success']) {
                    $this->notification()->error('Análisis IA no disponible', $result['error'] ?? 'OpenRouter no devolvió una respuesta.');

                    return;
                }

                $this->aiDryRunAnalysis = $result['content'];
                $this->aiDryRunAnalysisModel = $result['model'];
                $this->showAiAnalysisModal = true;
                $this->notification()->success('Análisis completado', 'La propuesta fue generada usando únicamente el contexto del calendario.');
            } finally {
                $this->aiDryRunAnalysisBusy = false;
            }
        }

    public function runDryRun(): void
    {
        if (! $this->calendarId) {
            session()->flash('error', 'Crea el calendario primero.');

            return;
        }

        $this->aiDryRunAnalysis = null;
        $this->aiDryRunAnalysisModel = null;
        $this->showAiAnalysisModal = false;
        $this->busy = true;
        $this->generationState = 'generating';

        try {
            $selectedPevIds = $this->selectedPevIds();
            GenerateTimetableJob::dispatchSync(
                $this->calendarId,
                dryRun: true,
                pevaluacionIds: $selectedPevIds ?: null,
            );
            $calendar = TimetableCalendar::find($this->calendarId);
            $this->preview = $calendar?->preview_payload;
            if ($this->preview) {
                $this->preview['generated_assignment'] = $this->preview['assignment'] ?? [];
                $this->preview['generated_unassigned'] = $this->preview['unassigned'] ?? [];
                $this->preview['preview_history'] = [];
            }
            $this->generationState = 'preview_ready';
        } finally {
            $this->busy = false;
        }
    }

    /**
     * Descarga un informe JSON auditable del último dry-run.
     */
    public function downloadDryRunResult(?int $seccionId = null)
    {
        if (! $this->calendarId || ! $this->preview || $this->generationState !== 'preview_ready') {
            $this->notification()->warning(
                'Sin resultado disponible',
                'Ejecuta primero «Previsualizar (dry-run)» para descargar el informe.',
            );

            return null;
        }

        $auditSectionId = $seccionId ?? (is_numeric($this->activeSeccionId)
            ? (int) $this->activeSeccionId
            : null);

        $calendar = TimetableCalendar::query()
            ->with(['lapso', 'pestudio', 'pescolar'])
            ->find($this->calendarId);
        if (! $calendar) {
            $this->notification()->error('Calendario no encontrado', 'No se pudo generar el informe del dry-run.');

            return null;
        }

        $readiness = $this->publicationReadiness();
        $lessons = $calendar->lessons()
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado.pestudio', 'pevaluacion.profesor', 'shift'])
            ->get()
            ->keyBy('id');
        $periods = $calendar->periods()->get()->keyBy('id');
        $assignment = collect($this->preview['assignment'] ?? []);
        $auditLessons = $lessons->values()->map(function (TimetableLesson $lesson): array {
            return [
                'record' => $lesson->toArray(),
                'pevaluacion' => $lesson->pevaluacion?->toArray(),
                'pensum' => $lesson->pevaluacion?->pensum?->toArray(),
                'asignatura' => $lesson->pevaluacion?->pensum?->asignatura?->toArray(),
                'seccion' => $lesson->pevaluacion?->seccion?->toArray(),
                'grado' => $lesson->pevaluacion?->seccion?->grado?->toArray(),
                'pestudio' => $lesson->pevaluacion?->seccion?->grado?->pestudio?->toArray(),
                'profesor' => $lesson->pevaluacion?->profesor?->toArray(),
                'grupo_estable' => $lesson->pevaluacion?->grupoEstable?->toArray(),
                'shift' => $lesson->shift?->toArray(),
            ];
        })->all();
        $auditTables = [
            'timetable_calendars' => [$calendar->toArray()],
            'timetable_periods' => $calendar->periods()->get()->map->toArray()->all(),
            'timetable_shifts' => TimetableShift::query()->orderBy('start_time')->get()->map->toArray()->all(),
            'timetable_rooms' => TimetableRoom::query()->get()->map->toArray()->all(),
            'timetable_lessons' => $auditLessons,
            'timetable_slots' => $calendar->slots()->with(['period', 'room', 'profesor', 'seccion', 'grupoEstable'])->get()->map(function (TimetableSlot $slot): array {
                return [
                    'record' => $slot->toArray(),
                    'period' => $slot->period?->toArray(),
                    'room' => $slot->room?->toArray(),
                    'profesor' => $slot->profesor?->toArray(),
                    'seccion' => $slot->seccion?->toArray(),
                    'grupo_estable' => $slot->grupoEstable?->toArray(),
                ];
            })->all(),
            'timetable_teacher_availability' => $calendar->availabilities()->with(['shift', 'profesor'])->get()->map(function (TimetableTeacherAvailability $availability): array {
                return [
                    'record' => $availability->toArray(),
                    'shift' => $availability->shift?->toArray(),
                    'profesor' => $availability->profesor?->toArray(),
                ];
            })->all(),
            'timetable_conflicts' => TimetableConflict::query()
                ->where('calendar_id', $calendar->id)
                ->get()
                ->map->toArray()
                ->all(),
            'timetable_calendar_versions' => $calendar->versions()->get()->map->toArray()->all(),
            'timetable_change_logs' => $calendar->changeLogs()->get()->map->toArray()->all(),
            'timetable_absences' => TimetableAbsence::query()
                ->where('calendar_id', $calendar->id)
                ->get()
                ->map->toArray()
                ->all(),
            'timetable_substitute_assignments' => TimetableSubstituteAssignment::query()
                ->whereHas('absence', fn ($query) => $query->where('calendar_id', $calendar->id))
                ->with(['absence', 'slot', 'substituteProfesor'])
                ->get()
                ->map(function (TimetableSubstituteAssignment $assignment): array {
                    return [
                        'record' => $assignment->toArray(),
                        'absence' => $assignment->absence?->toArray(),
                        'slot' => $assignment->slot?->toArray(),
                        'substitute_profesor' => $assignment->substituteProfesor?->toArray(),
                    ];
                })
                ->all(),
        ];

        $assignmentDetails = $assignment->map(function ($slots, $lessonId) use ($lessons, $periods): array {
            $lesson = $lessons->get((int) $lessonId);
            $pev = $lesson?->pevaluacion;
            $slots = collect($slots)->map(function (array $slot) use ($periods): array {
                $period = $periods->get((int) ($slot['period_id'] ?? 0));

                return [
                    'period_id' => (int) ($slot['period_id'] ?? 0),
                    'period' => $period ? [
                        'label' => $period->period_label,
                        'day_of_week' => (int) $period->day_of_week,
                        'order_in_day' => (int) $period->order_in_day,
                        'is_break' => (bool) $period->is_break,
                        'start_time' => $period->start_time,
                        'end_time' => $period->end_time,
                    ] : null,
                    'room_id' => $slot['room_id'] ?? null,
                    'teacher_id' => $slot['profesor_id'] ?? null,
                    'section_id' => $slot['seccion_id'] ?? null,
                    'source' => $slot['source'] ?? ($this->preview['assignment_source'] ?? null),
                ];
            })->values()->all();
            return [
                'lesson_id' => (int) $lessonId,
                'pevaluacion_id' => $lesson?->pevaluacion_id,
                'subject' => $pev?->pensum?->asignatura?->name,
                'section' => $pev?->seccion?->name,
                'teacher' => $pev?->profesor
                    ? trim(($pev->profesor->lastname ?? '').', '.($pev->profesor->name ?? ''))
                    : null,
                'required_blocks' => $lesson
                    ? (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p
                    : 0,
                'assigned_blocks' => collect($slots)->pluck('period_id')->filter()->unique()->count(),
                'slots' => $slots,
            ];
        })->values()->all();

        $assignmentByLesson = collect($assignmentDetails)->keyBy('lesson_id');
        $availability = app(\App\Services\Timetable\TimetableAvailabilityService::class);
        $roomsByType = app(TimetableRoomEligibilityService::class)->idsByType($calendar);
        $periodOccupancy = collect($assignmentDetails)->flatMap(function (array $detail) use ($lessons): array {
            $lesson = $lessons->get($detail['lesson_id']);

            return collect($detail['slots'])->map(fn (array $slot): array => [
                'period_id' => (int) $slot['period_id'],
                'lesson_id' => (int) $detail['lesson_id'],
                'section_id' => (int) ($lesson?->pevaluacion?->seccion_id ?? 0),
                'teacher_id' => (int) ($lesson?->pevaluacion?->profesor_id ?? 0),
                'group_id' => $lesson?->pevaluacion?->grupo_estable_id
                    ? (int) $lesson->pevaluacion->grupo_estable_id
                    : null,
                'is_half_group' => (bool) ($lesson?->is_half_group ?? false),
                'room_id' => $slot['room_id'] ? (int) $slot['room_id'] : null,
            ])->all();
                $readiness = [
            'published' => true,
            'source' => 'persisted_calendar',
            'hard_conflicts' => $tableContext['timetable_conflicts']['rows'] ?? [],
            'slots_count' => collect($assignment)->flatten(1)->count(),
                ];
        })->groupBy('period_id')->map(fn ($rows): array => $rows->values()->all())->all();
        $candidateDiagnostics = $lessons->filter(function (TimetableLesson $lesson) use ($assignmentByLesson): bool {
            $detail = $assignmentByLesson->get((int) $lesson->id);
            $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;

            return $required > (int) ($detail['assigned_blocks'] ?? 0);
        })->map(function (TimetableLesson $lesson) use (
            $periods,
            $assignmentByLesson,
            $periodOccupancy,
            $availability,
            $roomsByType,
        ): array {
            $detail = $assignmentByLesson->get((int) $lesson->id, ['assigned_blocks' => 0, 'slots' => []]);
            $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
            $assigned = (int) ($detail['assigned_blocks'] ?? 0);
            $teacherId = (int) ($lesson->pevaluacion?->profesor_id ?? 0);
            $sectionId = (int) ($lesson->pevaluacion?->seccion_id ?? 0);
            $groupId = $lesson->pevaluacion?->grupo_estable_id
                ? (int) $lesson->pevaluacion->grupo_estable_id
                : null;
            $eligibleRoomIds = $lesson->room_type_required !== null
                ? array_map('intval', $roomsByType[$lesson->room_type_required] ?? [])
                : [];
            $candidates = $periods->filter(fn (TimetablePeriod $period): bool => ! $period->is_break)
                ->map(function (TimetablePeriod $period) use (
                    $lesson,
                    $teacherId,
                    $sectionId,
                    $groupId,
                    $eligibleRoomIds,
                    $periodOccupancy,
                    $availability,
                ): array {
                    $occupied = collect($periodOccupancy[(int) $period->id] ?? []);
                    $reasons = [];
                    if ((int) $period->shift_id !== (int) $lesson->shift_id) {
                        $reasons[] = 'shift_mismatch';
                    }
                    if (! $availability->isAvailable((int) $lesson->calendar_id, $teacherId, $period)) {
                        $reasons[] = 'availability_blocked';
                    }
                    if ($occupied->contains(fn (array $row): bool => $row['teacher_id'] === $teacherId)) {
                        $reasons[] = 'teacher_busy';
                    }
                    $sameSection = $occupied->where('section_id', $sectionId);
                    if ($sameSection->contains(fn (array $row): bool => ! $row['is_half_group'])) {
                        $reasons[] = 'section_whole_busy';
                    } elseif ($lesson->is_half_group) {
                        if ($sameSection->where('is_half_group', true)->count() >= 2) {
                            $reasons[] = 'half_group_capacity_reached';
                        }
                    } elseif ($sameSection->isNotEmpty()) {
                        $reasons[] = 'section_busy';
                    }
                    if ($groupId !== null && $sameSection->contains(fn (array $row): bool => $row['group_id'] === $groupId)) {
                        $reasons[] = 'stable_group_conflict';
                    }
                    if ($eligibleRoomIds !== []) {
                        $occupiedRooms = $occupied->pluck('room_id')->filter()->map(fn ($id): int => (int) $id);
                        if ($occupiedRooms->intersect($eligibleRoomIds)->count() >= count($eligibleRoomIds)) {
                            $reasons[] = 'room_busy';
                        }
                    }

                    return [
                        'period_id' => (int) $period->id,
                        'period' => $period->period_label,
                        'shift_id' => (int) $period->shift_id,
                        'available' => $reasons === [],
                        'reasons' => array_values(array_unique($reasons)),
                        'occupied_by' => $occupied->values()->all(),
                    ];
                })->values();

            return [
                'lesson_id' => (int) $lesson->id,
                'pevaluacion_id' => (int) $lesson->pevaluacion_id,
                'subject' => $lesson->pevaluacion?->pensum?->asignatura?->name,
                'section_id' => $sectionId,
                'teacher_id' => $teacherId,
                'shift_id' => (int) $lesson->shift_id,
                'is_half_group' => (bool) $lesson->is_half_group,
                'required_blocks' => $required,
                'assigned_blocks' => $assigned,
                'missing_blocks' => max(0, $required - $assigned),
                'preassigned_period_ids' => collect($detail['slots'])->pluck('period_id')->filter()->unique()->values()->all(),
                'candidate_summary' => [
                    'total' => $candidates->count(),
                    'available' => $candidates->where('available', true)->count(),
                    'rejected' => $candidates->where('available', false)->count(),
                    'rejection_reasons' => $candidates->where('available', false)
                        ->flatMap(fn (array $candidate): array => $candidate['reasons'])
                        ->countBy()
                        ->all(),
                ],
                'candidates' => $candidates->all(),
            ];
        })->values()->all();

        $sectionCoverage = $lessons->filter(function (TimetableLesson $lesson) use ($auditSectionId): bool {
            return $auditSectionId !== null
                && (int) $lesson->pevaluacion?->seccion_id === $auditSectionId;
        })->map(function (TimetableLesson $lesson) use ($assignment): array {
            $slots = collect($assignment->get((string) $lesson->id, $assignment->get($lesson->id, [])));
            $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
            $assigned = $slots->pluck('period_id')->filter()->unique()->count();

            return [
                'lesson_id' => (int) $lesson->id,
                'pevaluacion_id' => (int) $lesson->pevaluacion_id,
                'subject' => $lesson->pevaluacion?->pensum?->asignatura?->name,
                'required_blocks' => $required,
                'assigned_blocks' => $assigned,
                'missing_blocks' => max(0, $required - $assigned),
                'complete' => $required > 0 && $assigned === $required,
            ];
        })->values();

        $actionableConflicts = collect($readiness['hard_conflicts'] ?? [])
            ->map(function (array $conflict): array {
                $actions = match ($conflict['type'] ?? '') {
                    'incomplete_assignment' => [
                        'Revisar bloques requeridos y disponibilidad del turno.',
                        'Ejecutar nuevamente el dry-run después de ajustar la lesson.',
                    ],
                    'teacher_double_booked' => ['Cambiar turno/período o disponibilidad de uno de los docentes.'],
                    'section_double_booked' => ['Mover una lesson a otro período o revisar medio grupo.'],
                    'room_double_booked' => ['Cambiar el aula o mover una de las lessons.'],
                    'period_not_in_calendar', 'break_period' => ['Asignar la lesson a un período de clase válido.'],
                    'missing_pevaluacion' => ['Restaurar la Pevaluación o retirar la lesson huérfana.'],
                    default => ['Revisar la configuración de la lesson y volver a generar.'],
                };

                return $conflict + ['actionable_resolution' => $actions];
            })->values()->all();

        $report = [
            'format' => 'cfla-timetable-dry-run-audit',
            'version' => 2,
            'generated_at' => now()->toIso8601String(),
            'section_id' => $auditSectionId,
            'context' => [
                'route_module' => $this->moduleRoutePrefix(),
                'calendar_id' => (int) $calendar->id,
                'calendar_name' => $calendar->name,
                'lapso' => ['id' => $calendar->lapso_id, 'name' => $calendar->lapso?->name],
                'pestudio' => ['id' => $calendar->pestudio_id, 'name' => $calendar->pestudio?->name],
                'active_section_id' => $auditSectionId,
                'section_id' => $auditSectionId,
                'selected_pevaluacion_ids' => $this->selectedPevIds(),
            ],
            'execution' => [
                'dry_run' => true,
                'strategy' => $this->preview['strategy'] ?? $calendar->strategy,
                'assignment_source' => $this->preview['assignment_source'] ?? null,
                'generated_at' => $this->preview['generated_at'] ?? null,
                'timed_out' => (bool) ($this->preview['timed_out'] ?? false),
                'elapsed_seconds' => $this->preview['elapsed_seconds'] ?? null,
                'max_subjects_per_period' => $calendar->max_subjects_per_period,
                'period_minutes' => $calendar->period_minutes,
            ],
            'summary' => [
                'lessons_total' => $lessons->count(),
                'assigned_lessons' => $readiness['assigned'] ?? 0,
                'unassigned_lessons' => $readiness['unassigned'] ?? 0,
                'hard_conflicts' => count($readiness['hard_conflicts'] ?? []),
                'warnings' => count($readiness['warnings'] ?? []),
                'coverage_percent' => $readiness['quality']['coverage'] ?? 0,
                'quality_score' => $readiness['quality']['score'] ?? 0,
                'ready_to_publish' => (bool) ($readiness['ready'] ?? false),
                'diagnostic_incomplete_lessons' => count($candidateDiagnostics),
                'diagnostic_available_candidate_slots' => collect($candidateDiagnostics)
                    ->sum(fn (array $diagnostic): int => (int) ($diagnostic['candidate_summary']['available'] ?? 0)),
            ],
            'section_coverage' => [
                'section_id' => $auditSectionId,
                'lessons' => $sectionCoverage->all(),
                'required_blocks' => $sectionCoverage->sum('required_blocks'),
                'assigned_blocks' => $sectionCoverage->sum('assigned_blocks'),
                'coverage_percent' => $sectionCoverage->sum('required_blocks') > 0
                    ? round($sectionCoverage->sum('assigned_blocks') * 100 / $sectionCoverage->sum('required_blocks'), 2)
                    : 100,
            ],
            'conflicts' => [
                'blocking' => $actionableConflicts,
                'warnings' => $readiness['warnings'] ?? [],
                'unassigned_lesson_ids' => array_map('intval', $this->preview['unassigned'] ?? []),
            ],
            'diagnostic_context' => [
                'report_schema' => 'cfla-timetable-dry-run-diagnostic-v2',
                'captured_at' => now()->toIso8601String(),
                'database' => [
                    'connection' => config('database.default'),
                    'driver' => config('database.connections.'.config('database.default').'.driver'),
                ],
                'scope' => [
                    'active_section_id' => $auditSectionId,
                    'section_id' => $auditSectionId,
                    'selected_pevaluacion_ids' => $this->selectedPevIds(),
                    'lessons_in_calendar' => $lessons->count(),
                ],
                'table_counts' => collect($auditTables)->map(fn (array $rows): int => count($rows))->all(),
                'solver_diagnostics' => [
                    'candidate_diagnostics' => $candidateDiagnostics,
                    'period_occupancy' => $periodOccupancy,
                    'selected_lessons' => $assignmentDetails,
                    'historical_slots_by_lesson' => $auditTables['timetable_slots']
                        ? collect($auditTables['timetable_slots'])->groupBy(fn (array $row): int => (int) ($row['record']['lesson_id'] ?? 0))->all()
                        : [],
                ],
                'tables' => $auditTables,
            ],
            'assignment' => $assignmentDetails,
            'raw_preview' => $this->preview,
        ];

        $activeSectionLesson = $auditSectionId !== null
            ? $lessons->first(fn (TimetableLesson $lesson): bool => (int) $lesson->pevaluacion?->seccion_id === $auditSectionId)
            : null;
        $gradeName = Str::slug((string) ($activeSectionLesson?->pevaluacion?->seccion?->grado?->name ?? 'grado'));
        $sectionName = Str::slug((string) ($activeSectionLesson?->pevaluacion?->seccion?->name ?? 'seccion'));
        $filename = 'auditoria-dry-run-calendario-'.(int) $calendar->id
            .'-'.$gradeName.'-'.$sectionName.'-'.now()->format('Ymd_His').'.json';

        return response()->streamDownload(function () use ($report): void {
            echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }, $filename, ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    /**
     * Persiste los slots de la sección activa sin publicar el calendario
     * completo. Las demás lessons y sus asignaciones permanecen intactas.
     */
    public function persistCurrentSectionSlots(): void
    {
        $sectionId = is_numeric($this->activeSeccionId) ? (int) $this->activeSeccionId : null;

        if (! $this->calendarId || ! $this->preview || ! $sectionId) {
            $this->notification()->warning(
                'Sección requerida',
                'Selecciona una sección con una previsualización disponible antes de guardar sus asignaciones.',
            );

            return;
        }

        $calendar = TimetableCalendar::query()->find($this->calendarId);
        if (! $calendar) {
            $this->notification()->error('Calendario no encontrado', 'No se pudieron guardar las asignaciones.');

            return;
        }

        $sectionLessons = $calendar->lessons()
            ->with('pevaluacion')
            ->whereHas('pevaluacion', fn ($query) => $query->where('seccion_id', $sectionId))
            ->get()
            ->keyBy('id');
        $assignment = collect($this->preview['assignment'] ?? []);
        $assignmentRows = [];
        foreach ($sectionLessons as $lessonId => $lesson) {
            $slots = $assignment->get((string) $lessonId, $assignment->get($lessonId, []));
            foreach (collect($slots)->unique(fn (array $slot): int => (int) ($slot['period_id'] ?? 0)) as $slot) {
                $periodId = (int) ($slot['period_id'] ?? 0);
                if ($periodId <= 0 || ! $lesson->pevaluacion) {
                    continue;
                }

                $assignmentRows[] = [
                    'calendar_id' => $calendar->id,
                    'lesson_id' => (int) $lessonId,
                    'period_id' => $periodId,
                    'profesor_id' => (int) $lesson->pevaluacion->profesor_id,
                    'seccion_id' => (int) $lesson->pevaluacion->seccion_id,
                    'grupo_estable_id' => $lesson->pevaluacion->grupo_estable_id
                        ? (int) $lesson->pevaluacion->grupo_estable_id
                        : null,
                    'is_half_group' => (bool) $lesson->is_half_group,
                    'room_id' => ! empty($slot['room_id']) ? (int) $slot['room_id'] : null,
                    'locked' => (bool) $lesson->locked,
                    'is_manual_override' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($assignmentRows === []) {
            $this->notification()->warning(
                'Sin asignaciones',
                'La sección activa no tiene slots asignados en la previsualización.',
            );

            return;
        }

        $lessonIds = $sectionLessons->keys()->map(fn ($id): int => (int) $id)->all();
        $periods = TimetablePeriod::query()
            ->whereIn('id', collect($assignmentRows)->pluck('period_id')->unique())
            ->get()
            ->keyBy('id');
        $persistedSlots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->whereIn('period_id', $periods->keys())
            ->whereNotIn('lesson_id', $lessonIds)
            ->with('lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.seccion', 'lesson.pevaluacion.profesor')
            ->get();
        $conflicts = collect($assignmentRows)
            ->map(function (array $candidate) use ($persistedSlots): ?array {
                $existing = $persistedSlots->first(function (TimetableSlot $slot) use ($candidate): bool {
                    if ((int) $slot->period_id !== (int) $candidate['period_id']) {
                        return false;
                    }

                    $persistedTeacherId = (int) ($slot->lesson?->pevaluacion?->profesor_id ?? $slot->profesor_id);
                    $persistedSectionId = (int) ($slot->lesson?->pevaluacion?->seccion_id ?? $slot->seccion_id);
                    $sameTeacher = $persistedTeacherId === (int) $candidate['profesor_id'];
                    $bothHalfGroup = (bool) $slot->is_half_group && (bool) $candidate['is_half_group'];
                    $sameRoom = $candidate['room_id'] !== null
                        && $slot->room_id !== null
                        && (int) $slot->room_id === (int) $candidate['room_id'];
                    $sameSection = $persistedSectionId === (int) $candidate['seccion_id']
                        && (
                            $candidate['grupo_estable_id'] === null
                            || (int) $slot->grupo_estable_id === (int) $candidate['grupo_estable_id']
                            || $slot->grupo_estable_id === null
                        );

                    return ($sameTeacher && ! $bothHalfGroup)
                        || $sameRoom
                        || ($sameSection && ! $bothHalfGroup);
                });

                if (! $existing) {
                    return null;
                }

                return [
                    'candidate_lesson_id' => (int) $candidate['lesson_id'],
                    'period_id' => (int) $candidate['period_id'],
                    'existing_lesson_id' => (int) $existing->lesson_id,
                    'type' => (int) ($existing->lesson?->pevaluacion?->profesor_id ?? $existing->profesor_id) === (int) $candidate['profesor_id']
                        ? 'docente'
                        : ((int) ($existing->lesson?->pevaluacion?->seccion_id ?? $existing->seccion_id) === (int) $candidate['seccion_id'] ? 'sección' : 'aula'),
                    'candidate_profesor_id' => (int) $candidate['profesor_id'],
                    'existing_profesor_id' => (int) ($existing->lesson?->pevaluacion?->profesor_id ?? $existing->profesor_id),
                    'candidate_section_id' => (int) $candidate['seccion_id'],
                    'existing_section_id' => (int) ($existing->lesson?->pevaluacion?->seccion_id ?? $existing->seccion_id),
                    'subject' => $existing->lesson?->pevaluacion?->pensum?->asignatura?->name ?? 'otra asignatura',
                    'section' => $existing->lesson?->pevaluacion?->seccion?->name ?? (string) $existing->seccion_id,
                ];
            })
            ->filter()
            ->unique(fn (array $conflict): string => implode(':', [
                $conflict['candidate_lesson_id'],
                $conflict['period_id'],
                $conflict['existing_lesson_id'],
            ]))
            ->values();

        if ($conflicts->isNotEmpty()) {
            $detail = $conflicts->map(function (array $conflict) use ($periods, $sectionLessons): string {
                $period = $periods->get($conflict['period_id']);
                $periodLabel = $period?->period_label ?? "período {$conflict['period_id']}";
                $candidate = $sectionLessons->get($conflict['candidate_lesson_id']);
                $candidateSubject = $candidate?->pevaluacion?->pensum?->asignatura?->name
                    ?? "lesson {$conflict['candidate_lesson_id']}";

                return "{$conflict['type']} en {$periodLabel}: {$candidateSubject} colisiona con "
                    ."{$conflict['subject']} · sección {$conflict['section']} "
                    .'(docente '.$conflict['candidate_profesor_id'].' vs '
                    .$conflict['existing_profesor_id'].'; sección '
                    .$conflict['candidate_section_id'].' vs '.$conflict['existing_section_id'].')';
            })->implode('; ');

            $this->notification()->error(
                'Asignaciones no guardadas',
                "La previsualización colisiona con una asignación preservada del calendario. {$detail}. "
                .'Para resolverlo, mueve la lesson indicada a otro período libre para el docente '
                .'o mueve la lesson preservada a otro bloque; luego ejecuta nuevamente el dry-run. '
                .'No uses «Guardar sección» hasta que desaparezca la colisión.',
            );

            return;
        }

        try {
            DB::transaction(function () use ($calendar, $lessonIds, $assignmentRows): void {
                TimetableSlot::query()
                    ->where('calendar_id', $calendar->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->delete();
                TimetableSlot::query()->insert($assignmentRows);
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            report($exception);
            $periodDetails = collect($assignmentRows)
                ->groupBy('period_id')
                ->map(function ($rows, $periodId) use ($periods, $sectionLessons): string {
                    $period = $periods->get((int) $periodId);
                    $periodLabel = $period?->period_label ?? "período {$periodId}";
                    $lessons = $rows->map(function (array $row) use ($sectionLessons): string {
                        $lesson = $sectionLessons->get($row['lesson_id']);
                        $subject = $lesson?->pevaluacion?->pensum?->asignatura?->name
                            ?? "lesson {$row['lesson_id']}";
                        $halfGroup = $row['is_half_group'] ? 'medio grupo' : 'grupo completo';

                        return "{$subject} ({$halfGroup}, docente #{$row['profesor_id']})";
                    })->implode(' + ');

                    return "{$periodLabel}: {$lessons}";
                })
                ->values()
                ->implode('; ');

            $this->notification()->error(
                'Asignaciones no guardadas',
                'No se guardaron los slots porque la base de datos rechazó una combinación '
                .'duplicada o incompatible. Revisa el día, bloque, docente, sección y aula '
                .'de las asignaciones. Detalle de la sección activa: '.$periodDetails
                .'. Si las lessons son de medio grupo, ambas deben tener habilitado «medio grupo» '
                .'y no deben compartir un aula ocupada. No se modificó ninguna asignación.',
            );

            return;
        }

        $this->notification()->success(
            'Asignaciones guardadas',
            'Los slots de la sección activa quedaron persistidos en la base de datos.',
        );
        $this->loadPublishedPreview($calendar->fresh());
    }

    public function downloadCurrentSectionSlotsBackup(?int $seccionId = null)
    {
        $sectionId = $seccionId ?: (is_numeric($this->activeSeccionId) ? (int) $this->activeSeccionId : null);
        if (! $this->calendarId || ! $sectionId) {
            $this->notification()->warning('Sección requerida', 'Selecciona una sección para respaldar sus slots.');

            return null;
        }

        $calendar = TimetableCalendar::query()->with(['lapso', 'pestudio'])->find($this->calendarId);
        $slots = $calendar?->slots()
            ->with(['lesson.pevaluacion.pensum.asignatura', 'period', 'seccion.grado'])
            ->where('seccion_id', $sectionId)
            ->get() ?? collect();
        if ($slots->isEmpty()) {
            $this->notification()->warning('Sin slots', 'La sección seleccionada no tiene slots persistidos para respaldar.');

            return null;
        }

        $backup = [
            'format' => 'cfla-timetable-section-slots-backup',
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'calendar' => [
                'lapso_id' => (int) $calendar->lapso_id,
                'pestudio_id' => (int) $calendar->pestudio_id,
            ],
            'section' => ['id' => $sectionId],
            'slots' => $slots->map(function (TimetableSlot $slot): array {
                $pev = $slot->lesson?->pevaluacion;

                return [
                    'pevaluacion_id' => $pev?->id,
                    'academic_identity' => [
                        'seccion_id' => $pev?->seccion_id,
                        'pensum_id' => $pev?->pensum_id,
                        'profesor_id' => $pev?->profesor_id,
                        'grupo_estable_id' => $pev?->grupo_estable_id,
                    ],
                    'period' => [
                        'shift_id' => $slot->period?->shift_id,
                        'day_of_week' => $slot->period?->day_of_week,
                        'order_in_day' => $slot->period?->order_in_day,
                    ],
                    'room_id' => $slot->room_id ? (int) $slot->room_id : null,
                    'is_practical' => (bool) ($slot->is_practical ?? false),
                ];
            })->values()->all(),
        ];
        $section = $slots->first()?->seccion;
        $gradeName = Str::slug((string) ($section?->grado?->name ?? 'grado'));
        $sectionName = Str::slug((string) ($section?->name ?? 'seccion'));
        $filename = 'respaldo-slots-calendario-'.(int) $calendar->id
            .'-'.$gradeName.'-'.$sectionName.'-'.now()->format('Ymd_His').'.json';

        return response()->streamDownload(function () use ($backup): void {
            echo json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }, $filename, ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    public function restoreCurrentSectionSlotsBackup(): void
    {
        if (! $this->calendarId || ! $this->slotsBackupFile) {
            $this->notification()->warning('Respaldo requerido', 'Selecciona un archivo JSON de slots para restaurar.');

            return;
        }

        if (strtolower((string) $this->slotsBackupFile->getClientOriginalExtension()) !== 'json'
            || (int) $this->slotsBackupFile->getSize() > 5 * 1024 * 1024
        ) {
            $this->notification()->error('Archivo no permitido', 'El respaldo debe ser un archivo JSON de hasta 5 MB.');

            return;
        }

        try {
            $payload = json_decode($this->slotsBackupFile->get(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $this->notification()->error('JSON inválido', 'El archivo no tiene un formato de slots válido.');

            return;
        }
        if (($payload['format'] ?? null) !== 'cfla-timetable-section-slots-backup'
            || (int) ($payload['version'] ?? 0) !== 1
            || ! is_array($payload['slots'] ?? null)
        ) {
            $this->notification()->error('Respaldo incompatible', 'El archivo no corresponde a un respaldo de slots de CFlat.');

            return;
        }

        $calendar = TimetableCalendar::query()->find($this->calendarId);
        $sectionId = (int) ($payload['section']['id'] ?? 0);
        $activeSectionId = is_numeric($this->activeSeccionId) ? (int) $this->activeSeccionId : null;
        if ($activeSectionId !== null && $sectionId !== $activeSectionId) {
            $this->notification()->error(
                'Sección incorrecta',
                "El respaldo pertenece a la sección #{$sectionId}, pero la sección activa es #{$activeSectionId}. Selecciona la sección correspondiente antes de restaurar.",
            );

            return;
        }
        if (! $calendar || (int) ($payload['calendar']['lapso_id'] ?? 0) !== (int) $calendar->lapso_id
            || (int) ($payload['calendar']['pestudio_id'] ?? 0) !== (int) $calendar->pestudio_id
        ) {
            $this->notification()->error('Calendario incompatible', 'El respaldo pertenece a otro lapso o plan de estudio.');

            return;
        }

        $lessons = $calendar->lessons()->with('pevaluacion')->get();
        $byPev = $lessons->keyBy('pevaluacion_id');
        $periods = $calendar->periods()->get()->keyBy(fn ($period) => implode(':', [
            $period->shift_id, $period->day_of_week, $period->order_in_day,
        ]));
        $rows = [];
        $unresolved = [];
        foreach ($payload['slots'] as $index => $slot) {
            $identity = $slot['academic_identity'] ?? [];
            $lesson = $byPev->get((int) ($slot['pevaluacion_id'] ?? 0));
            if (! $lesson || (int) $lesson->pevaluacion?->seccion_id !== $sectionId) {
                $lesson = $lessons->first(fn ($candidate) => (int) $candidate->pevaluacion?->seccion_id === $sectionId
                    && (int) $candidate->pevaluacion?->pensum_id === (int) ($identity['pensum_id'] ?? 0)
                    && (int) $candidate->pevaluacion?->profesor_id === (int) ($identity['profesor_id'] ?? 0)
                    && (int) ($candidate->pevaluacion?->grupo_estable_id ?? 0) === (int) ($identity['grupo_estable_id'] ?? 0));
            }
            $periodData = $slot['period'] ?? [];
            $period = $periods->get(implode(':', [
                (int) ($periodData['shift_id'] ?? 0),
                (int) ($periodData['day_of_week'] ?? 0),
                (int) ($periodData['order_in_day'] ?? 0),
            ]));
            if (! $lesson || ! $period) {
                $unresolved[] = [
                    'index' => (int) $index + 1,
                    'pevaluacion_id' => (int) ($slot['pevaluacion_id'] ?? 0),
                    'period' => implode(':', [
                        (int) ($periodData['shift_id'] ?? 0),
                        (int) ($periodData['day_of_week'] ?? 0),
                        (int) ($periodData['order_in_day'] ?? 0),
                    ]),
                    'reason' => ! $lesson ? 'lesson no encontrada o fuera de la sección' : 'período no encontrado',
                ];

                continue;
            }
            $row = [
                'calendar_id' => $calendar->id,
                'lesson_id' => $lesson->id,
                'period_id' => $period->id,
                'profesor_id' => $lesson->pevaluacion->profesor_id,
                'seccion_id' => $sectionId,
                'grupo_estable_id' => $lesson->pevaluacion->grupo_estable_id,
                'is_half_group' => $lesson->is_half_group,
                'room_id' => ! empty($slot['room_id']) ? (int) $slot['room_id'] : null,
                'locked' => $lesson->locked,
                'is_manual_override' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('timetable_slots', 'is_practical')) {
                $row['is_practical'] = (bool) ($slot['is_practical'] ?? false);
            }
            $rows[] = $row;
        }
        if ($unresolved !== []) {
            $details = collect($unresolved)
                ->take(5)
                ->map(fn (array $item): string => "#{$item['index']} lesson {$item['pevaluacion_id']} / período {$item['period']}: {$item['reason']}")
                ->implode('; ');
            $remaining = count($unresolved) - min(5, count($unresolved));
            $suffix = $remaining > 0 ? " También hay {$remaining} fila(s) adicional(es) sin resolver." : '';
            Log::channel('timetable')->warning('Restore de slots rechazado por coincidencias incompletas', [
                'calendar_id' => (int) $calendar->id,
                'section_id' => $sectionId,
                'backup_slot_count' => count($payload['slots']),
                'resolved_slot_count' => count($rows),
                'unresolved' => $unresolved,
            ]);
            $this->notification()->error(
                'Restore cancelado',
                "No se modificaron los slots porque el respaldo no coincide completamente con el calendario actual: {$details}.{$suffix}",
            );

            return;
        }
        if ($rows === []) {
            $this->notification()->error('Sin coincidencias', 'No se encontraron lessons y períodos compatibles para restaurar.');

            return;
        }

        $lessonIds = $lessons->filter(fn ($lesson) => (int) $lesson->pevaluacion?->seccion_id === $sectionId)->pluck('id');
        try {
            DB::transaction(function () use ($calendar, $sectionId, $lessonIds, $rows): void {
                TimetableSlot::query()->where('calendar_id', $calendar->id)->whereIn('lesson_id', $lessonIds)->delete();
                TimetableSlot::query()->insert($rows);
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            report($exception);
            $this->notification()->error('Restore no completado', 'El respaldo colisiona con otra asignación del calendario.');

            return;
        }

        $this->slotsBackupFile = null;
        $freshCalendar = $calendar->fresh();
        if ($freshCalendar->status === TimetableCalendar::STATUS_ACTIVE) {
            $this->loadPublishedPreview($freshCalendar);
        } else {
            $restoredAssignment = collect($rows)
                ->groupBy('lesson_id')
                ->map(fn ($lessonRows): array => $lessonRows->map(fn (array $row): array => [
                    'period_id' => (int) $row['period_id'],
                    'room_id' => $row['room_id'] !== null ? (int) $row['room_id'] : null,
                    'is_practical' => (bool) ($row['is_practical'] ?? false),
                ])->values()->all())
                ->all();
            $existingAssignment = is_array($this->preview['assignment'] ?? null)
                ? $this->preview['assignment']
                : [];
            foreach ($lessonIds as $lessonId) {
                unset($existingAssignment[(string) $lessonId], $existingAssignment[(int) $lessonId]);
            }
            $this->preview = array_merge($this->preview ?? [], [
                'dry_run' => false,
                'assignment' => array_merge($existingAssignment, $restoredAssignment),
                'assignment_source' => 'restored_slots',
                'unassigned' => [],
                'assignment_diagnostics' => [],
            ]);
            $this->generationState = 'preview_ready';
        }
        $this->notification()->success('Slots restaurados', 'Las asignaciones de la sección fueron restauradas y persistidas.');
    }

    public function openTeacherScheduleDialog(): void
    {
        $options = $this->teacherScheduleOptions();
        $this->teacherScheduleProfesorId = $options[0]['id'] ?? null;
        $this->showTeacherScheduleDialog = true;
    }

    public function closeTeacherScheduleDialog(): void
    {
        $this->showTeacherScheduleDialog = false;
    }

    private function teacherScheduleOptions(): array
    {
        if (! $this->calendarId || ! is_numeric($this->activeSeccionId)) {
            return [];
        }

        return TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereHas('pevaluacion', fn ($query) => $query->where('seccion_id', (int) $this->activeSeccionId))
            ->with('pevaluacion.profesor')
            ->get()
            ->map(fn (TimetableLesson $lesson): ?array => $lesson->pevaluacion?->profesor ? [
                'id' => (int) $lesson->pevaluacion->profesor->id,
                'name' => (string) ($lesson->pevaluacion->profesor->full_name
                    ?? trim(($lesson->pevaluacion->profesor->lastname ?? '').' '.($lesson->pevaluacion->profesor->name ?? ''))),
            ] : null)
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->all();
    }

    private function teacherScheduleGrid(): array
    {
        if (! $this->calendarId || ! $this->teacherScheduleProfesorId || ! is_numeric($this->activeSeccionId)) {
            return [];
        }

        $lessons = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereHas('pevaluacion', fn ($query) => $query
                ->where('seccion_id', (int) $this->activeSeccionId)
                ->where('profesor_id', (int) $this->teacherScheduleProfesorId))
            ->with('pevaluacion.pensum.asignatura', 'pevaluacion.seccion')
            ->get()
            ->keyBy('id');
        $periods = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->with('shift:id,code,name')
            ->orderBy('shift_id')
            ->orderBy('order_in_day')
            ->get();
        $assignment = collect($this->preview['assignment'] ?? []);
        $cells = [];

        foreach ($lessons as $lesson) {
            $slots = $assignment->get((string) $lesson->id, $assignment->get($lesson->id));
            if ($slots === null) {
                $slots = $lesson->slots()->get(['period_id'])->map(fn ($slot): array => [
                    'period_id' => (int) $slot->period_id,
                ])->all();
            }

            foreach ($slots as $slot) {
                $period = $periods->firstWhere('id', (int) ($slot['period_id'] ?? 0));
                if (! $period) {
                    continue;
                }
                $key = $period->shift_id.':'.$period->order_in_day;
                $cells[$key][$period->day_of_week][] = [
                    'subject' => $lesson->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura sin nombre',
                    'section' => $lesson->pevaluacion?->seccion?->name ?? '—',
                    'lesson_id' => (int) $lesson->id,
                    'start' => substr((string) $period->start_time, 0, 5),
                    'end' => substr((string) $period->end_time, 0, 5),
                ];
            }
        }

        return $periods
            ->groupBy(fn (TimetablePeriod $period): string => $period->shift_id.':'.$period->order_in_day)
            ->map(function ($periodGroup) use ($cells): array {
                $period = $periodGroup->first();
                return [
                    'shift' => $period->shift?->name ?? 'Turno '.$period->shift_id,
                    'code' => $period->shift?->code ?? 'T'.$period->shift_id,
                    'order' => (int) $period->order_in_day,
                    'cells' => $cells[$period->shift_id.':'.$period->order_in_day] ?? [],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Rehidrata la vista del Paso 5 desde los slots persistidos de un
     * calendario publicado, sin ejecutar nuevamente el solver.
     */
    private function loadPublishedPreview(TimetableCalendar $calendar): void
    {
        if ($calendar->status !== TimetableCalendar::STATUS_ACTIVE || ! $calendar->slots()->exists()) {
            return;
        }

        $assignment = [];
        $assignedLessonIds = [];

        TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->orderBy('lesson_id')
            ->orderBy('period_id')
            ->get()
            ->each(function (TimetableSlot $slot) use (&$assignment, &$assignedLessonIds): void {
                $lessonId = (int) $slot->lesson_id;
                $assignedLessonIds[$lessonId] = true;
                $assignment[$lessonId][] = [
                    'period_id' => (int) $slot->period_id,
                    'room_id' => $slot->room_id ? (int) $slot->room_id : null,
                    'is_practical' => (bool) ($slot->is_practical ?? false),
                ];
            });

        $unassigned = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->whereNotIn('id', array_keys($assignedLessonIds))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
        $diagnostics = [];
        $lessons = $calendar->lessons()
            ->get(['id', 'weekly_blocks_t', 'weekly_blocks_p'])
            ->keyBy('id');
        foreach ($assignment as $lessonId => $slots) {
            $lesson = $lessons->get((int) $lessonId);
            if (! $lesson) {
                continue;
            }

            $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
            $assigned = collect($slots)->pluck('period_id')->filter()->unique()->count();
            if ($required > 0 && $assigned !== $required) {
                $unassigned[] = (int) $lessonId;
                $diagnostics[] = [
                    'lesson_id' => (int) $lessonId,
                    'required_blocks' => $required,
                    'assigned_blocks' => $assigned,
                    'missing_blocks' => max(0, $required - $assigned),
                ];
            }
        }

        $this->preview = [
            'dry_run' => false,
            'assignment' => $assignment,
            'unassigned' => array_values(array_unique($unassigned)),
            'assignment_source' => 'published_slots',
            'strategy' => $calendar->strategy ?: TimetableCalendar::DEFAULT_STRATEGY,
            'timed_out' => false,
            'elapsed_seconds' => 0,
            'assignment_diagnostics' => $diagnostics,
        ];
        $this->generationState = 'published';
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
        $this->recordPreviewChange('update_draft_preview', null, [], [
            'assignment_count' => count($this->preview['assignment'] ?? []),
            'unassigned_count' => count($this->preview['unassigned'] ?? []),
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

        $calendar = TimetableCalendar::query()->find($this->calendarId);
        if (! $calendar) {
            return [];
        }

        $readiness = app(TimetablePublicationReadinessService::class)->evaluate($calendar, $this->preview);
        $unassigned = collect($this->preview['unassigned'] ?? []);
        $assignment = collect($this->preview['assignment'] ?? []);

        // Docentes con lecciones sin asignar (necesitan ajuste manual).
        $unassignedLessonIds = $unassigned->map(fn ($v) => (int) $v)->all();
        $teachersAffected = TimetableLesson::query()
            ->whereIn('id', $unassignedLessonIds)
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion.profesor')
            ->get()
            ->map(fn ($lesson) => trim(
                ($lesson->pevaluacion?->profesor?->lastname ?? '').' '.
                ($lesson->pevaluacion?->profesor?->name ?? '')
            ))
            ->filter()
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
            'calidad' => (int) round($readiness['quality']['score']),
            'cobertura' => $readiness['quality']['coverage'],
            'conflictos_bloqueantes' => count($readiness['hard_conflicts']),
            'warnings' => count($readiness['warnings']),
            'ready' => $readiness['ready'],
        ];
    }

    public function publicationReadiness(): array
    {
        if (! $this->calendarId || ! $this->preview) {
            return [];
        }

        $calendar = TimetableCalendar::query()->find($this->calendarId);

        if (! $calendar) {
            return [];
        }

        $readiness = app(TimetablePublicationReadinessService::class)->evaluate($calendar, $this->preview);
        $selectedPevIds = $this->selectedPevIds();
        $selectedLessonIds = $selectedPevIds === []
            ? collect()
            : TimetableLesson::query()
                ->where('calendar_id', $calendar->id)
                ->whereIn('pevaluacion_id', $selectedPevIds)
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

        $readiness['display_hard_conflicts'] = collect($readiness['hard_conflicts'] ?? [])
            ->filter(fn (array $conflict): bool => $selectedLessonIds->contains((int) ($conflict['lesson_id'] ?? 0)))
            ->values()
            ->all();

        return $readiness;
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
            ->with('shift', 'slots.period', 'pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado', 'pevaluacion.profesor')
            ->get();
        $calendarStrategy = TimetableCalendar::query()
            ->whereKey($this->calendarId)
            ->value('strategy');

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
                $roomBusy,
                $calendarStrategy
            ): array {
                $pev = $lesson->pevaluacion;
                $subject = $pev?->pensum?->asignatura?->name ?? 'Sin asignatura';
                $section = $pev?->seccion?->name ?? 'Sin sección';
                $sectionId = (int) ($pev?->seccion_id ?? 0);
                $grade = $pev?->seccion?->grado?->name ?? 'Sin grado';
                $teacher = trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')) ?: 'Sin docente';
                $blocks = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
                $assignedBlocks = $lesson->slots
                    ->map(fn ($slot) => $slot->period
                        ? $slot->period->period_label
                        .' · '.substr((string) $slot->period->end_time, 0, 5)
                        : null)
                    ->filter()
                    ->values()
                    ->all();
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

                if ($calendarStrategy === TimetableCalendar::STRATEGY_LEGACY && $lesson->slots->isEmpty()) {
                    $reason = 'La estrategia legacy conserva las posiciones importadas y esta lección no tiene bloques legacy.';
                    $actions = [
                        'Cambia la estrategia del calendario a optimizada para que el solver la ubique.',
                        'O importa/asigna sus bloques legacy antes de volver a previsualizar.',
                    ];
                } elseif (! $lesson->shift_id) {
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
                    'section_id' => $sectionId,
                    'grade' => $grade,
                    'teacher' => $teacher,
                    'shift' => $lesson->shift?->code ?? '—',
                    'blocks_t' => (int) $lesson->weekly_blocks_t,
                    'blocks_p' => (int) $lesson->weekly_blocks_p,
                    'assigned_blocks' => $assignedBlocks,
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
            ->groupBy(fn (array $item) => $item['section_id'])
            ->map(fn ($group) => [
                'section_id' => (int) $group->first()['section_id'],
                'grade' => $group->first()['grade'],
                'section' => $group->first()['section'],
                'count' => $group->count(),
                'items' => $group->values()->all(),
            ])
            ->sortBy(fn ($group) => $group['grade'].' '.$group['section'])
            ->keyBy('section_id')
            ->all();
    }

    public function confirmAndPublish(): void
    {
        if (! $this->calendarId || ! $this->preview) {
            session()->flash('error', 'Primero ejecuta una previsualización (dry-run).');

            return;
        }

        $calendar = TimetableCalendar::query()->find($this->calendarId);

        if (! $calendar) {
            $this->notification()->error(
                'Publicación bloqueada',
                'No se encontró el calendario seleccionado.',
            );

            return;
        }

        $lessons = $this->eligibleCalendarLessons($calendar);
        $selectedPevIds = collect($this->selectedPevIds())->map(fn ($id): int => (int) $id);
        $selectedPevIdsBeforeScope = $selectedPevIds;
        $selectedLessonIds = $lessons
            ->filter(fn (TimetableLesson $lesson): bool => $selectedPevIds->contains((int) $lesson->pevaluacion_id))
            ->keys()
            ->map(fn ($id): int => (int) $id)
            ->values();
        $excludedSelectedPevIds = $selectedPevIdsBeforeScope
            ->reject(fn (int $pevaluacionId): bool => $lessons->contains(
                fn (TimetableLesson $lesson): bool => (int) $lesson->pevaluacion_id === $pevaluacionId
            ))
            ->values();
        if ($excludedSelectedPevIds->isNotEmpty()) {
            $this->notification()->warning(
                'Lessons fuera del alcance',
                'Se omitieron '.$excludedSelectedPevIds->count().' lesson(s) seleccionada(s) porque su grado, sección, plan de estudio o lapso ya no pertenece al calendario activo.',
            );
        }
        if ($selectedLessonIds->isEmpty()) {
            $this->notification()->warning(
                'Sin lessons seleccionadas',
                'Selecciona al menos una lesson en el paso 3 antes de publicar.',
            );

            return;
        }
        $readiness = app(TimetablePublicationReadinessService::class)->evaluate($calendar, $this->preview);
        $selectedHardConflicts = collect($readiness['hard_conflicts'] ?? [])
            ->filter(fn (array $conflict): bool => $selectedLessonIds->contains((int) ($conflict['lesson_id'] ?? 0)))
            ->values();
        $readiness['hard_conflicts'] = $selectedHardConflicts->all();
        $blockedSectionIds = $selectedHardConflicts
            ->map(fn (array $conflict): ?int => $lessons->get((int) ($conflict['lesson_id'] ?? 0))?->pevaluacion?->seccion_id
                ? (int) $lessons->get((int) ($conflict['lesson_id'] ?? 0))->pevaluacion->seccion_id
                : null)
            ->filter()
            ->unique()
            ->values();
        $publishableSectionIds = $lessons
            ->filter(fn (TimetableLesson $lesson): bool => $selectedLessonIds->contains((int) $lesson->id))
            ->map(fn (TimetableLesson $lesson): ?int => $lesson->pevaluacion?->seccion_id
                ? (int) $lesson->pevaluacion->seccion_id
                : null)
            ->filter()
            ->unique()
            ->reject(fn (int $sectionId): bool => $blockedSectionIds->contains($sectionId))
            ->values();
        $publishableLessonIds = $lessons
            ->filter(fn (TimetableLesson $lesson): bool => $selectedLessonIds->contains((int) $lesson->id)
                && $lesson->pevaluacion?->seccion_id
                && $publishableSectionIds->contains((int) $lesson->pevaluacion->seccion_id))
            ->keys()
            ->map(fn ($id): int => (int) $id)
            ->all();

        // Una publicación parcial conserva los slots fuera de alcance. Esos
        // slots también forman parte de la ocupación real y no pueden ser
        // reemplazados por una lesson seleccionada.
        $preservedSlots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->whereNotIn('lesson_id', $publishableLessonIds)
            ->get(['lesson_id', 'period_id', 'profesor_id', 'seccion_id', 'room_id', 'is_half_group', 'grupo_estable_id']);
        $preservedConflicts = collect($publishableLessonIds)
            ->flatMap(function (int $lessonId) use ($lessons, $preservedSlots): array {
                $lesson = $lessons->get($lessonId);
                $teacherId = (int) ($lesson?->pevaluacion?->profesor_id ?? 0);
                $sectionId = (int) ($lesson?->pevaluacion?->seccion_id ?? 0);
                $assignments = $this->preview['assignment'][(string) $lessonId]
                    ?? $this->preview['assignment'][$lessonId]
                    ?? [];

                return collect($assignments)->flatMap(function (array $assignment) use (
                    $lessonId,
                    $lesson,
                    $teacherId,
                    $sectionId,
                    $preservedSlots
                ): array {
                    $periodId = (int) ($assignment['period_id'] ?? 0);
                    $existing = $preservedSlots->first(function (TimetableSlot $slot) use (
                        $periodId,
                        $teacherId,
                        $sectionId,
                        $lesson,
                        $assignment
                    ): bool {
                        if ((int) $slot->period_id !== $periodId) {
                            return false;
                        }

                        $bothHalfGroup = (bool) $slot->is_half_group
                            && (bool) $lesson?->is_half_group;
                        $sameTeacher = $teacherId > 0
                            && (int) $slot->profesor_id === $teacherId
                            && ! $bothHalfGroup;
                        $sameSection = $sectionId > 0
                            && (int) $slot->seccion_id === $sectionId
                            && ! $bothHalfGroup;
                        $sameRoom = ! empty($assignment['room_id'])
                            && $slot->room_id !== null
                            && (int) $slot->room_id === (int) $assignment['room_id'];

                        return $sameTeacher || $sameSection || $sameRoom;
                    });

                    return $existing ? [[
                        'lesson_id' => $lessonId,
                        'period_id' => $periodId,
                        'existing_lesson_id' => (int) $existing->lesson_id,
                        'section_id' => $sectionId,
                        'type' => (int) $existing->seccion_id === $sectionId
                            ? 'section'
                            : ((int) $existing->profesor_id === $teacherId ? 'teacher' : 'room'),
                    ]] : [];
                })->all();
            })
            ->unique(fn (array $conflict): string => implode(':', [
                $conflict['lesson_id'],
                $conflict['period_id'],
                $conflict['existing_lesson_id'],
            ]))
            ->values();
        $preservedConflictSectionIds = $preservedConflicts
            ->pluck('section_id')
            ->filter()
            ->unique()
            ->values();
        $blockedSectionIds = $blockedSectionIds
            ->merge($preservedConflictSectionIds)
            ->unique()
            ->values();
        $preservedConflictCount = $preservedConflictSectionIds->count();
        if ($preservedConflictCount > 0) {
            $readiness['hard_conflicts'] = collect($readiness['hard_conflicts'] ?? [])
                ->merge($preservedConflicts->map(fn (array $conflict): array => [
                    'type' => 'preserved_slot_collision',
                    'section_id' => $conflict['section_id'],
                    'lesson_id' => $conflict['lesson_id'],
                    'period_id' => $conflict['period_id'],
                    'title' => 'Colisión con horario preservado',
                    'message' => 'La lesson seleccionada colisiona con una asignación preservada ('.$conflict['type'].').',
                ]))
                ->values()
                ->all();
        }
        $publishableSectionIds = $publishableSectionIds
            ->reject(fn (int $sectionId): bool => $blockedSectionIds->contains($sectionId))
            ->values();
        $publishableLessonIds = $lessons
            ->filter(fn (TimetableLesson $lesson): bool => in_array((int) $lesson->id, $publishableLessonIds, true)
                && $lesson->pevaluacion?->seccion_id
                && $publishableSectionIds->contains((int) $lesson->pevaluacion->seccion_id))
            ->keys()
            ->map(fn ($id): int => (int) $id)
            ->all();

        $preservedLessonIds = $lessons->keys()
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $lessonId): bool => in_array($lessonId, $publishableLessonIds, true))
            ->values()
            ->all();
        $existingAssignment = $calendar->slots()
            ->get()
            ->groupBy('lesson_id')
            ->map(fn ($slots) => $slots->map(fn (TimetableSlot $slot): array => [
                'period_id' => (int) $slot->period_id,
                'room_id' => $slot->room_id ? (int) $slot->room_id : null,
                'is_practical' => (bool) ($slot->is_practical ?? false),
            ])->values()->all())
            ->all();
        $publishableAssignment = collect($this->preview['assignment'] ?? [])
            ->filter(fn ($slots, $lessonId): bool => in_array((int) $lessonId, $publishableLessonIds, true))
            ->all();
        $scopedAssignment = array_replace(
            $existingAssignment,
            collect($publishableAssignment)->mapWithKeys(fn (array $slots, $lessonId): array => [
                (int) $lessonId => $slots,
            ])->all(),
        );
        $publicationRows = collect($publishableLessonIds)->flatMap(function (int $lessonId) use ($lessons, $publishableAssignment): array {
            $lesson = $lessons->get($lessonId);
            $slots = $publishableAssignment[(string) $lessonId] ?? $publishableAssignment[$lessonId] ?? [];

            return collect($slots)->map(fn (array $slot): array => [
                'lesson_id' => $lessonId,
                'period_id' => (int) ($slot['period_id'] ?? 0),
                'teacher_id' => (int) ($lesson?->pevaluacion?->profesor_id ?? 0),
                'section_id' => (int) ($lesson?->pevaluacion?->seccion_id ?? 0),
                'grupo_estable_id' => $lesson?->pevaluacion?->grupo_estable_id
                    ? (int) $lesson->pevaluacion->grupo_estable_id
                    : null,
                'is_half_group' => (bool) ($lesson?->is_half_group ?? false),
            ])->all();
        })->filter(fn (array $row): bool => $row['period_id'] > 0);
        $internalPublicationConflict = $publicationRows
            ->groupBy(fn (array $row): string => $row['period_id'].':'.$row['section_id'])
            ->first(function ($rows): bool {
                $complete = $rows->filter(fn (array $row): bool => ! $row['is_half_group'])->values();
                for ($index = 0; $index < $complete->count(); $index++) {
                    for ($otherIndex = $index + 1; $otherIndex < $complete->count(); $otherIndex++) {
                        $firstGroup = $complete[$index]['grupo_estable_id'];
                        $secondGroup = $complete[$otherIndex]['grupo_estable_id'];
                        if ($firstGroup === null || $secondGroup === null || $firstGroup === $secondGroup) {
                            return true;
                        }
                    }
                }

                return false;
            });
        if ($internalPublicationConflict) {
            $periodId = (int) $internalPublicationConflict->first()['period_id'];
            $period = TimetablePeriod::query()->find($periodId);
            $periodLabel = $period?->period_label ?? "período {$periodId}";
            $sectionId = (int) $internalPublicationConflict->first()['section_id'];
            $section = $lessons->get((int) $internalPublicationConflict->first()['lesson_id'])
                ?->pevaluacion?->seccion;
            $sectionName = $section?->name ?? 'Sección no disponible';
            $conflictDetails = $internalPublicationConflict
                ->filter(fn (array $row): bool => ! $row['is_half_group'])
                ->filter(function (array $row) use ($internalPublicationConflict): bool {
                    return $internalPublicationConflict->contains(function (array $other) use ($row): bool {
                        if ($other['lesson_id'] === $row['lesson_id'] || $other['is_half_group']) {
                            return false;
                        }

                        return $row['grupo_estable_id'] === null
                            || $other['grupo_estable_id'] === null
                            || $row['grupo_estable_id'] === $other['grupo_estable_id'];
                    });
                })
                ->map(function (array $row) use ($lessons): array {
                    $lesson = $lessons->get($row['lesson_id']);
                    $pevaluacion = $lesson?->pevaluacion;

                    return [
                        'lesson_id' => (int) $row['lesson_id'],
                        'subject' => (string) ($pevaluacion?->pensum?->asignatura?->name ?? 'Sin asignatura'),
                        'teacher_id' => (int) ($pevaluacion?->profesor_id ?? 0),
                        'teacher' => (string) ($pevaluacion?->profesor?->full_name
                            ?? $pevaluacion?->profesor?->name
                            ?? 'Docente no disponible'),
                        'section_id' => (int) ($pevaluacion?->seccion_id ?? 0),
                        'section' => (string) ($pevaluacion?->seccion?->name ?? 'Sección no disponible'),
                        'grupo_estable_id' => $row['grupo_estable_id'],
                        'is_half_group' => (bool) $row['is_half_group'],
                    ];
                })
                ->values();
            $subjects = $conflictDetails
                ->map(fn (array $detail): string => $detail['subject'].' (lesson #'.$detail['lesson_id'].', docente '
                    .$detail['teacher'].' #'.$detail['teacher_id'].')')
                ->implode(' y ');

            Log::channel('timetable')->warning('Publicación bloqueada por colisión interna de sección', [
                'calendar_id' => (int) $this->calendarId,
                'selected_lesson_ids' => $selectedLessonIds->values()->all(),
                'publishable_lesson_ids' => $publishableLessonIds,
                'period_id' => $periodId,
                'period_label' => $periodLabel,
                'section_id' => $sectionId,
                'section' => $sectionName,
                'conflicts' => $conflictDetails->all(),
                'publication_rows' => $internalPublicationConflict->values()->all(),
                'preview_assignment_source' => $this->preview['assignment_source'] ?? null,
                'manual_override' => (bool) ($this->preview['manual_override'] ?? false),
                'user_id' => auth()->id(),
            ]);

            $this->notification()->error(
                'Publicación bloqueada',
                "La sección {$sectionName} (#{$sectionId}) tiene más de una lesson de grupo completo "
                ."en {$periodLabel}: {$subjects}. Cada lesson tiene que ocupar un período diferente, "
                .'o ambas deben configurarse explícitamente como medio grupo si el caso académico lo permite.',
            );

            return;
        }
        $publishablePreview = $this->preview;
        $publishablePreview['assignment'] = $scopedAssignment;
        $publishablePreview['unassigned'] = [];
        $publishablePreview['partial_publish'] = $blockedSectionIds->isNotEmpty();
        $publishablePreview['published_section_ids'] = $publishableSectionIds->all();
        $publishablePreview['excluded_section_ids'] = $blockedSectionIds->all();
        $publishablePreview['selected_lesson_ids'] = $selectedLessonIds->all();
        $publishablePreview['preserved_lesson_ids'] = $preservedLessonIds;

        if ($publishableSectionIds->isEmpty()) {
            $conflictCount = count($readiness['hard_conflicts'] ?? []);
            $this->notification()->error(
                'Publicación bloqueada',
                $conflictCount > 0
                    ? "Se detectaron {$conflictCount} conflicto(s) bloqueante(s), incluyendo colisiones con horarios preservados. Revisa el detalle antes de publicar."
                    : 'No hay secciones publicables con la selección actual. Revisa los horarios preservados y cambia la selección.',
            );

            return;
        }

        $this->busy = true;
        try {
            GenerateTimetableJob::dispatchSync(
                $this->calendarId,
                dryRun: false,
                previewPayload: $publishablePreview,
                lessonIds: $publishableLessonIds,
            );
            $publishedCalendar = TimetableCalendar::query()->find($this->calendarId);
            if (! $publishedCalendar) {
                throw new \RuntimeException('El calendario publicado no está disponible después de guardar el horario.');
            }
            if ($publishedCalendar->status !== TimetableCalendar::STATUS_ACTIVE) {
                throw new \RuntimeException(
                    'La publicación no se completó: el calendario permanece en estado '.$publishedCalendar->status.'.',
                );
            }
            $publishedCalendar->refresh();
            $this->loadCalendars();
            $lastVersion = (int) TimetableCalendarVersion::query()
                ->where('calendar_id', $this->calendarId)
                ->max('version');
            $version = max((int) $publishedCalendar->version, $lastVersion) + 1;
            if ((int) $publishedCalendar->version < $version) {
                $publishedCalendar->update(['version' => $version]);
            }
            $calendarVersion = TimetableCalendarVersion::create([
                'calendar_id' => $this->calendarId,
                'version' => $version,
                'status' => TimetableCalendar::STATUS_ACTIVE,
                'published_by' => auth()->id(),
                'published_at' => now(),
                'quality_score' => $readiness['quality']['score'] ?? null,
                'summary_json' => $readiness + [
                    'partial_publish' => $blockedSectionIds->isNotEmpty(),
                    'published_section_ids' => $publishableSectionIds->all(),
                    'excluded_section_ids' => $blockedSectionIds->all(),
                    'preserved_lesson_ids' => $preservedLessonIds,
                ],
            ]);
            TimetableChangeLog::create([
                'calendar_id' => $this->calendarId,
                'version_id' => $calendarVersion->id,
                'user_id' => auth()->id(),
                'action' => 'publish',
                'metadata_json' => [
                    'previous_version' => $calendar->version,
                    'published_version' => $version,
                    'correlation_id' => (string) str()->uuid(),
                ],
            ]);
            session()->flash(
                'message',
                $blockedSectionIds->isNotEmpty()
                    ? 'Horario publicado parcialmente: se guardaron las secciones sin conflictos bloqueantes.'
                    : 'Horario publicado.',
            );

        } catch (\Throwable $exception) {
            report($exception);
            $this->notification()->error(
                'Publicación no completada',
                'El calendario no se activó y se conservaron los datos existentes. Revisa el log para conocer la causa técnica.',
            );
        } finally {
            $this->busy = false;

            // Rehidrata siempre el componente desde la base de datos al
            // finalizar el proceso, sin solicitar una navegación al navegador.
            $this->refreshWizard();
        }
    }

    private function recordPreviewChange(string $action, ?int $lessonId = null, array $before = [], array $after = []): void
    {
        if (! $this->calendarId) {
            return;
        }

        TimetableChangeLog::create([
            'calendar_id' => $this->calendarId,
            'user_id' => auth()->id(),
            'action' => $action,
            'lesson_id' => $lessonId,
            'before_json' => $before ?: null,
            'after_json' => $after ?: null,
            'metadata_json' => ['preview' => true],
        ]);
    }

    public function undoLastPreviewChange(): void
    {
        if (! $this->preview || ! $this->calendarId) {
            return;
        }

        $change = TimetableChangeLog::query()
            ->where('calendar_id', $this->calendarId)
            ->where('user_id', auth()->id())
            ->where('metadata_json->preview', true)
            ->whereNotIn('action', ['undo_move_preview_lesson', 'undo_remove_preview_lesson', 'undo_add_preview_lesson'])
            ->latest('id')
            ->first();

        if (! $change || ! $change->lesson_id) {
            $this->notification()->warning('Sin cambios para deshacer', 'No hay un cambio manual reciente disponible.');

            return;
        }

        $assignment = $this->preview['assignment'] ?? [];
        $lessonKey = (string) $change->lesson_id;
        $before = $change->before_json ?? [];
        if ($before === []) {
            unset($assignment[$lessonKey]);
        } else {
            $assignment[$lessonKey] = $before;
        }

        $this->preview['assignment'] = $assignment;
        $this->preview['unassigned'] = array_values(array_filter(
            $this->preview['unassigned'] ?? [],
            fn ($id): bool => (int) $id !== (int) $change->lesson_id,
        ));
        $this->preview['manual_override'] = true;
        $this->preview['assignment_source'] = 'manual_preview';
        $this->recordPreviewChange('undo_'.$change->action, $change->lesson_id, $change->after_json ?? [], $before);
        $this->notification()->success('Cambio deshecho', 'Se restauró el estado anterior de la lección.');
    }

    public function restoreGeneratedPreview(): void
    {
        if (! $this->preview || ! array_key_exists('generated_assignment', $this->preview)) {
            $this->notification()->warning('Generación requerida', 'Ejecuta primero una previsualización para poder restaurarla.');

            return;
        }

        $this->preview['assignment'] = $this->preview['generated_assignment'];
        $this->preview['unassigned'] = $this->preview['generated_unassigned'] ?? [];
        $this->preview['manual_override'] = false;
        $this->preview['assignment_source'] = 'solver';
        $this->recordPreviewChange('restore_generated_preview');
        $this->notification()->success('Preview restaurado', 'Se recuperó el resultado original del dry-run.');
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
            : TimetableCalendar::DEFAULT_STRATEGY;
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
            $calendarLessons = TimetableLesson::query()
                ->where('calendar_id', $selectedCalendar->id)
                ->with('pevaluacion.profesor')
                ->withCount('slots')
                ->get();
            $teacherTotals = $calendarLessons
                ->filter(fn (TimetableLesson $lesson): bool => (bool) $lesson->pevaluacion?->profesor_id)
                ->groupBy(fn (TimetableLesson $lesson): int => (int) $lesson->pevaluacion->profesor_id)
                ->map(function ($lessons): array {
                    $profesor = $lessons->first()->pevaluacion->profesor;

                    return [
                        'id' => (int) $profesor->id,
                        'name' => (string) ($profesor->full_name
                            ?? trim(($profesor->lastname ?? '').' '.($profesor->name ?? ''))),
                        'lessons' => $lessons->count(),
                        'blocks_t' => $lessons->sum(fn (TimetableLesson $lesson): int => (int) $lesson->weekly_blocks_t),
                        'blocks_p' => $lessons->sum(fn (TimetableLesson $lesson): int => (int) $lesson->weekly_blocks_p),
                        'required_blocks' => $lessons->sum(
                            fn (TimetableLesson $lesson): int => (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p
                        ),
                        'assigned_slots' => $lessons->sum(fn (TimetableLesson $lesson): int => (int) $lesson->slots_count),
                    ];
                })
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();
            $selectedCalendarDetail = [
                'id' => $selectedCalendar->id,
                'name' => $selectedCalendar->name,
                'status' => $selectedCalendar->status,
                'version' => (int) $selectedCalendar->version,
                'quality_score' => $selectedCalendar->quality_score !== null ? (float) $selectedCalendar->quality_score : null,
                'period_minutes' => (int) $selectedCalendar->period_minutes,
                'max_subjects_per_period' => (int) ($selectedCalendar->max_subjects_per_period ?? 2),
                'strategy' => $selectedCalendar->strategy ?: TimetableCalendar::DEFAULT_STRATEGY,
                'lapso_name' => $selectedCalendar->lapso?->name,
                'pestudio_name' => $selectedCalendar->pestudio?->name,
                'is_editable' => $selectedCalendar->is_editable,
                'created_at' => optional($selectedCalendar->created_at)->format('d/m/Y H:i'),
                'updated_at' => optional($selectedCalendar->updated_at)->format('d/m/Y H:i'),
                'shifts_count' => $periodsOfCalendar->groupBy('shift_id')->count(),
                'periods_count' => $periodsOfCalendar->count(),
                'class_periods_count' => $periodsOfCalendar->where('is_break', false)->count(),
                'break_periods_count' => $periodsOfCalendar->where('is_break', true)->count(),
                'lessons_count' => $calendarLessons->count(),
                'slots_count' => $slotsCount,
                'conflicts_count' => $conflictsCount,
                'teacher_totals' => $teacherTotals,
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
        $teacherScheduleOptions = $this->showTeacherScheduleDialog
            ? $this->teacherScheduleOptions()
            : [];
        $teacherScheduleGrid = $this->showTeacherScheduleDialog
            ? $this->teacherScheduleGrid()
            : [];

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
            'sectionSlotParity' => $this->sectionSlotParity(),
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
            'teacherScheduleOptions' => $teacherScheduleOptions,
            'teacherScheduleGrid' => $teacherScheduleGrid,
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
