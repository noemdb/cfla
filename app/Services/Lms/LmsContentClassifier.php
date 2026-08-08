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

    // ─── Clasificación fina por contenido y agregación por sección (Spec
    //     "Campo content_type en lms_activity_sections") ───────────────────

    /** Tipos de contenido de sección (valores de lms_activity_sections.content_type). */
    public const SECTION_TYPES = [
        'text', 'markdown', 'html', 'mermaid', 'svg', 'image',
        'math', 'video', 'audio', 'mixed', 'none',
    ];

    /** Etiquetas legibles por tipo (UI, badges, reportes). */
    public const SECTION_TYPE_LABELS = [
        'text'     => 'Texto',
        'markdown' => 'Markdown',
        'html'     => 'HTML',
        'mermaid'  => 'Mermaid',
        'svg'      => 'SVG',
        'image'    => 'Imagen',
        'math'     => 'Math (LaTeX)',
        'video'    => 'Video',
        'audio'    => 'Audio',
        'mixed'    => 'Mixto',
        'none'     => 'Sin contenido',
    ];

    /**
     * Clasifica UN contenido al tipo fino (precedencia deliberada):
     * mermaid > svg/image > math > video/audio > html > markdown > text.
     *
     * @param  string       $type       ENUM del contenido (TEXT/HTML/IMAGE/VIDEO/...).
     * @param  string       $body       Body crudo (puede contener SVG/mermaid/LaTeX).
     * @param  string|null  $mediaMime  MIME del media adjunto (para distinguir svg de raster).
     */
    public function classifyContent(string $type, string $body, ?string $mediaMime = null): string
    {
        if ($this->isMermaidBody($body)) {
            return 'mermaid';
        }

        if ($this->isImageBody($type, $body)) {
            return ($mediaMime !== null && ! str_contains($mediaMime, 'svg'))
                ? 'image'
                : 'svg';
        }

        if ($this->isMathBody($body)) {
            return 'math';
        }

        if ($type === 'VIDEO') {
            return 'video';
        }

        if ($type === 'AUDIO') {
            return 'audio';
        }

        if ($type === 'HTML') {
            return 'html';
        }

        if ($this->isMarkdownBody($body)) {
            return 'markdown';
        }

        return 'text';
    }

    /**
     * Agrega los contenidos visibles de una sección → tipo de sección.
     *
     * @param  iterable<int, object>  $contents  Colección de contenidos (necesita type/body/media).
     */
    public function classifySection(iterable $contents): ?string
    {
        $types = [];
        foreach ($contents as $content) {
            $types[$this->classifyContent(
                (string) ($content->type ?? 'TEXT'),
                (string) ($content->body ?? ''),
                $content->media?->mime_type,
            )] = true;
        }

        if (count($types) === 0) {
            return 'none';
        }

        if (count($types) > 1) {
            return 'mixed';
        }

        return array_key_first($types);
    }

    /**
     * ¿Body con LaTeX/KaTeX? ($...$, $$...$$, \(...\), \[...\]).
     */
    public function isMathBody(string $body): bool
    {
        return preg_match('/(?<!\\\\)(\$\$|\\$|\\\\\(|\\\\\[)/', $body) === 1;
    }

    /**
     * ¿Markdown estructural (listas, citas, tablas, títulos) en vez de prosa?
     * Detecta sobre el body ya convertido a HTML (patrones que deja Str::markdown).
     */
    public function isMarkdownBody(string $body): bool
    {
        return preg_match('/<(ul|ol|blockquote|table|h[2-4])(\s|>)/i', $body) === 1;
    }
}
