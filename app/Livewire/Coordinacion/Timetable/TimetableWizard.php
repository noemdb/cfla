<?php

namespace App\Livewire\Coordinacion\Timetable;

use App\Imports\TimetableLessonsImport;
use App\Jobs\Timetable\GenerateTimetableJob;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableTeacherAvailability;
use Illuminate\Support\Facades\DB;
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
    use WireUiActions, WithFileUploads;

    public int $currentStep = 1;

    // ─── Paso 1 · Calendario ──────────────────────────────────
    public $calendarId = null;

    public array $calendars = [];

    public $lapsoId = null;

    public $pescolarId = null;

    public string $calendarName = '';

    public int $periodMinutes = 60;

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

    // Edición de aula (dialog)
    public bool $roomEditOpen = false;

    public ?int $editingRoomId = null;

    public string $editRoomCode = '';

    public string $editRoomName = '';

    public int $editRoomCapacity = 30;

    public string $editRoomType = 'aula';

    // ─── Paso 3 · Lecciones ────────────────────────────────────
    public array $selectedPevs = [];

    public array $lessons = [];

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

    // ─── Paso 5 · Generar ───────────────────────────────────────
    public ?string $generationState = null;

    public ?array $preview = null;

    public bool $busy = false;

    public bool $dryRunFirst = true;

    public const ROOM_TYPES = ['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'];

    public function mount(): void
    {
        // PLAN-TIMETABLE-002 §4.5: prioriza el activo del lapso vigente; si no
        // hay, mantiene el comportamiento previo (último draft|active).
        $activeCalendar = TimetableCalendar::activeForCurrentLapso()
            ?? TimetableCalendar::query()
                ->whereIn('status', ['draft', 'active'])
                ->latest('id')
                ->first();

        if ($activeCalendar) {
            $this->calendarId = $activeCalendar->id;
            $this->loadCalendar($activeCalendar);
            $this->loadCalendars();
        }

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
            'calendarName' => 'required|string|max:255',
            'periodMinutes' => 'required|integer|min:30|max:120',
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
            'name' => $this->calendarName,
            'period_minutes' => $this->periodMinutes,
            'status' => 'draft',
            'version' => 0,
        ]);

        $this->calendarId = $calendar->id;
        $this->loadCalendar($calendar);
        $this->loadCalendars();
        session()->flash('message', 'Borrador creado. Ahora crea los turnos y períodos.');
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
        $this->periods = [];
        $this->generationState = null;
        $this->preview = null;
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

        $periods = [];
        foreach ([1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie'] as $day => $label) {
            foreach (range(1, 6) as $order) {
                $periods[] = [
                    'day' => $day,
                    'order' => $order,
                    'label' => "{$label} · bloque {$order}",
                ];
            }
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

        $minutes = (int) $this->periodMinutes;
        $perShift = 6;
        $start = strtotime($this->shiftStart);

        DB::transaction(function () use ($minutes, $start) {
            foreach ($this->periods as $p) {
                $offset = ($p['order'] - 1) * $minutes;
                $startTime = date('H:i', $start + $offset * 60);
                $endTime = date('H:i', $start + ($offset + $minutes) * 60);

                TimetablePeriod::create([
                    'calendar_id' => $this->calendarId,
                    'shift_id' => $this->shiftId,
                    'day_of_week' => $p['day'],
                    'order_in_day' => $p['order'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_break' => false,
                ]);
            }
        });

        $this->periods = [];
        session()->flash('message', 'Períodos creados para la semana (Lun–Vie).');
        $this->goToStep(2);
    }

    // ─── Paso 2 · Aulas ────────────────────────────────────────

    public function saveRoom(): void
    {
        $this->validate([
            'roomCode' => 'required|string|max:20|unique:timetable_rooms,code',
            'roomName' => 'required|string|max:80',
            'roomCapacity' => 'required|integer|min:1',
            'roomType' => 'required|in:'.implode(',', self::ROOM_TYPES),
        ]);

        TimetableRoom::create([
            'code' => $this->roomCode,
            'name' => $this->roomName,
            'capacity' => $this->roomCapacity,
            'type' => $this->roomType,
            'status_active' => true,
        ]);

        $this->roomCode = '';
        $this->roomName = '';
        $this->roomCapacity = 30;
        $this->roomType = 'aula';
        $this->reloadRooms();
    }

    public function deleteRoom($roomId): void
    {
        TimetableRoom::query()->where('id', $roomId)->delete();
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
     * (seccion_id, índice único).
     */
    public function bulkCreateRooms(): void
    {
        if (! $this->calendarId) {
            $this->notification()->warning('Calendario requerido', 'Crea el calendario primero.');

            return;
        }

        $seccions = \App\Models\app\Academy\Seccion::query()
            ->where('seccions.status_active', 'true')
            ->whereHas('grado', fn ($q) => $q
                ->where('grados.status_active', 'true')
                ->whereHas('pestudio', fn ($p) => $p->where('pestudios.status_active', 'true')))
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
            // Regla "uno y solo un aula por grado/sección": se omite si la
            // sección ya tiene aula asociada (seccion_id, índice único). No se
            // deduce por nombre porque dos secciones de pestudios distintos
            // pueden compartir el mismo nombre de grado+sección.
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
        TimetableRoom::query()->delete();
        $this->pendingRooms = [];
        $this->reloadRooms();

        $this->notification()->success('Aulas eliminadas', 'Se eliminaron todas las aulas registradas.');
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
            ->with(['seccion.grado.pestudio.peducativo'])
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
        $this->loadLessons();
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
        foreach ($this->lessons as $pevId => &$lesson) {
            $lesson['room_type_required'] = $this->bulkRoomType ?: null;
        }
        unset($lesson);

        $this->autosaveLessons();
        $this->notification()->success('Aula aplicada', 'Se asignó el tipo de aula a todas las lecciones.');
    }

    /**
     * Autoguardado (borrador): persiste las lecciones sin navegar de paso.
     */
    public function autosaveLessons(): void
    {
        if (! $this->calendarId) {
            return;
        }

        $this->persistLessons();
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

            return;
        }

        $calendar = TimetableCalendar::find($this->calendarId);
        if (! $calendar) {
            $this->lessons = [];

            return;
        }

        $periodMinutes = max(1, (int) $calendar->period_minutes);
        $ids = $this->selectedPevIds();
        $pevs = Pevaluacion::query()
            ->with(['pensum.asignatura', 'seccion', 'profesor', 'grupoEstable'])
            ->where('lapso_id', $calendar->lapso_id)
            ->when($ids, fn ($q) => $q->whereIn('id', $ids))
            ->get();

        // Preserva los valores que el usuario ya ajustó (turno/aula/prioridad/
        // locked) al reconstruir tras marcar/desmarcar otra lección.
        $existing = $this->lessons;

        $this->lessons = $pevs->mapWithKeys(function ($pev) use ($periodMinutes, $existing) {
            $lesson = $this->buildLesson($pev, $periodMinutes);

            if (isset($existing[$pev->id])) {
                $lesson['shift_id'] = $existing[$pev->id]['shift_id'];
                $lesson['room_type_required'] = $existing[$pev->id]['room_type_required'] ?? null;
                $lesson['priority'] = (int) ($existing[$pev->id]['priority'] ?? 0);
                $lesson['locked'] = (bool) ($existing[$pev->id]['locked'] ?? false);
            }

            return [$pev->id => $lesson];
        })->all();
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

        $this->validateLessons();
        $this->persistLessons();
        session()->flash('message', count($this->lessons).' lecciones registradas.');
        $this->goToStep(4);
    }

    private function validateLessons(): void
    {
        foreach ($this->lessons as $lesson) {
            if (($lesson['weekly_blocks_t'] ?? 0) <= 0 && ($lesson['weekly_blocks_p'] ?? 0) <= 0) {
                session()->flash('error', "La lección «{$lesson['name']}» debe tener al menos un bloque.");

                return;
            }
        }
    }

    private function persistLessons(): void
    {
        TimetableLesson::query()->where('calendar_id', $this->calendarId)->delete();

        foreach ($this->lessons as $lesson) {
            TimetableLesson::create([
                'calendar_id' => $this->calendarId,
                'pevaluacion_id' => $lesson['pev_id'],
                'shift_id' => $lesson['shift_id'],
                'weekly_blocks_t' => (int) $lesson['weekly_blocks_t'],
                'weekly_blocks_p' => (int) $lesson['weekly_blocks_p'],
                'room_type_required' => $lesson['room_type_required'] ?: null,
                'priority' => (int) ($lesson['priority'] ?? 0),
                'locked' => (bool) ($lesson['locked'] ?? false),
            ]);
        }
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

            $pevIds = array_filter(array_map('intval', array_column($import->rows, 'pevaluacion_id')));
            $pevs = Pevaluacion::query()
                ->with(['pensum.asignatura', 'seccion', 'profesor', 'grupoEstable'])
                ->where('lapso_id', $calendar->lapso_id)
                ->whereIn('id', $pevIds)
                ->get()
                ->keyBy('id');

            $imported = [];
            $errors = [];
            $seen = [];

            foreach ($import->rows as $i => $row) {
                $rowNum = $i + 1;
                $pevId = (int) ($row['pevaluacion_id'] ?? 0);

                if (! $pevId) {
                    $errors[] = "Fila {$rowNum}: falta pevaluacion_id.";

                    continue;
                }

                if (isset($seen[$pevId])) {
                    $errors[] = "Fila {$rowNum}: la lección {$pevId} está duplicada en el archivo.";

                    continue;
                }
                $seen[$pevId] = true;

                $pev = $pevs->get($pevId);
                if (! $pev) {
                    $errors[] = "Fila {$rowNum}: la lección {$pevId} no existe en el lapso.";

                    continue;
                }

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
            fputcsv($out, ['pevaluacion_id', 'turno', 'bloques_t', 'bloques_p', 'aula', 'prioridad']);
            fputcsv($out, ['', 'M', '', '', 'aula', '0']);
            fclose($out);
        }, 'plantilla-lecciones.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ─── Paso 4 · Disponibilidad ───────────────────────────────

    public function setAllAvailable(): void
    {
        if (! $this->calendarId) {
            return;
        }

        $periodIds = TimetablePeriod::query()
            ->where('calendar_id', $this->calendarId)
            ->pluck('id')
            ->all();

        $profesorIds = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->with('pevaluacion')
            ->get()
            ->pluck('pevaluacion.profesor_id')
            ->unique()
            ->values()
            ->all();

        foreach ($profesorIds as $profesorId) {
            foreach ($periodIds as $periodId) {
                TimetableTeacherAvailability::updateOrCreate(
                    [
                        'calendar_id' => $this->calendarId,
                        'profesor_id' => $profesorId,
                        'period_id' => $periodId,
                    ],
                    ['is_available' => true],
                );
            }
        }

        session()->flash('message', 'Disponibilidad marcada para todos los docentes.');
    }

    public function saveAvailability(): void
    {
        if (! $this->calendarId) {
            session()->flash('error', 'Crea el calendario primero.');

            return;
        }

        foreach ($this->availability as $profesorId => $periods) {
            foreach ($periods as $periodId => $isAvailable) {
                TimetableTeacherAvailability::updateOrCreate(
                    [
                        'calendar_id' => $this->calendarId,
                        'profesor_id' => $profesorId,
                        'period_id' => $periodId,
                    ],
                    ['is_available' => (bool) $isAvailable],
                );
            }
        }

        session()->flash('message', 'Disponibilidad guardada.');
        $this->goToStep(5);
    }

    public function updatedCalendarId(): void
    {
        // Switcher global: al cambiar el calendario se recarga todo el contexto.
        $this->selectCalendar($this->calendarId);
    }

    public function loadAvailability(): void
    {
        $this->availability = [];
        if (! $this->calendarId) {
            return;
        }

        $rows = TimetableTeacherAvailability::query()
            ->where('calendar_id', $this->calendarId)
            ->get();

        foreach ($rows as $row) {
            $this->availability[$row->profesor_id][$row->period_id] = (bool) $row->is_available;
        }
    }

    // ─── Paso 5 · Generar ───────────────────────────────────────

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

    public function confirmAndPublish(): void
    {
        if (! $this->calendarId || ! $this->preview) {
            session()->flash('error', 'Primero ejecuta una previsualización (dry-run).');

            return;
        }

        $this->busy = true;
        try {
            GenerateTimetableJob::dispatchSync($this->calendarId, dryRun: false);
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
        $this->calendarName = $calendar->name;
        $this->periodMinutes = (int) $calendar->period_minutes;
    }

    /**
     * PLAN-TIMETABLE-002 §4.5 — Alternativas (calendarios) del lapso en edición,
     * ordenadas activo → borrador → generando → archivado.
     */
    private function loadCalendars(): void
    {
        $this->calendars = $this->lapsoId
            ? TimetableCalendar::query()
                ->forLapso($this->lapsoId)
                ->orderByRaw("FIELD(status, 'active', 'draft', 'generating', 'archived'), id DESC")
                ->get()
                ->map(fn ($c) => $c->toArray())
                ->values()
                ->all()
            : [];
    }

    private function defaultShiftId(): int
    {
        return TimetableShift::query()->orderBy('id')->value('id') ?? 0;
    }

    public function goToStep(int $step): void
    {
        $this->currentStep = max(1, min(5, $step));
    }

    /**
     * Layout de render. Los submódulos (p. ej. planning) lo sobreescriben.
     */
    protected function getLayout(): string
    {
        return 'coordinacion.layouts.app';
    }

    public function render(): \Illuminate\View\View
    {
        $lapsos = Lapso::orderBy('finicial', 'desc')->get();

        $shifts = TimetableShift::query()->orderBy('start_time')->get();

        $pevaluaciones = $this->allPevaluaciones();

        $profesores = collect();
        if ($this->calendarId && $pevaluaciones->isNotEmpty()) {
            $profesores = \App\Models\app\Academy\Profesor::query()
                ->whereIn('id', $pevaluaciones->pluck('profesor_id')->unique())
                ->orderBy('lastname')
                ->get();
        }

        $periodsList = $this->calendarId
            ? TimetablePeriod::query()
                ->where('calendar_id', $this->calendarId)
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

        $selectedCount = count(array_filter($this->selectedPevs));

        return view('livewire.coordinacion.timetable.timetable-wizard', [
            'lapsos' => $lapsos,
            'shifts' => $shifts,
            'pevaluaciones' => $pevaluaciones,
            'pevaluacionesGrouped' => $tabData['pevaluacionesGrouped'],
            'tabPestudioOptions' => $tabData['tabPestudioOptions'],
            'tabGradoOptions' => $tabData['tabGradoOptions'],
            'tabSeccionOptions' => $tabData['tabSeccionOptions'],
            'tabActivePevaluaciones' => $tabData['tabActivePevaluaciones'],
            'savedPevIds' => $savedPevIds,
            'step3Warnings' => $step3Warnings,
            'step3SelectedCount' => $selectedCount,
            'profesores' => $profesores,
            'periodsList' => $periodsList,
            'calendarPeriodMinutes' => $calendarPeriodMinutes,
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

        foreach ($this->lessons as $pevId => $lesson) {
            $list = [];

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

        $tabSeccionOptions = collect($activeGrado['secciones'] ?? [])->map(fn ($s) => ['id' => $s['seccion_id'], 'name' => $s['seccion_name']])->values()->all();

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
