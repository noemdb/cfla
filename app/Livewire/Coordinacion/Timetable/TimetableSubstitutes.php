<?php

namespace App\Livewire\Coordinacion\Timetable;

use App\Jobs\Timetable\NotifySubstituteJob;
use App\Models\app\Academy\Profesor;
use App\Models\app\Timetable\TimetableAbsence;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\app\Timetable\TimetableSubstituteAssignment;
use App\Services\Timetable\SubstituteService;
use Livewire\Component;

/**
 * SPEC-TIMETABLE-001 §7 (v1.2) — Gestión de ausencias y suplentes.
 *
 * Registra ausencias de docentes, muestra los slots afectados, sugiere
 * suplentes candidatos y asigna suplencias (pending) notificando por cola.
 */
class TimetableSubstitutes extends Component
{
    public $calendarId = null;

    public $absentProfesorId = null;

    public string $dateStart = '';

    public string $dateEnd = '';

    public string $reason = '';

    public $selectedAbsenceId = null;

    public ?string $message = null;

    protected SubstituteService $service;

    public function boot(): void
    {
        $this->service = app(SubstituteService::class);
    }

    public function mount($calendarId = null): void
    {
        $this->calendarId = $calendarId ? (int) $calendarId : null;
    }

    public function registerAbsence(): void
    {
        $this->validate([
            'calendarId' => 'required',
            'absentProfesorId' => 'required',
            'dateStart' => 'required|date',
            'dateEnd' => 'required|date|after_or_equal:dateStart',
        ]);

        $this->service->registerAbsence(
            calendarId: $this->calendarId,
            profesorId: $this->absentProfesorId,
            dateStart: $this->dateStart,
            dateEnd: $this->dateEnd,
            reason: $this->reason ?: null,
        );

        $this->reset(['absentProfesorId', 'dateStart', 'dateEnd', 'reason']);
        $this->message = 'Ausencia registrada. Seleccionala para ver los bloques afectados.';
    }

    public function selectAbsence($absenceId): void
    {
        $this->selectedAbsenceId = (int) $absenceId;
    }

    /**
     * Asigna un suplente a un slot afectado y encola la notificación.
     */
    public function assignSubstitute(int $slotId, int $substituteProfesorId): void
    {
        $absence = TimetableAbsence::find($this->selectedAbsenceId);
        $slot = TimetableSlot::find($slotId);

        if (! $absence || ! $slot) {
            return;
        }

        $assignment = $this->service->assignSubstitute($absence, $slot, $substituteProfesorId);
        NotifySubstituteJob::dispatch($assignment->id);

        $this->message = 'Suplencia asignada. El docente suplente fue notificado.';
    }

    protected function getLayout(): string
    {
        return 'coordinacion.layouts.app';
    }

    public function render(): \Illuminate\View\View
    {
        $calendars = TimetableCalendar::query()
            ->orderByDesc('id')
            ->get(['id', 'name', 'status']);

        if (! $this->calendarId && $calendars->isNotEmpty()) {
            $this->calendarId = $calendars->first()->id;
        }

        $profesores = Profesor::query()
            ->where('status_active', 'true')
            ->orderBy('lastname')
            ->get();

        $absences = $this->calendarId
            ? TimetableAbsence::query()
                ->where('calendar_id', $this->calendarId)
                ->with('profesor')
                ->orderByDesc('date_start')
                ->get()
            : collect();

        $selectedAbsence = $absences->firstWhere('id', $this->selectedAbsenceId);
        $affectedSlots = collect();
        $candidatesBySlot = [];

        if ($selectedAbsence) {
            $affectedSlots = $this->service->affectedSlots($selectedAbsence);
            foreach ($affectedSlots as $slot) {
                $candidatesBySlot[$slot->id] = $this->service->candidateSubstitutes($slot);
            }
        }

        return view('livewire.coordinacion.timetable.timetable-substitutes', [
            'calendars' => $calendars,
            'profesores' => $profesores,
            'absences' => $absences,
            'selectedAbsence' => $selectedAbsence,
            'affectedSlots' => $affectedSlots,
            'candidatesBySlot' => $candidatesBySlot,
            'assignmentsBySlot' => TimetableSubstituteAssignment::query()
                ->whereIn('slot_id', $affectedSlots->pluck('id'))
                ->with('substituteProfesor')
                ->get()
                ->groupBy('slot_id'),
        ])->layout($this->getLayout());
    }
}
