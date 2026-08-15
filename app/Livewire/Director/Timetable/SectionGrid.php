<?php

namespace App\Livewire\Director\Timetable;

/**
 * SPEC-TIMETABLE-001 §9 (mejora 3) — Horario SOLO lectura para dirección.
 * Mismo alcance que leadership (cualquier sección del calendario activo),
 * con el layout del panel de dirección.
 */
class SectionGrid extends \App\Livewire\Leadership\Timetable\SectionGrid
{
    protected function getLayout(): string
    {
        return 'director.layouts.app';
    }
}
