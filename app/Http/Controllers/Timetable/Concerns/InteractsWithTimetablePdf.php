<?php

namespace App\Http\Controllers\Timetable\Concerns;

use App\Models\app\Timetable\TimetableCalendar;

/**
 * Infraestructura compartida por todos los endpoints PDF de horarios
 * (SPEC-TIMETABLE-001 §8): límite de memoria para reportes grandes, semántica
 * del badge «publicado / borrador», resolución de slots persistidos y stream
 * con cabeceras anti-caché.
 */
trait InteractsWithTimetablePdf
{
    /**
     * Los informes multi-registro (todos los docentes, todos los grados, etc.)
     * generan un único documento HTML que dompdf debe parsear y renderizar; el
     * límite por defecto (128M) se agota fácilmente. Se sube para estas
     * exportaciones puntuales.
     */
    protected function raisePdfMemoryLimit(): void
    {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(300);
    }

    /**
     * Indica si el horario debe resolverse desde los slots persistidos en BD
     * (calendario publicado o clonado, cuyo `preview_payload` no contiene
     * 'assignment') en lugar del payload de vista previa (dry-run).
     */
    protected function shouldUsePersistedSlots(TimetableCalendar $calendar): bool
    {
        return ! is_array($calendar->preview_payload['assignment'] ?? null);
    }

    /**
     * Semántica compartida del badge «HORARIO PUBLICADO / BORRADOR» en todos
     * los PDFs: el badge debe reflejar la FUENTE real de los datos.
     *
     * Un calendario activo publicado se lee desde `timetable_slots` y se
     * rotula HORARIO PUBLICADO. Un calendario activo pero con una simulación
     * vigente en `preview_payload` se lee desde esa simulación y se rotula
     * BORRADOR (es una vista previa). Los borradores siempre son «preview».
     */
    protected function isPublishedSchedule(TimetableCalendar $calendar, bool $usePersistedSlots): bool
    {
        return $calendar->status === TimetableCalendar::STATUS_ACTIVE && $usePersistedSlots;
    }

    /**
     * Stream de PDF con cabeceras anti-caché: los reportes reflejan el estado
     * vigente del calendario, así que el navegador no debe reutilizar versiones
     * anteriores de la misma URL (badge/nombres desactualizados).
     */
    protected function pdfResponse($pdf, string $filename)
    {
        $response = $pdf->stream($filename);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s').' GMT');

        return $response;
    }
}
