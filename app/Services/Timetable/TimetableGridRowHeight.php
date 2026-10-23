<?php

namespace App\Services\Timetable;

/**
 * Estimador de la altura de fila para las grillas de horario en PDF (DomPDF).
 *
 * Contexto: DomPDF ignora `height` en estilos inline y en `tr`, pero lo respeta
 * cuando llega al `td` desde una regla del stylesheet. Con
 * `table-layout:fixed` el corte de líneas es determinista, así que esta clase
 * calcula la altura de la celda más cargada de la grilla; la vista emite esa
 * altura como regla CSS para TODAS las filas de esa grilla y así quedan
 * uniformes sin importar el contenido («LONGUA» a 1 línea vs celdas de 6 líneas).
 *
 * Dos dimensiones independientes:
 *
 * - Modo (`?row_mode=`, calibración con render real):
 *   - `conservative` (default): margen de seguridad en ancho/factor. Puede salir
 *     ~1 línea más alta que el mínimo en grillas poco cargadas (efecto
 *     «espacios vacíos»), pero jamás se parte una fila en DomPDF.
 *   - `strict`: tolerance 0 — factor 1.0 y ancho útil real. Devuelve el mínimo
 *     exacto del greedy wrap; útil para medir el desajuste real de DomPDF. Si
 *     queda corto, DomPDF partiría la celda: solo para calibrar.
 *
 * - Perfil visual (`$style`, métricas reales de la plantilla destino): cada
 *   plantilla usa font-sizes y anchos de celda distintos; calcular con las del
 *   estilo equivocado estimaría mal:
 *   - `standard`: `preview-all-pestudios` (subject 10.5pt, meta 6.5pt,
 *     rótulos ½ 0.85em, separador ½ de 5px, td padding 1px/2px + borde 2px).
 *   - `preview-pestudio`: `preview-pestudio` (subject 8.8pt, group 8pt, ambos
 *     line-height:1.15; rótulos ½ 0.85em; gap entre mitades 3px (dashed), gap
 *     entre asignaturas 5px (`cell-assignment + cell-assignment`), td padding
 *     2px vertical + borde 1px colapsado; celda útil ≈145px).
 *
 * El corte de líneas usa las métricas AFM de Helvetica con el mismo algoritmo
 * greedy de DomPDF y un factor de corrección por modo. Prefiere pasarse por
 * arriba (conservador) antes que quedarse corto (rompería la uniformidad).
 */
class TimetableGridRowHeight
{
    public const MODE_CONSERVATIVE = 'conservative';

    public const MODE_STRICT = 'strict';

    public const STYLE_STANDARD = 'standard';

    public const STYLE_PREVIEW_PESTUDIO = 'preview-pestudio';

    /** Métricas por perfil visual. `avail`/`factor`/`tail` por modo:
     * avail = ancho útil del CONTENIDO de td (px CSS), tail = padding vertical
     * + borde colapsado del td (px). subject/meta/label/gaps en px. */
    private const STYLES = [
        self::STYLE_STANDARD => [
            'subject' => 14.0, // 10.5pt (line-height:1)
            'meta' => 8.7,     // 6.5pt
            'label' => 10.8,   // rótulo ½ (0.85em ≈ 10.8)
            'halfGap' => 5.0,  // separador ½ sup./½ inf.
            'entryGap' => 0.0, // sin separador entre asignaturas completas (lo cubre el factor)
            'tail' => 6.0,     // padding 2px×2 + borde 2px
            'avail' => [self::MODE_CONSERVATIVE => 152.0, self::MODE_STRICT => 161.0],
            'factor' => [self::MODE_CONSERVATIVE => 1.05, self::MODE_STRICT => 1.0],
        ],
        self::STYLE_PREVIEW_PESTUDIO => [
            'subject' => 13.5, // .subject 8.8pt ×1.15
            'meta' => 12.3,    // .group 8pt ×1.15
            'label' => 10.4,   // rótulo ½: 0.85em de 8pt ×1.15
            'halfGap' => 3.0,  // dashed 1px + padding/margen 1px
            'entryGap' => 5.0, // .cell-assignment + .cell-assignment: 2+2+1px
            'tail' => 5.0,     // padding vertical 2px×2 + borde colapsado 1px
            'avail' => [self::MODE_CONSERVATIVE => 138.0, self::MODE_STRICT => 145.0],
            'factor' => [self::MODE_CONSERVATIVE => 1.05, self::MODE_STRICT => 1.0],
        ],
    ];

