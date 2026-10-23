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
 * Supuestos (letter horizontal, márgenes de 0.7cm, 6 columnas iguales con
 * `table-layout:fixed`, padding 1px/2px y borde 2px con border-collapse): el
 * ancho útil por celda es ~161px. El corte de líneas usa las métricas AFM de
 * Helvetica con el mismo algoritmo greedy de DomPDF y un factor de corrección
 * calibrado con el render real. Prefiere pasarse por arriba (filas algo más
 * altas que lo estrictamente necesario) antes que quedarse corto (rompería la
 * uniformidad).
 */
class TimetableGridRowHeight
{
    /** Ancho útil (px CSS) del contenido de cada columna (6 columnas iguales:
     * letter horizontal 1056px, márgenes 0.7cm, padding 2px y borde 2px).
     * Tomado conservador (por debajo del real ~161) para que la estimación
     * jamás quede por debajo del corte real de DomPDF. */
    private const DAY_AVAIL_PX = 152.0;

    /** Ancho de línea para los rótulos ½ sup./½ inf. y sus separadores. */
    private const HALF_EXTRA_PX = 26.6;

    /** Ancho de línea de un texto con line-height:1 (px = font-size). */
    private const SUBJECT_PX = 14.0; // 10.5pt

    private const META_PX = 8.7;     // 6.5pt (grupo/docente)

    private const BASE_PX = 12.7;    // 9.5pt (rótulos de media franja, 0.85em ≈ 10.8)

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

    /**
     * Altura uniforme (px) para todas las filas de una grilla = su celda más
     * cargada (+ padding/borde). Los recesos no aportan texto.
     *
     * @param  iterable  $rows  filas de `$shiftGrid['rows']` (period + days)
     */
    public static function forGrid(iterable $rows, bool $showTeachers): int
    {
        $maxPx = 0.0;
        foreach ($rows as $row) {
            if ($row['period']->is_break) {
                continue;
            }
            foreach ($row['days'] as $day) {
                $maxPx = max($maxPx, self::cellPx($day['assignments'] ?? [], $showTeachers));
            }
        }

        return max(24, (int) ceil($maxPx + 6.0));
    }

    /** Altura (px) del contenido de una celda, incluyendo medias franjas. */
    private static function cellPx(iterable $slots, bool $showTeachers): float
    {
        $px = 0.0;
        $sup = [];
        $inf = [];

        foreach ($slots as $slot) {
            $half = data_get($slot, 'half');
            if ($half === null) {
                $px += self::entryPx($slot, $showTeachers);

                continue;
            }
            if ((int) $half === 0) {
                $sup[] = $slot;
            } else {
                $inf[] = $slot;
            }
        }

        if ($sup !== [] || $inf !== []) {
            $px += 2 * (self::BASE_PX * 0.85) + 5.0; // rótulos ½ sup./½ inf. + separador
            foreach ($sup as $slot) {
                $px += self::entryPx($slot, $showTeachers);
            }
            foreach ($inf as $slot) {
                $px += self::entryPx($slot, $showTeachers);
            }
        }

        return $px;
    }

    /** Altura (px) de una asignatura (+ grupo y docente si corresponden). */
    private static function entryPx($cell, bool $showTeachers): float
    {
        $px = self::linesPx((string) ($cell['asignatura'] ?? '?'), self::SUBJECT_PX, true);
        if (! empty($cell['grupo'])) {
            $px += self::linesPx((string) $cell['grupo'], self::META_PX, true);
        }
        if ($showTeachers && ! empty($cell['profesor'])) {
            $px += self::linesPx((string) $cell['profesor'], self::META_PX, false);
        }

        return $px;
    }

    /** Líneas que ocupará un texto × su alto de línea (line-height:1). */
    private static function linesPx(string $text, float $linePx, bool $bold): float
    {
        return self::wrapLines($text, self::DAY_AVAIL_PX, $linePx, $bold) * $linePx;
    }

    /** Greedy word-wrap idéntico en espíritu al de DomPDF. */
    private static function wrapLines(string $text, float $avail, float $linePx, bool $bold): int
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $text));
        if ($text === '') {
            return 0;
        }

        $lines = 1;
        $used = 0.0;
        foreach (preg_split('/\s+/', $text) as $word) {
            $wordPx = self::widthPx($word, $linePx, $bold);
            $spacePx = $used > 0.0 ? self::widthPx(' ', $linePx, $bold) : 0.0;

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

    /** Ancho de un texto (px) con métricas Helvetica calibradas. */
    private static function widthPx(string $text, float $linePx, bool $bold): float
    {
        $table = $bold ? self::BOLD : self::REGULAR;
        $units = 0.0;
        foreach (mb_str_split(mb_strtoupper($text)) as $char) {
            $units += $table[$char] ?? 750.0;
        }

        return ($units / 1000.0) * $linePx * 1.05;
    }
}
