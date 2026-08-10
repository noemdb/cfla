<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Entity\Institucion;
use App\Services\Estudiant\StudentScopeService;
use App\Services\Lms\LmsPublicationStatus;
use Illuminate\Http\Request;

class ActivityPrintController extends Controller
{
    public function show(Request $request, Activity $activity)
    {
        // Consistencia de acceso con el detalle (ActivityView): la actividad
        // debe ser visible para este estudiante (sección de su inscripción +
        // publicación LMS vigente). Evita imprimir lecciones de otras secciones.
        abort_unless(
            app(StudentScopeService::class, ['user' => auth()->user()])
                ->isActivityVisible($activity),
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
            'lmsPublication',
        ]);

        // Obtener el título para la vista
        $titulo = 'LECCIÓN LMS · CONTENIDO COMPLETO';
        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $contexto = 'Estudiante';
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');
        $filters = []; // No hay filtros en la vista de actividad individual
        $filterLabels = [];

        // Etiqueta/clase legibles para el estado de publicación (ADIR-007).
        $estado = $activity->lmsPublication?->status ?? null;
        $estadoLabel = LmsPublicationStatus::label($estado);
        $estadoClass = LmsPublicationStatus::cssClass($estado);

        // Devolver la vista de impresión
        return view('livewire.student.lms.lessons-print', compact(
            'activity',
            'titulo',
            'institucion',
            'contexto',
            'fecha',
            'filters',
            'filterLabels',
            'estado',
            'estadoLabel',
            'estadoClass'
        ));
    }

    /**
     * Etiqueta/clase legible del estado — compartido (P5): LmsPublicationStatus.
     */
}
