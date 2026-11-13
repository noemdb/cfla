<?php

namespace Tests\Unit\Planning;

use App\Models\app\Academy\Achievement;
use App\Models\app\Academy\Activity;
use App\Services\Planning\DidacticPlanBuilder;
use App\Services\Planning\DidacticPlanPeriodResolver;
use App\Services\Planning\DidacticPlanScorer;
use Tests\TestCase;

class DidacticPlanBuilderTest extends TestCase
{
    private DidacticPlanBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $resolver = new DidacticPlanPeriodResolver;
        $scorer = new DidacticPlanScorer($resolver);
        $this->builder = new DidacticPlanBuilder($resolver, $scorer);
    }

    public function test_maps_activity_fields_to_session(): void
    {
        $activity = $this->activity([
            'topic' => 'Soy parte de una cultura',
            'thematic' => 'Alimentación e identidad',
            'learning' => 'Reconoce platos típicos.',
            'teaching' => 'INICIO: Rincón de sabores. DESARROLLO: Moldeamos platos. CIERRE: Mesa venezolana.',
            'references' => 'Láminas y masa.',
            'description' => 'Se evalúa con lista de cotejo.',
            'observations' => 'ODS 4. ODS 16.',
            'finicial' => '2026-11-17',
            'ffinal' => '2026-11-17',
        ]);

        $activity->id = 4501;

        $achievement = new Achievement([
            'name' => 'Identifica dos tradiciones',
            'weighting' => 4,
            'status_quantitative_weighting' => true,
        ]);
        $achievement->id = 7;

        $activity->setRelation('achievements', collect([$achievement]));

        $session = $this->builder->buildSession($activity, '2026-11-17', '2026-11-21');

        $this->assertSame('2026-11-17', $session['date']);
        $this->assertSame(2, $session['day_index']);
        $this->assertSame('Soy parte de una cultura', $session['title']);
        $this->assertSame('Reconoce platos típicos.', $session['objective']);
        $this->assertSame('Rincón de sabores.', $session['start']);
        $this->assertSame('Moldeamos platos.', $session['development']);
        $this->assertSame('Mesa venezolana.', $session['close']);
        $this->assertSame('Láminas y masa.', $session['resources']);
        $this->assertSame('Lista de cotejo', $session['evaluation_instrument']);
        $this->assertSame(['4', '16'], $session['ods']);
        $this->assertSame(4501, $session['source_activity_id']);
        $this->assertCount(1, $session['indicators']);
        $this->assertSame('Identifica dos tradiciones', $session['indicators'][0]['name']);
        $this->assertTrue($session['indicators'][0]['is_quantitative']);
        $this->assertSame([], $session['warnings']);
    }

    public function test_warns_when_indicators_are_missing(): void
    {
        $activity = $this->activity([
            'topic' => 'Alimentación saludable',
            'teaching' => 'INICIO: A. DESARROLLO: B. CIERRE: C.',
            'finicial' => '2026-11-17',
            'ffinal' => '2026-11-17',
        ]);

        $session = $this->builder->buildSession($activity, '2026-11-17', '2026-11-21');

        $this->assertSame([], $session['indicators']);
        $this->assertContains(
            'La actividad no tiene indicadores; agrega al menos uno o justifica la ausencia.',
            $session['warnings']
        );
    }

    public function test_partial_sequence_uses_available_text_and_warns(): void
    {
        $activity = $this->activity([
            'topic' => 'Continuidad de alimentación',
            'teaching' => 'INICIO: Recordamos. DESARROLLO: Dibujamos la lonchera.',
            'finicial' => '2026-11-20',
            'ffinal' => '2026-11-20',
        ]);

        $session = $this->builder->buildSession($activity, '2026-11-17', '2026-11-21');

        $this->assertSame('Recordamos.', $session['start']);
        $this->assertSame('Dibujamos la lonchera.', $session['development']);
        $this->assertNull($session['close']);
        $this->assertContains('Secuencia parcial: falta CIERRE.', $session['warnings']);
    }

    public function test_warns_when_activity_is_outside_week_or_without_date(): void
    {
        $outside = $this->activity([
            'topic' => 'Gastronomía navideña',
            'teaching' => 'INICIO: A. DESARROLLO: B. CIERRE: C.',
            'finicial' => '2026-11-24',
            'ffinal' => '2026-11-24',
        ]);

        $session = $this->builder->buildSession($outside, '2026-11-17', '2026-11-21');

        $this->assertContains(
            'La fecha de la actividad está fuera de la semana elegida.',
            $session['warnings']
        );

        $withoutDate = $this->activity([
            'topic' => 'Sin fecha',
            'teaching' => 'INICIO: A. DESARROLLO: B. CIERRE: C.',
        ]);

        $session = $this->builder->buildSession($withoutDate, '2026-11-17', '2026-11-21');

        $this->assertNull($session['date']);
        $this->assertNull($session['day_index']);
        $this->assertContains(
            'La actividad no tiene una fecha válida; asigna una fecha dentro de la semana.',
            $session['warnings']
        );
    }

    public function test_detects_evaluation_instruments(): void
    {
        $this->assertSame('Lista de cotejo', $this->builder->extractInstrument('Evaluar con lista de cotejo.'));
        $this->assertSame('Escala de estimación', $this->builder->extractInstrument('Usar una escala de estimacion.'));
        $this->assertSame('Rúbrica', $this->builder->extractInstrument('Se aplicará una rúbrica.'));
        $this->assertNull($this->builder->extractInstrument('Actividad sin instrumento explícito.'));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function activity(array $attributes): Activity
    {
        return new Activity($attributes);
    }
}
