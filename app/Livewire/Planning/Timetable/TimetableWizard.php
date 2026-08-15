<?php

namespace App\Livewire\Planning\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard as BaseTimetableWizard;

/**
 * Wizard de horario para el módulo Planning (ADR-TT-006: is_planner comparte
 * los mismos permisos de gestión que is_coordinacion).
 */
class TimetableWizard extends BaseTimetableWizard
{
    protected function getLayout(): string
    {
        return 'planning.layouts.app';
    }
}
