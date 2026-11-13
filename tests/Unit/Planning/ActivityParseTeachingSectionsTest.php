<?php

namespace Tests\Unit\Planning;

use App\Models\app\Academy\Activity;
use Tests\TestCase;

class ActivityParseTeachingSectionsTest extends TestCase
{
    public function test_parses_complete_sequence(): void
    {
        $activity = $this->activity(
            'INICIO: Abrimos la clase. DESARROLLO: Clasificamos alimentos. CIERRE: Reflexionamos.'
        );

        $parsed = $activity->parseTeachingSections();

        $this->assertSame([], $parsed['missing']);
        $this->assertSame('Abrimos la clase.', $parsed['sections']['INICIO']);
        $this->assertSame('Clasificamos alimentos.', $parsed['sections']['DESARROLLO']);
        $this->assertSame('Reflexionamos.', $parsed['sections']['CIERRE']);
    }

    public function test_parses_partial_sequence_and_reports_missing_section(): void
    {
        $activity = $this->activity('INICIO: Abrimos. DESARROLLO: Trabajamos.');

        $parsed = $activity->parseTeachingSections();

        $this->assertSame(['CIERRE'], $parsed['missing']);
        $this->assertArrayNotHasKey('CIERRE', $parsed['sections']);
        $this->assertSame([], $activity->getTeachingSections());
        $this->assertFalse($activity->hasTeachingStructure());
    }

    public function test_empty_teaching_returns_all_sections_missing(): void
    {
        $parsed = $this->activity('')->parseTeachingSections();

        $this->assertSame([], $parsed['sections']);
        $this->assertSame(['INICIO', 'DESARROLLO', 'CIERRE'], $parsed['missing']);
    }

    public function test_markers_in_unexpected_order_are_ordered_by_position(): void
    {
        $activity = $this->activity('CIERRE: Cierre real. INICIO: Inicio real. DESARROLLO: Desarrollo real.');

        $parsed = $activity->parseTeachingSections();

        $this->assertSame([], $parsed['missing']);
        $this->assertSame('Cierre real.', $parsed['sections']['CIERRE']);
        $this->assertSame('Inicio real.', $parsed['sections']['INICIO']);
        $this->assertSame('Desarrollo real.', $parsed['sections']['DESARROLLO']);

        $ordered = $activity->getTeachingSections();
        $this->assertSame(['INICIO', 'DESARROLLO', 'CIERRE'], array_keys($ordered));
        $this->assertSame('Inicio real.', $ordered['INICIO']);
        $this->assertSame('Desarrollo real.', $ordered['DESARROLLO']);
        $this->assertSame('Cierre real.', $ordered['CIERRE']);
    }

    public function test_lowercase_words_are_not_treated_as_markers(): void
    {
        $activity = $this->activity(
            'El inicio de la jornada fue tranquilo. Durante el desarrollo hubo participación. Como cierre, cantamos.'
        );

        $parsed = $activity->parseTeachingSections();

        $this->assertSame([], $parsed['sections']);
        $this->assertSame(['INICIO', 'DESARROLLO', 'CIERRE'], $parsed['missing']);
    }

    private function activity(string $teaching): Activity
    {
        return new Activity(['teaching' => $teaching]);
    }
}
