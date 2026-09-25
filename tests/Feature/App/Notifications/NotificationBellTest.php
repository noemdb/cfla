<?php

namespace Tests\Feature\App\Notifications;

use App\Livewire\App\Notifications\NotificationBell;
use App\Models\User;
use App\Notifications\LessonScheduledForApproval;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\TestCase;

class NotificationBellTest extends TestCase
{
    use DatabaseTransactions;

    private function makeNotification(User $user, string $id, array $extra = []): void
    {
        $user->notifications()->create([
            'id' => $id,
            'type' => LessonScheduledForApproval::class,
            'data' => array_merge([
                'activity_id' => 1,
                'type' => 'lesson_scheduled',
                'teacher_name' => 'Prof. Carlos',
                'activity_title' => 'Álgebra',
                'scheduled_at' => '14/08/2026 10:00',
                'message' => "Notificación {$id}",
                'url' => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
            ], $extra),
            'created_at' => now(),
        ]);
    }

    /**
     * Fila con la forma histórica: el tipo va en `event_type` y la URL en
     * `action_url`, sin `type`/`url`.
     */
    private function makeLegacyNotification(User $user, string $id, array $data): void
    {
        $user->notifications()->create([
            'id' => $id,
            'type' => LessonScheduledForApproval::class,
            'data' => $data,
            'created_at' => now(),
        ]);
    }

    public function test_render_muestra_ultimas_notificaciones_y_conteo(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'notif-1', ['created_at' => now()]);
        $this->makeNotification($user, 'notif-2', ['created_at' => now()->addMinute()]);

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 2)
            ->assertSee('Notificación notif-2')
            ->assertSee('Notificación notif-1');
    }

    public function test_echo_listener_incluye_canal_y_evento_correctos(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->actingAs($user);

        $component = Livewire::test(NotificationBell::class);

        $listeners = (new ReflectionMethod(NotificationBell::class, 'getListeners'))
            ->invoke($component->instance());

        $expected = 'echo-private:App.Models.User.'.$user->id.',.notification.received';
        $this->assertArrayHasKey($expected, $listeners);
        $this->assertSame('onNotificationReceived', $listeners[$expected]);
    }

    public function test_on_notification_received_inserta_optimista_y_no_duplica(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->actingAs($user);

        $payload = [
            'id' => 'notif-x',
            'data' => [
                'type' => 'lesson_scheduled',
                'message' => 'Mensaje optimista X',
                'created_at' => now()->toIso8601String(),
            ],
        ];

        $component = Livewire::test(NotificationBell::class)
            ->call('onNotificationReceived', $payload)
            ->call('onNotificationReceived', $payload);

        $component->assertSet('unreadCount', 1)
            ->assertSee('Mensaje optimista X');

        $count = collect($component->get('notifications'))
            ->filter(fn ($item) => $item['id'] === 'notif-x')
            ->count();
        $this->assertSame(1, $count);
    }

    public function test_mark_all_as_read_marca_y_actualiza_conteo(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'notif-1');
        $this->makeNotification($user, 'notif-2');

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 2)
            ->call('markAllAsRead')
            ->assertSet('unreadCount', 0);

        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_mark_as_read_marca_una_sola(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'notif-1');
        $this->makeNotification($user, 'notif-2');

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 2)
            ->call('markAsRead', 'notif-1')
            ->assertSet('unreadCount', 1);

        $this->assertSame(1, $user->unreadNotifications()->count());
    }

    public function test_boton_ver_todas_enlaza_a_index(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertSee(route('app.notifications.index'));
    }

    /**
     * Las filas históricas (TimetableChanged/SubstituteAssigned y anteriores)
     * guardan el tipo en `event_type`. Sin el fallback se pintarían como
     * "generic" en la campana.
     */
    public function test_las_filas_historicas_con_event_type_no_se_pintan_como_genericas(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->makeLegacyNotification($user, 'notif-legacy', [
            'event_type' => 'substitute_assigned',
            'action_url' => 'https://destino.test/suplencias',
            'message' => 'Suplencia asignada (fila histórica)',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 1);

        $item = collect($component->get('notifications'))->firstWhere('id', 'notif-legacy');

        $this->assertNotNull($item, 'la notificación histórica no apareció en la campana');
        $this->assertSame('substitute_assigned', $item['type']);
        $this->assertNotSame('generic', $item['type']);
        $this->assertSame('https://destino.test/suplencias', $item['url']);
    }

    /**
     * El destino por rol se decide con `type` o, en filas antiguas, con
     * `event_type`: un planner multi-rol debe ir a planificación, no a
     * liderazgo (que era lo que pasaba por el orden inverso).
     */
    public function test_la_campana_aplica_el_destino_por_rol_a_filas_historicas(): void
    {
        $user = User::factory()->create([
            'is_planner' => true,
            'is_leadership' => true,
            'is_coordinacion' => true,
        ]);
        $this->makeLegacyNotification($user, 'notif-legacy', [
            'event_type' => 'activity_created',
            'action_url' => route('app.leadership.activities'),
            'message' => 'Se registró una nueva actividad (fila histórica)',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(NotificationBell::class);
        $item = collect($component->get('notifications'))->firstWhere('id', 'notif-legacy');

        $this->assertSame(route('app.planning.activities.index'), $item['url']);
    }
}
