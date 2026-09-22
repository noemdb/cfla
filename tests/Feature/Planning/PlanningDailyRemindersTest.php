<?php

namespace Tests\Feature\Planning;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use App\Notifications\PendingApprovalReminderNotification;
use App\Notifications\ScheduledLessonsReminderNotification;
use App\Notifications\StaleActivitiesReminderNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Recordatorios diarios de planificación (planning:daily-reminders):
 * aprobaciones de actividades cuya finicial cae en 3 días (Jefe de Área,
 * Coordinación y Planificación), 5+ lecciones programadas y áreas inactivas.
 *
 * Se usa una fecha objetivo lejana (2099-01-01) para aislar los datos de la
 * BD real: ninguna actividad preexistente cae en esa fecha.
 */
class PlanningDailyRemindersTest extends TestCase
{
    use DatabaseTransactions;

    private const TARGET = '2099-01-01';

    /**
     * Crea la cadena Pevaluacion → Pensum → Asignatura (con área del líder
     * opcional) para colgar actividades.
     *
     * @return array{0: Pevaluacion, 1: Asignatura}
     */
    private function makeContext(?User $leader = null): array
    {
        $peducativo = Peducativo::factory()->create(['status_active' => 'true']);
        $pestudio = Pestudio::factory()->create([
            'peducativo_id' => $peducativo->id,
            'status_active' => 'true',
            'planning_module' => 1,
        ]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);

        if ($leader) {
            $area = AreaConocimiento::create([
                'leader_id' => $leader->id,
                'name' => 'Área '.Str::random(6),
                'code' => 'A'.Str::random(4),
                'peducativo_id' => $peducativo->id,
                'pestudio_id' => $pestudio->id,
                'order' => 1,
            ]);
            $asignatura->areasConocimiento()->attach($area->id);
        }

        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        $profUser = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $profUser->id,
            'name' => 'Doc',
            'lastname' => 'Test',
            'ci_profesor' => 'CI-'.Str::random(10),
            'status_active' => 'true',
        ]);

        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        return [$pevaluacion, $asignatura];
    }

    private function runCommand(): void
    {
        $this->artisan('planning:daily-reminders', ['--date' => self::TARGET])->assertExitCode(0);
    }

    public function test_jefe_de_area_recibe_actividades_sin_aprobar_en_tres_dias(): void
    {
        Notification::fake();
        Event::fake();

        $leader = User::factory()->create(['is_leadership' => true]);
        $otherLeader = User::factory()->create(['is_leadership' => true]);
        [$pevaluacion] = $this->makeContext($leader);

        Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'finicial' => self::TARGET,
            'status' => false,
        ]);

        $this->runCommand();

        Notification::assertSentTo(
            $leader,
            PendingApprovalReminderNotification::class,
            fn ($n) => $n->type === 'pending_approval_leadership' && $n->count >= 1
        );
        Notification::assertNotSentTo($otherLeader, PendingApprovalReminderNotification::class);
    }

    public function test_planificacion_recibe_actividades_sin_aprobar_en_tres_dias(): void
    {
        Notification::fake();
        Event::fake();

        $planner = User::factory()->create(['is_planner' => true]);
        [$pevaluacion] = $this->makeContext();

        Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'finicial' => self::TARGET,
            'status' => false,
        ]);

        $this->runCommand();

        Notification::assertSentTo(
            $planner,
            PendingApprovalReminderNotification::class,
            fn ($n) => $n->type === 'pending_approval_planner' && $n->count >= 1
        );
    }

    public function test_no_notifica_actividades_ya_aprobadas(): void
    {
        Notification::fake();
        Event::fake();

        $leader = User::factory()->create(['is_leadership' => true]);
        [$pevaluacion] = $this->makeContext($leader);

        Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'finicial' => self::TARGET,
            'status' => true,
        ]);

        $this->runCommand();

        Notification::assertNotSentTo($leader, PendingApprovalReminderNotification::class);
    }

    public function test_jefe_de_area_recibe_alerta_con_cinco_lecciones_programadas(): void
    {
        Notification::fake();
        Event::fake();

        $leader = User::factory()->create(['is_leadership' => true]);
        [$pevaluacion] = $this->makeContext($leader);

        for ($i = 0; $i < 5; $i++) {
            $activity = Activity::factory()->create([
                'pevaluacion_id' => $pevaluacion->id,
                'finicial' => now()->addDays(30),
                'status' => true,
            ]);

            LmsActivityPublication::factory()->create([
                'activity_id' => $activity->id,
                'status' => 'SCHEDULED',
                'publish_at' => self::TARGET,
            ]);
        }

        $this->runCommand();

        Notification::assertSentTo(
            $leader,
            ScheduledLessonsReminderNotification::class,
            fn ($n) => $n->count >= 5 && $n->date === self::TARGET
        );
    }

    public function test_jefe_de_area_con_menos_de_cinco_lecciones_no_recibe_alerta(): void
    {
        Notification::fake();
        Event::fake();

        $leader = User::factory()->create(['is_leadership' => true]);
        [$pevaluacion] = $this->makeContext($leader);

        for ($i = 0; $i < 4; $i++) {
            $activity = Activity::factory()->create([
                'pevaluacion_id' => $pevaluacion->id,
                'finicial' => now()->addDays(30),
                'status' => true,
            ]);

            LmsActivityPublication::factory()->create([
                'activity_id' => $activity->id,
                'status' => 'SCHEDULED',
                'publish_at' => self::TARGET,
            ]);
        }

        $this->runCommand();

        Notification::assertNotSentTo($leader, ScheduledLessonsReminderNotification::class);
    }

    public function test_jefe_de_area_recibe_alerta_de_area_inactiva(): void
    {
        Notification::fake();
        Event::fake();

        $leader = User::factory()->create(['is_leadership' => true]);
        [$pevaluacion] = $this->makeContext($leader);

        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'finicial' => now()->subDays(30),
            'status' => true,
        ]);

        // Sin actividad reciente: última actualización hace 10 días (> 5).
        DB::table('activities')
            ->where('id', $activity->id)
            ->update(['updated_at' => now()->subDays(10)]);

        $this->runCommand();

        Notification::assertSentTo(
            $leader,
            StaleActivitiesReminderNotification::class,
            fn ($n) => $n->daysSince >= 10
        );
    }

    public function test_dry_run_no_persiste_ni_emite_notificaciones(): void
    {
        Notification::fake();
        Event::fake();

        $leader = User::factory()->create(['is_leadership' => true]);
        [$pevaluacion] = $this->makeContext($leader);

        Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'finicial' => self::TARGET,
            'status' => false,
        ]);

        $this->artisan('planning:daily-reminders', [
            '--date' => self::TARGET,
            '--dry-run' => true,
        ])->assertExitCode(0);

        Notification::assertNothingSent();
    }
}
