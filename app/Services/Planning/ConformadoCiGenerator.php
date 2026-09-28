<?php

namespace App\Services\Planning;

use App\Models\app\Learner\Estudiant;

/**
 * Genera CI conformados para las filas sin cédula: 7 dígitos + 3 letras
 * mayúsculas (p. ej. 4829137XKQ).
 *
 * Son aleatorios no consecutivos y únicos: se verifican contra la tabla
 * `estudiants` (índice único) y contra los ya reservados en la ejecución.
 */
class ConformadoCiGenerator
{
    private const LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @param  array<string, bool>  $reserved  CIs ya generados en esta ejecución.
     */
    public function generate(array &$reserved): string
    {
        do {
            $ci = $this->candidate();
        } while (isset($reserved[$ci]) || Estudiant::where('ci_estudiant', $ci)->exists());

        $reserved[$ci] = true;

        return $ci;
    }

    public function candidate(): string
    {
        $digits = '';
        for ($i = 0; $i < 7; $i++) {
            $digits .= (string) random_int(0, 9);
        }

        $letters = '';
        for ($i = 0; $i < 3; $i++) {
            $letters .= self::LETTERS[random_int(0, 25)];
        }

        return $digits.$letters;
    }

    public static function isConformado(string $ci): bool
    {
        return preg_match('/^\d{7}[A-Z]{3}$/', $ci) === 1;
    }
}
