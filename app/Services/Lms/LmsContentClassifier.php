<?php

namespace App\Services\Lms;

/**
 * Clasificador único de contenido LMS (pasos de sección).
 *
 * P4: centraliza la detección de tipo que estaba duplicada en 4 vistas:
 *   - livewire/student/lms/lessons-print.blade.php  (IMAGE + MERMAID + extracción)
 *   - livewire/student/lms/_content-renderer.blade.php (caso TEXT y caso HTML)
 *   - components/lms/student-preview.blade.php       (modal de previsualización)
 *
 * Una sola fuente de verdad para las regex: añadir un dialecto de mermaid o
 * ajustar la detección se hace aquí y queda cubierto por tests unitarios.
 */
class LmsContentClassifier
{
    /** Dialectos mermaid soportados (keyword inicial del diagrama). */
    public const MERMAID_KEYWORDS = 'flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline';

    /**
     * ¿Es una ilustración SVG cruda ("Generar Imagen") que se renderiza tal cual?
     * Un body con <svg> se considera imagen aunque el type sea otro.
     */
    public function isImageBody(string $type, string $body): bool
    {
        return $type === 'IMAGE' || preg_match('/<svg\b/', $body) === 1;
    }

    /**
     * ¿Es un diagrama mermaid? (clase CSS `mermaid` o keyword inicial).
     */
    public function isMermaidBody(string $body): bool
    {
        if (preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $body) === 1) {
            return true;
        }

        return preg_match('/^(' . self::MERMAID_KEYWORDS . ')\b/m', trim($body)) === 1;
    }

    /**
     * Extrae el código mermaid del body (etiqueta de la clase mermaid o el body
     * completo si el diagrama viene "desnudo").
     *
     * A1: conserva <br/> de labels multi-línea (strip_tags puro los eliminaría
     * y concatenaría el texto en una sola línea larga que desborda la columna
     * al imprimir).
     */
    public function extractMermaidCode(string $body): string
    {
        $code = '';
        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $body, $m)) {
            $code = trim(html_entity_decode(strip_tags($m[1] ?? '', '<br><br/>')));
        }

        if ($code === '') {
            $code = trim(html_entity_decode(strip_tags($body, '<br><br/>')));
        }

        return $code;
    }
}
