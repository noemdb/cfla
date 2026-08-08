<?php

namespace App\Services\Lms;

/**
 * Repara y valida cuerpos de contenido que incrustan SVG (tipo IMAGE).
 *
 * Problema que resuelve: el generador de ilustraciones (LLM) a veces produce
 * SVG malformados — un tag de apertura sin su '>' que se "come" un cierre
 * ajeno (p.ej. '<rect x="560" ... rx="8"</svg>'). El navegador interpreta el
 * tag como un <rect> SIN atributo fill → se pinta NEGRO (fill por defecto del
 * SVG) y el '</svg>' tragado deja la estructura rota.
 *
 * - repair(): saneamiento defensivo en render (nunca lanza, nunca falla).
 *   Elimina el tag roto y reinserta el '</svg>' faltante. Un contenido dañado
 *   pierde solo el elemento incompleto — nunca vuelve a pintar un rectángulo
 *   negro ni a romper el layout de la página.
 * - isWellFormed(): validación en el lado del generador (rechaza el SVG antes
 *   de guardarlo en BD). Complementa la recomendación del blueprint/estudiant
 *   (bug histórico "Multiple root elements"): validar el body al guardar.
 */
class LmsSvgRepairService
{
    /**
     * Tag de apertura roto: '<nombre ...' sin su '>' propio que se ha comido
     * un cierre ajeno (p.ej. '<rect x="1" y="2"</svg>').
     *
     * Un tag bien formado NUNCA casa: '[^<>]' no puede cruzar su propio '>',
     * así que la secuencia '</...>' solo es alcanzable si el '>' del tag de
     * apertura no existe (truncamiento a mitad de tag).
     */
    private const BROKEN_TAG_RE = '/<[a-zA-Z][\w:-]*\b(?:(?!\/>)[^<>])*?<\/[^>]*>/s';

    /**
     * Repara un body de contenido con SVG embebido.
     */
    public function repair(string $body): string
    {
        if (! str_contains($body, '<svg')) {
            return $body;
        }

        // 1) Eliminar tags rotos (el match incluye el cierre ajeno que tragó).
        $repaired = preg_replace(self::BROKEN_TAG_RE, '', $body);

        // 2) Si el tag roto se había comido el '</svg>', reinsertarlo al final.
        //    En el parser HTML, los cierres de wrapper posteriores ('</div>',
        //    '</figure>') dentro de un <svg> abierto se ignoran (parse error) y
        //    el '</svg>' final cierra el elemento: el DOM queda exactamente con
        //    el contenido SVG y el figure intacto.
        $opens  = preg_match_all('/<svg\b/', $repaired);
        $closes = preg_match_all('#</svg>#', $repaired);
        if ($opens > $closes) {
            $repaired .= "\n</svg>";
        }

        return $repaired;
    }

    /**
     * Valida que un bloque SVG (recién generado) esté bien formado.
     *
     * @param  string  $svg  Bloque crudo tal como lo devuelve el LLM.
     */
    public function isWellFormed(string $svg): bool
    {
        $svg = trim($svg);

        if (! str_starts_with($svg, '<svg') || ! str_ends_with($svg, '</svg>')) {
            return false;
        }

        if (preg_match(self::BROKEN_TAG_RE, $svg) === 1) {
            return false;
        }

        $opens  = preg_match_all('/<svg\b/', $svg);
        $closes = preg_match_all('#</svg>#', $svg);

        return $opens === 1 && $closes === 1;
    }
}
