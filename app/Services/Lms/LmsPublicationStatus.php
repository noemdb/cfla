<?php

namespace App\Services\Lms;

/**
 * Etiquetas y clases CSS del estado de publicación LMS (P5).
 *
 * Antes duplicado como métodos privados en 3 controllers de impresión
 * (estudiante/director/profesor). Una sola fuente de verdad.
 */
class LmsPublicationStatus
{
    /** Etiqueta legible en español para el estado de publicación. */
    public static function label(?string $estado): string
    {
        return match ($estado) {
            'PUBLISHED' => 'Publicado',
            'SCHEDULED' => 'Programado',
            'ARCHIVED' => 'Archivado',
            null => 'N.PUB',
            default => 'Borrador',
        };
    }

    /** Clase CSS del estado para las vistas de impresión. */
    public static function cssClass(?string $estado): string
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
