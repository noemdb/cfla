<?php

namespace App\Livewire\Coordinacion\Timetable;

use App\Livewire\Planning\Timetable\TimetableLight as BaseTimetableLight;

/**
 * Asistente «light» de horarios para el módulo Coordinación (ADR-TT-006).
 *
 * Reutiliza toda la lógica del asistente de Planning (selección de calendario +
 * grilla, agregar/intercambiar lecciones y alertar colisiones) pero con el
 * prefijo de ruta y el layout del módulo de Coordinación, de modo que los
 * enlaces a los PDFs (`app.coordinacion.timetable.pdf.*`) y el layout
 * (`coordinacion.layouts.app`) sean los correctos para este rol.
 */
class TimetableLight extends BaseTimetableLight
{
    protected function getLayout(): string
    {
        return 'coordinacion.layouts.app';
    }

    public function moduleRoutePrefix(): string
    {
        return 'app.coordinacion';
    }
}
