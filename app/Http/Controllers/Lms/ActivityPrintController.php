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

        // Vista previa (now() < publish_at): solo la 1ª sección y sus adjuntos
        // vinculados, igual que en el detalle (ActivityView). Los adjuntos
        // globales (section_id vacío) quedan ocultos.
        $isPreview = $activity->lmsPublication?->isPreviewToStudents() ?? false;

        // Embeds HTML (diagramas Mermaid, contenido embebido) — mismo pipeline
        // de normalización que el detalle (ActivityView).
        $htmlEmbeds = $activity->lmsHtmlEmbeds()
            ->where('is_visible', true)
            ->get()
            ->map(function ($embed) {
                $data = app(\App\Services\Lms\LmsContentRendererService::class)
                    ->ensureMermaidWrapper($embed->toArray());
                $embed->html_content = $data['html_content'];
                $embed->is_mermaid = $data['is_mermaid'];

                return $embed;
            });

        if ($isPreview) {
            $firstSection = $activity->lmsSections
                ->where('is_visible', true)
                ->first();
            $firstSectionId = $firstSection?->id;

            $activity->setRelation(
                'lmsSections',
                $firstSection ? collect([$firstSection]) : collect()
            );
            $activity->setRelation(
                'lmsResources',
                $activity->lmsResources->filter(
                    fn ($r) => $r->is_visible && $r->section_id === $firstSectionId
                )
            );
            $activity->setRelation(
                'lmsLinks',
                $activity->lmsLinks->filter(
                    fn ($l) => $l->is_visible && $l->section_id === $firstSectionId
                )
            );
            $htmlEmbeds = $htmlEmbeds->filter(
                fn ($e) => $e->section_id === $firstSectionId
            );
        }

        // Obtener el título para la vista
        $titulo = $isPreview
            ? 'LECCIÓN LMS · VISTA PREVIA'
            : 'LECCIÓN LMS · CONTENIDO COMPLETO';
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
            'estadoClass',
            'isPreview',
            'htmlEmbeds'
        ));
    }

    /**
     * Etiqueta/clase legible del estado — compartido (P5): LmsPublicationStatus.
     */
}
