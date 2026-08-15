<?php

namespace App\Livewire\Coordinacion\Timetable;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;
use App\Services\Timetable\ConflictValidator;
use Livewire\Component;

/**
 * SPEC-TIMETABLE-001 §7 — Editor manual (drag-and-drop).
 *
 * Grilla días (Lu–Vi) × períodos por sección o docente, sin librería externa:
 * Alpine.js con drag nativo HTML5. Al soltar se valida en vivo con las reglas
 * duras de ConflictValidator; si falla se muestra el conflicto y no se guarda.
 */
class TimetableEditor extends Component
{
    public $calendarId = null;

    public string $view = 'section'; // 'section' | 'teacher'

    public $sectionFilterId = null;

    public $teacherFilterId = null;

    public ?string $conflictMessage = null;

    public bool $loading = false;

    protected ConflictValidator $validator;

    public function boot(): void
    {
        $this->validator = app(ConflictValidator::class);
    }

    public function mount($calendarId = null): void
    {
        if ($calendarId) {
            $this->calendarId = (int) $calendarId;
        }
    }

    public function moveSlot(int $slotId, int $newPeriodId): void
    {
        $this->conflictMessage = null;

        $slot = TimetableSlot::query()->find($slotId);
        if (! $slot) {
            return;
        }

        if ($slot->locked) {
            $this->conflictMessage = 'Este bloque está fijado (locked) y no se puede mover.';

            return;
        }

        $result = $this->validator->validate(
            calendarId: $slot->calendar_id,
            lessonId: $slot->lesson_id,
            periodId: $newPeriodId,
            profesorId: $slot->profesor_id,
            seccionId: $slot->seccion_id,
            roomId: $slot->room_id,
            ignoreSlotId: $slot->id,
        );

        if (! $result['valid']) {
            $this->conflictMessage = implode(' ', $result['reasons']);

            return;
        }

        $slot->update([
            'period_id' => $newPeriodId,
            'is_manual_override' => true,
        ]);

        $this->dispatch('timetable.slot-moved', slotId: $slotId, periodId: $newPeriodId);
    }

    public function dropLesson(int $lessonId, int $periodId): void
    {
        $this->conflictMessage = null;

        $lesson = TimetableLesson::query()->with('pevaluacion')->find($lessonId);
        if (! $lesson || ! $lesson->pevaluacion) {
            return;
        }

        $calendar = TimetableCalendar::find($this->calendarId ?? $lesson->calendar_id);

        // El período destino debe coincidir con el turno de la lección.
        $period = TimetablePeriod::find($periodId);
        if (! $period) {
            return;
        }

        $result = $this->validator->validate(
            calendarId: $calendar->id,
            lessonId: $lesson->id,
            periodId: $periodId,
            profesorId: $lesson->pevaluacion->profesor_id,
            seccionId: $lesson->pevaluacion->seccion_id,
            roomId: null,
        );

        if (! $result['valid']) {
            $this->conflictMessage = implode(' ', $result['reasons']);

            return;
        }

        TimetableSlot::create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $periodId,
            'profesor_id' => $lesson->pevaluacion->profesor_id,
            'seccion_id' => $lesson->pevaluacion->seccion_id,
            'room_id' => null,
            'is_manual_override' => true,
            'locked' => false,
        ]);

        $this->dispatch('timetable.slot-created', lessonId: $lessonId, periodId: $periodId);
    }

    public function removeSlot(int $slotId): void
    {
        $this->conflictMessage = null;

        $slot = TimetableSlot::query()->find($slotId);
        if (! $slot) {
            return;
        }

        if ($slot->locked) {
            $this->conflictMessage = 'Este bloque está fijado (locked) y no se puede quitar.';

            return;
        }

        $slot->delete();
        $this->dispatch('timetable.slot-removed', slotId: $slotId);
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
        $calendar = $this->calendarId ? TimetableCalendar::find($this->calendarId) : null;

        $periods = collect();
        $slots = collect();
        $sections = collect();
        $teachers = collect();
        $unplacedLessons = collect();

        if ($calendar) {
            $periods = TimetablePeriod::query()
                ->where('calendar_id', $calendar->id)
                ->with('shift')
                ->orderBy('day_of_week')
                ->orderBy('order_in_day')
                ->get();

            $slots = TimetableSlot::query()
                ->where('calendar_id', $calendar->id)
                ->with(['lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.seccion'])
                ->get();

            if ($this->view === 'section' && $this->sectionFilterId) {
                $slots = $slots->where('seccion_id', (int) $this->sectionFilterId);
            }

            if ($this->view === 'teacher' && $this->teacherFilterId) {
                $slots = $slots->where('profesor_id', (int) $this->teacherFilterId);
            }

            $sections = \App\Models\app\Academy\Seccion::query()
                ->whereIn('id', TimetableSlot::query()->where('calendar_id', $calendar->id)->pluck('seccion_id')->unique())
                ->orderBy('name')
                ->get();

            $teachers = \App\Models\app\Academy\Profesor::query()
                ->whereIn('id', TimetableSlot::query()->where('calendar_id', $calendar->id)->pluck('profesor_id')->unique())
                ->orderBy('lastname')
                ->get();

            $unplacedLessons = TimetableLesson::query()
                ->where('calendar_id', $calendar->id)
                ->whereDoesntHave('slots')
                ->with('pevaluacion.pensum.asignatura', 'pevaluacion.seccion')
                ->get();
        }

        return view('livewire.coordinacion.timetable.timetable-editor', [
            'calendar' => $calendar,
            'periods' => $periods,
            'slots' => $slots,
            'sections' => $sections,
            'teachers' => $teachers,
            'unplacedLessons' => $unplacedLessons,
        ])->layout($this->getLayout());
    }
}
