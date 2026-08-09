<?php

namespace App\Services\Lms;

/**
 * Reparaciones deterministas a registros LMS para mejorar la vista de impresión
 * (modo libro, orientación horizontal).
 *
 * Objetivo: que al imprimir la lección (/app/estudiante/activity/ID/print)
 * el resultado sea un libro profesional con:
 *   - Portada con título legible (sin saltos de líneaawkward)
 *   - Contenido en dos columnas legibles (medida 45–75 caracteres/línea)
 *   - SVGs y tablas que caben en las columnas sin overflow
 *   - Tipografía consistente (sin tamaños explosivos de IA)
 *
 * Reglas:
 *   - Solo BAJA tamaños, nunca sube (conservador).
 *   - Idempotente: segunda corrida = 0 cambios.
 *   - Nunca lanza excepciones; registra warnings.
 */
class LmsPrintRepairService
{
    // ── Límites de tipografía para impresión ──────────────────────────
    /** Tamaño máximo de texto (párrafos) en contenido: 11pt para legibilidad en dos columnas. */
    private const MAX_BODY_PT = 11;

    /** Tamaño máximo de títulos (h1, h2, h3) en contenido: 14pt. */
    private const MAX_HEADING_PT = 14;

    /** Tamaño máximo de subtítulos (h4, h5): 12pt. */
    private const MAX_SUBHEADING_PT = 12;

    // ── Límites de contenido ──────────────────────────────────────────
    /** Longitud máxima de título de sección para portada (evita saltos awkward). */
    private const MAX_COVER_TITLE_LEN = 80;

    // ── Clases Tailwind que aplastan tipografía ──────────────────────
    /** Clases de tamaño de texto que aplastamos para print. */
    private const CLAMPED_TAILWIND = [
        'text-9xl' => 'text-sm',   'text-8xl' => 'text-sm',
        'text-7xl' => 'text-sm',   'text-6xl' => 'text-sm',
        'text-5xl' => 'text-sm',   'text-4xl' => 'text-base',
        'text-3xl' => 'text-base', 'text-2xl' => 'text-base',
        'text-xl'  => 'text-base',
    ];

    /**
     * Repara el body de un LmsActivityContent para impresión.
     *
     * @return array{body: string, changed: bool, issues: string[]}
     */
    public function repairBody(string $body): array
    {
        if ($body === '' || $body === null) {
            return ['body' => $body, 'changed' => false, 'issues' => []];
        }

        $original = $body;
        $issues = [];

        // 1) Aplastar clases Tailwind de tamaño de texto excesivo.
        $body = $this->clampTailwindText($body, $issues);

        // 2) Normalizar tamaños inline (font-size: 20px+, text-XXpx) a pt.
        $body = $this->clampInlineFontSizes($body, $issues);

        // 3) Asegurar que <img> tenga max-width:100% para caber en columna.
        $body = $this->ensureImageMaxWidth($body, $issues);

        // 4) Asegurar que <table> tenga overflow-x:auto para columnas.
        $body = $this->ensureTableOverflow($body, $issues);

        // 5) Asegurar que <svg> tenga max-width:100% y viewBox.
        $body = $this->ensureSvgConstraints($body, $issues);

        // 6) Limpiar !important de estilos inline (rompen CSS de impresión).
        $body = $this->cleanInlineImportant($body, $issues);

        // 7) Normalizar tamaños de heading (h1–h6) a escala print.
        $body = $this->clampHeadingSizes($body, $issues);

        $changed = $body !== $original;

        return ['body' => $body, 'changed' => $changed, 'issues' => $issues];
    }

    /**
     * Trunca un título de sección para que sea adecuado para la portada.
     *
     * @return array{title: string, changed: bool}
     */
    public function repairTitle(string $title): array
    {
        $original = $title;
        $trimmed = trim($title);

        if (mb_strlen($trimmed) > self::MAX_COVER_TITLE_LEN) {
            $trimmed = mb_substr($trimmed, 0, self::MAX_COVER_TITLE_LEN);
            // Cortar en el último espacio completo para no cortar palabras.
            $lastSpace = mb_strrpos($trimmed, ' ');
            if ($lastSpace > self::MAX_COVER_TITLE_LEN * 0.6) {
                $trimmed = mb_substr($trimmed, 0, $lastSpace);
            }
            $trimmed .= '…';
        }

        return ['title' => $trimmed, 'changed' => $trimmed !== $original];
    }

    // ── Métodos internos ─────────────────────────────────────────────

    /**
     * Aplasta clases Tailwind de tamaño de texto excesivo.
     */
    private function clampTailwindText(string $html, array &$issues): string
    {
        return preg_replace_callback(
            '/class="([^"]*)"/',
            function (array $m) use (&$issues): string {
                $classes = preg_split('/\s+/', trim($m[1]));
                $changed = false;
                $out = [];

                foreach ($classes as $cls) {
                    if (isset(self::CLAMPED_TAILWIND[$cls])) {
                        $out[] = self::CLAMPED_TAILWIND[$cls];
                        $changed = true;
                    } else {
                        $out[] = $cls;
                    }
                }

                if ($changed) {
                    $issues[] = 'tailwind_text_clamped';
                }

                return 'class="' . implode(' ', $out) . '"';
            },
            $html,
        );
    }

