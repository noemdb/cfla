<?php

namespace Tests\Feature\App\Notifications;

use App\Events\NotificationReceived;
use App\Models\User;
use App\Notifications\LessonScheduledForApproval;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeRecipient(): User
    {
        return User::factory()->create(['is_planner' => true]);
    }

    private function makeNotification(): LessonScheduledForApproval
    {
        return new LessonScheduledForApproval(
            activityId: 1,
            teacherName: 'Prof. Carlos',
            activityTitle: 'Álgebra',
            scheduledAt: '14/08/2026 10:00',
        );
    }

    public function test_notify_users_persiste_notificaciones_db(): void
    {
        $recipient = $this->makeRecipient();

        app(NotificationService::class)->notifyUsers([$recipient], $this->makeNotification());

        $this->assertSame(1, $recipient->notifications()->count());
        $data = $recipient->notifications()->first()->data;
        $this->assertSame('lesson_scheduled', $data['type']);
        $this->assertSame(1, $data['activity_id']);
        $this->assertStringContainsString('Prof. Carlos', $data['message']);
    }

    public function test_notify_users_emite_broadcast_por_destinatario(): void
    {
        Event::fake([NotificationReceived::class]);

        $recipient = $this->makeRecipient();

        app(NotificationService::class)->notifyUsers([$recipient], $this->makeNotification());

        Event::assertDispatched(NotificationReceived::class, function ($event) use ($recipient) {
            return $event->userId === $recipient->id
                && ($event->payload['activity_id'] ?? null) === 1;
        });
    }

    public function test_notify_users_invalida_cache_de_no_leidas_por_destinatario(): void
    {
        $recipient = $this->makeRecipient();

        Cache::put(NotificationService::UNREAD_PREFIX.$recipient->id, 0, 60);
        $this->assertSame(0, Cache::get(NotificationService::UNREAD_PREFIX.$recipient->id));

        app(NotificationService::class)->notifyUsers([$recipient], $this->makeNotification());

        $this->assertNull(Cache::get(NotificationService::UNREAD_PREFIX.$recipient->id));
    }

    public function test_unread_count_for_devuelve_conteo_fresco_y_cacheado(): void
    {
        $recipient = $this->makeRecipient();
        $service = app(NotificationService::class);

        $service->notifyUsers([$recipient], $this->makeNotification());

        $this->assertSame(1, $service->unreadCountFor($recipient->id));

        // Segunda llamada: cacheado (no se vuelve a la DB).
        Cache::put(NotificationService::UNREAD_PREFIX.$recipient->id, 5, 60);
        $this->assertSame(5, $service->unreadCountFor($recipient->id));
    }

    public function test_invalidate_unread_count_borra_cache(): void
    {
        $recipient = $this->makeRecipient();
        $service = app(NotificationService::class);

        Cache::put(NotificationService::UNREAD_PREFIX.$recipient->id, 3, 60);

        $service->invalidateUnreadCount($recipient->id);

        $this->assertNull(Cache::get(NotificationService::UNREAD_PREFIX.$recipient->id));
    }
}
