<?php

namespace Tests\Feature\App\Notifications;

use App\Models\User;
use App\Notifications\ActivityCreatedNotification;
use App\Notifications\BinnacleBacklogNotification;
use App\Notifications\DiagQuestionNotification;
use App\Notifications\ReverbTestNotification;
use App\Services\NotificationSampleFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Tests\TestCase;

class NotificationSampleFactoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Ejecuta `reverb:test` con la conexión de broadcast puesta a `reverb`:
     * phpunit.xml usa `BROADCAST_CONNECTION=null`, que haría fallar el paso 1
     * de configuración y devolvería exit 1 aunque la notificación se envíe.
     */
    private function reverbTest(array $options = [])
    {
        config([
            'broadcasting.default' => 'reverb',
            'reverb.servers.reverb.host' => '127.0.0.1',
            'reverb.servers.reverb.port' => 8090,
        ]);

        return $this->artisan('reverb:test', $options);
    }

    public function test_resuelve_por_alias_nombre_de_clase_y_fqcn(): void
    {
        $this->assertSame(
            ActivityCreatedNotification::class,
            NotificationSampleFactory::resolveClass('activity_created')
        );
        $this->assertSame(
            ActivityCreatedNotification::class,
            NotificationSampleFactory::resolveClass('ActivityCreatedNotification')
        );
        $this->assertSame(
            ActivityCreatedNotification::class,
            NotificationSampleFactory::resolveClass(ActivityCreatedNotification::class)
        );
        // Tolerante a mayúsculas, guiones y guiones bajos.
        $this->assertSame(
            ActivityCreatedNotification::class,
            NotificationSampleFactory::resolveClass('activity-created')
        );
        $this->assertSame(
            DiagQuestionNotification::class,
            NotificationSampleFactory::resolveClass('diag_question')
        );
    }

    /**
     * Sin comillas, bash se come los backslashes de la FQCN y el comando
     * recibe `AppNotificationsDiagQuestionNotification`.
     */
    public function test_resuelve_fqcn_a_la_que_el_shell_le_comio_los_backslashes(): void
    {
        $this->assertSame(
            DiagQuestionNotification::class,
            NotificationSampleFactory::resolveClass('AppNotificationsDiagQuestionNotification')
        );
        $this->assertSame(
            DiagQuestionNotification::class,
            NotificationSampleFactory::resolveClass('App\\Notifications\\DiagQuestionNotification')
        );
        $this->assertInstanceOf(
            DiagQuestionNotification::class,
            NotificationSampleFactory::make('AppNotificationsDiagQuestionNotification')
        );
    }

    public function test_rechaza_un_tipo_desconocido_con_mensaje_util(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/--list-notifications/');

        NotificationSampleFactory::resolveClass('no_existe');
    }

    public function test_rechaza_notificaciones_que_no_usan_canal_database(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/canal "database"/');

        NotificationSampleFactory::make('binnacle_backlog');
    }

    public function test_todas_las_muestras_del_catalogo_son_una_clase_de_notificacion(): void
    {
        foreach (NotificationSampleFactory::catalogue() as $alias => $meta) {
            $this->assertTrue(
                class_exists($meta['class']),
                "El alias '{$alias}' apunta a una clase inexistente: {$meta['class']}"
            );
            $this->assertTrue(
                is_subclass_of($meta['class'], \Illuminate\Notifications\Notification::class),
                "El alias '{$alias}' no es una Notification: {$meta['class']}"
            );
        }
    }

    public function test_make_devuelve_instancias_creables_para_los_tipos_de_la_campana(): void
    {
        $catalogue = NotificationSampleFactory::catalogue();

        $database = array_filter(
            $catalogue,
            fn (array $meta) => str_contains($meta['channel'], 'database')
        );

        $this->assertNotEmpty($database);
        $this->assertArrayHasKey('activity_created', $database);

        foreach ($database as $alias => $meta) {
            $notification = NotificationSampleFactory::make($alias, 'usuario-prueba');

            $this->assertInstanceOf($meta['class'], $notification, "El alias '{$alias}' no construyó su clase");

            $payload = method_exists($notification, 'toDatabase')
                ? (array) $notification->toDatabase(new \App\Models\User)
                : (array) $notification->toArray(new \App\Models\User);

            // Lo que la campana necesita para pintar el item. Todas las
            // notificaciones del catálogo emiten ya la forma canónica
            // `type` (las que usaban `event_type` se migraron en
            // TimetableChanged/SubstituteAssigned).
            $this->assertArrayHasKey(
                'type',
                $payload,
                "El alias '{$alias}' no aporta 'type' (usaría event_type y se pintaría como genérica)"
            );
            $this->assertArrayHasKey('message', $payload, "El alias '{$alias}' no aporta 'message'");
            $this->assertNotEmpty($payload['message'], "El alias '{$alias}' trae 'message' vacío");
            $this->assertNotSame(
                'generic',
                $payload['type'],
                "El alias '{$alias}' se pintaría como genérica en la campana"
            );
        }
    }

    public function test_make_usa_el_default_de_reverb_test(): void
    {
        $this->assertInstanceOf(
            ReverbTestNotification::class,
            NotificationSampleFactory::make('reverb_test', 'ccortez23')
        );
    }

    public function test_la_muestra_de_activity_created_trae_ruta_real_y_datos_de_presentacion(): void
    {
        $notification = NotificationSampleFactory::make('activity_created');
        $payload = (array) $notification->toDatabase(new \App\Models\User);

        $this->assertSame('activity_created', $payload['type']);
        $this->assertSame(route('app.leadership.activities'), $payload['url']);
        $this->assertNotEmpty($payload['message']);
    }

    public function test_comando_lista_los_tipos_disponibles(): void
    {
        $this->reverbTest(['--list-notifications' => true])
            ->assertExitCode(0);
    }

    public function test_comando_envia_el_tipo_solicitado_y_lo_persiste(): void
    {
        $user = User::factory()->create(['is_planner' => true]);

        $before = $user->notifications()->count();

        $this->reverbTest([
            '--notify' => $user->username,
            '--notification' => 'activity_created',
            '--only-notify' => true,
        ])->assertExitCode(0);

        $this->assertSame($before + 1, $user->notifications()->count());

        $last = $user->notifications()->orderByDesc('created_at')->first();
        $this->assertSame(ActivityCreatedNotification::class, $last->type);
        $this->assertSame('activity_created', $last->data['type']);
    }

    public function test_comando_acepta_el_nombre_de_la_clase(): void
    {
        $user = User::factory()->create();

        $this->reverbTest([
            '--notify' => $user->username,
            '--notification' => 'DiagQuestionNotification',
            '--only-notify' => true,
        ])->assertExitCode(0);

        $this->assertSame(
            DiagQuestionNotification::class,
            $user->notifications()->orderByDesc('created_at')->first()->type
        );
    }

    public function test_comando_falla_con_tipo_desconocido_sin_escribir_nada(): void
    {
        $user = User::factory()->create();
        $before = $user->notifications()->count();

        $this->reverbTest([
            '--notify' => $user->username,
            '--notification' => 'no_existe',
            '--only-notify' => true,
        ])->assertExitCode(1);

        $this->assertSame($before, $user->notifications()->count());
    }

    public function test_comando_falla_con_tipo_que_no_usa_database(): void
    {
        $user = User::factory()->create();
        $before = $user->notifications()->count();

        $this->reverbTest([
            '--notify' => $user->username,
            '--notification' => BinnacleBacklogNotification::class,
            '--only-notify' => true,
        ])->assertExitCode(1);

        $this->assertSame($before, $user->notifications()->count());
    }

    public function test_comando_falla_con_usuario_inexistente(): void
    {
        $this->reverbTest([
            '--notify' => 'usuario-que-no-existe',
            '--notification' => 'peducativo',
            '--only-notify' => true,
        ])->assertExitCode(1);
    }
}
