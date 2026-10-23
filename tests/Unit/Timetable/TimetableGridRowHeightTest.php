<?php

namespace Tests\Unit\Timetable;

use App\Services\Timetable\TimetableGridRowHeight;
use PHPUnit\Framework\TestCase;

class TimetableGridRowHeightTest extends TestCase
{
    /** Grilla mínima de prueba: una fila lunes a viernes (sin recesos). */
    private function rows(array $days): iterable
    {
        yield ['period' => (object) ['is_break' => false], 'days' => $days];
    }

    /** @test */
    public function strict_mode_never_yields_more_conservative_height(): void
    {
        $days = array_map(fn () => [
            'assignments' => [
                [
                    'asignatura' => 'MATEMÁTICA I',
                    'grupo' => '1A',
                    'profesor' => 'GARCIA PEREZ, JUAN CARLOS',
                ],
            ],
        ], range(1, 5));

        $conservative = TimetableGridRowHeight::forGrid($this->rows($days), true, TimetableGridRowHeight::MODE_CONSERVATIVE);
        $strict = TimetableGridRowHeight::forGrid($this->rows($days), true, TimetableGridRowHeight::MODE_STRICT);

        $this->assertGreaterThanOrEqual($strict, $conservative);
        $this->assertEquals($strict, TimetableGridRowHeight::forGrid($this->rows($days), true, 'strict'));
        $this->assertEquals($conservative, TimetableGridRowHeight::forGrid($this->rows($days), true, 'CONSERVATIVE'));
    }

    /** @test */
    public function unknown_mode_or_missing_value_falls_back_to_conservative(): void
    {
        $days = array_map(fn () => [
            'assignments' => [
                ['asignatura' => 'BIOLOGÍA'],
            ],
        ], range(1, 5));

        $base = TimetableGridRowHeight::forGrid($this->rows($days), false, TimetableGridRowHeight::MODE_CONSERVATIVE);

        $this->assertSame($base, TimetableGridRowHeight::forGrid($this->rows($days), false, 'row-height-inexistente'));
        $this->assertSame($base, TimetableGridRowHeight::forGrid($this->rows($days), false, ''));
    }

    /** @test */
    public function empty_grid_respects_minimum_height_of_24px(): void
    {
        $this->assertSame(
            24,
            TimetableGridRowHeight::forGrid($this->rows(array_map(
                fn () => ['assignments' => []],
                range(1, 5)
            )), true)
        );
    }

    /** @test */
    public function break_rows_do_not_count_toward_height(): void
    {
        // Celda cargada SOLO en el receso: el máximo debe salir del resto (vacío).
        $rows = [
            ['period' => (object) ['is_break' => true], 'days' => array_map(
                fn () => ['assignments' => [['asignatura' => 'MUY LARGO '.str_repeat('X', 60)]]],
                range(1, 5)
            )],
            ['period' => (object) ['is_break' => false], 'days' => array_map(
                fn () => ['assignments' => []],
                range(1, 5)
            )],
        ];

        $this->assertSame(24, TimetableGridRowHeight::forGrid($rows, true));
    }

    /** @test */
    public function hidden_teachers_shrink_the_height(): void
    {
        $days = array_map(fn () => [
            'assignments' => [
                [
                    'asignatura' => 'LENGUA Y LITERATURA',
                    'profesor' => 'HERNANDEZ RODRIGUEZ, MARIA FERNANDA',
                ],
            ],
        ], range(1, 5));

        $withTeachers = TimetableGridRowHeight::forGrid($this->rows($days), true, TimetableGridRowHeight::MODE_STRICT);
        $withoutTeachers = TimetableGridRowHeight::forGrid($this->rows($days), false, TimetableGridRowHeight::MODE_STRICT);

        $this->assertGreaterThan($withoutTeachers, $withTeachers);
    }

    /**
     * @test
     * El docblock del modo strict: el mínimo no puede salir del conteo de
     * líneas del greedy wrap (factor 1.0, ancho real 161px).
     */
    public function one_subject_line_is_single_line_in_both_modes(): void
    {
        $days = array_map(fn () => [
            'assignments' => [
                ['asignatura' => 'INGLÉS'],
            ],
        ], range(1, 5));

        $expected = 24; // 1 línea de 14px + 6px de padding/borde → 20px, mínimo 24
        $this->assertSame($expected, TimetableGridRowHeight::forGrid($this->rows($days), false, TimetableGridRowHeight::MODE_STRICT));
        $this->assertSame($expected, TimetableGridRowHeight::forGrid($this->rows($days), false, TimetableGridRowHeight::MODE_CONSERVATIVE));
    }

    /**
     * @test
     * El perfil `preview-pestudio` usa las métricas de su plantilla (subject
     * 8.8pt, tail 5px): con solo asignatura el alto es menor que el `standard`
     * (10.5pt + tail 6px).
     */
    public function preview_pestudio_style_uses_its_own_metrics(): void
    {
        // Suficientemente largo para envolver a 2+ líneas en ambos perfiles.
        $days = array_map(fn () => [
            'assignments' => [
                ['asignatura' => 'MATEMÁTICA FINANCIERA APLICADA A LA GESTIÓN ESCOLAR'],
            ],
        ], range(1, 5));

        $standard = TimetableGridRowHeight::forGrid($this->rows($days), false, TimetableGridRowHeight::MODE_STRICT, TimetableGridRowHeight::STYLE_STANDARD);
        $pestudio = TimetableGridRowHeight::forGrid($this->rows($days), false, TimetableGridRowHeight::MODE_STRICT, TimetableGridRowHeight::STYLE_PREVIEW_PESTUDIO);

        $this->assertGreaterThanOrEqual($pestudio, $standard);
    }

    /** @test — perfil desconocido → standard, modo desconocido → conservative. */
    public function unknown_style_falls_back_to_standard(): void
    {
        $days = array_map(fn () => [
            'assignments' => [
                ['asignatura' => 'FÍSICA', 'grupo' => '3B'],
            ],
        ], range(1, 5));

        $base = TimetableGridRowHeight::forGrid($this->rows($days), false, TimetableGridRowHeight::MODE_CONSERVATIVE, TimetableGridRowHeight::STYLE_STANDARD);

        $this->assertSame($base, TimetableGridRowHeight::forGrid($this->rows($days), false, 'nosotros', '_template-fantasma'));
    }
}
