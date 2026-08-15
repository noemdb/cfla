<?php

namespace App\Livewire\Timetable;

use App\Services\Timetable\TimetableViewService;
use Livewire\Component;

/**
 * SPEC-TIMETABLE-001 §8 (mejora 4/3) — Vista de horario SOLO lectura por rol.
 *
 * Resuelve el calendario activo y la grilla del alcance del rol (sección del
 * estudiante, slots del docente, o sección filtrada para leadership/director).
 * Las subclases definen `scope()` y el layout.
 */
abstract class TimetableRoleView extends Component
{
    protected TimetableViewService $viewService;

    public function boot(): void
    {
        $this->viewService = app(TimetableViewService::class);
    }

    /**
     * @return array{calendar: \App\Models\app\Timetable\TimetableCalendar, grid: \Illuminate\Support\Collection, label: string}
     */
    abstract protected function scope(): array;

    public function render(): \Illuminate\View\View
    {
        $data = $this->scope();

        return view('livewire.timetable.role-view', [
            'calendar' => $data['calendar'],
            'grid' => $data['grid'],
            'subjectLabel' => $data['label'],
        ])->layout($this->getLayout());
    }

    protected function getLayout(): string
    {
        return 'coordinacion.layouts.app';
    }
}
