<?php

namespace Tests\Feature\Coordinacion;

use App\Livewire\Coordinacion\ActivityList;
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
use App\Notifications\PevaluacionObservationNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityObservationNotificationTest extends TestCase
{
    use DatabaseTransactions;

    private function makeCoordinatorContext(): array
    {
        $coordinator = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'enable']);
        $peducativo = Peducativo::factory()->create(['manager_id' => $coordinator->id, 'status_active' => 'true']);
        $pestudio = Pestudio::factory()->create(['peducativo_id' => $peducativo->id, 'status_active' => 'true', 'planning_module' => 1]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $lapso = Lapso::factory()->create();
        $profUser = User::factory()->create();
        $profesor = Profesor::create(['user_id' => $profUser->id, 'name' => 'Test', 'lastname' => 'Prof', 'ci_profesor' => 'T'.uniqid(), 'status_active' => 'true']);
        $pevaluacion = Pevaluacion::create(['profesor_id' => $profesor->id, 'pensum_id' => $pensum->id, 'seccion_id' => $seccion->id, 'lapso_id' => $lapso->id]);

        return compact('coordinator', 'pestudio', 'grado', 'seccion', 'asignatura', 'pensum', 'lapso', 'profesor', 'pevaluacion', 'peducativo', 'profUser');
    }

    public function test_registrar_observacion_notifica_a_planners(): void
    {
        Notification::fake();
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);
        $ctx = $this->makeCoordinatorContext();

        Livewire::actingAs($ctx['coordinator'])
            ->test(ActivityList::class)
            ->call('editObservations', $ctx['pevaluacion']->id)
            ->set('observations', 'Observación de prueba')
            ->call('saveObservations')
            ->assertHasNoErrors();

        Notification::assertSentTo($planner, PevaluacionObservationNotification::class, fn ($n) => $n->pevaluacionId === $ctx['pevaluacion']->id);
    }

    public function test_actualizar_observacion_notifica_a_planners(): void
    {
        Notification::fake();
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);
        $ctx = $this->makeCoordinatorContext();
        $ctx['pevaluacion']->update(['observations' => 'Inicial']);

        Livewire::actingAs($ctx['coordinator'])
            ->test(ActivityList::class)
            ->call('editObservations', $ctx['pevaluacion']->id)
            ->set('observations', 'Actualizada')
            ->call('saveObservations');

        Notification::assertSentTo($planner, PevaluacionObservationNotification::class);
    }

    public function test_no_notifica_si_observacion_vacia_o_igual(): void
    {
        Notification::fake();
        User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);
        $ctx = $this->makeCoordinatorContext();
        $ctx['pevaluacion']->update(['observations' => 'Misma']);

        Livewire::actingAs($ctx['coordinator'])
            ->test(ActivityList::class)
            ->call('editObservations', $ctx['pevaluacion']->id)
            ->set('observations', 'Misma')
            ->call('saveObservations');

        Notification::assertNothingSent();

        Notification::fake();
        Livewire::actingAs($ctx['coordinator'])
            ->test(ActivityList::class)
            ->call('editObservations', $ctx['pevaluacion']->id)
            ->set('observations', '')
            ->call('saveObservations');

        Notification::assertNothingSent();
    }
}