    /**
     * Normaliza font-size inline (20px+, 1.5em+) a pt razonables.
     */
    private function clampInlineFontSizes(string $html, array &$issues): string
    {
        // font-size: NNpx donde NN > 18 → 11pt (body) o 14pt (heading context).
        $replaced = preg_replace_callback(
            '/font-size\s*:\s*(\d+(?:\.\d+)?)(px|pt|em|rem)/i',
            function (array $m): string {
                $val = (float) $m[1];
                $unit = strtolower($m[2]);

                $maxPt = self::MAX_BODY_PT;
                if ($unit === 'px' && $val > 18) {
                    return "font-size: {$maxPt}pt";
                }
                if ($unit === 'pt' && $val > 14) {
                    return "font-size: {$maxPt}pt";
                }
                if ($unit === 'em' && $val > 1.3) {
                    return "font-size: {$maxPt}pt";
                }
                if ($unit === 'rem' && $val > 1.3) {
                    return "font-size: {$maxPt}pt";
                }

                return $m[0]; // sin cambio
            },
            $html,
        );

        if ($replaced !== $html) {
            $issues[] = 'inline_font_sizes_clamped';
        }

        return $replaced;
    }

    /**
     * Asegura que todos los <img> tengan max-width:100% para caber en columna.
     */
    private function ensureImageMaxWidth(string $html, array &$issues): string
    {
        // Si ya tiene max-width, no tocar.
        if (preg_match_all('/<img\b[^>]*>/i', $html, $imgs)) {
            $count = 0;
            $result = preg_replace_callback(
                '/<img\b([^>]*)>/i',
                function (array $m) use (&$count): string {
                    $attrs = $m[1];
                    // Si ya tiene max-width, no tocar.
                    if (preg_match('/max-width/i', $attrs)) {
                        return $m[0];
                    }
                    $count++;
                    return '<img' . $attrs . ' style="max-width:100%;height:auto;">';
                },
                $html,
            );
            if ($count > 0) {
                $issues[] = "images_max_width_added:{$count}";
            }
            return $result;
        }

        return $html;
    }

    /**
     * Asegura que las tablas tengan overflow-x:auto para caber en columnas.
     */
    private function ensureTableOverflow(string $html, array &$issues): string
    {
        if (preg_match('/<table\b/i', $html)) {
            // No tocar si ya tiene overflow en algún wrapper.
            if (str_contains($html, 'overflow-x') || str_contains($html, 'overflow: auto')) {
                return $html;
            }

            // Envolver cada <table> en un div con overflow-x:auto.
            $result = preg_replace(
                '/<table\b(.*?)>(.*?)<\/table>/si',
                '<div style="overflow-x:auto;-webkit-overflow-scrolling:touch;"><table$1>$2</table></div>',
                $html,
                -1,
                $count,
            );
            if ($count > 0) {
                $issues[] = "tables_wrapped_scroll:{$count}";
            }
            return $result;
        }

        return $html;
    }

    /**
     * Asegura que los <svg> tengan max-width:100% para caber en columna.
     */
    private function ensureSvgConstraints(string $html, array &$issues): string
    {
        if (! str_contains($html, '<svg')) {
            return $html;
        }

        $result = preg_replace_callback(
            '/<svg\b([^>]*)>/i',
            function (array $m): string {
                $attrs = $m[1];
                if (preg_match('/max-width/i', $attrs)) {
                    return $m[0];
                }
                return '<svg' . $attrs . ' style="max-width:100%;height:auto;">';
            },
            $html,
            -1,
            $count,
        );

        if ($count > 0) {
            $issues[] = "svgs_max_width_added:{$count}";
        }

        return $result;
    }

    /**
     * Limpia !important de estilos inline (rompen CSS de impresión).
     */
    private function cleanInlineImportant(string $html, array &$issues): string
    {
        $result = preg_replace('/!important\b\s*/i', '', $html, -1, $count);
        if ($count > 0) {
            $issues[] = "inline_important_removed:{$count}";
        }
        return $result;
    }

    /**
     * Normaliza tamaños de heading (h1–h6) a escala de impresión.
     */
    private function clampHeadingSizes(string $html, array &$issues): string
    {
        $headings = ['h1' => self::MAX_HEADING_PT, 'h2' => self::MAX_HEADING_PT,
                      'h3' => self::MAX_HEADING_PT, 'h4' => self::MAX_SUBHEADING_PT,
                      'h5' => self::MAX_SUBHEADING_PT, 'h6' => self::MAX_SUBHEADING_PT];

        $changed = false;
        foreach ($headings as $tag => $maxPt) {
            $html = preg_replace_callback(
                "/<{$tag}\b([^>]*)>(.*?)<\/{$tag}>/si",
                function (array $m) use ($tag, $maxPt, &$changed): string {
                    $attrs = $m[1];
                    // Si ya tiene font-size, no tocar.
                    if (preg_match('/font-size/i', $attrs)) {
                        return $m[0];
                    }
                    // Si ya tiene style, agregar font-size.
                    if (preg_match('/style\s*=\s*"([^"]*)"/i', $attrs, $sm)) {
                        $existingStyle = $sm[1];
                        if (preg_match('/font-size/i', $existingStyle)) {
                            return $m[0];
                        }
                        $newStyle = $existingStyle . ";font-size:{$maxPt}pt";
                        $newAttrs = str_replace($sm[0], 'style="' . $newStyle . '"', $attrs);
                        $changed = true;
                        return "<{$tag}{$newAttrs}>{$m[2]}</{$tag}>";
                    }
                    // Sin style: agregar.
                    $changed = true;
                    return "<{$tag} style=\"font-size:{$maxPt}pt\"{$attrs}>{$m[2]}</{$tag}>";
                },
                $html,
            );
        }

        if ($changed) {
            $issues[] = 'heading_sizes_normalized';
        }

        return $html;
    }
}
