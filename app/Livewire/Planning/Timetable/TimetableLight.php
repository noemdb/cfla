<?php

namespace App\Livewire\Planning\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard as BaseTimetableWizard;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use Illuminate\Support\Facades\DB;
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
 * La selección Grado → Sección es común a ambos tabs y se construye desde
 * todas las Pevaluaciones del lapso. Reutiliza toda la lógica
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

    /**
     * Paso 2: cambia el calendario desde el selector de la cabecera. Solo
     * permite calendarios activos para no abrir borradores o archivados por
     * accidente.
     */
    public function switchCalendar(int $calendarId): void
    {
        if ($calendarId === (int) $this->calendarId) {
            return;
        }

        $calendar = TimetableCalendar::query()
            ->where('status', TimetableCalendar::STATUS_ACTIVE)
            ->find($calendarId);

        if (! $calendar) {
            $this->notification()->warning(
                'Calendario no disponible',
                'Solo puedes cambiar a un calendario activo.',
            );

            return;
        }

        $this->chooseCalendar((int) $calendar->id);
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

    public function movePreviewLesson(int $lessonId, int $fromPeriodId, int $newPeriodId, ?int $half = null): void
    {
        parent::movePreviewLesson($lessonId, $fromPeriodId, $newPeriodId, $half);
        $this->afterEdit();
    }

    public function addPreviewLesson(int $lessonId, ?int $half = null): void
    {
        parent::addPreviewLesson($lessonId, $half);
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

    // ─── Sincronización con la carga académica asignada ─────────────────────

    /**
     * Estado de sincronía entre las Pevaluaciones vigentes del calendario y
     * las clases persistidas en `timetable_lessons`.
     *
     * - missing: carga académica activa que aún no tiene una clase creada.
     * - missing_locked: faltantes cuya sección tiene el horario bloqueado.
     * - orphaned: clases persistidas que ya no pertenecen a la carga vigente
     *   (Pevaluación eliminada, sección/grado inactivo o fuera del P.Estudio).
     *
     * @return array{active:int, missing:int, missing_locked:int, orphaned:int, in_sync:bool}
     */
    public function academicSyncStatus(): array
    {
        $calendar = $this->calendarId
            ? TimetableCalendar::query()->find($this->calendarId)
            : null;

        return $calendar
            ? $this->academicSyncStatusForCalendar($calendar)
            : $this->emptyAcademicSyncStatus();
    }

    /**
     * Estado de sincronización de un calendario concreto. Se reutiliza tanto
     * en el espacio de trabajo como en las tarjetas del Paso 1.
     *
     * @return array{active:int, missing:int, missing_locked:int, orphaned:int, in_sync:bool}
     */
    private function academicSyncStatusForCalendar(TimetableCalendar $calendar): array
    {
        $pevaluaciones = $this->pevaluacionesForCalendar($calendar)->get();
        $lessons = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->get(['id', 'pevaluacion_id']);

        $availableIds = $pevaluaciones
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->flip();
        $persistedIds = $lessons
            ->pluck('pevaluacion_id')
            ->map(fn ($id): int => (int) $id)
            ->flip();

        $missing = $pevaluaciones->reject(
            fn ($pev): bool => $persistedIds->has((int) $pev->id)
        );
        $missingLocked = $missing->filter(
            fn ($pev): bool => (bool) ($pev->seccion?->timetable_locked ?? false)
        )->count();
        $orphaned = $lessons->reject(
            fn (TimetableLesson $lesson): bool => $availableIds->has((int) $lesson->pevaluacion_id)
        )->count();

        return [
            'active' => $pevaluaciones->count(),
            'missing' => $missing->count(),
            'missing_locked' => $missingLocked,
            'orphaned' => $orphaned,
            'in_sync' => $missing->isEmpty() && $orphaned === 0,
        ];
    }

    /**
     * @return array{active:int, missing:int, missing_locked:int, orphaned:int, in_sync:bool}
     */
    private function emptyAcademicSyncStatus(): array
    {
        return [
            'active' => 0,
            'missing' => 0,
            'missing_locked' => 0,
            'orphaned' => 0,
            'in_sync' => true,
        ];
    }

    /**
     * Crea las clases faltantes desde la carga académica vigente y refresca
     * las clases y el preview persistido. No elimina clases huérfanas ni
     * modifica los bloques que el usuario ya configuró.
     */
    public function syncAcademicLoad(): void
    {
        $calendar = $this->calendarId
            ? TimetableCalendar::query()->find($this->calendarId)
            : null;

        if (! $calendar) {
            $this->notification()->warning(
                'Calendario requerido',
                'Selecciona un calendario antes de sincronizar la carga académica.',
            );

            return;
        }

        $result = $this->syncAcademicLoadForCalendar($calendar);

        if (! $result['ok']) {
            if ($result['reason'] === 'no_shift') {
                $this->notification()->error(
                    'Sin turnos configurados',
                    'No hay turnos disponibles para crear las clases faltantes.',
                );
            } else {
                $this->notification()->warning(
                    'Sin carga académica',
                    'No hay Pevaluaciones activas para el lapso y P.Estudio de este calendario.',
                );
            }

            return;
        }

        $this->refreshLightAfterAcademicSync($calendar);

        if ($result['created'] === 0) {
            if ($result['locked'] > 0) {
                $this->notification()->warning(
                    'Secciones bloqueadas',
                    "{$result['locked']} carga(s) académica(s) no se crearon porque su sección tiene el horario bloqueado.",
                );
            } else {
                $this->notification()->info(
                    'Carga académica sincronizada',
                    'Todas las Pevaluaciones vigentes ya tienen una clase en este calendario.',
                );
            }

            return;
        }

        $orphaned = $this->academicSyncStatus()['orphaned'];
        $message = "{$result['created']} clase(s) creada(s) desde la carga académica vigente.";

        if ($result['locked'] > 0) {
            $message .= " {$result['locked']} omitida(s) por sección bloqueada.";
        }

        if ($orphaned > 0) {
            $message .= " {$orphaned} clase(s) fuera de la carga vigente se mantienen para revisión.";
        }

        $this->notification()->success('Carga académica sincronizada', $message);
    }

    /**
     * Sincroniza la carga académica sin emitir notificaciones. Devuelve el
     * resultado para que la limpieza de huérfanas pueda abortar si no fue
     * posible crear las clases faltantes.
     *
     * @return array{ok:bool, created:int, locked:int, reason:string}
     */
    private function syncAcademicLoadForCalendar(TimetableCalendar $calendar): array
    {
        $pevaluaciones = $this->pevaluacionesForCalendar($calendar)->get();

        if ($pevaluaciones->isEmpty()) {
            return ['ok' => false, 'created' => 0, 'locked' => 0, 'reason' => 'empty'];
        }

        $persistedIds = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->pluck('pevaluacion_id')
            ->map(fn ($id): int => (int) $id)
            ->flip();

        $missing = $pevaluaciones->reject(
            fn ($pev): bool => $persistedIds->has((int) $pev->id)
        );
        $syncable = $missing->reject(
            fn ($pev): bool => (bool) ($pev->seccion?->timetable_locked ?? false)
        );
        $locked = $missing->count() - $syncable->count();

        if ($syncable->isEmpty()) {
            return ['ok' => true, 'created' => 0, 'locked' => $locked, 'reason' => 'already'];
        }

        $shiftId = (int) TimetableShift::query()->orderBy('id')->value('id');

        if ($shiftId <= 0) {
            return ['ok' => false, 'created' => 0, 'locked' => $locked, 'reason' => 'no_shift'];
        }

        $periodMinutes = max(1, (int) $calendar->period_minutes);
        $created = 0;

        DB::transaction(function () use ($calendar, $syncable, $periodMinutes, $shiftId, &$created): void {
            foreach ($syncable as $pev) {
                $lesson = $this->buildLesson($pev, $periodMinutes);
                $model = TimetableLesson::query()->firstOrCreate(
                    [
                        'calendar_id' => $calendar->id,
                        'pevaluacion_id' => (int) $pev->id,
                    ],
                    [
                        'shift_id' => (int) ($lesson['shift_id'] ?? 0) ?: $shiftId,
                        'weekly_blocks_t' => max(0, (int) ($lesson['weekly_blocks_t'] ?? 0)),
                        'weekly_blocks_p' => max(0, (int) ($lesson['weekly_blocks_p'] ?? 0)),
                        'room_type_required' => $lesson['room_type_required'] ?? null,
                        'is_half_group' => (bool) ($lesson['is_half_group'] ?? false),
                        'allow_shared_teacher' => (bool) ($lesson['allow_shared_teacher'] ?? false),
                        'half_block' => (bool) ($lesson['half_block'] ?? false),
                        'priority' => max(0, (int) ($lesson['priority'] ?? 0)),
                        'locked' => (bool) ($lesson['locked'] ?? false),
                    ],
                );

                if ($model->wasRecentlyCreated) {
                    $created++;
                }
            }
        });

        return ['ok' => true, 'created' => $created, 'locked' => $locked, 'reason' => 'created'];
    }

    /**
     * IDs de Pevaluaciones que hoy son la fuente de verdad del calendario:
     * lapso + P.Estudio, con grado, sección, pensum y asignatura activos.
     *
     * @return list<int>
     */
    private function activePevaluacionIdsForCalendar(TimetableCalendar $calendar): array
    {
        return $this->pevaluacionesForCalendar($calendar)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    /**
     * IDs de clases del calendario cuya Pevaluación ya no pertenece a la
     * carga académica vigente. Incluye Pevaluaciones eliminadas, secciones,
     * grados, P.Estudios, pensum o asignaturas inactivas, y cargas de otro
     * lapso/P.Estudio.
     *
     * @return list<int>
     */
    private function orphanLessonIdsForCalendar(TimetableCalendar $calendar): array
    {
        $activeIds = $this->activePevaluacionIdsForCalendar($calendar);

        return TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->when(
                $activeIds !== [],
                fn ($query) => $query->whereNotIn('pevaluacion_id', $activeIds),
            )
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    /**
     * Confirma la limpieza de clases que ya no existen en la carga académica
     * vigente. La eliminación incluye sus slots, pero nunca las Pevaluaciones.
     */
    public function confirmRemoveOrphanLessons(): void
    {
        $calendar = $this->calendarId
            ? TimetableCalendar::query()->find($this->calendarId)
            : null;

        if (! $calendar) {
            $this->notification()->warning(
                'Calendario requerido',
                'Selecciona un calendario antes de limpiar clases huérfanas.',
            );

            return;
        }

        if ($this->activePevaluacionIdsForCalendar($calendar) === []) {
            $this->notification()->warning(
                'Sin carga académica vigente',
                'No se limpiaron clases porque no hay Pevaluaciones activas para este lapso/P.Estudio. Verifica la carga académica antes de continuar.',
            );

            return;
        }

        $orphanIds = $this->orphanLessonIdsForCalendar($calendar);

        if ($orphanIds === []) {
            $this->notification()->warning(
                'Sin clases huérfanas',
                'Todas las clases del calendario corresponden a la carga académica vigente.',
            );

            return;
        }

        $this->dialog()->confirm([
            'title' => '¿Limpiar clases huérfanas?',
            'description' => 'Primero se sincronizará la carga académica. Después se eliminarán '.count($orphanIds).' clase(s) huérfana(s) y sus slots. Si la sincronización falla, no se elimina ningún registro. Las Pevaluaciones no se modifican.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Sí, limpiar clases',
                'method' => 'removeOrphanLessons',
                'color' => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
                'color' => 'secondary',
            ],
        ]);
    }

    /**
     * Elimina las clases fuera de la carga vigente y sus slots. Las
     * Pevaluaciones son la fuente de verdad y no se tocan.
     */
    public function removeOrphanLessons(): void
    {
        $calendar = $this->calendarId
            ? TimetableCalendar::query()->find($this->calendarId)
            : null;

        if (! $calendar) {
            return;
        }

        // La sincronización es obligatoria antes de eliminar: si no se pueden
        // crear las clases faltantes, tampoco se tocan las clases huérfanas.
        $sync = $this->syncAcademicLoadForCalendar($calendar);

        if (! $sync['ok']) {
            $this->notification()->warning(
                'Limpieza cancelada',
                $sync['reason'] === 'no_shift'
                    ? 'No se pudo sincronizar la carga académica porque no hay turnos configurados. No se eliminó ningún registro.'
                    : 'No se pudo sincronizar la carga académica porque no hay Pevaluaciones activas. No se eliminó ningún registro.',
            );

            return;
        }

        $orphanIds = $this->orphanLessonIdsForCalendar($calendar);

        if ($orphanIds === []) {
            $this->refreshLightAfterAcademicSync($calendar);

            $this->notification()->warning(
                'Sin cambios',
                'Las clases huérfanas ya fueron retiradas o corregidas.',
            );

            return;
        }

        $removed = count($orphanIds);

        DB::transaction(function () use ($calendar, $orphanIds): void {
            TimetableSlot::query()
                ->where('calendar_id', $calendar->id)
                ->whereIn('lesson_id', $orphanIds)
                ->delete();

            TimetableLesson::query()
                ->where('calendar_id', $calendar->id)
                ->whereIn('id', $orphanIds)
                ->delete();
        });

        $this->refreshLightAfterAcademicSync($calendar);

        $message = $sync['created'] > 0
            ? "{$sync['created']} clase(s) creada(s) desde la carga vigente y {$removed} clase(s) huérfana(s) eliminada(s)."
            : "{$removed} clase(s) huérfana(s) eliminada(s).";

        if ($sync['locked'] > 0) {
            $message .= " {$sync['locked']} carga(s) activa(s) se omitieron por sección bloqueada.";
        }

        $message .= ' La carga académica no fue modificada.';

        $this->notification()->success('Carga sincronizada y huérfanas retiradas', $message);
    }

    /**
     * Rehidrata la selección, las clases en memoria y el preview persistido
     * después de una sincronización académica.
     */
    private function refreshLightAfterAcademicSync(TimetableCalendar $calendar): void
    {
        $this->selectedPevs = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->pluck('pevaluacion_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->mapWithKeys(fn (int $id): array => [$id => true])
            ->all();

        $this->loadLessons(true);
        $this->syncPreviewPayloadFromSlots();

        $calendar->refresh();
        $this->preview = $calendar->preview_payload;
        $this->generationState = 'preview_ready';
        $this->lessonsDirty = false;
        $this->lessonsSavedAt = now()->format('H:i:s');
        $this->lastSavedAt = now()->format('H:i:s');

        $this->initializeActiveSectionFromLessons();
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
     * @return list<array{id:int, name:string, status:string, lapso:string, pestudio:string, slots:int, lessons:int, sync:array{active:int, missing:int, missing_locked:int, orphaned:int, in_sync:bool}}>
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
                'sync' => $this->academicSyncStatusForCalendar($calendar),
            ])
            ->all();
    }

    /**
     * Calendarios activos disponibles en el selector del paso 2.
     *
     * @return list<array{id:int, name:string, lapso:string, pestudio:string}>
     */
    private function activeCalendarSwitcher(): array
    {
        return TimetableCalendar::query()
            ->with(['lapso:id,name', 'pestudio:id,name'])
            ->where('status', TimetableCalendar::STATUS_ACTIVE)
            ->orderBy('name')
            ->get()
            ->map(fn (TimetableCalendar $calendar): array => [
                'id' => (int) $calendar->id,
                'name' => (string) $calendar->name,
                'lapso' => (string) ($calendar->lapso?->name ?? ''),
                'pestudio' => (string) ($calendar->pestudio?->name ?? ''),
            ])
            ->all();
    }

    /**
     * Render propio del asistente: períodos, turnos, selección común
     * (Grado → Sección) y, en el tab Grilla, la previsualización
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
        $calendarSwitcher = $this->lightStep === 2 ? $this->activeCalendarSwitcher() : [];

        $sectionPreviewGrid = [];
        $tabPestudioOptions = [];
        $tabGradoOptions = [];
        $tabSeccionOptions = [];
        $step3 = [];
        $academicSync = [
            'active' => 0,
            'missing' => 0,
            'missing_locked' => 0,
            'orphaned' => 0,
            'in_sync' => true,
        ];

        if ($this->calendarId) {
            $academicSync = $this->academicSyncStatus();

            // La selección común (Grado → Sección) se construye desde TODAS
            // las Pevaluaciones del lapso, no solo desde las
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
            'calendarSwitcher' => $calendarSwitcher,
            'shifts' => $shifts,
            'periodsList' => $periodsList,
            'sectionPreviewGrid' => $sectionPreviewGrid,
            'activeSectionLocked' => $this->activeSectionTimetableLocked(),
            'tabPestudioOptions' => $tabPestudioOptions,
            'tabGradoOptions' => $tabGradoOptions,
            'tabSeccionOptions' => $tabSeccionOptions,
            'academicSync' => $academicSync,
            'moduleRoutePrefix' => $this->moduleRoutePrefix(),
            'teacherScheduleOptions' => $teacherScheduleOptions,
            'teacherScheduleCalendars' => $teacherScheduleCalendars,
            'teacherScheduleGrid' => $teacherScheduleGrid,
            'teacherScheduleHasAssignments' => $teacherScheduleHasAssignments,
        ], $step3))->layout($this->getLayout());
    }
}
