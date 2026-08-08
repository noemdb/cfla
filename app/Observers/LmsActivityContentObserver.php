<?php

namespace App\Observers;

use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Services\Lms\LmsContentClassifier;

/**
 * Mantiene la caché `lms_activity_sections.content_type` sincronizada con
 * los contenidos (Spec "Campo content_type en lms_activity_sections").
 *
 * Cualquier mutación de un contenido (crear, editar, ocultar/mostrar,
 * eliminar) recalcula el tipo de su sección con LmsContentClassifier.
 *
 * Notas:
 * - `saved` cubre create + update (incluido el toggle de `is_visible`).
 * - `saveQuietly` en la sección para no re-disparar eventos en cascada.
 * - Carga `visibleContents.media` en 1-2 consultas (mutaciones de contenido
 *   son poco frecuentes: admin/wizard).
 */
class LmsActivityContentObserver
{
    public function __construct(private readonly LmsContentClassifier $classifier)
    {
    }

    public function saved(LmsActivityContent $content): void
    {
        $this->refreshSection($content);
    }

    public function deleted(LmsActivityContent $content): void
    {
        $this->refreshSection($content);
    }

    private function refreshSection(LmsActivityContent $content): void
    {
        $section = $content->section()->with('visibleContents.media')->first();

        if (! $section) {
            return;
        }

        $section->content_type = $this->classifier->classifySection($section->visibleContents);
        $section->saveQuietly();
    }
}
