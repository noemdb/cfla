<?php

namespace App\Livewire\Planning\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard as BaseTimetableWizard;

/**
 * Wizard de horario para el módulo Planning (ADR-TT-006: is_planner comparte
 * los mismos permisos de gestión que is_coordinacion).
 *
 * La notificación a is_planner al activar un calendario vive en el componente
 * base (Coordinacion\Timetable\TimetableWizard), de modo que aplica tanto desde
 * /app/planning/timetable como /app/coordinacion/timetable.
 */
class TimetableWizard extends BaseTimetableWizard
{
    protected function getLayout(): string
    {
        return 'planning.layouts.app';
    }

    public function moduleRoutePrefix(): string
    {
        return 'app.planning';
    }
}
