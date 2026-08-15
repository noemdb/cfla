<?php

namespace App\Livewire\Planning\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableEditor as BaseTimetableEditor;

/**
 * Editor de horario para el módulo Planning (ADR-TT-006).
 */
class TimetableEditor extends BaseTimetableEditor
{
    protected function getLayout(): string
    {
        return 'planning.layouts.app';
    }
}