    /**
     * Altura uniforme (px) para todas las filas de una grilla = su celda más
     * cargada (+ padding/borde). Los recesos no aportan texto.
     *
     * @param  iterable  $rows  filas de `shiftGrid['rows']` (period + days)
     * @param  string  $rowMode  'conservative'|'strict' (parámetro `row_mode`)
     * @param  string  $style  perfil visual de la plantilla destino
     */
    public static function forGrid(
        iterable $rows,
        bool $showTeachers,
        string $rowMode = self::MODE_CONSERVATIVE,
        string $style = self::STYLE_STANDARD
    ): int {
        [
            'avail' => $avail,
            'factor' => $factor,
            'subject' => $subjectPx,
            'meta' => $metaPx,
            'label' => $labelPx,
            'halfGap' => $halfGap,
            'entryGap' => $entryGap,
            'tail' => $tail,
        ] = self::config($style, $rowMode);

        $maxPx = 0.0;
        foreach ($rows as $row) {
            if ($row['period']->is_break) {
                continue;
            }
            foreach ($row['days'] as $day) {
                $maxPx = max($maxPx, self::cellPx(
                    $day['assignments'] ?? [],
                    $showTeachers,
                    compact('avail', 'factor', 'subjectPx', 'metaPx', 'labelPx', 'halfGap', 'entryGap')
                ));
            }
        }

        return max(24, (int) ceil($maxPx + $tail));
    }

    /** Altura (px) del contenido de una celda, incluyendo medias franjas. */
    private static function cellPx(iterable $slots, bool $showTeachers, array $m): float
    {
        $px = 0.0;
        $sup = [];
        $inf = [];
        $fullCount = 0;

        foreach ($slots as $slot) {
            $half = data_get($slot, 'half');
            if ($half === null) {
                if ($fullCount > 0) {
                    $px += $m['entryGap']; // separador entre asignaturas completas
                }
                $px += self::entryPx($slot, $showTeachers, $m);
                $fullCount++;

                continue;
            }
            if ((int) $half === 0) {
                $sup[] = $slot;
            } else {
                $inf[] = $slot;
            }
        }

        if ($sup !== [] || $inf !== []) {
            $px += 2 * $m['labelPx'] + $m['halfGap']; // rótulos ½ sup./½ inf. + separador
            foreach ($sup as $slot) {
                $px += self::entryPx($slot, $showTeachers, $m);
            }
            foreach ($inf as $slot) {
                $px += self::entryPx($slot, $showTeachers, $m);
            }
        }

        return $px;
    }

    /** Altura (px) de una asignatura (+ grupo y docente si corresponden). */
    private static function entryPx($cell, bool $showTeachers, array $m): float
    {
        $px = self::linesPx((string) ($cell['asignatura'] ?? '?'), $m['subjectPx'], true, $m);
        if (! empty($cell['grupo'])) {
            $px += self::linesPx((string) $cell['grupo'], $m['metaPx'], true, $m);
        }
        if ($showTeachers && ! empty($cell['profesor'])) {
            $px += self::linesPx((string) $cell['profesor'], $m['metaPx'], false, $m);
        }

        return $px;
    }

    /** Líneas que ocupará un texto × su alto de línea. */
    private static function linesPx(string $text, float $linePx, bool $bold, array $m): float
    {
        return self::wrapLines($text, $m['avail'], $linePx, $bold, $m['factor']) * $linePx;
    }

