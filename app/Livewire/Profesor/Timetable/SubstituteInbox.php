<?php

namespace App\Livewire\Profesor\Timetable;

use App\Models\app\Academy\Profesor;
use App\Models\app\Timetable\TimetableSubstituteAssignment;
use App\Services\Timetable\SubstituteService;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * SPEC-TIMETABLE-001 §7 (v1.2) — Bandeja de suplencias del profesor.
 *
 * El docente ve sus suplencias asignadas (pending) y confirma o rechaza.
 * Solo las suyas: se resuelve el Profesor por user_id del autenticado.
 */
class SubstituteInbox extends Component
{
    public ?string $message = null;

    protected SubstituteService $service;

    public function boot(): void
    {
        $this->service = app(SubstituteService::class);
    }

    public function confirmAssignment(int $assignmentId): void
    {
        $assignment = TimetableSubstituteAssignment::find($assignmentId);

        if (! $assignment || $assignment->substitute_profesor_id !== $this->myProfesorId()) {
            return;
        }

        $this->service->confirm($assignmentId);
        $this->message = 'Suplencia confirmada. Gracias por cubrir la clase.';
    }

    public function declineAssignment(int $assignmentId): void
    {
        $assignment = TimetableSubstituteAssignment::find($assignmentId);

        if (! $assignment || $assignment->substitute_profesor_id !== $this->myProfesorId()) {
            return;
        }

        $this->service->decline($assignmentId);
        $this->message = 'Suplencia rechazada. Coordinación será notificada.';
    }

    private function myProfesorId(): ?int
    {
        return Profesor::query()
            ->where('user_id', auth()->id())
            ->value('id');
    }

    #[Layout('profesors.layouts.app')]
    public function render(): \Illuminate\View\View
    {
        $profesorId = $this->myProfesorId();

        $assignments = $profesorId
            ? TimetableSubstituteAssignment::query()
                ->where('substitute_profesor_id', $profesorId)
                ->with([
                    'slot.lesson.pevaluacion.pensum.asignatura',
                    'slot.lesson.pevaluacion.seccion',
                    'slot.period',
                    'absence.profesor',
                ])
                ->orderByDesc('id')
                ->get()
            : collect();

        return view('livewire.profesor.timetable.substitute-inbox', [
            'assignments' => $assignments,
        ]);
    }
}
