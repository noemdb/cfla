<?php

namespace Tests\Feature\Planning;

use App\Models\app\Academy\DidacticPlan;
use App\Models\app\Academy\DidacticPlanIndicator;
use App\Models\app\Academy\DidacticPlanSource;
use App\Models\app\Academy\Pevaluacion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DidacticPlanSchemaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_plan_relations_and_status_helpers_work(): void
    {
        $user = User::where('is_planner', true)->first();

        if (! $user) {
            $this->markTestSkipped('No hay usuario de planificación en la base de pruebas.');
        }

        $pevaluacion = Pevaluacion::with(['pensum', 'seccion'])
            ->whereHas('pensum.pestudio', fn ($q) => $q->where('planning_module', true))
            ->firstOrFail();

        $plan = DidacticPlan::create([
            'pestudio_id' => $pevaluacion->pensum->pestudio_id,
            'grado_id' => $pevaluacion->seccion->grado_id,
            'seccion_id' => $pevaluacion->seccion_id,
            'lapso_id' => $pevaluacion->lapso_id,
            'period_start' => '2026-11-17',
            'period_end' => '2026-11-21',
            'filter_snapshot' => ['seccion_id' => $pevaluacion->seccion_id],
            'filter_hash' => str_repeat('a', 64),
            'universe_hash' => str_repeat('b', 64),
            'filtered_count' => 1,
            'selected_count' => 1,
            'created_by' => $user->id,
        ]);

        $plan->refresh();

        $this->assertSame('draft', $plan->status);
        $this->assertTrue($plan->isDraft());
        $this->assertTrue($plan->isEditable());
        $this->assertFalse($plan->isDownloadable());
        $this->assertSame('2026-11-17', $plan->period_start->toDateString());

        $session = $plan->sessions()->create([
            'date' => '2026-11-17',
            'day_index' => 2,
            'title' => 'Alimentación saludable',
            'start' => 'Inicio',
            'development' => 'Desarrollo',
            'close' => 'Cierre',
        ]);

        $indicator = DidacticPlanIndicator::create([
            'didactic_plan_id' => $plan->id,
            'didactic_plan_session_id' => $session->id,
            'name' => 'Identifica alimentos saludables',
            'origin' => 'manual',
        ]);

        $source = DidacticPlanSource::create([
            'didactic_plan_id' => $plan->id,
            'pevaluacion_id' => $pevaluacion->id,
            'included' => true,
        ]);

        $this->assertTrue($plan->fresh()->sessions->contains($session));
        $this->assertTrue($plan->fresh()->indicators->contains($indicator));
        $this->assertTrue($plan->fresh()->sources->contains($source));
    }

    public function test_approved_key_is_unique(): void
    {
        $user = User::where('is_planner', true)->first();

        if (! $user) {
            $this->markTestSkipped('No hay usuario de planificación en la base de pruebas.');
        }

        $pevaluacion = Pevaluacion::with(['pensum', 'seccion'])
            ->whereHas('pensum.pestudio', fn ($q) => $q->where('planning_module', true))
            ->firstOrFail();

        $base = [
            'pestudio_id' => $pevaluacion->pensum->pestudio_id,
            'grado_id' => $pevaluacion->seccion->grado_id,
            'seccion_id' => $pevaluacion->seccion_id,
            'lapso_id' => $pevaluacion->lapso_id,
            'period_start' => '2026-11-17',
            'period_end' => '2026-11-21',
            'filter_snapshot' => [],
            'filter_hash' => str_repeat('a', 64),
            'universe_hash' => str_repeat('b', 64),
            'created_by' => $user->id,
            'approved_key' => DidacticPlan::approvedKeyFor(
                $pevaluacion->seccion_id,
                $pevaluacion->lapso_id,
                '2026-11-17',
                '2026-11-21'
            ),
        ];

        DidacticPlan::create($base);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DidacticPlan::create($base);
    }
}
