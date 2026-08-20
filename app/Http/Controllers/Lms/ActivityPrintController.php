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
            'lmsSections' => fn ($q) => $q->where('is_visible', true),
            'lmsSections.visibleContents.media',
            'lmsResources' => fn ($q) => $q->where('is_visible', true),
            'lmsResources.media',
            'lmsLinks' => fn ($q) => $q->where('is_visible', true),
            'pevaluacion.pensum.asignatura',
            'pevaluacion.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'lmsPublication',
        ]);

        // Vista previa (now() < publish_at) y embeds HTML normalizados — misma
        // lógica que el detalle (ActivityView), centralizada en LmsPreviewService.
        $previewService = app(\App\Services\Lms\LmsPreviewService::class);
        $isPreview = $previewService->isPreview($activity);
        $htmlEmbeds = $previewService->normalizeEmbeds(
            $activity->lmsHtmlEmbeds()
                ->where('is_visible', true)
                ->get()
        );

        $preview = $previewService->applyPreview(
            $activity->lmsSections,
            $activity->lmsResources,
            $activity->lmsLinks,
            $htmlEmbeds,
            $isPreview
        );
        $activity->setRelation('lmsSections', $preview['sections']);
        $activity->setRelation('lmsResources', $preview['resources']);
        $activity->setRelation('lmsLinks', $preview['links']);
        $htmlEmbeds = $preview['htmlEmbeds'];

        // Datos para la vista de impresión
        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');

        // Etiqueta/clase legibles para el estado de publicación (ADIR-007).
        $estado = $activity->lmsPublication?->status ?? null;
        $estadoLabel = LmsPublicationStatus::label($estado);
        $estadoClass = LmsPublicationStatus::cssClass($estado);

        // Devolver la vista de impresión
        return view('livewire.student.lms.lessons-print', compact(
            'activity',
            'institucion',
            'fecha',
            'estado',
            'estadoLabel',
            'estadoClass',
            'isPreview',
            'htmlEmbeds'
        ));
    }
}
