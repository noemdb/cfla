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
}
