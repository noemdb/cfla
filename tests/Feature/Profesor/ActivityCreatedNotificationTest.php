<?php

namespace Tests\Feature\Profesor;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use App\Notifications\ActivityCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Cobertura de ActivityObserver::created(): al registrar una actividad por
 * cualquier vía se notifica al jefe de área (leader_id activo y con rol),
 * al planner puro y a la coordinación en cuyo ámbito cae la pevaluacion;
 * sin duplicados, sin auto-notificación al creador y con anti-spam por
 * pevaluacion (24h). Sin usuario autenticado (seeders/consola) no se emite.
 */
class ActivityCreatedNotificationTest extends TestCase
{
    use DatabaseTransactions;

    private function makeContext(?int $leaderId = null): array
    {
        $profUser = User::factory()->create();
        $coordIn = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'enable']);
        $pedIn = Peducativo::factory()->create(['manager_id' => $coordIn->id, 'status_active' => 'true']);
        $pestudio = Pestudio::factory()->create(['peducativo_id' => $pedIn->id, 'status_active' => 'true', 'planning_module' => 1]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $lapso = Lapso::factory()->create();
        $profesor = Profesor::create(['user_id' => $profUser->id, 'name' => 'Test', 'lastname' => 'Prof', 'ci_profesor' => 'T'.uniqid(), 'status_active' => 'true']);
        $pevaluacion = Pevaluacion::create(['profesor_id' => $profesor->id, 'pensum_id' => $pensum->id, 'seccion_id' => $seccion->id, 'lapso_id' => $lapso->id]);

        if ($leaderId !== null) {
            $area = AreaConocimiento::create([
                'peducativo_id' => $pedIn->id,
                'pestudio_id' => $pestudio->id,
                'leader_id' => $leaderId,
                'name' => 'Área test',
                'code' => 'AT'.uniqid(),
                'order' => 1,
            ]);
            CampoConocimiento::create([
                'area_conocimiento_id' => $area->id,
                'asignatura_id' => $asignatura->id,
                'pensum_id' => $pensum->id,
                'order' => 1,
            ]);
        }

        return compact('profUser', 'coordIn', 'pevaluacion');
    }

    private function createActivity(User $creator, Pevaluacion $pevaluacion): Activity
    {
        $this->actingAs($creator);

        return Activity::create(['pevaluacion_id' => $pevaluacion->id, 'topic' => 'Tema test', 'status' => false]);
    }

    public function test_notifica_a_lider_activo_planner_puro_y_coord_en_ambito(): void
    {
        Notification::fake();
        $leader = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);
        $ctx = $this->makeContext($leader->id);
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $activity = $this->createActivity($ctx['profUser'], $ctx['pevaluacion']);

        Notification::assertSentTo($leader, ActivityCreatedNotification::class);
        Notification::assertSentTo($planner, ActivityCreatedNotification::class);
        Notification::assertSentTo($ctx['coordIn'], ActivityCreatedNotification::class);

        // La auditoría del evento queda registrada (ítem 9).
        $this->assertDatabaseHas('broadcast_events', [
            'event' => 'activity.created',
            'subject_id' => $activity->id,
            'actor_user_id' => $ctx['profUser']->id,
        ]);
    }

    public function test_no_notifica_a_lider_inactivo_ni_sin_rol_ni_fuera_de_ambito(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $inactiveLeader = User::factory()->create(['is_leadership' => true, 'is_active' => 'disable']);
        $rolelessLeader = User::factory()->create(['is_active' => 'enable']);
        $coordOut = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'enable']);
        $plannerCoord = User::factory()->create(['is_planner' => true, 'is_active' => 'enable', 'is_coordinacion' => true, 'is_admin' => false]);
        $inactiveCoord = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'disable']);

        // Pensum sin área: solo aplican los lotes de planner/coordinación.
        $this->createActivity($ctx['profUser'], $ctx['pevaluacion']);

        Notification::assertNotSentTo($inactiveLeader, ActivityCreatedNotification::class);
        Notification::assertNotSentTo($rolelessLeader, ActivityCreatedNotification::class);
        Notification::assertNotSentTo($coordOut, ActivityCreatedNotification::class);
        Notification::assertNotSentTo($plannerCoord, ActivityCreatedNotification::class);
        Notification::assertNotSentTo($inactiveCoord, ActivityCreatedNotification::class);
        // La coordinación en ámbito sí recibe aunque el pensum no tenga área.
        Notification::assertSentTo($ctx['coordIn'], ActivityCreatedNotification::class);
    }

    public function test_no_auto_notifica_al_creador(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $creator = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->createActivity($creator, $ctx['pevaluacion']);

        Notification::assertNotSentTo($creator, ActivityCreatedNotification::class);
        Notification::assertSentTo($ctx['coordIn'], ActivityCreatedNotification::class);
    }

    public function test_segunda_actividad_misma_pevaluacion_no_duplica_en_24h(): void
    {
        // Sin fake: el anti-spam lee las no-leídas persistidas en BD
        // (el rollback de DatabaseTransactions limpia al final).
        $ctx = $this->makeContext();
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->createActivity($ctx['profUser'], $ctx['pevaluacion']);
        $this->createActivity($ctx['profUser'], $ctx['pevaluacion']);

        $this->assertSame(1, $planner->notifications()->where('data->type', 'activity_created')->count());
        $this->assertSame(1, $ctx['coordIn']->notifications()->where('data->type', 'activity_created')->count());
    }

    public function test_sin_usuario_autenticado_no_emite(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        Activity::create(['pevaluacion_id' => $ctx['pevaluacion']->id, 'topic' => 'Tema test', 'status' => false]);

        Notification::assertNothingSent();
    }
}
