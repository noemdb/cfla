<?php

namespace App\Services\Lms;

/**
 * Normalizador determinista de escala tipográfica para contenido HTML
 * generado por IA (Spec "Armonía tipográfica generateSlideHtmlTags").
 *
 * Defensa en profundidad: aunque el LLM ignore el prompt, el HTML generado
 * queda dentro de la escala del sistema (LmsDesignTokens).
 *
 * Conservador: SOLO baja tamaños, nunca sube. Las clases menores o iguales
 * a la escala pasan intactas.
 *
 * Reglas:
 *   - text-2xl+ → text-lg; EXCEPCIÓN: text-2xl + font-extrabold (patrón de
 *     stat card, token 'stat' = text-2xl) se conserva.
 *   - text-[NNpx] con NN > 18 → text-lg.
 *   - padding p-5+ (y variantes px/py/pt/pr/pb) → p-4 (misma variante).
 *   - shadow-md+ → shadow-sm.
 */
class LmsTypographyNormalizerService
{
    /** Tamaño máximo de texto (título) en la escala del sistema. */
    private const MAX_TEXT_PX = 18;

    /** Tamaño máximo para números de stat card (patrón font-extrabold). */
    private const MAX_STAT_PX = 24;

    public function normalize(string $html): string
    {
        return preg_replace_callback('/class="([^"]*)"/', function (array $m): string {
            $classes = preg_split('/\s+/', trim($m[1]));
            $hasExtrabold = in_array('font-extrabold', $classes, true);

            $out = [];
            foreach ($classes as $class) {
                $clamped = $this->clampClass($class, $hasExtrabold);
                if ($clamped !== null) {
                    $out[$clamped] = true;
                }
            }

            return 'class="' . implode(' ', array_keys($out)) . '"';
        }, $html);
    }

    private function clampClass(string $class, bool $hasExtrabold): ?string
    {
        // Tamaños de texto estándar.
        if (preg_match('/^text-(2xl|3xl|4xl|5xl|6xl|7xl|8xl|9xl)$/', $class)) {
            // text-2xl solo se conserva en patrón de stat card (font-extrabold).
            return ($class === 'text-2xl' && $hasExtrabold) ? 'text-2xl' : 'text-lg';
        }

        if ($class === 'text-xl') {
            return 'text-lg';
        }

        // Tamaños arbitrarios text-[NNpx] — clamp por píxel.
        if (preg_match('/^text-\[(\d+(?:\.\d+)?)px\]$/', $class, $m)) {
            $px = (float) $m[1];
            $max = $hasExtrabold ? self::MAX_STAT_PX : self::MAX_TEXT_PX;

            return $px > $max ? 'text-lg' : $class;
        }

        // Padding: p-5+ (y variantes) → misma variante en -4.
        if (preg_match('/^p([trblxy]?)-(5|6|7|8|9|10|12|14|16)$/', $class, $m)) {
            return 'p' . $m[1] . '-4';
        }

        // Sombras: shadow-md+ → shadow-sm.
        if (preg_match('/^shadow-(md|lg|xl|2xl|inner)$/', $class)) {
            return 'shadow-sm';
        }

        // El resto pasa intacto (text-lg, text-base, text-sm, text-xs, p-4, shadow-sm...).
        return $class;
    }
}
