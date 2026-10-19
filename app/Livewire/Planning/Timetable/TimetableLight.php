<?php

namespace App\Livewire\Planning\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard as BaseTimetableWizard;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use Illuminate\View\View;
use Livewire\Attributes\Url;

/**
 * Asistente «light» de horarios (ADR-TT-006): dos pasos.
 *
 *  1. Elegir el calendario (combinación del Paso 1 del wizard completo).
 *  2. Espacio de trabajo con dos tabs sobre la sección activa:
 *     «Grilla» (Paso 5: drag & drop y colisiones) y «Clases» (Paso 3:
 *     asignaturas, bloques, turno, aula y agrupación).
 *
 * La selección P.Estudio → Grado → Sección es común a ambos tabs y se
 * construye desde todas las Pevaluaciones del lapso. Reutiliza toda la lógica
 * del wizard completo (drag & drop, persistencia, detección de colisiones,
 * agregar/retirar) pero con una vista reducida.
 */
class TimetableLight extends BaseTimetableWizard
{
    /** 1 = seleccionar calendario; 2 = espacio de trabajo (grilla | clases). */
    public int $lightStep = 1;

    /** Tab activo dentro del paso 2: grid (grilla) | classes (Paso 3). */
    #[Url(as: 'tab', history: true)]
    public string $lightTab = 'grid';

    /** Docente resaltado en la grilla (null = sin resaltado). */
    public ?int $highlightProfesorId = null;

    /** Hora del último guardado (indicador «guardado / guardando»). */
    public ?string $lastSavedAt = null;

    public function mount(int|string|null $calendar = null): void
    {
        // Normaliza ?tab=... también en la carga inicial (Livewire hidrata la
        // propiedad desde la URL antes de mount, pero no siempre dispara el
        // hook `updatedLightTab`).
        $this->lightTab = $this->lightTab === 'classes' ? 'classes' : 'grid';

        if (is_numeric($calendar)) {
            $this->calendarId = (int) $calendar;
        } elseif (! $this->calendarId && is_numeric(request()->query('calendarId'))) {
            // Entrada directa con ?calendarId=N (el #[Url] del componente base
            // puede hidratarse después del mount): se toma el query string.
            $this->calendarId = (int) request()->query('calendarId');
        }

        parent::mount();

        if ($this->calendarId) {
            // El tab Clases administra todas las lessons guardadas, incluidas
            // las que aún no tienen slots (recién creadas desde la tabla).
            $this->loadLessons(true);
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

    /** Paso 2 → 1: vuelve a la selección de calendario (pantalla inicial). */
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
        $this->showTeacherScheduleDialog = false;
        $this->teacherScheduleActiveCalendarId = null;
    }

    /** Cambia el tab visible del modo rápido (grid ↔ classes). */
    public function setLightTab(string $tab): void
    {
        $this->lightTab = $tab === 'classes' ? 'classes' : 'grid';

        // Al cambiar de tab se cierran overlays de la grilla para no dejar
        // capas abiertas sobre la otra vista.
        $this->showAddPreviewLessonModal = false;
        $this->showTeacherScheduleDialog = false;
        $this->highlightProfesorId = null;
    }

    /** Normaliza valores entrantes por URL (?tab=...) a un tab soportado. */
    public function updatedLightTab(string $value): void
    {
        $normalized = $value === 'classes' ? 'classes' : 'grid';

        if ($this->lightTab !== $normalized) {
            $this->lightTab = $normalized;
        }
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

    /**
     * Guardar desde el tab Clases vuelve a la grilla para ver el resultado.
     * Reconstruye el payload desde los slots persistidos (incluido el caso
     * de 0 slots) y refresca la copia en memoria usada por la grilla.
     */
    protected function afterLessonsSaved(): void
    {
        $this->lightTab = 'grid';
        $this->lightStep = 2;
        $this->currentStep = 5;

        $this->syncPreviewPayloadFromSlots();

        $calendar = TimetableCalendar::query()->find($this->calendarId);

        if ($calendar) {
            $this->preview = $calendar->preview_payload;
            $this->generationState = 'preview_ready';
        }

        $this->lastSavedAt = now()->format('H:i:s');

        $this->notification()->success(
            'Clases guardadas',
            'La grilla se actualizó con los cambios.',
        );
    }

    /**
     * Al abrir un calendario, prioriza la sección que ya tiene lessons.
     * Si no hay lessons, el render cae en la primera Pevaluación disponible
     * (selección común construida desde todas las Pevaluaciones del lapso).
     */
    private function initializeActiveSectionFromLessons(): void
    {
        if (is_numeric($this->activeSeccionId) && (int) $this->activeSeccionId > 0) {
            return;
        }

        $lesson = TimetableLesson::query()
            ->where('calendar_id', $this->calendarId)
            ->whereHas('pevaluacion.seccion', fn ($query) => $query->where('seccions.status_active', 'true'))
            ->whereHas('pevaluacion.seccion.grado', fn ($query) => $query->where('grados.status_active', 'true'))
            ->with('pevaluacion.seccion.grado')
            ->orderBy('id')
            ->first();

        $seccion = $lesson?->pevaluacion?->seccion;
        $grado = $seccion?->grado;

        if (! $seccion || ! $grado) {
            return;
        }

        $this->activePestudioId = $grado->pestudio_id;
        $this->activeGradoId = $grado->id;
        $this->activeSeccionId = $seccion->id;
    }

    private function enterGrid(): void
    {
        $this->initializeActiveSectionFromLessons();

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
     * Render propio del asistente: períodos, turnos, selección común
     * (P.Estudio → Grado → Sección) y, en el tab Grilla, la previsualización
     * de la sección activa. La tabla del tab Clases reutiliza el parcial del
     * Paso 3 con las Pevaluaciones de la sección activa.
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
        $step3 = [];

        if ($this->calendarId) {
            // La selección común (P.Estudio → Grado → Sección) se construye
            // desde TODAS las Pevaluaciones del lapso, no solo desde las
            // lessons ya creadas. Así el tab Clases puede crear clases desde
            // cero para cualquier sección.
            $step3 = $this->step3ViewData($periodsList);
            $tabPestudioOptions = $step3['tabPestudioOptions'];
            $tabGradoOptions = $step3['tabGradoOptions'];
            $tabSeccionOptions = $step3['tabSeccionOptions'];

            // La tabla de clases siempre trabaja con la sección activa.
            $step3['rows'] = $step3['tabActivePevaluaciones'];
            $step3['step3SelectedCount'] = $step3['tabActivePevaluaciones']
                ->filter(function ($pev): bool {
                    $pevId = (int) $pev->id;

                    return (array_key_exists($pevId, $this->selectedPevs) && (bool) $this->selectedPevs[$pevId])
                        || in_array($pevId, array_map('intval', array_values($this->selectedPevs)), true);
                })
                ->count();

            if ($this->lightTab !== 'classes') {
                $sectionPreviewGrid = $this->previewSectionGrid(
                    is_numeric($this->activeSeccionId) ? (int) $this->activeSeccionId : 0,
                );
            }
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

        return view('livewire.planning.timetable.timetable-light', array_merge([
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
        ], $step3))->layout($this->getLayout());
    }
}
