<?php

namespace App\Livewire\Coordinacion\Timetable;

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

/**
 * SPEC-TIMETABLE-001 §5 — Wizard de horario (pasos 1 a 5).
 *
 * Un solo componente Livewire con `currentStep`, layout coordinacion, sin
 * clases de pasos separadas (patrón LessonWizard de LMS).
 */
class TimetableWizard extends Component
{
    public int $currentStep = 1;

    // ─── Paso 1 · Calendario ──────────────────────────────────
    public $calendarId = null;

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

    public string $roomCode = '';

    public string $roomName = '';

    public int $roomCapacity = 30;

    public string $roomType = 'aula';

    // ─── Paso 3 · Lecciones ────────────────────────────────────
    public array $selectedPevs = [];

    public array $lessons = [];

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
        $activeCalendar = TimetableCalendar::query()
            ->whereIn('status', ['draft', 'active'])
            ->latest('id')
            ->first();

        if ($activeCalendar) {
            $this->calendarId = $activeCalendar->id;
            $this->loadCalendar($activeCalendar);
        }

        $this->rooms = TimetableRoom::query()
            ->orderBy('code')
            ->get()
            ->map(fn ($r) => $r->toArray())
            ->values()
            ->all();
    }

    public function updatedLapsoId($value): void
    {
        if (! $value) {
            return;
        }

        $lapso = Lapso::find($value);
        if ($lapso) {
            $this->calendarName = 'Horario '.$lapso->name;
            $this->pescolarId = $lapso->pescolar_id;
        }
    }

    public function createCalendar(): void
    {
        $this->validate([
            'lapsoId' => 'required',
            'calendarName' => 'required|string|max:255',
            'periodMinutes' => 'required|integer|min:30|max:120',
        ]);

        $exists = TimetableCalendar::query()->where('lapso_id', $this->lapsoId)->exists();
        if ($exists) {
            session()->flash('error', 'Ya existe un calendario para ese lapso.');

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
        session()->flash('message', 'Calendario creado. Ahora crea los turnos y períodos.');
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

        $shift = TimetableShift::create([
            'code' => $this->shiftCode,
            'name' => $this->shiftName,
            'start_time' => $this->shiftStart,
            'end_time' => $this->shiftEnd,
        ]);

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

        if (TimetablePeriod::query()->where('calendar_id', $this->calendarId)->exists()) {
            session()->flash('error', 'Los períodos ya están generados para este calendario.');

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

        if (TimetablePeriod::query()->where('calendar_id', $this->calendarId)->exists()) {
            session()->flash('error', 'Los períodos ya existen.');

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

    private function reloadRooms(): void
    {
        $this->rooms = TimetableRoom::query()
            ->orderBy('code')
            ->get()
            ->map(fn ($r) => $r->toArray())
            ->values()
            ->all();
    }

    // ─── Paso 3 · Lecciones ────────────────────────────────────

    public function updatedSelectedPevs(): void
    {
        $this->loadLessons();
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
        $pevs = Pevaluacion::query()
            ->with(['pensum.asignatura', 'seccion', 'profesor'])
            ->where('lapso_id', $calendar->lapso_id)
            ->when($this->selectedPevs, fn ($q) => $q->whereIn('id', $this->selectedPevs))
            ->get();

        $this->lessons = $pevs->map(function ($pev) use ($periodMinutes) {
            $asignatura = $pev->pensum?->asignatura;

            return [
                'pev_id' => $pev->id,
                'name' => $asignatura?->name.' · '.($pev->seccion?->name ?? ''),
                'seccion_id' => $pev->seccion_id,
                'profesor_id' => $pev->profesor_id,
                'weekly_blocks_t' => (int) ceil(((int) ($asignatura?->hour_t_week ?? 0)) * 60 / $periodMinutes),
                'weekly_blocks_p' => (int) ceil(((int) ($asignatura?->hour_p_week ?? 0)) * 60 / $periodMinutes),
                'shift_id' => $this->defaultShiftId(),
                'room_type_required' => null,
                'priority' => 0,
                'locked' => false,
            ];
        })->values()->all();
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
        $this->loadAvailability();
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

        $pevaluaciones = collect();
        $profesores = collect();

        if ($this->calendarId) {
            $calendar = TimetableCalendar::find($this->calendarId);
            if ($calendar) {
                $pevaluaciones = Pevaluacion::query()
                    ->with(['pensum.asignatura', 'seccion', 'profesor'])
                    ->where('lapso_id', $calendar->lapso_id)
                    ->get();

                $profesores = \App\Models\app\Academy\Profesor::query()
                    ->whereIn('id', $pevaluaciones->pluck('profesor_id')->unique())
                    ->orderBy('lastname')
                    ->get();
            }
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

        return view('livewire.coordinacion.timetable.timetable-wizard', [
            'lapsos' => $lapsos,
            'shifts' => $shifts,
            'pevaluaciones' => $pevaluaciones,
            'profesores' => $profesores,
            'periodsList' => $periodsList,
            'calendarPeriodMinutes' => $calendarPeriodMinutes,
        ])->layout($this->getLayout());
    }
}
