<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * SPEC-TIMETABLE-001g — Importación masiva de lecciones desde CSV/Excel.
 *
 * Cada fila define una lección a programar (envuelve una Pevaluacion). La
 * primera fila se usa como cabecera (WithHeadingRow) y las columnas se
 * normalizan a snake_case. El procesado/validación lo hace el wizard
 * (TimetableWizard::importLessons), que resuelve la Pevaluacion, el turno y
 * los bloques; aquí solo se extraen las filas del archivo.
 */
class TimetableLessonsImport implements ToCollection, WithHeadingRow
{
    /** @var array<int, array<string, mixed>> */
    public array $rows = [];

    public function collection(Collection $rows): void
    {
        $this->rows = $rows->map(fn (Collection $row) => $row->toArray())->values()->all();
    }
}
