<?php

namespace App\Http\Controllers\Timetable\Concerns;

use App\Models\app\Academy\Pevaluacion;

/**
 * Métricas y formateo compartidos por los generadores de horarios (PDF / XLS):
 * peso de media franja (ADR-TT-017), grupo estable, líneas half-aware y alturas
 * de celda de Excel, con el mismo criterio que timetable.partials.half-cell.
 */
trait MeasuresTimetableBlocks
{
    /**
     * Nombre del grupo estable de la Pevaluación, solo cuando está definido.
     * Si el id existe pero no la relación, se usa un identificador legible.
     */
    protected function grupoEstableName(?Pevaluacion $pev): ?string
    {
        if (! $pev || ! $pev->grupo_estable_id) {
            return null;
        }

        return (string) ($pev->grupoEstable?->name ?? 'G'.$pev->grupo_estable_id);
    }

    /**
     * ADR-TT-017: una media franja pesa 0.5 bloque; un bloque completo, 1.
     */
    protected function halfBlockWeight(mixed $half): float
    {
        return $half === null || $half === '' ? 1.0 : 0.5;
    }

    /**
     * Conserva el tipo entero cuando el total no tiene fracción (compatibilidad
     * con reportes y pruebas existentes) y devuelve float para las medias.
     */
    protected function normalizeBlockValue(float $value): int|float
    {
        $rounded = round($value, 2);

        return floor($rounded) == $rounded ? (int) $rounded : $rounded;
    }

    /**
     * Líneas de una celda XLS con el mismo criterio que
     * timetable.partials.half-cell: primero los bloques completos y luego las
     * bandas ½ sup. / ½ inf. (con «—» cuando una banda está vacía).
     *
     * @param  iterable<int, mixed>  $entries
     * @return list<string>
     */
    protected function xlsHalfAwareLines(iterable $entries, callable $line): array
    {
        $full = [];
        $sup = [];
        $inf = [];

        foreach ($entries as $entry) {
            $half = data_get($entry, 'half');

            if ($half === null) {
                $full[] = (string) $line($entry);
            } elseif ((int) $half === 0) {
                $sup[] = (string) $line($entry);
            } else {
                $inf[] = (string) $line($entry);
            }
        }

        if ($sup === [] && $inf === []) {
            return $full;
        }

        return array_merge(
            $full,
            ['½ sup.'],
            $sup !== [] ? $sup : ['—'],
            ['½ inf.'],
            $inf !== [] ? $inf : ['—'],
        );
    }

    /**
     * Altura necesaria para que Excel no recorte las líneas de una celda.
     *
     * @param  list<string>  $lines
     */
    protected function xlsLinesHeight(array $lines, float $base = 28.0): float
    {
        $lineCount = 0;

        foreach ($lines as $line) {
            $lineCount += substr_count((string) $line, "\n") + 1;
        }

        return min(120.0, max($base, 9.0 * max(1, $lineCount)));
    }
}
