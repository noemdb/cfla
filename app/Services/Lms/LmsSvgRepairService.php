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
        $opens = preg_match_all('/<svg\b/', $repaired);
        $closes = preg_match_all('#</svg>#', $repaired);
        if ($opens > $closes) {
            $repaired .= "\n</svg>";
        }

        // ── 3) Normalizar el contraste de los textos (ver normalizeContrast) ──
        return $this->normalizeContrast($repaired);
    }

    /**
     * Oscurece los textos de los SVG que quedan ilegibles sobre los fondos
     * pastel (problema informado con el "Diagrama: Dinámicas de poder…").
     *
     * Contexto: el generador (LLM) pinta los textos de los diagramas con
     * grises claros (#555555, #666666 …) y subtítulos en gris medio (#999999,
     * #cccccc) sobre fondos casi blancos (#f8f9fa) y pastel (#fff3e0, #e3f2fd…).
     * A 12–14px esos grises tienen contraste insuficiente (≈1.9:1–4.6:1 según
     * el tono) y resultan difíciles de leer, sobre todo subtítulos y etiquetas.
     *
     * Este paso, aplicado en render (vía repair()) y en el generador, remapea
     * SOLO los fills de <text>/<tspan> que son tonos grisáceos claros a un gris
     * oscuro equivalente (contraste ≥ 7:1 sobre el fondo blanco). No toca:
     * - fill/stroke de formas (rect, circle, path…): mantienen el diseño.
     * - textos ya oscuros (#1a1a1a, #333333): ya cumplen AA.
     * - textos con color con matiz (rojos, azules…): se preservan.
     */
    public function normalizeContrast(string $svg): string
    {
        if (! str_contains($svg, '<svg')) {
            return $svg;
        }

        // Fills grises claros presentes en los diagramas reales → gris oscuro.
        $map = [
            '#eeeeee' => '#666666',
            '#dddddd' => '#555555',
            '#cccccc' => '#4d4d4d',
            '#bbbbbb' => '#444444',
            '#aaaaaa' => '#444444',
            '#999999' => '#3d3d3d',
            '#888888' => '#3d3d3d',
            '#777777' => '#333333',
            '#666666' => '#333333',
            '#555555' => '#333333',
            // Forma corta de 3 dígitos
            '#eee' => '#666',
            '#ddd' => '#555',
            '#ccc' => '#4d4d4d',
            '#bbb' => '#444',
            '#aaa' => '#444',
            '#999' => '#3d3d3d',
            '#888' => '#3d3d3d',
            '#777' => '#333',
            '#666' => '#333',
            '#555' => '#333',
        ];

        // Solo en textos: reemplazar el atributo fill de <text …> y <tspan …>.
        return preg_replace_callback(
            '~<(text|tspan|textPath)\b[^>]*\bfill="([^"]+)"~',
            function ($m) use ($map) {
                $fill = strtolower($m[2]);

                return str_replace('fill="'.$m[2].'"', 'fill="'.($map[$fill] ?? $m[2]).'"', $m[0]);
            },
            $svg
        );
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

        $opens = preg_match_all('/<svg\b/', $svg);
        $closes = preg_match_all('#</svg>#', $svg);

        return $opens === 1 && $closes === 1;
    }

    /**
     * Normaliza el viewBox de un SVG al bounding box real de su contenido:
     * recorta el espacio vacío inferior del canvas.
     *
     * ¿Por qué? El generador (LLM) dibuja sobre un canvas alto fijo (p.ej.
     * 1000×950) pero el contenido ocupa solo la parte superior (p.ej. hasta
     * y=380). Al escalar el SVG al ancho de la columna de impresión
     * (max-width:100%), el hueco vacío inferior se amplifica y deja un gran
     * espacio en blanco debajo del diagrama (medido: 60% en el contenido 2232).
     *
     * Conservador: solo actúa cuando el vacío inferior es ≥ 20% del canvas
     * (umbral del diagnóstico). Si no hay recorte que valga la pena, devuelve
     * el SVG sin cambios.
     */
    public function cropToContent(string $svg): string
    {
        if (! preg_match('/<svg\b[^>]*viewBox="([^"]+)"/', $svg, $vm)) {
            return $svg;
        }

        [$vx, $vy, $vw, $vh] = array_map('floatval', preg_split('/[\s,]+/', $vm[1]));
        if ($vw <= 0 || $vh <= 0) {
            return $svg;
        }

        $maxBottom = 0.0;
        $hasContent = false;

        // rects (excluye el rect de fondo que cubre ≥ 90% del canvas)
        if (preg_match_all('/<rect\b[^>]*>/', $svg, $rects)) {
            foreach ($rects[0] as $rect) {
                $w = (float) ($this->attr($rect, 'width') ?? 0);
                $h = (float) ($this->attr($rect, 'height') ?? 0);
                $y = (float) ($this->attr($rect, 'y') ?? 0);

                if ($w * $h >= 0.9 * $vw * $vh) {
                    continue; // fondo del canvas
                }
                if ($w > 0 && $h > 0) {
                    $maxBottom = max($maxBottom, $y + $h);
                    $hasContent = true;
                }
            }
        }

        // texts (el borde inferior ≈ y + font-size)
        if (preg_match_all('/<text\b[^>]*>/', $svg, $texts)) {
            foreach ($texts[0] as $t) {
                $y = (float) ($this->attr($t, 'y') ?? 0);
                if ($y > 0) {
                    $fontSize = (float) ($this->attr($t, 'font-size') ?? 14);
                    $maxBottom = max($maxBottom, $y + $fontSize);
                    $hasContent = true;
                }
            }
        }

        if (! $hasContent || $maxBottom / $vh > 0.80) {
            return $svg; // sin vacío significativo o sin geometría detectable
        }

        $margin = min(40.0, round($vw * 0.04, 1));
        $newH = (int) ceil(min($maxBottom + $margin, $vh));
        $newBox = sprintf('%d %d %d %d', (int) $vx, (int) $vy, (int) $vw, $newH);

        // preg_replace_callback: evita la ambigüedad de backreferences con
        // dígitos a continuación ('$1'.$newBox.'$2' se leía como '$10').
        return preg_replace_callback(
            '/(<svg\b[^>]*viewBox=")[^"]+(")/',
            fn ($m) => $m[1].$newBox.$m[2],
            $svg,
            1
        );
    }

    /**
     * Lee un atributo de un tag SVG (comillas dobles).
     */
    private function attr(string $tag, string $name): ?string
    {
        return preg_match('/'.$name.'="([^"]*)"/', $tag, $m) ? $m[1] : null;
    }
}
