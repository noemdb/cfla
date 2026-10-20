<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Activity\PevaluacionList;
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
 * El listado de actividades del profesor (/app/profesors/activities) solo
 * debe mostrar pevaluaciones cuyo grado y pensum estén activos.
 */
class ActivityActiveFiltersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_profesor_hides_pevaluacions_from_inactive_grado_or_pensum(): void
    {
        $user = User::factory()->create(['is_profesor' => true]);
        $profesor = Profesor::factory()->create(['user_id' => $user->id]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create([
            'planning_module' => true,
            'status_active' => 'true',
        ]);

        $activeGrado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'status_active' => 'true',
        ]);
        $activeSeccion = Seccion::factory()->create([
            'grado_id' => $activeGrado->id,
            'status_active' => 'true',
        ]);

        $inactiveGrado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'status_active' => 'false',
        ]);
        $inactiveGradoSeccion = Seccion::factory()->create([
            'grado_id' => $inactiveGrado->id,
            'status_active' => 'true',
        ]);

        $activeName = 'ASIG-ACTIVA-'.uniqid();
        $inactiveGradeName = 'ASIG-GRADO-INACTIVO-'.uniqid();
        $inactivePensumName = 'ASIG-PENSUM-INACTIVO-'.uniqid();
        $nullPensumName = 'ASIG-PENSUM-NULO-'.uniqid();

        $this->makePevaluacion($profesor, $lapso, $pestudio, $activeGrado, $activeSeccion, $activeName, true);
        $this->makePevaluacion($profesor, $lapso, $pestudio, $inactiveGrado, $inactiveGradoSeccion, $inactiveGradeName, true);
        $this->makePevaluacion($profesor, $lapso, $pestudio, $activeGrado, $activeSeccion, $inactivePensumName, false);
        $this->makePevaluacion($profesor, $lapso, $pestudio, $activeGrado, $activeSeccion, $nullPensumName, null);

        Livewire::actingAs($user)
            ->test(PevaluacionList::class, ['lapsoId' => $lapso->id])
            ->assertOk()
            ->assertSee($activeName)
            ->assertDontSee($inactiveGradeName)
            ->assertDontSee($inactivePensumName)
            ->assertDontSee($nullPensumName);
    }

    private function makePevaluacion(
        Profesor $profesor,
        Lapso $lapso,
        Pestudio $pestudio,
        Grado $grado,
        Seccion $seccion,
        string $asignaturaName,
        ?bool $pensumActive,
    ): Pevaluacion {
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

        return Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);
    }
}
