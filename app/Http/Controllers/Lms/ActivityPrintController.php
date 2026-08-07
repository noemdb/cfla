<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use Illuminate\Http\Request;

class ActivityPrintController extends Controller
{
    public function show(Request $request, Activity $activity)
    {
        // Verificar que la actividad sea visible para el estudiante
        abort_unless(
            $activity->lmsPublication?->isVisibleToStudents(),
            404
        );

        // Registrar la vista de impresión (opcional)
        LmsActivityLog::record(
            $activity->id,
            auth()->id(),
            'PRINT_VIEW',
            null,
            Activity::class
        );

        // Cargar las relaciones necesarias para la vista de impresión
        $activity->load([
            'lmsSections.visibleContents.media',
            'lmsResources.media',
            'lmsLinks',
            'pevaluacion.pensum.asignatura',
            'pevaluacion.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'lmsPublication'
        ]);

        // Obtener el título para la vista
        $titulo = "LECCIÓN LMS · CONTENIDO COMPLETO";
        $institucion = null;
        $contexto = 'Estudiante';
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');
        $filters = []; // No hay filtros en la vista de actividad individual
        $filterLabels = [];

        // Devolver la vista de impresión
        return view('livewire.student.lms.lessons-print', compact(
            'activity',
            'titulo',
            'institucion',
            'contexto',
            'fecha',
            'filters',
            'filterLabels'
        ));
    }
}