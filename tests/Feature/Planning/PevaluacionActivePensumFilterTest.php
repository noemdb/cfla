<?php

namespace Tests\Feature\Planning;

use App\Livewire\Planning\Activities\IndexComponent as ActivitiesIndex;
use App\Livewire\Planning\Pevaluacion\IndexComponent as PevaluacionIndex;
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
 * En Planning el estado del pensum se filtra con un select en la barra de
 * filtros. Por defecto (Todos) los usuarios de planificación deben ver todas
 * las pevaluaciones, incluyendo pensum activo, inactivo o nulo.
 *
 * Cubre:
 *  - /app/planning/activities   (Planning\Activities\IndexComponent)
 *  - /app/planning/pevaluacions (Planning\Pevaluacion\IndexComponent)
 */
class PevaluacionActivePensumFilterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Crea una cadena Pestudio → Grado → Sección → Pensum → Pevaluación
     * con el estado de pensum indicado.
     *
     * @return array<string, mixed>
     */
    private function makePevaluacion(Lapso $lapso, ?bool $pensumActive, string $asignaturaName): array
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
            'status_active' => $pensumActive,
        ]);
        $profesor = Profesor::factory()->create();
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        return compact(
            'pestudio',
            'grado',
            'seccion',
            'asignatura',
            'pensum',
            'profesor',
            'pevaluacion'
        );
    }

    public function test_planning_activities_shows_all_pensum_states_by_default(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();

        $activeName = 'ASIG-ACTIVA-'.uniqid();
        $inactiveName = 'ASIG-INACTIVA-'.uniqid();

        $this->makePevaluacion($lapso, true, $activeName);
        $this->makePevaluacion($lapso, false, $inactiveName);

        Livewire::actingAs($planner)
            ->test(ActivitiesIndex::class)
            ->set('lapso_id', $lapso->id)
            ->assertOk()
            ->assertSee($activeName)
            ->assertSee($inactiveName);
    }

    public function test_planning_pevaluacions_shows_all_pensum_states_by_default(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();

        $activeName = 'ASIG-ACTIVA-'.uniqid();
        $inactiveName = 'ASIG-INACTIVA-'.uniqid();
        $nullName = 'ASIG-NULA-'.uniqid();

        $this->makePevaluacion($lapso, true, $activeName);
        $this->makePevaluacion($lapso, false, $inactiveName);
        $this->makePevaluacion($lapso, null, $nullName);

        Livewire::actingAs($planner)
            ->test(PevaluacionIndex::class)
            ->set('filter_lapso', $lapso->id)
            ->assertOk()
            ->assertSee('Activos')
            ->assertSee('Inactivos')
            ->assertSee($activeName)
            ->assertSee($inactiveName)
            ->assertSee($nullName);
    }

    public function test_planning_pevaluacions_select_filters_by_pensum_status(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();

        $activeName = 'ASIG-ACTIVA-'.uniqid();
        $inactiveName = 'ASIG-INACTIVA-'.uniqid();
        $nullName = 'ASIG-NULA-'.uniqid();

        $this->makePevaluacion($lapso, true, $activeName);
        $this->makePevaluacion($lapso, false, $inactiveName);
        $this->makePevaluacion($lapso, null, $nullName);

        // "Activos" sólo muestra pensum activo.
        Livewire::actingAs($planner)
            ->test(PevaluacionIndex::class)
            ->set('filter_lapso', $lapso->id)
            ->set('filter_pensum_status', 'active')
            ->assertOk()
            ->assertSee($activeName)
            ->assertDontSee($inactiveName)
            ->assertDontSee($nullName);

        // "Inactivos" incluye false y null.
        Livewire::actingAs($planner)
            ->test(PevaluacionIndex::class)
            ->set('filter_lapso', $lapso->id)
            ->set('filter_pensum_status', 'inactive')
            ->assertOk()
            ->assertDontSee($activeName)
            ->assertSee($inactiveName)
            ->assertSee($nullName);
    }
}
