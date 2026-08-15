<?php

namespace App\Http\Controllers\Timetable;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableRoom;
use App\Services\Timetable\TimetableViewService;
use Illuminate\Http\Request;

/**
 * SPEC-TIMETABLE-001 §8 — Vista pública por enlace firmado (sin login).
 * Patrón "compartir por enlace" ya usado en la app (temporarySignedRoute).
 */
class TimetablePublicController extends Controller
{
    public function __construct(private TimetableViewService $viewService) {}

    public function section(Request $request, $calendarId, $seccionId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $seccion = Seccion::query()->with('grado')->findOrFail($seccionId);
        $grid = $this->viewService->gridForSection($calendar, $seccionId);

        return view('timetable.public', [
            'calendar' => $calendar,
            'subjectLabel' => "Sección {$seccion->name}",
            'grid' => $grid,
        ]);
    }

    public function teacher(Request $request, $calendarId, $profesorId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $profesor = Profesor::query()->findOrFail($profesorId);
        $grid = $this->viewService->gridForTeacher($calendar, $profesorId);

        return view('timetable.public', [
            'calendar' => $calendar,
            'subjectLabel' => "Docente: {$profesor->lastname}, {$profesor->name}",
            'grid' => $grid,
        ]);
    }

    public function room(Request $request, $calendarId, $roomId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $room = TimetableRoom::query()->findOrFail($roomId);
        $grid = $this->viewService->gridForRoom($calendar, $roomId);

        return view('timetable.public', [
            'calendar' => $calendar,
            'subjectLabel' => "Aula {$room->code}",
            'grid' => $grid,
        ]);
    }
}
