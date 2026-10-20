<?php

namespace Tests\Feature\Leadership;

use App\Livewire\Leadership\ActivityOverview;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El listado de actividades de Jefes de Área (/app/leadership/activities)
 * solo debe mostrar pevaluaciones cuyo pensum esté activo.
 */
class ActivityActivePensumFilterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Crea un líder con visibilidad sobre un pensum concreto.
     */
    private function makeContext(): array
    {
        $leader = User::factory()->leadership()->create();
        $lapso = Lapso::factory()->create();

        $pestudio = Pestudio::factory()->create([
            'status_active' => 'true',
            'planning_module' => true,
        ]);
        $grado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'status_active' => 'true',
        ]);
        $seccion = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'status_active' => 'true',
        ]);

        $area = AreaConocimiento::create([
            'leader_id' => $leader->id,
            'pestudio_id' => $pestudio->id,
            'name' => 'ÁREA LIDERADA '.uniqid(),
            'code' => 'AL'.random_int(100, 999),
        ]);

        $profesor = Profesor::factory()->create();

        return compact('leader', 'lapso', 'pestudio', 'grado', 'seccion', 'area', 'profesor');
    }

    /**
     * @return array<string, mixed>
     */
    private function makePevaluacion(array $ctx, ?bool $pensumActive, string $asignaturaName, ?Grado $grado = null): array
    {
        $grado = $grado ?? $ctx['grado'];
        $seccion = $grado->is($ctx['grado'])
            ? $ctx['seccion']
            : Seccion::factory()->create([
                'grado_id' => $grado->id,
                'status_active' => 'true',
            ]);

        $asignatura = Asignatura::factory()->create([
            'pestudio_id' => $ctx['pestudio']->id,
            'name' => $asignaturaName,
        ]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $ctx['pestudio']->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => $pensumActive,
        ]);
        CampoConocimiento::create([
            'area_conocimiento_id' => $ctx['area']->id,
            'asignatura_id' => $asignatura->id,
            'pensum_id' => $pensum->id,
        ]);
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $ctx['profesor']->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $ctx['lapso']->id,
        ]);

        return compact('asignatura', 'pensum', 'pevaluacion');
    }

    public function test_leadership_hides_pevaluacions_with_inactive_or_null_pensum(): void
    {
        $ctx = $this->makeContext();

        $activeName = 'ASIG-ACTIVA-'.uniqid();
        $inactiveName = 'ASIG-INACTIVA-'.uniqid();
        $nullName = 'ASIG-NULA-'.uniqid();

        $this->makePevaluacion($ctx, true, $activeName);
        $this->makePevaluacion($ctx, false, $inactiveName);
        $this->makePevaluacion($ctx, null, $nullName);

        Livewire::actingAs($ctx['leader'])
            ->test(ActivityOverview::class)
            ->set('lapso_id', $ctx['lapso']->id)
            ->assertOk()
            ->assertSee($activeName)
            ->assertDontSee($inactiveName)
            ->assertDontSee($nullName);
    }

    public function test_leadership_hides_pevaluacions_from_inactive_grado(): void
    {
        $ctx = $this->makeContext();

        $activeGradeName = 'ASIG-GRADO-ACTIVO-'.uniqid();
        $inactiveGradeName = 'ASIG-GRADO-INACTIVO-'.uniqid();

        $this->makePevaluacion($ctx, true, $activeGradeName);

        $inactiveGrado = Grado::factory()->create([
            'pestudio_id' => $ctx['pestudio']->id,
            'status_active' => 'false',
        ]);
        $this->makePevaluacion($ctx, true, $inactiveGradeName, $inactiveGrado);

        Livewire::actingAs($ctx['leader'])
            ->test(ActivityOverview::class)
            ->set('lapso_id', $ctx['lapso']->id)
            ->assertOk()
            ->assertSee($activeGradeName)
            ->assertDontSee($inactiveGradeName);
    }

    public function test_leadership_search_filters_by_asignatura(): void
    {
        $ctx = $this->makeContext();

        $target = 'ASIG-BUSCADA-'.uniqid();
        $other = 'ASIG-OTRA-'.uniqid();

        $this->makePevaluacion($ctx, true, $target);
        $this->makePevaluacion($ctx, true, $other);

        Livewire::actingAs($ctx['leader'])
            ->test(ActivityOverview::class)
            ->set('lapso_id', $ctx['lapso']->id)
            ->set('search', $target)
            ->assertOk()
            ->assertSee($target)
            ->assertDontSee($other)
            ->assertSee('Mostrando')
            ->assertSee('Limpiar filtros');
    }
}
