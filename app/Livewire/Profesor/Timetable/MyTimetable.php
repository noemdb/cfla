<?php

namespace App\Livewire\Profesor\Timetable;

use App\Livewire\Timetable\TimetableRoleView;
use App\Models\app\Academy\Profesor;
use App\Models\app\Timetable\TimetableCalendar;
use App\Services\Timetable\TimetableViewService;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SPEC-TIMETABLE-001 §8 (mejora 4) — Horario del docente: SOLO sus slots.
 *
 * Resuelve el Profesor por user_id del autenticado y muestra la grilla de sus
 * clases en el calendario activo.
 */
class MyTimetable extends TimetableRoleView
{
    protected function scope(): array
    {
        $profesor = Profesor::query()
            ->where('user_id', auth()->id())
            ->first();

        if (! $profesor) {
            throw new NotFoundHttpException('No tenés un perfil de docente asociado para ver tu horario.');
        }

        $calendar = TimetableCalendar::query()
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (! $calendar) {
            throw new NotFoundHttpException('Todavía no hay un horario publicado.');
        }

        return [
            'calendar' => $calendar,
            'grid' => app(TimetableViewService::class)->gridForTeacher($calendar, $profesor->id),
            'label' => 'Mi horario',
            'shareUrl' => URL::temporarySignedRoute('timetable.public.teacher', now()->addDays(7), [
                'calendar' => $calendar->id,
                'profesor' => $profesor->id,
            ]),
        ];
    }

    protected function getLayout(): string
    {
        return 'profesors.layouts.app';
    }
}
