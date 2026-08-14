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
        $data = ['url' => 'https://fallback.test/monitor'];

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
}
