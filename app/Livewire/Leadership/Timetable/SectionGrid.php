<?php

namespace App\Livewire\Leadership\Timetable;

use App\Livewire\Timetable\TimetableRoleView;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Services\Timetable\TimetableViewService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SPEC-TIMETABLE-001 §9 (mejora 3) — Horario SOLO lectura para leadership.
 * Puede consultar el horario de cualquier sección del calendario activo.
 */
class SectionGrid extends TimetableRoleView
{
    public $seccionId = null;

    public function mount(): void
    {
        $this->seccionId = request()->route('seccion');
    }

    protected function scope(): array
    {
        // PLAN-TIMETABLE-002 §4.4: el único activo del lapso vigente.
        $calendar = TimetableCalendar::activeForCurrentLapso();

        if (! $calendar) {
            throw new NotFoundHttpException('Todavía no hay un horario publicado.');
        }

        if (! $this->seccionId) {
            throw new NotFoundHttpException('Selecciona una sección.');
        }

        $seccion = Seccion::query()->with('grado')->findOrFail($this->seccionId);

        return [
            'calendar' => $calendar,
            'grid' => app(TimetableViewService::class)->gridForSection($calendar, $seccion->id),
            'label' => 'Horario · Sección '.$seccion->name,
        ];
    }

    protected function getLayout(): string
    {
        return 'leadership.layouts.app';
    }
}
