<?php

namespace App\Http\Controllers\Timetable;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableRoom;
use App\Services\Timetable\TimetableViewService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * SPEC-TIMETABLE-001 §8 — Exportación PDF del horario por sección/docente/aula.
 *
 * Mismo patrón que BinnaclePdfController / CatchmentPDFController.
 */
class TimetablePdfController extends Controller
{
    public function __construct(private TimetableViewService $viewService) {}

    public function section(Request $request, $calendarId, $seccionId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $seccion = Seccion::query()->with('grado')->findOrFail($seccionId);

        $grid = $this->viewService->gridForSection($calendar, $seccionId);

        $institucion = \App\Models\app\Entity\Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.timetable.section', [
            'calendar' => $calendar,
            'seccion' => $seccion,
            'grid' => $grid,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horario-{$seccion->name}.pdf");
    }

    public function teacher(Request $request, $calendarId, $profesorId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $profesor = Profesor::query()->findOrFail($profesorId);

        $grid = $this->viewService->gridForTeacher($calendar, $profesorId);

        $institucion = \App\Models\app\Entity\Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.timetable.teacher', [
            'calendar' => $calendar,
            'profesor' => $profesor,
            'grid' => $grid,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horario-{$profesor->lastname}-{$profesor->name}.pdf");
    }

    public function room(Request $request, $calendarId, $roomId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $room = TimetableRoom::query()->findOrFail($roomId);

        $grid = $this->viewService->gridForRoom($calendar, $roomId);

        $institucion = \App\Models\app\Entity\Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.timetable.room', [
            'calendar' => $calendar,
            'room' => $room,
            'grid' => $grid,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horario-aula-{$room->code}.pdf");
    }
}
