<?php

namespace App\Livewire\Planning\Timetable;

/**
 * Suplencias en el módulo Planning: misma lógica que Coordinación, solo
 * cambia el layout (ADR-TT-006, patrón del wizard/editor).
 */
class TimetableSubstitutes extends \App\Livewire\Coordinacion\Timetable\TimetableSubstitutes
{
    protected function getLayout(): string
    {
        return 'planning.layouts.app';
    }
}
