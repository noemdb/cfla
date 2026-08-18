<?php

namespace App\Livewire\Student\Lms;

use App\Livewire\Timetable\TimetableRoleView;
use App\Models\app\Timetable\TimetableCalendar;
use App\Services\Timetable\TimetableViewService;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SPEC-TIMETABLE-001 §8 (mejora 4) — Horario del estudiante: SOLO su sección.
 *
 * Vía `Estudiant → inscripcion → seccion → slots`. Si el usuario no tiene
 * inscripción asociada (o no hay calendario activo), se muestra un aviso en
 * vez de un error.
 */
class Timetable extends TimetableRoleView
{
    protected function scope(): array
    {
        $user = auth()->user();

        $estudiant = $user?->estudiant;
        $seccion = $estudiant?->inscripcion?->seccion;

        if (! $seccion) {
            throw new NotFoundHttpException('No tenés una inscripción asociada para ver tu horario.');
        }

        $calendar = TimetableCalendar::activeForCurrentLapso();

        if (! $calendar) {
            throw new NotFoundHttpException('Todavía no hay un horario publicado.');
        }

        return [
            'calendar' => $calendar,
            'grid' => app(TimetableViewService::class)->gridForSection($calendar, $seccion->id),
            'label' => 'Mi horario · Sección '.$seccion->name,
            'shareUrl' => URL::temporarySignedRoute('timetable.public.section', now()->addDays(7), [
                'calendar' => $calendar->id,
                'seccion' => $seccion->id,
            ]),
        ];
    }

    protected function getLayout(): string
    {
        return 'student.layouts.app';
    }
}
