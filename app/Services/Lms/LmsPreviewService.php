<?php

namespace App\Services\Lms;

use App\Models\app\Academy\Activity;
use Illuminate\Support\Collection;

/**
 * Lógica de vista previa de una lección LMS (now() < publish_at).
 *
 * Centraliza el criterio "solo la 1ª sección y sus adjuntos vinculados" que
 * antes estaba duplicado en ActivityView (detalle) y ActivityPrintController
 * (impresión), para que ambas vistas no puedan divergir.
 */
class LmsPreviewService
{
    /**
     * ¿La lección está en vista previa para los estudiantes?
     */
    public function isPreview(Activity $activity): bool
    {
        return $activity->lmsPublication?->isPreviewToStudents() ?? false;
    }

    /**
     * Normaliza los embeds HTML (detección de Mermaid y extracción del código)
     * con el mismo pipeline del detalle.
     *
     * @param  Collection<int, \App\Models\app\Academy\Lms\LmsHtmlEmbed>  $embeds
     * @return Collection<int, \App\Models\app\Academy\Lms\LmsHtmlEmbed>
     */
    public function normalizeEmbeds(Collection $embeds): Collection
    {
        return $embeds->map(function ($embed) {
            $data = app(LmsContentRendererService::class)
                ->ensureMermaidWrapper($embed->toArray());
            $embed->html_content = $data['html_content'];
            $embed->is_mermaid = $data['is_mermaid'];

            return $embed;
        });
    }

    /**
     * Aplica el filtrado de vista previa sobre las colecciones visibles de la
     * lección: solo la 1ª sección y sus adjuntos vinculados (section_id == id
     * de la primera sección). Los adjuntos globales (section_id vacío) quedan
     * ocultos.
     *
     * Si no es vista previa, devuelve las colecciones tal cual.
     *
     * @param  Collection  $sections  Colecciones YA filtradas por is_visible.
     * @return array{sections: Collection, resources: Collection, links: Collection, htmlEmbeds: Collection}
     */
    public function applyPreview(
        Collection $sections,
        Collection $resources,
        Collection $links,
        Collection $htmlEmbeds,
        bool $isPreview
    ): array {
        if (! $isPreview) {
            return compact('sections', 'resources', 'links', 'htmlEmbeds');
        }

        $firstSection = $sections->first();
        $firstSectionId = $firstSection?->id;

        return [
            'sections' => $firstSection ? collect([$firstSection]) : collect(),
            'resources' => $resources->filter(fn ($r) => $r->section_id === $firstSectionId),
            'links' => $links->filter(fn ($l) => $l->section_id === $firstSectionId),
            'htmlEmbeds' => $htmlEmbeds->filter(fn ($e) => $e->section_id === $firstSectionId),
        ];
    }
}
