<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Entity\Institucion;
use App\Services\Estudiant\StudentScopeService;
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
        $estadoLabel = $this->estadoLabel($estado);
        $estadoClass = $this->estadoClass($estado);

        // Devolver la vista de impresión
        return view('livewire.student.lms.lessons-print', compact(
            'activity',
            'titulo',
            'institucion',
            'contexto',
            'fecha',
            'filters',
            'filterLabels',
            'estadoLabel',
            'estadoClass'
        ));
    }

    /**
     * Etiqueta legible para el estado de publicación.
     */
    private function estadoLabel(?string $estado): string
    {
        return match ($estado) {
            'PUBLISHED' => 'Publicado',
            'SCHEDULED' => 'Programado',
            'ARCHIVED' => 'Archivado',
            null => 'N.PUB',
            default => 'Borrador',
        };
    }

    /**
     * Clase CSS del estado para la vista de impresión.
     */
    private function estadoClass(?string $estado): string
    {
        return match ($estado) {
            'PUBLISHED' => 'estado-pub',
            'SCHEDULED' => 'estado-prog',
            'ARCHIVED' => 'estado-arc',
            null => 'estado-npub',
            default => 'estado-draft',
        };
    }
}
