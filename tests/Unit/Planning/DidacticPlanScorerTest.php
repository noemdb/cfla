<?php

namespace Tests\Unit\Planning;

use App\Models\app\Academy\Achievement;
use App\Models\app\Academy\Activity;
use App\Services\Planning\DidacticPlanPeriodResolver;
use App\Services\Planning\DidacticPlanScorer;
use Tests\TestCase;

class DidacticPlanScorerTest extends TestCase
{
    private DidacticPlanScorer $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scorer = new DidacticPlanScorer(new DidacticPlanPeriodResolver);
    }

    public function test_complete_activity_scores_one_hundred(): void
    {
        $activity = $this->activity([
            'topic' => 'Alimentación saludable y cultura',
            'teaching' => 'INICIO: A. DESARROLLO: B. CIERRE: C.',
            'description' => 'Se evalúa con lista de cotejo.',
            'observations' => 'ODS 3: Salud y bienestar.',
            'learning' => 'Reconoce alimentos saludables.',
            'finicial' => '2026-11-17',
            'ffinal' => '2026-11-17',
        ]);

        $activity->setRelation('achievements', collect([
            new Achievement(['name' => 'Identifica alimentos saludables']),
        ]));

        $result = $this->scorer->score($activity, '2026-11-17', '2026-11-21');

        $this->assertSame(100, $result['score']);
        $this->assertSame('completa', $result['level']);
        $this->assertTrue($result['complete']);
        $this->assertFalse($result['partial']);
        $this->assertTrue($result['theme_affinity']);
        $this->assertTrue($result['within_week']);
    }

    public function test_partial_activity_is_penalized(): void
    {
        $activity = $this->activity([
            'topic' => 'Matemáticas',
            'teaching' => 'INICIO: A.',
            'finicial' => '2026-11-24',
        ]);

        $result = $this->scorer->score($activity, '2026-11-17', '2026-11-21');

        $this->assertSame(0, $result['score']);
        $this->assertSame('debil', $result['level']);
        $this->assertTrue($result['partial']);
        $this->assertFalse($result['theme_affinity']);
        $this->assertFalse($result['within_week']);
    }

    public function test_levels_follow_thresholds(): void
    {
        $this->assertSame('completa', $this->scorer->level(70));
        $this->assertSame('apta', $this->scorer->level(40));
        $this->assertSame('parcial', $this->scorer->level(20));
        $this->assertSame('debil', $this->scorer->level(19));
    }

    public function test_extracts_multiple_ods_codes_without_duplicates(): void
    {
        $this->assertSame(['3', '16'], $this->scorer->extractOds('ODS 3- ODS 16 y ODS 3'));
        $this->assertSame([], $this->scorer->extractOds('Sin objetivos de desarrollo'));
    }

    public function test_preselection_requires_theme_week_and_threshold_and_caps_at_ten(): void
    {
        $activities = [];

        foreach (range(1, 12) as $index) {
            $activity = $this->activity([
                'topic' => "Alimentación saludable {$index}",
                'teaching' => 'INICIO: A. DESARROLLO: B. CIERRE: C.',
                'description' => 'Lista de cotejo.',
                'observations' => 'ODS 3.',
                'learning' => 'Aprende.',
                'finicial' => '2026-11-17',
                'ffinal' => '2026-11-17',
            ]);
            $activity->id = $index;
            $activities[] = $activity;
        }

        $outsideWeek = $this->activity([
            'topic' => 'Alimentación fuera de semana',
            'teaching' => 'INICIO: A. DESARROLLO: B. CIERRE: C.',
            'description' => 'Lista de cotejo.',
            'observations' => 'ODS 3.',
            'learning' => 'Aprende.',
            'finicial' => '2026-11-24',
            'ffinal' => '2026-11-24',
        ]);
        $outsideWeek->id = 99;
        $activities[] = $outsideWeek;

        $notThemed = $this->activity([
            'topic' => 'Fracciones equivalentes',
            'teaching' => 'INICIO: A. DESARROLLO: B. CIERRE: C.',
            'description' => 'Lista de cotejo.',
            'observations' => 'ODS 4.',
            'learning' => 'Aprende.',
            'finicial' => '2026-11-17',
            'ffinal' => '2026-11-17',
        ]);
        $notThemed->id = 100;
        $activities[] = $notThemed;

        $selected = $this->scorer->preselectForAxis($activities, '2026-11-17', '2026-11-21');

        $this->assertCount(10, $selected);
        $this->assertNotContains(99, $selected);
        $this->assertNotContains(100, $selected);
        $this->assertSame(range(1, 10), $selected);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function activity(array $attributes): Activity
    {
        return new Activity($attributes);
    }
}
