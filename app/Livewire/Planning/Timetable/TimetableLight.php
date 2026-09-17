<?php

namespace App\Livewire\Planning\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard as BaseTimetableWizard;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use Illuminate\View\View;

/**
 * Asistente «light» de horarios (ADR-TT-006): solo dos pasos.
 *
 *  1. Elegir el calendario (combinación del Paso 1 del wizard completo).
 *  2. Grilla de la sección (Paso 5) centrada en dos acciones: agregar /
 *     intercambiar lecciones en los slots y alertar colisiones (no bloqueantes).
 *
 * Reutiliza toda la lógica del wizard completo (drag & drop, persistencia,
 * detección de colisiones, agregar/retirar) pero con una vista reducida.
 */
class TimetableLight extends BaseTimetableWizard
{
    /** 1 = seleccionar calendario; 2 = grilla. */
    public int $lightStep = 1;

    /** Docente resaltado en la grilla (null = sin resaltado). */
    public ?int $highlightProfesorId = null;

    /** Hora del último guardado (indicador «guardado / guardando»). */
    public ?string $lastSavedAt = null;

    public function mount(int|string|null $calendar = null): void
    {
        if (is_numeric($calendar)) {
            $this->calendarId = (int) $calendar;
        }

        parent::mount();

        if ($this->calendarId) {
            $this->enterGrid();
        } else {
            $this->lightStep = 1;
            $this->currentStep = 1;
        }
    }

    protected function getLayout(): string
    {
        return 'planning.layouts.app';
    }

    public function moduleRoutePrefix(): string
    {
        return 'app.planning';
    }

    /** Paso 1 → 2: selecciona el calendario y abre la grilla. */
    public function chooseCalendar(int $calendarId): void
    {
        $calendar = TimetableCalendar::query()->find($calendarId);

        if (! $calendar) {
            $this->notification()->error(
                'Calendario no encontrado',
                'No se pudo abrir el horario seleccionado.',
            );

            return;
        }

        $this->selectCalendar((int) $calendar->id);
        $this->enterGrid();
    }

    /** Paso 2 → 1: vuelve a la selección de calendario. */
    public function changeCalendar(): void
    {
        $this->lightStep = 1;
        $this->currentStep = 1;
        $this->calendarId = null;
        $this->activePestudioId = null;
        $this->activeGradoId = null;
        $this->activeSeccionId = null;
        $this->preview = null;
        $this->generationState = null;
        $this->showAddPreviewLessonModal = false;
        $this->highlightProfesorId = null;
    }

    /** Alterna el resaltado de todas las celdas de un docente. */
    public function toggleHighlightProfesor(int $profesorId): void
    {
        if ($profesorId <= 0) {
            return;
        }

        $this->highlightProfesorId = $this->highlightProfesorId === $profesorId
            ? null
            : $profesorId;
    }

    public function clearHighlightProfesor(): void
    {
        $this->highlightProfesorId = null;
    }

    // ─── Sincronización de `preview_payload` ────────────────────────────────
    // El light edita `timetable_slots` directamente; tras cada cambio se
    // reconstruye el payload para que el wizard completo y los PDFs por
    // defecto reflejen el horario persistido (no una previsualización vieja).

    public function movePreviewLesson(int $lessonId, int $fromPeriodId, int $newPeriodId): void
    {
        parent::movePreviewLesson($lessonId, $fromPeriodId, $newPeriodId);
        $this->afterEdit();
    }

    public function addPreviewLesson(int $lessonId): void
    {
        parent::addPreviewLesson($lessonId);
        $this->afterEdit();
    }

    public function addPreviewPevaluacion(int $pevaluacionId): void
    {
        parent::addPreviewPevaluacion($pevaluacionId);
        $this->afterEdit();
    }

    public function removePreviewLesson(int $lessonId, ?int $periodId = null): void
    {
        parent::removePreviewLesson($lessonId, $periodId);
        $this->afterEdit();
    }

    public function togglePreviewSlotLock(int $lessonId, int $periodId): void
    {
        parent::togglePreviewSlotLock($lessonId, $periodId);
        $this->afterEdit();
    }

