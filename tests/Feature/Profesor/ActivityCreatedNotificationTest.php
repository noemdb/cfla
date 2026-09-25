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
 * cualquier vía se notifica al jefe de área (leader_id activo y con rol), a
 * administración y planificación (alcance global) y a la coordinación en cuyo
 * ámbito cae la pevaluacion. Los conjuntos se unen, de modo que un usuario
 * multi-rol (p. ej. planner + coordinación, o admin + jefatura) también recibe
 * el aviso, una sola vez; sin auto-notificar al creador que sea solo profesor
 * (si además tiene rol de supervisión sí la recibe) y con anti-spam por
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

    public function test_notifica_a_lider_activo_admin_planner_y_coord_en_ambito(): void
    {
        Notification::fake();
        $leader = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);
        $ctx = $this->makeContext($leader->id);
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => 'enable']);

        $activity = $this->createActivity($ctx['profUser'], $ctx['pevaluacion']);

        Notification::assertSentTo($leader, ActivityCreatedNotification::class);
        Notification::assertSentTo($planner, ActivityCreatedNotification::class);
        Notification::assertSentTo($admin, ActivityCreatedNotification::class);
        Notification::assertSentTo($ctx['coordIn'], ActivityCreatedNotification::class);

        // La auditoría del evento queda registrada (ítem 9).
        $this->assertDatabaseHas('broadcast_events', [
            'event' => 'activity.created',
            'subject_id' => $activity->id,
            'actor_user_id' => $ctx['profUser']->id,
        ]);
    }

    /**
     * Regresión: los usuarios con varios roles a la vez (el caso de quien es
     * planner y coordinación, o admin y jefatura) quedaban fuera de todos los
     * conjuntos por los filtros de rol "puro" y nunca recibían el aviso. Ahora
     * los conjuntos se unen y el destino del clic lo decide el resolver por
     * rol, así que deben recibirlo una sola vez.
     */
    public function test_los_usuarios_multi_rol_lo_reciben_una_sola_vez(): void
    {
        Notification::fake();
        $leaderId = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable'])->id;
        $ctx = $this->makeContext($leaderId);

        $plannerCoord = User::factory()->create([
            'is_planner' => true, 'is_coordinacion' => true, 'is_active' => 'enable',
        ]);
        $plannerCoord->forceFill(['is_admin' => false])->save();

        $adminLeader = User::factory()->create([
            'is_admin' => true, 'is_leadership' => true, 'is_planner' => true, 'is_active' => 'enable',
        ]);

        $this->createActivity($ctx['profUser'], $ctx['pevaluacion']);

        Notification::assertSentToTimes($plannerCoord, ActivityCreatedNotification::class, 1);
        Notification::assertSentToTimes($adminLeader, ActivityCreatedNotification::class, 1);
    }

    public function test_no_notifica_a_lider_inactivo_ni_sin_rol_ni_coord_fuera_de_ambito(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $inactiveLeader = User::factory()->create(['is_leadership' => true, 'is_active' => 'disable']);
        $rolelessLeader = User::factory()->create(['is_active' => 'enable']);
        $coordOut = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'enable']);
        $inactiveCoord = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'disable']);
        $inactivePlanner = User::factory()->create(['is_planner' => true, 'is_active' => 'disable']);

        // Pensum sin área: solo aplican los lotes de admin/planner/coordinación.
        $this->createActivity($ctx['profUser'], $ctx['pevaluacion']);

        Notification::assertNotSentTo($inactiveLeader, ActivityCreatedNotification::class);
        Notification::assertNotSentTo($rolelessLeader, ActivityCreatedNotification::class);
        Notification::assertNotSentTo($coordOut, ActivityCreatedNotification::class);
        Notification::assertNotSentTo($inactivePlanner, ActivityCreatedNotification::class);
        Notification::assertNotSentTo($inactiveCoord, ActivityCreatedNotification::class);
        // La coordinación en ámbito sí recibe aunque el pensum no tenga área.
        Notification::assertSentTo($ctx['coordIn'], ActivityCreatedNotification::class);
    }

    /**
     * El profesor "puro" que registra la actividad no recibe su propio aviso.
     */
    public function test_no_auto_notifica_al_creador_profesor_puro(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();

        $this->createActivity($ctx['profUser'], $ctx['pevaluacion']);

        Notification::assertNotSentTo($ctx['profUser'], ActivityCreatedNotification::class);
        Notification::assertSentTo($ctx['coordIn'], ActivityCreatedNotification::class);
    }

    /**
     * Regresión: si el creador además tiene un rol de supervisión (p. ej.
     * planner/admin que entra como profesor a registrar la actividad) debe
     * recibirla igual, porque en esa capacidad tiene que enterarse de que hay
     * una actividad nueva que revisar. Antes la exclusión del creador lo
     * dejaba siempre sin aviso.
     */
    public function test_el_creador_con_rol_de_supervision_si_recibe_la_notificacion(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();

        $supervisor = User::factory()->create([
            'is_profesor' => true,
            'is_planner' => true,
            'is_coordinacion' => true,
            'is_leadership' => true,
            'is_admin' => true,
            'is_active' => 'enable',
        ]);

        $this->createActivity($supervisor, $ctx['pevaluacion']);

        Notification::assertSentToTimes($supervisor, ActivityCreatedNotification::class, 1);
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

    /**
     * El anti-spam es por usuario y por pevaluacion: otra pevaluacion sí
     * genera aviso, y si el anterior ya se leyó también.
     */
    public function test_el_anti_spam_no_afecta_a_otra_pevaluacion_ni_a_lo_ya_leido(): void
    {
        $ctxA = $this->makeContext();
        $ctxB = $this->makeContext();
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->createActivity($ctxA['profUser'], $ctxA['pevaluacion']);

        // Otra pevaluacion → sí avisa.
        $this->createActivity($ctxB['profUser'], $ctxB['pevaluacion']);
        $this->assertSame(2, $planner->notifications()->where('data->type', 'activity_created')->count());

        // Si se leen, un registro nuevo vuelve a avisar.
        $planner->notifications()->update(['read_at' => now()]);
        $this->createActivity($ctxA['profUser'], $ctxA['pevaluacion']);
        $this->assertSame(3, $planner->notifications()->where('data->type', 'activity_created')->count());
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
