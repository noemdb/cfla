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
     * decide el rol: jefatura/admin a su listado con scope, planner puro al
     * de planificación global y coordinación al suyo. Vale también para las
     * filas históricas con URL de jefatura almacenada.
     */
    public function test_activity_created_redirige_a_planning_solo_al_planner_puro(): void
    {
        $resolver = app(NotificationTargetResolver::class);
        $index = route('app.notifications.index');
        $leadership = route('app.leadership.activities');

        foreach ([$index, $leadership] as $stored) {
            $data = ['type' => 'activity_created', 'url' => $stored];

            $purePlanner = User::factory()->create(['is_planner' => true]);
            $this->assertSame(
                route('app.planning.activities.index'),
                $resolver->resolveFor($purePlanner, $data),
                "planner puro con stored={$stored}"
            );

            // Con jefatura (aunque también sea planner): listado de jefatura.
            $plannerLeader = User::factory()->create(['is_planner' => true, 'is_leadership' => true]);
            $this->assertSame($leadership, $resolver->resolveFor($plannerLeader, $data));

            $leader = User::factory()->create(['is_leadership' => true]);
            $this->assertSame($leadership, $resolver->resolveFor($leader, $data));

            $admin = User::factory()->create(['is_admin' => true]);
            $this->assertSame($leadership, $resolver->resolveFor($admin, $data));

            // Coordinación pura: su propio listado de actividades.
            $coord = User::factory()->create(['is_coordinacion' => true]);
            $this->assertSame(
                route('app.coordinacion.activities'),
                $resolver->resolveFor($coord, $data)
            );

            // Con jefatura (aunque también coordine): listado de jefatura.
            $leaderCoord = User::factory()->create(['is_leadership' => true, 'is_coordinacion' => true]);
            $this->assertSame($leadership, $resolver->resolveFor($leaderCoord, $data));
        }
    }
}
