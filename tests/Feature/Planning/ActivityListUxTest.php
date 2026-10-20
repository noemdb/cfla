<?php

namespace Tests\Feature\Planning;

use App\Livewire\Planning\Activities\IndexComponent as ActivitiesIndex;
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
 * UX de la barra de filtros de /app/planning/activities:
 * búsqueda global, contador de resultados y limpieza de filtros.
 */
class ActivityListUxTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array<string, mixed>
     */
    private function makePevaluacion(Lapso $lapso, string $asignaturaName, string $profesorName): array
    {
        $pestudio = Pestudio::factory()->create([
            'peducativo_id' => Peducativo::factory(),
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
        $asignatura = Asignatura::factory()->create([
            'pestudio_id' => $pestudio->id,
            'name' => $asignaturaName,
        ]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => true,
        ]);
        $profesor = Profesor::factory()->create([
            'name' => $profesorName,
        ]);
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        return compact('asignatura', 'pensum', 'profesor', 'pevaluacion');
    }

    public function test_search_filters_by_asignatura_and_shows_result_summary(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();

        $target = 'ASIG-BUSCADA-'.uniqid();
        $other = 'ASIG-OTRA-'.uniqid();

        $this->makePevaluacion($lapso, $target, 'PROF-UNO-'.uniqid());
        $this->makePevaluacion($lapso, $other, 'PROF-DOS-'.uniqid());

        Livewire::actingAs($planner)
            ->test(ActivitiesIndex::class)
            ->set('lapso_id', $lapso->id)
            ->set('search', $target)
            ->assertOk()
            ->assertSee($target)
            ->assertDontSee($other)
            ->assertSee('Mostrando')
            ->assertSee('Limpiar filtros');
    }

    public function test_reset_filters_clears_search_and_filter_state(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();

        $this->makePevaluacion($lapso, 'ASIG-UNO-'.uniqid(), 'PROF-UNO-'.uniqid());

        Livewire::actingAs($planner)
            ->test(ActivitiesIndex::class)
            ->set('lapso_id', $lapso->id)
            ->set('search', 'algo')
            ->set('status_activities', 'SI')
            ->set('filter_status', 'pending')
            ->set('filter_observations', true)
            ->set('paginate', 50)
            ->call('resetFilters')
            ->assertOk()
            ->assertSet('search', '')
            ->assertSet('status_activities', '')
            ->assertSet('filter_status', '')
            ->assertSet('filter_observations', false)
            ->assertSet('paginate', 10);
    }
}
