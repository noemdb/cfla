<?php

namespace Tests\Feature\App\Notifications;

use App\Livewire\App\Notifications\NotificationsIndex;
use App\Models\User;
use App\Notifications\LessonScheduledForApproval;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsIndexTest extends TestCase
{
    use DatabaseTransactions;

    private function makeNotification(User $user, string $id, ?string $readAt = null): void
    {
        $user->notifications()->create([
            'id' => $id,
            'type' => LessonScheduledForApproval::class,
            'data' => [
                'activity_id' => 1,
                'type' => 'lesson_scheduled',
                'teacher_name' => 'Prof. Carlos',
                'activity_title' => 'Álgebra',
                'scheduled_at' => '14/08/2026 10:00',
                'message' => "Notificación {$id}",
                'url' => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
            ],
            'read_at' => $readAt,
            'created_at' => now(),
        ]);
    }

    public function test_ruta_index_carga_para_rol_autenticado(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->actingAs($user);

        $this->get(route('app.notifications.index'))
            ->assertOk()
            ->assertSee('Notificaciones');
    }

    public function test_index_muestra_solo_notificaciones_del_usuario(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $other = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'notif-1');
        $this->makeNotification($other, 'notif-other');

        $this->actingAs($user);

        Livewire::test(NotificationsIndex::class)
            ->assertSee('Notificación notif-1')
            ->assertDontSee('Notificación notif-other');
    }

    public function test_tabs_filtran_por_estado_de_lectura(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'notif-read', readAt: now()->toDateTimeString());
        $this->makeNotification($user, 'notif-unread');

        $this->actingAs($user);

        Livewire::test(NotificationsIndex::class)
            ->call('setTab', 'unread')
            ->assertSee('Notificación notif-unread')
            ->assertDontSee('Notificación notif-read');
    }

    public function test_mark_all_as_read_marca_todas(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'notif-1');
        $this->makeNotification($user, 'notif-2');

        $this->actingAs($user);

        Livewire::test(NotificationsIndex::class)
            ->call('markAllAsRead');

        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_mark_as_read_marca_una(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'notif-1');
        $this->makeNotification($user, 'notif-2');

        $this->actingAs($user);

        Livewire::test(NotificationsIndex::class)
            ->call('markAsRead', 'notif-1');

        $this->assertSame(1, $user->unreadNotifications()->count());
        $this->assertNotNull($user->notifications()->where('id', 'notif-1')->first()->read_at);
    }
}
