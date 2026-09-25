<?php

namespace Tests\Feature\Profesor;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\CampoConocimiento;
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
use App\Notifications\LmsActivityPublicationNotification;
use App\Services\NotificationTargetResolver;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * `LmsActivityPublicationObserver`: avisa del ciclo de publicación de una
 * lección con el mismo destinatario que el observer de Activity (jefatura del
 * área, administración y planificación, coordinación en ámbito y el profesor).
 *
 * Solo se observan dos eventos: la transición a `status = PUBLISHED` y el
 * borrado del registro de publicación.
 */
class LmsPublicationNotificationTest extends TestCase
{
    use DatabaseTransactions;

    private static int $chainCounter = 0;

    private function makeContext(?int $leaderId = null): array
    {
        self::$chainCounter++;
        $s = self::$chainCounter;
        $code = fn (string $base) => "{$base}-{$s}";

        $profUser = User::factory()->create(['is_profesor' => true, 'is_active' => 'enable']);
        $coordIn = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'enable']);
        $pedIn = Peducativo::factory()->create(['manager_id' => $coordIn->id, 'status_active' => 'true']);
        $pestudio = Pestudio::factory()->create(['peducativo_id' => $pedIn->id, 'status_active' => 'true', 'planning_module' => 1]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $lapso = Lapso::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $profUser->id, 'name' => 'Test', 'lastname' => 'Prof',
            'ci_profesor' => 'T'.uniqid(), 'status_active' => 'true',
        ]);
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id, 'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id, 'lapso_id' => $lapso->id,
        ]);

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

        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic' => 'Ecuaciones de primer grado',
        ]);

        return compact('profUser', 'coordIn', 'pevaluacion', 'activity', 's', 'code');
    }

    private function publish(Activity $activity, User $actor, string $status = 'PUBLISHED'): LmsActivityPublication
    {
        $this->actingAs($actor);

        return LmsActivityPublication::updateOrCreate(
            ['activity_id' => $activity->id],
            [
                'published_by' => $actor->id,
                'status' => $status,
                'publish_at' => now(),
                'published_at' => $status === 'PUBLISHED' ? now() : null,
            ]
        );
    }

    public function test_publicar_avisa_a_profesor_lider_planner_y_coordinacion(): void
    {
        Notification::fake();

        $leader = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);
        $ctx = $this->makeContext($leader->id);
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);
        // El que publica es un responsable (no el profesor de la pevaluación).
        $publisher = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->publish($ctx['activity'], $publisher);

        Notification::assertSentTo($ctx['profUser'], LmsActivityPublicationNotification::class);
        Notification::assertSentTo($leader, LmsActivityPublicationNotification::class);
        Notification::assertSentTo($planner, LmsActivityPublicationNotification::class);
        Notification::assertSentTo($ctx['coordIn'], LmsActivityPublicationNotification::class);
    }

    public function test_el_tipo_y_el_payload_son_los_de_publicacion(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $publisher = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'enable']);

        $publication = $this->publish($ctx['activity'], $publisher);

        Notification::assertSentTo(
            $ctx['profUser'],
            LmsActivityPublicationNotification::class,
            function ($notification) use ($publication, $ctx) {
                $data = (array) $notification->toDatabase($ctx['profUser']);

                return $data['type'] === 'lms_activity_published'
                    && $data['publication_id'] === (int) $publication->id
                    && $data['activity_id'] === (int) $ctx['activity']->id
                    && $data['pevaluacion_id'] === (int) $ctx['pevaluacion']->id
                    && str_contains($data['message'], 'Ecuaciones de primer grado');
            }
        );
    }

    public function test_alta_directa_en_published_tambien_avisa(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $publisher = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        // Primera publicación de la lección: no había fila previa, así que
        // LmsPublicationService la CREA ya en PUBLISHED.
        $this->publish($ctx['activity'], $publisher, 'PUBLISHED');

        Notification::assertSentTo($ctx['profUser'], LmsActivityPublicationNotification::class);
    }

    public function test_la_transicion_desde_draft_tambien_avisa(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $publisher = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->publish($ctx['activity'], $publisher, 'DRAFT');
        Notification::assertNothingSent();

        $this->publish($ctx['activity'], $publisher, 'PUBLISHED');

        Notification::assertSentTo($ctx['profUser'], LmsActivityPublicationNotification::class);
    }

    public function test_otro_estado_no_avisa(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $publisher = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        // DRAFT y SCHEDULED no notifican: `LmsPublicationService` ya avisa por
        // su cuenta cuando un profesor PROGRAMA una lección.
        $this->publish($ctx['activity'], $publisher, 'DRAFT');
        $this->publish($ctx['activity'], $publisher, 'SCHEDULED');
        $this->publish($ctx['activity'], $publisher, 'ARCHIVED');

        Notification::assertNothingSent();
    }

    public function test_republicar_no_vuelve_a_notificar(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $publisher = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->publish($ctx['activity'], $publisher, 'DRAFT');
        $this->publish($ctx['activity'], $publisher, 'PUBLISHED');
        // Un update que NO cambia el status (p. ej. allow_comments) no avisa.
        $this->actingAs($publisher);
        LmsActivityPublication::where('activity_id', $ctx['activity']->id)
            ->update(['allow_comments' => false]);

        Notification::assertSentToTimes($ctx['profUser'], LmsActivityPublicationNotification::class, 1);
    }

    public function test_borrar_la_publicacion_avisa(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $publisher = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $publication = $this->publish($ctx['activity'], $publisher, 'DRAFT');
        Notification::fake(); // descarta lo del alta

        $this->actingAs($publisher);
        $publication->delete();

        Notification::assertSentTo(
            $ctx['profUser'],
            LmsActivityPublicationNotification::class,
            function ($notification) use ($ctx) {
                return ((array) $notification->toDatabase($ctx['profUser']))['type'] === 'lms_publication_deleted';
            }
        );
    }

    public function test_sin_usuario_autenticado_no_emite(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();

        LmsActivityPublication::updateOrCreate(
            ['activity_id' => $ctx['activity']->id],
            ['published_by' => $ctx['profUser']->id, 'status' => 'PUBLISHED', 'publish_at' => now()]
        );

        Notification::assertNothingSent();
    }

    public function test_no_avisa_a_coordinacion_fuera_de_ambito(): void
    {
        Notification::fake();
        $ctx = $this->makeContext();
        $coordOut = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'enable']);
        $publisher = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->publish($ctx['activity'], $publisher);

        Notification::assertNotSentTo($coordOut, LmsActivityPublicationNotification::class);
    }

    public function test_el_anti_spam_evita_dos_avisos_de_la_misma_publicacion(): void
    {
        // Sin fake: el anti-spam y la idempotencia leen la BD.
        $ctx = $this->makeContext();
        $publisher = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->publish($ctx['activity'], $publisher, 'DRAFT');
        $this->publish($ctx['activity'], $publisher, 'PUBLISHED');
        $this->publish($ctx['activity'], $publisher, 'PUBLISHED');

        $this->assertSame(
            1,
            $ctx['profUser']->notifications()->where('data->type', 'lms_activity_published')->count()
        );
    }

    public function test_el_destino_se_resuelve_por_rol(): void
    {
        $resolver = app(NotificationTargetResolver::class);
        $data = [
            'type' => 'lms_activity_published',
            'activity_id' => 77,
            'url' => 'https://x.test/indice',
        ];

        $this->assertSame(
            route('app.planning.lms.preview', ['activity' => 77]),
            $resolver->resolveFor(User::factory()->create(['is_planner' => true]), $data)
        );
        $this->assertSame(
            route('app.leadership.lms.preview', ['activity' => 77]),
            $resolver->resolveFor(User::factory()->create(['is_leadership' => true]), $data)
        );
        $this->assertSame(
            route('app.profesors.lms.editor', ['activity' => 77]),
            $resolver->resolveFor(User::factory()->create(['is_profesor' => true]), $data)
        );
        $this->assertSame(
            route('app.coordinacion.lessons'),
            $resolver->resolveFor(User::factory()->create(['is_coordinacion' => true]), $data)
        );

        // Sin activity_id en el payload no se puede construir el enlace con
        // parámetro: cae a la URL almacenada.
        $this->assertSame(
            'https://x.test/indice',
            $resolver->resolveFor(
                User::factory()->create(['is_profesor' => true]),
                ['type' => 'lms_activity_published', 'url' => 'https://x.test/indice']
            )
        );

        // Tipo borrado: cada rol a su listado (ya no hay nada que previsualizar).
        $deleted = ['type' => 'lms_publication_deleted', 'activity_id' => 77, 'url' => 'https://x.test/indice'];
        $this->assertSame(
            route('app.planning.lms.monitor'),
            $resolver->resolveFor(User::factory()->create(['is_planner' => true]), $deleted)
        );
        $this->assertSame(
            route('app.profesors.activities.index'),
            $resolver->resolveFor(User::factory()->create(['is_profesor' => true]), $deleted)
        );
    }
}
