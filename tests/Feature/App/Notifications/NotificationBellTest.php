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
}