    /** Greedy word-wrap idéntico en espíritu al de DomPDF. */
    private static function wrapLines(string $text, float $avail, float $linePx, bool $bold, float $factor): int
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $text));
        if ($text === '') {
            return 0;
        }

        $lines = 1;
        $used = 0.0;
        foreach (preg_split('/\s+/', $text) as $word) {
            $wordPx = self::widthPx($word, $linePx, $bold, $factor);
            $spacePx = $used > 0.0 ? self::widthPx(' ', $linePx, $bold, $factor) : 0.0;

            if ($used + $spacePx + $wordPx <= $avail) {
                $used += $spacePx + $wordPx;

                continue;
            }
            // Palabra más ancha que la celda: corte duro por línea.
            if ($wordPx > $avail) {
                if ($used > 0.0) {
                    $lines++;
                }
                $lines += (int) floor($wordPx / $avail);
                $used = $wordPx - floor($wordPx / $avail) * $avail;

                continue;
            }
            $lines++;
            $used = $wordPx;
        }

        return $lines;
    }

    /** Ancho de un texto (px) con métricas Helvetica + factor del modo. */
    private static function widthPx(string $text, float $linePx, bool $bold, float $factor): float
    {
        $table = $bold ? self::BOLD : self::REGULAR;
        $units = 0.0;
        foreach (mb_str_split(mb_strtoupper($text)) as $char) {
            $units += $table[$char] ?? 750.0;
        }

        return ($units / 1000.0) * $linePx * $factor;
    }

    /** Config del perfil visual + modo; fallback a standard/conservative. */
    private static function config(string $style, string $rowMode): array
    {
        $styleKey = isset(self::STYLES[$style]) ? $style : self::STYLE_STANDARD;
        $mode = self::paramsKey($rowMode);
        $s = self::STYLES[$styleKey];

        return [
            'avail' => $s['avail'][$mode] ?? $s['avail'][self::MODE_CONSERVATIVE],
            'factor' => $s['factor'][$mode] ?? $s['factor'][self::MODE_CONSERVATIVE],
            'subject' => $s['subject'],
            'meta' => $s['meta'],
            'label' => $s['label'],
            'halfGap' => $s['halfGap'],
            'entryGap' => $s['entryGap'],
            'tail' => $s['tail'],
        ];
    }

    private static function paramsKey(string $mode): string
    {
        $mode = strtolower(trim($mode));

        return in_array($mode, [self::MODE_CONSERVATIVE, self::MODE_STRICT], true)
            ? $mode
            : self::MODE_CONSERVATIVE;
    }

    /** Métricas AFM Helvetica-Bold (/1000 del font-size). */
    private const BOLD = [
        ' ' => 278, 'A' => 722, 'B' => 722, 'C' => 722, 'D' => 722, 'E' => 667,
        'F' => 611, 'G' => 778, 'H' => 722, 'I' => 278, 'J' => 556, 'K' => 722,
        'L' => 611, 'M' => 833, 'N' => 722, 'O' => 778, 'P' => 667, 'Q' => 778,
        'R' => 722, 'S' => 667, 'T' => 611, 'U' => 722, 'V' => 667, 'W' => 944,
        'X' => 667, 'Y' => 667, 'Z' => 611,
        '0' => 556, '1' => 556, '2' => 556, '3' => 556, '4' => 556,
        '5' => 556, '6' => 556, '7' => 556, '8' => 556, '9' => 556,
        'Á' => 722, 'É' => 667, 'Í' => 278, 'Ó' => 778, 'Ú' => 722, 'Ñ' => 722,
        'Ü' => 722, '-' => 333, '.' => 278, ',' => 278, '´' => 333, '/' => 278,
        ':' => 333, '–' => 556, '(' => 333, ')' => 333,
    ];

    /** Métricas AFM Helvetica (/1000). */
    private const REGULAR = [
        ' ' => 278, 'A' => 667, 'B' => 667, 'C' => 722, 'D' => 722, 'E' => 667,
        'F' => 611, 'G' => 778, 'H' => 722, 'I' => 278, 'J' => 500, 'K' => 667,
        'L' => 556, 'M' => 833, 'N' => 722, 'O' => 778, 'P' => 667, 'Q' => 778,
        'R' => 722, 'S' => 667, 'T' => 611, 'U' => 722, 'V' => 667, 'W' => 944,
        'X' => 667, 'Y' => 667, 'Z' => 611,
        '0' => 556, '1' => 556, '2' => 556, '3' => 556, '4' => 556,
        '5' => 556, '6' => 556, '7' => 556, '8' => 556, '9' => 556,
        'Á' => 667, 'É' => 667, 'Í' => 278, 'Ó' => 778, 'Ú' => 722, 'Ñ' => 722,
        'Ü' => 722, '-' => 333, '.' => 278, ',' => 278, '´' => 333, '/' => 278,
        ':' => 278, '–' => 556, '(' => 333, ')' => 333,
    ];
}
