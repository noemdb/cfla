<?php

namespace Tests\Feature\Coordinacion;

use App\Livewire\Coordinacion\ActivityList;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
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
 * UX de la barra de filtros de /app/coordinacion/activities:
 * búsqueda global, filtros de lecciones y limpieza de filtros.
 */
class ActivityListUxTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array<string, mixed>
     */
    private function makeCoordinatorContext(): array
    {
        $coordinator = User::factory()->coordinacion()->create(['is_active' => 'enable']);
        $peducativo = Peducativo::factory()->create([
            'manager_id' => $coordinator->id,
            'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create([
            'peducativo_id' => $peducativo->id,
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
        $lapso = Lapso::factory()->create();
        $profesor = Profesor::factory()->create();

        return compact('coordinator', 'peducativo', 'pestudio', 'grado', 'seccion', 'lapso', 'profesor');
    }

    /**
     * @return array<string, mixed>
     */
    private function makeActivity(array $ctx, string $asignaturaName, string $topic): array
    {
        $asignatura = Asignatura::factory()->create([
            'pestudio_id' => $ctx['pestudio']->id,
            'name' => $asignaturaName,
        ]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $ctx['pestudio']->id,
            'grado_id' => $ctx['grado']->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => true,
        ]);
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $ctx['profesor']->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $ctx['seccion']->id,
            'lapso_id' => $ctx['lapso']->id,
        ]);
        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic' => $topic,
        ]);

        return compact('asignatura', 'pensum', 'pevaluacion', 'activity');
    }

    public function test_search_filters_by_asignatura_and_result_summary_is_rendered(): void
    {
        $ctx = $this->makeCoordinatorContext();

        $targetAsignatura = 'ASIG-BUSCADA-'.uniqid();
        $otherAsignatura = 'ASIG-OTRA-'.uniqid();
        $targetTopic = 'TEMA-BUSCADO-'.uniqid();
        $otherTopic = 'TEMA-OTRO-'.uniqid();

        $this->makeActivity($ctx, $targetAsignatura, $targetTopic);
        $this->makeActivity($ctx, $otherAsignatura, $otherTopic);

        Livewire::actingAs($ctx['coordinator'])
            ->test(ActivityList::class)
            ->set('lapsoId', $ctx['lapso']->id)
            ->set('search', $targetAsignatura)
            ->assertOk()
            ->assertSee($targetTopic)
            ->assertDontSee($otherTopic)
            ->assertSee('Mostrando')
            ->assertSee('Limpiar filtros');
    }

    public function test_filters_only_offer_lessons_and_reset_clears_state(): void
    {
        $ctx = $this->makeCoordinatorContext();

        $this->makeActivity($ctx, 'ASIG-UNO-'.uniqid(), 'TEMA-UNO-'.uniqid());

        Livewire::actingAs($ctx['coordinator'])
            ->test(ActivityList::class)
            ->set('lapsoId', $ctx['lapso']->id)
            ->assertSee('Con lecciones')
            ->assertSee('Sin lecciones')
            ->assertDontSee('Con actividades')
            ->assertDontSee('Sin actividades')
            ->set('search', 'algo')
            ->set('statusActivities', 'SI_LE')
            ->set('filterStatus', 'pending')
            ->set('filterObservations', true)
            ->set('paginate', 50)
            ->call('resetFilters')
            ->assertOk()
            ->assertSet('search', '')
            ->assertSet('statusActivities', '')
            ->assertSet('filterStatus', '')
            ->assertSet('filterObservations', false)
            ->assertSet('paginate', 15);
    }
}
