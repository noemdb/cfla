<?php

namespace Tests\Feature;

use App\Events\ActiveSessionsUpdated;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ActiveSessionsBroadcastTest extends TestCase
{
    use DatabaseTransactions;

    public function test_event_broadcasts_on_private_admin_sessions_channel(): void
    {
        $event = new ActiveSessionsUpdated([
            'active_sessions' => 3,
            'authenticated_online' => 1,
            'timestamp' => '12:00:00',
        ]);

        $channel = $event->broadcastOn();

        $this->assertInstanceOf(PrivateChannel::class, $channel);
        $this->assertSame('private-admin.sessions', $channel->name);
        $this->assertSame('sessions.updated', $event->broadcastAs());
        $this->assertSame([
            'active_sessions' => 3,
            'authenticated_online' => 1,
            'timestamp' => '12:00:00',
        ], $event->broadcastWith());
    }

    public function test_current_payload_has_expected_keys(): void
    {
        $payload = ActiveSessionsUpdated::currentPayload();

        $this->assertArrayHasKey('active_sessions', $payload);
        $this->assertArrayHasKey('authenticated_online', $payload);
        $this->assertArrayHasKey('timestamp', $payload);
        $this->assertIsInt($payload['active_sessions']);
        $this->assertIsInt($payload['authenticated_online']);
    }

    public function test_command_dispatches_event(): void
    {
        Event::fake([ActiveSessionsUpdated::class]);

        $this->artisan('admin:broadcast-sessions')->assertSuccessful();

        Event::assertDispatched(ActiveSessionsUpdated::class);
    }

    /**
     * La firma de auth del broadcaster `null` (phpunit) es no-op, así que
     * estos tests usan el driver `reverb`: firma HMAC local sin red y sí
     * evalúa los callbacks de autorización del canal.
     */
    private function withReverbBroadcaster(): void
    {
        config()->set('broadcasting.default', 'reverb');
        // Los canales se registran en la instancia del driver por defecto
        // al bootear; al cambiar de driver hay que re-registrarlos.
        require base_path('routes/channels.php');
    }

    public function test_admin_can_subscribe_to_private_channel(): void
    {
        $this->withReverbBroadcaster();
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post('/broadcasting/auth', [
            'channel_name' => 'private-admin.sessions',
            'socket_id' => '123.456',
        ]);

        $response->assertOk();
        $this->assertArrayHasKey('auth', $response->json());
    }

    public function test_diagnostic_can_subscribe_to_private_channel(): void
    {
        $this->withReverbBroadcaster();
        $diagnostic = User::factory()->create(['is_diagnostic' => true]);

        $response = $this->actingAs($diagnostic)->post('/broadcasting/auth', [
            'channel_name' => 'private-admin.sessions',
            'socket_id' => '123.456',
        ]);

        $response->assertOk();
    }

    public function test_standard_user_cannot_subscribe_to_private_channel(): void
    {
        $this->withReverbBroadcaster();
        $user = User::factory()->create([
            'is_admin' => false,
            'is_diagnostic' => false,
        ]);

        $response = $this->actingAs($user)->post('/broadcasting/auth', [
            'channel_name' => 'private-admin.sessions',
            'socket_id' => '123.456',
        ]);

        $response->assertForbidden();
    }

    public function test_dashboard_renders_websocket_subscription_without_polling(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee("Echo.private('admin.sessions')", false);
        $response->assertSee('sessions.updated', false);
        $response->assertDontSee('admin.active-sessions', false);
        $response->assertDontSee('setInterval(refreshSessions', false);
    }
}
