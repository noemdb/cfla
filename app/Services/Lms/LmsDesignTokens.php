<?php

namespace App\Services\Lms;

/**
 * Design tokens de tipografía del contenido LMS
 * (Spec "Armonía tipográfica generateSlideHtmlTags").
 *
 * Única fuente de verdad de la escala visual del contenido educativo.
 * Consumidores:
 *   - HtmlTaggingService (prompt de etiquetado → escala obligatoria)
 *   - LmsTypographyNormalizerService (clamp determinista post-generación)
 *
 * Principio: los títulos NUNCA superan text-lg (18px), los números de stat
 * card NUNCA superan text-2xl (24px) y las cards internas NUNCA superan
 * p-4 + shadow-sm — el resto de la lección renderiza a 14–17px.
 */
class LmsDesignTokens
{
    /** Escala tipográfica: token => clases Tailwind (máximo absoluto). */
    public const SCALE = [
        'heading-1'   => 'text-lg font-bold',          // título de contenido (h3) — NUNCA mayor
        'heading-2'   => 'text-base font-semibold',    // subtítulo (h4)
        'body'        => 'text-[15px] text-gray-700 leading-relaxed',
        'quote'       => 'text-[15px] italic text-gray-700',
        'stat'        => 'text-2xl font-extrabold',    // número de stat card — NUNCA mayor
        'badge'       => 'text-xs',
        'card-pad'    => 'p-4',                        // padding máximo de cards internas
        'card-shadow' => 'shadow-sm',                  // sombra máxima
        'accordion'   => 'text-[15px] font-semibold',  // summary del acordeón
    ];

    /**
     * Reglas de escala en texto plano para los prompts de generación.
     * Se genera desde SCALE para no duplicar (prompt ↔ código sin drift).
     */
    public static function promptRules(): string
    {
        return implode("\n", [
            '═══ ESCALA TIPOGRÁFICA OBLIGATORIA ═══',
            'El contenido se inserta junto a otros pasos de la lección que usan',
            'tipografía pequeña (14–17px). Tu HTML NO debe verse más grande que el resto:',
            '',
            '- Título de contenido (h3):   '.self::SCALE['heading-1'].' — NUNCA uses text-2xl ni text-3xl.',
            '- Subtítulo (h4):             '.self::SCALE['heading-2'].' — NUNCA uses text-lg.',
            '- Párrafos y listas:          '.self::SCALE['body'].'.',
            '- Citas (blockquote):         '.self::SCALE['quote'].'.',
            '- Número de stat card:        '.self::SCALE['stat'].' — NUNCA uses text-3xl ni mayor.',
            '- Badges:                     '.self::SCALE['badge'].'.',
            '- Padding de cards internas:  '.self::SCALE['card-pad'].' — NUNCA uses p-5, p-6 ni mayor.',
            '- Sombras:                    '.self::SCALE['card-shadow'].' — NUNCA uses shadow-lg, shadow-xl.',
            '- Acordeón (summary):         '.self::SCALE['accordion'].'.',
            '',
            'PROHIBIDO: text-3xl, text-4xl, text-2xl en títulos, p-5/p-6, shadow-lg/shadow-xl.',
            'NO repitas el título de la sección como heading: la plantilla ya lo muestra.',
        ]);
    }
}
