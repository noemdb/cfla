<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Broadcast;
use ReflectionProperty;
use Tests\TestCase;

/**
 * Canal de presencia `presence-app.sessions` (dashboard /admin).
 *
 * Es lo que hace que el indicador y el chart sigan a los usuarios conectados
 * en tiempo real: Reverb mantiene la cuenta de miembros y empuja
 * member_added/member_removed, sin polling ni scheduler.
 *
 * Se prueba contra el endpoint real de autorización (`/broadcasting/auth`),
 * que es lo que consume el cliente para unirse al canal: de ahí sale el
 * `channel_data` con los datos de cada miembro.
 */
class ActiveSessionsPresenceChannelTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * phpunit.xml deja `BROADCAST_CONNECTION=null`; el broadcaster `null`
     * devuelve una respuesta vacía y no se puede comprobar el
     * `channel_data` que consume el cliente. Se fuerza `reverb` (misma vía
     * Pusher que usa producción, sin abrir ninguna conexión: la autorización
     * del canal es local).
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb.driver' => 'reverb',
            'broadcasting.connections.reverb.key' => config('reverb.apps.apps.0.key'),
            'broadcasting.connections.reverb.secret' => config('reverb.apps.apps.0.secret'),
            'broadcasting.connections.reverb.app_id' => config('reverb.apps.apps.0.app_id'),
        ]);

        // `Broadcast::channel()` se ejecuta en el boot sobre el broadcasTER del
        // driver que hubiera entonces (aquí, `null`), así que al cambiar el
        // driver hay que volver a registrar los callbacks: es exactamente lo
        // que hace BroadcastServiceProvider al arrancar.
        require base_path('routes/channels.php');
    }

    /**
     * Invoca el callback del canal registrado para el usuario dado y devuelve
     * su resultado: el array de datos del miembro (canal de presencia) o
     * `false` si no tiene acceso.
     *
     * Se prueba el callback y no la firma que devuelve Pusher porque el
     * `channel_data` viaja dentro del `auth` con firma HMAC: lo que importa
     * para el cliente es lo que el servidor autoriza como miembro.
     */
    private function channelResult(string $channel, User $user)
    {
        $broadcaster = Broadcast::driver();

        $property = new ReflectionProperty($broadcaster, 'channels');
        $property->setAccessible(true);

        $callback = ($property->getValue($broadcaster))[$channel] ?? null;

        $this->assertNotNull($callback, "el canal '{$channel}' no está registrado");

        return $callback($user);
    }

    /**
     * Autoriza el canal de presencia vía HTTP (lo que hace el navegador).
     *
     * @return array{0: int} [status]
     */
    private function authorizePresence(User $user): array
    {
        return [$this->actingAs($user)->postJson('/broadcasting/auth', [
            'channel_name' => 'presence-app.sessions',
            'socket_id' => '1234.5678',
        ])->status()];
    }

    public function test_un_admin_recibe_sus_datos_como_miembro_del_canal(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => 'enable']);

        [$status] = $this->authorizePresence($admin);
        $this->assertSame(200, $status, 'el canal de presencia debe autorizarse');

        // Devolver un ARRAY (no `true`) es lo que convierte el canal en de
        // presencia: si devolviera true, Reverb no llevaría la cuenta.
        $info = $this->channelResult('app.sessions', $admin);

        $this->assertIsArray($info);
        $this->assertSame((int) $admin->id, $info['id']);
        $this->assertSame($admin->username, $info['username']);
        $this->assertSame('Administrador', $info['role']);
    }

    public function test_los_roles_priorizados_tambien_publican_nombre_y_rol(): void
    {
        $cases = [
            [['is_planner' => true], 'Planificación'],
            [['is_coordinacion' => true], 'Coordinación'],
            [['is_leadership' => true], 'Jefe de Área'],
            [['is_profesor' => true], 'Profesor'],
        ];

        foreach ($cases as [$flags, $role]) {
            $user = User::factory()->create($flags + ['is_active' => 'enable']);

            [$status] = $this->authorizePresence($user);
            $this->assertSame(200, $status);

            $info = $this->channelResult('app.sessions', $user);
            $this->assertSame($user->username, $info['username'] ?? null, 'un rol priorizado debe publicar su nombre');
            $this->assertSame($role, $info['role'] ?? null);
        }
    }

    public function test_un_estudiante_solo_publica_su_id(): void
    {
        // Privacidad: el listado de "quién está en línea" no se le expone al
        // alumnado; solo se le permite contar.
        $student = User::factory()->create(['is_student' => true, 'is_active' => 'enable']);

        [$status] = $this->authorizePresence($student);
        $this->assertSame(200, $status);

        $info = $this->channelResult('app.sessions', $student);

        $this->assertSame((int) $student->id, $info['id'] ?? null);
        $this->assertArrayNotHasKey('username', $info);
        $this->assertArrayNotHasKey('fullname', $info);
        $this->assertArrayNotHasKey('role', $info);
    }

    public function test_un_usuario_dado_de_baja_no_entra_al_canal(): void
    {
        $inactive = User::factory()->create(['is_admin' => true, 'is_active' => 'disable']);

        [$status] = $this->authorizePresence($inactive);

        $this->assertSame(403, $status, 'un usuario disable no debe sentirse en línea');
    }

    public function test_un_visitante_no_puede_autorizar_el_canal(): void
    {
        $this->postJson('/broadcasting/auth', [
            'channel_name' => 'presence-app.sessions',
            'socket_id' => '1234.5678',
        ])->assertStatus(403);
    }

    public function test_el_canal_privado_de_sesiones_sigue_siendo_para_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => 'enable']);
        $diagnostic = User::factory()->create(['is_diagnostic' => true, 'is_active' => 'enable']);
        $student = User::factory()->create(['is_student' => true, 'is_active' => 'enable']);

        $request = fn (User $user) => $this->actingAs($user)->postJson('/broadcasting/auth', [
            'channel_name' => 'private-admin.sessions',
            'socket_id' => '1234.5678',
        ]);

        $this->assertSame(200, $request($admin)->status());
        $this->assertSame(200, $request($diagnostic)->status());
        $this->assertSame(403, $request($student)->status());
    }
}