    public function toggleSectionPreviewSlotsLock(): void
    {
        parent::toggleSectionPreviewSlotsLock();
        $this->afterEdit();
    }

    /**
     * Sincroniza el payload persistido y marca la hora del último guardado.
     * El indicador «guardado / guardando» lo consume la vista.
     */
    private function afterEdit(): void
    {
        $this->syncPreviewPayloadFromSlots();
        $this->lastSavedAt = now()->format('H:i:s');
    }

    private function enterGrid(): void
    {
        // Un calendario sin slots no tiene preview persistido: se arranca con
        // una asignación vacía para que la grilla permita agregar lecciones.
        if (! $this->preview) {
            $this->preview = [
                'assignment' => [],
                'unassigned' => [],
                'assignment_source' => 'light_empty',
                'timed_out' => false,
            ];
            $this->generationState = 'preview_ready';
        }

        $this->lightStep = 2;
        $this->currentStep = 5;
    }

    /**
     * Calendarios disponibles para el Paso 1, con metadatos para la tarjeta.
     *
     * @return list<array{id:int, name:string, status:string, lapso:string, pestudio:string, slots:int, lessons:int}>
     */
    public function lightCalendars(): array
    {
        return TimetableCalendar::query()
            ->with(['lapso:id,name', 'pestudio:id,name'])
            ->withCount(['slots', 'lessons'])
            ->orderByRaw("FIELD(status, 'active', 'draft', 'generating', 'archived'), id DESC")
            ->get()
            ->map(fn (TimetableCalendar $calendar): array => [
                'id' => (int) $calendar->id,
                'name' => (string) $calendar->name,
                'status' => (string) $calendar->status,
                'lapso' => (string) ($calendar->lapso?->name ?? ''),
                'pestudio' => (string) ($calendar->pestudio?->name ?? ''),
                'slots' => (int) $calendar->slots_count,
                'lessons' => (int) $calendar->lessons_count,
            ])
            ->all();
    }

    /**
     * Navegación P.Estudio → Grado → Sección para los selectores del Paso 2,
     * calculada directamente desde las lessons del calendario (sin el costo del
     * render completo del wizard). Auto-selecciona el primer valor disponible.
     *
     * @return array{pestudios: list<array{id:int,name:string}>, grados: list<array{id:int,name:string,pestudio_id:int}>, secciones: list<array{id:int,name:string,grado_id:int}>}
     */
    private function lightNavigation(): array
    {
        $lessons = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereHas('pevaluacion.seccion', fn ($query) => $query->where('seccions.status_active', 'true'))
            ->whereHas('pevaluacion.seccion.grado', fn ($query) => $query->where('grados.status_active', 'true'))
            ->with(['pevaluacion.seccion.grado.pestudio'])
            ->get();

        $pestudios = [];
        $grados = [];
        $secciones = [];

        foreach ($lessons as $lesson) {
            $seccion = $lesson->pevaluacion?->seccion;
            $grado = $seccion?->grado;

            if (! $seccion || ! $grado) {
                continue;
            }

            $pestudio = $grado->pestudio;
            $pestudioId = (int) ($pestudio?->id ?? 0);

            if ($pestudioId > 0) {
                $pestudios[$pestudioId] = ['id' => $pestudioId, 'name' => (string) $pestudio->name];
            }

            $grados[(int) $grado->id] = [
                'id' => (int) $grado->id,
                'name' => (string) $grado->name,
                'pestudio_id' => $pestudioId,
            ];

            $secciones[(int) $seccion->id] = [
                'id' => (int) $seccion->id,
                'name' => (string) $seccion->name,
                'grado_id' => (int) $grado->id,
            ];
        }

        $pestudios = array_values($pestudios);

        if ($pestudios !== [] && ! collect($pestudios)->contains('id', (int) $this->activePestudioId)) {
            $this->activePestudioId = $pestudios[0]['id'];
        }

        $gradosFiltrados = array_values(array_filter(
            $grados,
            fn (array $grado): bool => (int) $grado['pestudio_id'] === (int) $this->activePestudioId,
        ));

        if ($gradosFiltrados !== [] && ! collect($gradosFiltrados)->contains('id', (int) $this->activeGradoId)) {
            $this->activeGradoId = $gradosFiltrados[0]['id'];
        }

        $seccionesFiltradas = array_values(array_filter(
            $secciones,
            fn (array $seccion): bool => (int) $seccion['grado_id'] === (int) $this->activeGradoId,
        ));

        if ($seccionesFiltradas !== [] && ! collect($seccionesFiltradas)->contains('id', (int) $this->activeSeccionId)) {
            $this->activeSeccionId = $seccionesFiltradas[0]['id'];
        }

        return [
            'pestudios' => $pestudios,
            'grados' => $gradosFiltrados,
            'secciones' => $seccionesFiltradas,
        ];
    }

