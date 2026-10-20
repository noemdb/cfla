<?php

namespace Tests\Feature\Director;

use App\Livewire\Director\CargaAcademicaList;
use App\Models\app\Academy\Asignatura;
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
 * El listado de carga académica de Dirección (/app/director/carga-academica)
 * solo debe mostrar pevaluaciones cuyo pensum esté activo.
 */
class CargaAcademicaActivePensumFilterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array<string, mixed>
     */
    private function makeContext(): array
    {
        $director = User::factory()->director()->create();
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
        $profesor = Profesor::factory()->create();

        return compact('director', 'lapso', 'pestudio', 'grado', 'seccion', 'profesor');
    }

    private function makePevaluacion(array $ctx, ?bool $pensumActive, string $asignaturaName): void
    {
        $asignatura = Asignatura::factory()->create([
            'pestudio_id' => $ctx['pestudio']->id,
            'name' => $asignaturaName,
        ]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $ctx['pestudio']->id,
            'grado_id' => $ctx['grado']->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => $pensumActive,
        ]);
        Pevaluacion::create([
            'profesor_id' => $ctx['profesor']->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $ctx['seccion']->id,
            'lapso_id' => $ctx['lapso']->id,
        ]);
    }

    public function test_director_hides_pevaluacions_with_inactive_or_null_pensum(): void
    {
        $ctx = $this->makeContext();

        $activeName = 'ASIG-ACTIVA-'.uniqid();
        $inactiveName = 'ASIG-INACTIVA-'.uniqid();
        $nullName = 'ASIG-NULA-'.uniqid();

        $this->makePevaluacion($ctx, true, $activeName);
        $this->makePevaluacion($ctx, false, $inactiveName);
        $this->makePevaluacion($ctx, null, $nullName);

        Livewire::actingAs($ctx['director'])
            ->test(CargaAcademicaList::class)
            ->set('lapsoId', $ctx['lapso']->id)
            ->assertOk()
            ->assertSee($activeName)
            ->assertDontSee($inactiveName)
            ->assertDontSee($nullName);
    }
}
