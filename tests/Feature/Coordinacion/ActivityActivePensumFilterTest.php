<?php

namespace Tests\Feature\Coordinacion;

use App\Livewire\Coordinacion\ActivityList;
use App\Livewire\Coordinacion\CargaAcademicaList;
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
 * Los listados de Coordinación solo deben mostrar información cuyo pensum
 * esté activo:
 *  - /app/coordinacion/activities       (ActivityList)
 *  - /app/coordinacion/carga-academica  (CargaAcademicaList)
 */
class ActivityActivePensumFilterTest extends TestCase
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
    private function makePevaluacion(array $ctx, ?bool $pensumActive, string $asignaturaName, string $topic): array
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

    public function test_coordinacion_activities_hide_inactive_pensum(): void
    {
        $ctx = $this->makeCoordinatorContext();

        $activeTopic = 'ACTIVIDAD-ACTIVA-'.uniqid();
        $inactiveTopic = 'ACTIVIDAD-INACTIVA-'.uniqid();
        $nullTopic = 'ACTIVIDAD-NULA-'.uniqid();

        $this->makePevaluacion($ctx, true, 'ASIG-ACTIVA-'.uniqid(), $activeTopic);
        $this->makePevaluacion($ctx, false, 'ASIG-INACTIVA-'.uniqid(), $inactiveTopic);
        $this->makePevaluacion($ctx, null, 'ASIG-NULA-'.uniqid(), $nullTopic);

        Livewire::actingAs($ctx['coordinator'])
            ->test(ActivityList::class)
            ->set('lapsoId', $ctx['lapso']->id)
            ->assertOk()
            ->assertSee($activeTopic)
            ->assertDontSee($inactiveTopic)
            ->assertDontSee($nullTopic);
    }

    public function test_coordinacion_carga_academica_hides_inactive_pensum(): void
    {
        $ctx = $this->makeCoordinatorContext();

        $activeName = 'ASIG-ACTIVA-'.uniqid();
        $inactiveName = 'ASIG-INACTIVA-'.uniqid();
        $nullName = 'ASIG-NULA-'.uniqid();

        $this->makePevaluacion($ctx, true, $activeName, 'T-ACTIVA-'.uniqid());
        $this->makePevaluacion($ctx, false, $inactiveName, 'T-INACTIVA-'.uniqid());
        $this->makePevaluacion($ctx, null, $nullName, 'T-NULA-'.uniqid());

        Livewire::actingAs($ctx['coordinator'])
            ->test(CargaAcademicaList::class)
            ->set('lapsoId', $ctx['lapso']->id)
            ->assertOk()
            ->assertSee($activeName)
            ->assertDontSee($inactiveName)
            ->assertDontSee($nullName);
    }
}