    /**
     * Render propio y ligero: solo lo que la vista del asistente consume
     * (períodos, turnos, grilla de la sección y navegación), en vez del render
     * completo del wizard (pevaluaciones, disponibilidad, aulas, paridad…).
     */
    public function render(): View
    {
        $shifts = TimetableShift::query()->orderBy('start_time')->get();

        $periodsList = $this->calendarId
            ? TimetablePeriod::query()
                ->where('calendar_id', $this->calendarId)
                ->with('shift:id,code,name')
                ->orderBy('day_of_week')
                ->orderBy('order_in_day')
                ->get()
            : collect();

        $lightCalendars = $this->lightStep === 1 ? $this->lightCalendars() : [];

        $sectionPreviewGrid = [];
        $tabPestudioOptions = [];
        $tabGradoOptions = [];
        $tabSeccionOptions = [];

        if ($this->calendarId) {
            $nav = $this->lightNavigation();
            $tabPestudioOptions = $nav['pestudios'];
            $tabGradoOptions = $nav['grados'];
            $tabSeccionOptions = $nav['secciones'];

            $sectionPreviewGrid = $this->previewSectionGrid(
                is_numeric($this->activeSeccionId) ? (int) $this->activeSeccionId : 0,
            );
        }

        $teacherScheduleOptions = [];
        $teacherScheduleCalendars = [];
        $teacherScheduleGrid = [];
        $teacherScheduleHasAssignments = false;

        if ($this->showTeacherScheduleDialog) {
            $teacherScheduleOptions = $this->teacherScheduleOptions();
            $blocks = $this->teacherScheduleCalendarGrids();

            $teacherScheduleCalendars = collect($blocks)->map(fn (array $block): array => [
                'calendar_id' => $block['calendar_id'],
                'pestudio' => $block['pestudio'],
                'calendar' => $block['calendar'],
            ])->all();

            $activeCalendarId = (int) ($this->teacherScheduleActiveCalendarId ?? 0);
            $activeBlock = $activeCalendarId > 0
                ? collect($blocks)->firstWhere('calendar_id', $activeCalendarId)
                : null;

            if (! $activeBlock && $blocks !== []) {
                $activeBlock = $blocks[0];
                $this->teacherScheduleActiveCalendarId = (int) $activeBlock['calendar_id'];
            }

            $teacherScheduleGrid = $activeBlock['rows'] ?? [];
            $teacherScheduleHasAssignments = (bool) ($activeBlock['has_assignments'] ?? false);
        }

        return view('livewire.planning.timetable.timetable-light', [
            'lightCalendars' => $lightCalendars,
            'shifts' => $shifts,
            'periodsList' => $periodsList,
            'sectionPreviewGrid' => $sectionPreviewGrid,
            'activeSectionLocked' => $this->activeSectionTimetableLocked(),
            'tabPestudioOptions' => $tabPestudioOptions,
            'tabGradoOptions' => $tabGradoOptions,
            'tabSeccionOptions' => $tabSeccionOptions,
            'moduleRoutePrefix' => $this->moduleRoutePrefix(),
            'teacherScheduleOptions' => $teacherScheduleOptions,
            'teacherScheduleCalendars' => $teacherScheduleCalendars,
            'teacherScheduleGrid' => $teacherScheduleGrid,
            'teacherScheduleHasAssignments' => $teacherScheduleHasAssignments,
        ])->layout($this->getLayout());
    }
}
