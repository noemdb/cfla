<?php

namespace Tests\Unit;

use App\Models\app\Academy\Asignatura;
use PHPUnit\Framework\TestCase;

class AsignaturaColorKeyTest extends TestCase
{
    private const PALETTE = ['sky', 'emerald', 'amber', 'indigo', 'purple', 'orange', 'rose', 'teal'];

    /**
     * Nombres reales del plan de estudios → clave de paleta esperada.
     * Especificación D2: mates=sky, lengua=emerald, ciencias=amber.
     */
    public function test_real_subject_names_map_to_expected_keys(): void
    {
        $cases = [
            'MATEMÁTICAS' => 'sky',
            'LENGUA' => 'emerald',
            'CASTELLANO' => 'emerald',
            'CIENCIAS NATURALES' => 'amber',
            'INGLES Y OTRAS LENGUAS EXTRANJERAS' => 'indigo',
            'EDUCACIÓN FÍSICA' => 'orange',
            'EDUCACIÓN ESTÉTICA' => 'purple',
            'ÁREA COMPLEMENTARIA FORMACIÓN HUMANO CRISTIANA' => 'rose',
        ];

        foreach ($cases as $name => $expected) {
            $this->assertSame($expected, Asignatura::colorKey($name), "colorKey('{$name}') debe ser {$expected}");
        }
    }

    /**
     * El mapeo es insensible a mayúsculas y acentos: "matemáticas"
     * equivale a "MATEMÁTICAS" y pinta el mismo color.
     */
    public function test_mapping_is_case_and_accent_insensitive(): void
    {
        $this->assertSame(Asignatura::colorKey('MATEMÁTICAS'), Asignatura::colorKey('matemáticas'));
        $this->assertSame('sky', Asignatura::colorKey('matemáticas'));
        $this->assertSame(Asignatura::colorKey('EDUCACIÓN FÍSICA'), Asignatura::colorKey('educacion fisica'));
        $this->assertSame('orange', Asignatura::colorKey('educacion fisica'));
    }

    /**
     * Nombre ausente/vacío → slate (neutro), nunca una clave de color.
     */
    public function test_null_and_empty_name_fall_back_to_slate(): void
    {
        $this->assertSame('slate', Asignatura::colorKey(null));
        $this->assertSame('slate', Asignatura::colorKey(''));
        $this->assertSame('slate', Asignatura::colorKey('   '));
    }

    /**
     * Asignaturas no conocidas por el mapa semántico obtienen un color
     * determinista (misma entrada → misma clave, siempre dentro de la paleta).
     */
    public function test_unknown_subject_is_deterministic_and_within_palette(): void
    {
        $name = 'GEOGRAFÍA DE VENEZUELA';
        $first = Asignatura::colorKey($name);
        $second = Asignatura::colorKey($name);

        $this->assertSame($first, $second, 'La misma asignatura debe pintar siempre el mismo color');
        $this->assertContains($first, self::PALETTE, "colorKey('{$name}') debe ser una clave de paleta");
    }
}
