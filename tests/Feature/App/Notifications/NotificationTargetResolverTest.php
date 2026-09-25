<?php

namespace Tests\Feature\App\Notifications;

use App\Models\User;
use App\Services\NotificationTargetResolver;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotificationTargetResolverTest extends TestCase
{
    use DatabaseTransactions;

    public function test_resuelve_url_segun_rol(): void
    {
        $resolver = app(NotificationTargetResolver::class);
        $data = ['type' => 'lesson_scheduled', 'url' => 'https://fallback.test/monitor'];

        $cases = [
            'admin' => [
                'state' => ['is_admin' => true],
                'expected' => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
            ],
            'planner' => [
                'state' => ['is_planner' => true],
                'expected' => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
            ],
            'coordinacion' => [
                'state' => ['is_coordinacion' => true],
                'expected' => route('app.coordinacion.lessons'),
            ],
            'leadership' => [
                'state' => ['is_leadership' => true],
                'expected' => route('app.leadership.lessons'),
            ],
            'director' => [
                'state' => ['is_director' => true],
                'expected' => route('app.director.lessons'),
            ],
        ];

        foreach ($cases as $name => $case) {
            $user = User::factory()->create($case['state']);
            $this->assertSame($case['expected'], $resolver->resolveFor($user, $data), "falló para {$name}");
        }
    }

    public function test_usuario_sin_rol_responsable_usa_el_url_almacenado(): void
    {
        $user = User::factory()->create();
        $resolver = app(NotificationTargetResolver::class);

        $this->assertSame(
            'https://fallback.test/monitor',
            $resolver->resolveFor($user, ['url' => 'https://fallback.test/monitor'])
        );
        $this->assertSame(
            route('app.notifications.index'),
            $resolver->resolveFor($user, [])
        );
    }

    /**
     * Las notificaciones que no son `lesson_scheduled` conservan su URL
     * almacenada aunque el destinatario tenga rol responsable: antes el
     * resolver las redirigía al listado de lecciones del rol (enlace erróneo).
     */
    public function test_notificaciones_no_leccion_respetan_su_url_para_cualquier_rol(): void
    {
        $resolver = app(NotificationTargetResolver::class);

        $roles = [
            'admin' => ['is_admin' => true],
            'planner' => ['is_planner' => true],
            'coordinacion' => ['is_coordinacion' => true],
            'leadership' => ['is_leadership' => true],
            'director' => ['is_director' => true],
        ];

        foreach ($roles as $name => $state) {
            $user = User::factory()->create($state);

            $this->assertSame(
                'https://destino.test/actividades',
                $resolver->resolveFor($user, [
                    'type' => 'pending_approval_leadership',
                    'url' => 'https://destino.test/actividades',
                ]),
                "falló para {$name} (url)"
            );

            // Compatibilidad con notificaciones históricas que usan action_url.
            $this->assertSame(
                'https://destino.test/suplencias',
                $resolver->resolveFor($user, [
                    'event_type' => 'substitute_assigned',
                    'action_url' => 'https://destino.test/suplencias',
                ]),
                "falló para {$name} (action_url)"
            );
        }
    }

    /**
     * `activity_created` se almacena con URL neutra (índice) y el destino lo
     * decide el rol según el orden de prioridad
     * is_admin → is_planner → is_director → is_diagnostic → is_coordinacion →
     * is_leadership → is_profesor → is_student. Gana el primer rol que el
     * usuario tenga, aunque tenga varios.
     */
    public function test_activity_created_sigue_el_orden_de_prioridad_de_roles(): void
    {
        $resolver = app(NotificationTargetResolver::class);
        $planning = route('app.planning.activities.index');

        foreach ([route('app.notifications.index'), route('app.leadership.activities')] as $stored) {
            $data = ['type' => 'activity_created', 'url' => $stored];

            // is_planner gana sobre is_coordinacion/is_leadership/is_profesor.
            $this->assertSame($planning, $resolver->resolveFor(
                User::factory()->create(['is_planner' => true, 'is_leadership' => true, 'is_coordinacion' => true, 'is_profesor' => true]),
                $data
            ), "planner multi-rol con stored={$stored}");

            // is_admin es el primero de la lista.
            $this->assertSame($planning, $resolver->resolveFor(
                User::factory()->create(['is_admin' => true, 'is_leadership' => true]),
                $data
            ), "admin con stored={$stored}");

            // Roles puros: cada uno a su listado.
            $this->assertSame($planning, $resolver->resolveFor(User::factory()->create(['is_planner' => true]), $data));
            $this->assertSame(route('app.director.activities'), $resolver->resolveFor(User::factory()->create(['is_director' => true]), $data));
            $this->assertSame($planning, $resolver->resolveFor(User::factory()->create(['is_diagnostic' => true]), $data));
            $this->assertSame(route('app.coordinacion.activities'), $resolver->resolveFor(User::factory()->create(['is_coordinacion' => true]), $data));
            $this->assertSame(route('app.leadership.activities'), $resolver->resolveFor(User::factory()->create(['is_leadership' => true]), $data));
            $this->assertSame(route('app.profesors.activities.index'), $resolver->resolveFor(User::factory()->create(['is_profesor' => true]), $data));

            // is_diagnostic gana sobre is_coordinacion; is_coordinacion sobre
            // is_leadership.
            $this->assertSame($planning, $resolver->resolveFor(
                User::factory()->create(['is_diagnostic' => true, 'is_coordinacion' => true]),
                $data
            ), "diagnostic+coordinacion con stored={$stored}");

            $this->assertSame(route('app.coordinacion.activities'), $resolver->resolveFor(
                User::factory()->create(['is_coordinacion' => true, 'is_leadership' => true]),
                $data
            ), "coordinacion+leadership con stored={$stored}");

            // El alumnado no gestiona actividades: cae a la URL almacenada.
            $this->assertSame($stored, $resolver->resolveFor(
                User::factory()->create(['is_student' => true]),
                $data
            ), "student con stored={$stored}");
        }
    }

    /**
     * El orden también aplica a `lesson_scheduled`, y los roles sin pantalla
     * propia para ese tipo (el profesorado) se saltan al siguiente.
     */
    public function test_lesson_scheduled_sigue_el_orden_de_prioridad(): void
    {
        $resolver = app(NotificationTargetResolver::class);
        $data = ['type' => 'lesson_scheduled', 'url' => 'https://fallback.test/monitor'];
        $monitor = route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']);

        $this->assertSame($monitor, $resolver->resolveFor(User::factory()->create(['is_admin' => true]), $data));
        $this->assertSame($monitor, $resolver->resolveFor(User::factory()->create(['is_planner' => true]), $data));
        $this->assertSame(route('app.director.lessons'), $resolver->resolveFor(User::factory()->create(['is_director' => true]), $data));
        $this->assertSame($monitor, $resolver->resolveFor(User::factory()->create(['is_diagnostic' => true]), $data));
        $this->assertSame(route('app.coordinacion.lessons'), $resolver->resolveFor(User::factory()->create(['is_coordinacion' => true]), $data));
        $this->assertSame(route('app.leadership.lessons'), $resolver->resolveFor(User::factory()->create(['is_leadership' => true]), $data));
        $this->assertSame(route('student.lms.lessons'), $resolver->resolveFor(User::factory()->create(['is_student' => true]), $data));

        // El profesorado no tiene listado de lecciones: se salta y, si no hay
        // otro rol, se respeta la URL almacenada.
        $this->assertSame(
            'https://fallback.test/monitor',
            $resolver->resolveFor(User::factory()->create(['is_profesor' => true]), $data)
        );

        // profesor + coordinación → gana coordinación.
        $this->assertSame(
            route('app.coordinacion.lessons'),
            $resolver->resolveFor(User::factory()->create(['is_profesor' => true, 'is_coordinacion' => true]), $data)
        );
    }

    /**
     * Las filas históricas guardan el tipo en `event_type` (no en `type`): el
     * resolver debe seguir aplicándoles el destino por rol.
     */
    public function test_las_filas_historicas_con_event_type_conservan_el_destino_por_rol(): void
    {
        $resolver = app(NotificationTargetResolver::class);

        $this->assertSame(
            route('app.planning.activities.index'),
            $resolver->resolveFor(
                User::factory()->create(['is_planner' => true, 'is_leadership' => true]),
                ['event_type' => 'activity_created', 'action_url' => route('app.leadership.activities')]
            )
        );

        $this->assertSame(
            'https://destino.test/suplencias',
            $resolver->resolveFor(
                User::factory()->create(['is_admin' => true]),
                ['event_type' => 'substitute_assigned', 'action_url' => 'https://destino.test/suplencias']
            ),
            'un type sin destino por rol debe respetar la URL almacenada'
        );
    }
}
