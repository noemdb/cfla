<?php

namespace Tests\Feature\App\Notifications;

use App\Http\Middleware\MarkNotificationsAsRead;
use App\Models\User;
use App\Notifications\ActivityCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Marcar como leídas al visitar el destino (ítem 12).
 *
 * `config/notifications.php#auto_read` declara qué tipos de aviso se dan por
 * vistos en cada ruta, y el middleware `notifications.auto-read` lo aplica en
 * la petición GET inicial (no en cada render de Livewire).
 */
class NotificationAutoReadTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Añade una entrada a `auto_read` por nombre de ruta EXACTO: los nombres
     * llevan puntos y `config(['notifications.auto_read.x.y'])` los trataría
     * como anidamiento.
     *
     * @param  array<int, string>  $types
     */
    private function mapAutoRead(string $routeName, array $types): void
    {
        config(['notifications.auto_read' => array_merge(
            (array) config('notifications.auto_read', []),
            [$routeName => $types]
        )]);
    }

    private function makeNotification(User $user, string $type, string $id, bool $read = false): void
    {
        $user->notifications()->create([
            'id' => $id,
            'type' => ActivityCreatedNotification::class,
            'data' => [
                'type' => $type,
                'message' => 'aviso '.$type,
                'url' => route('app.notifications.index'),
            ],
            'read_at' => $read ? now() : null,
            'created_at' => now(),
        ]);
    }

    public function test_al_entrar_en_la_ruta_se_marcan_los_tipos_configurados(): void
    {
        Route::middleware(['web', 'auth', 'notifications.auto-read'])
            ->get('/test-auto-read', fn () => 'ok')
            ->name('test.auto-read');

        $this->mapAutoRead('test.auto-read', ['activity_created', 'diag_question_updated']);

        $user = User::factory()->create(['is_planner' => true]);
        $otro = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'activity_created', 'n-1');
        $this->makeNotification($user, 'diag_question_updated', 'n-2');
        $this->makeNotification($user, 'peducativo_updated', 'n-3');   // no configurado
        $this->makeNotification($otro, 'activity_created', 'n-4');    // de otro usuario

        $this->actingAs($user)->get('/test-auto-read')->assertOk();

        $this->assertNotNull($user->notifications()->find('n-1')->read_at);
        $this->assertNotNull($user->notifications()->find('n-2')->read_at);
        $this->assertNull($user->notifications()->find('n-3')->read_at, 'un tipo no configurado no se toca');
        $this->assertNull($otro->notifications()->find('n-4')->read_at, 'nunca se toca a otro usuario');
    }

    public function test_una_ruta_sin_configuracion_no_marca_nada(): void
    {
        Route::middleware(['web', 'auth', 'notifications.auto-read'])
            ->get('/test-sin-mapa', fn () => 'ok')
            ->name('test.sin-mapa');

        $user = User::factory()->create(['is_planner' => true]);
        $this->makeNotification($user, 'activity_created', 'n-1');

        $this->actingAs($user)->get('/test-sin-mapa')->assertOk();

        $this->assertNull($user->notifications()->find('n-1')->read_at);
    }

    public function test_sin_sesion_no_ocurre_nada(): void
    {
        Route::middleware(['notifications.auto-read'])
            ->get('/test-sin-usuario', fn () => 'ok')
            ->name('test.sin-usuario');

        $this->mapAutoRead('test.sin-usuario', ['activity_created']);

        $user = User::factory()->create();
        $this->makeNotification($user, 'activity_created', 'n-1');

        $this->get('/test-sin-usuario')->assertOk();

        $this->assertNull($user->notifications()->find('n-1')->read_at);
    }

    public function test_el_middleware_no_falla_si_la_ruta_no_tiene_nombre(): void
    {
        $middleware = new MarkNotificationsAsRead;

        $response = $middleware->handle(
            \Illuminate\Http\Request::create('/ruta-sin-nombre', 'GET'),
            fn () => new \Illuminate\Http\Response('ok')
        );

        $this->assertSame('ok', $response->getContent());
    }

    /**
     * Guarda contra erratas: un nombre de ruta mal escrito en la config dejaría
     * avisos sin marcar para siempre, y no se ve hasta que un aviso se atasca.
     */
    public function test_todas_las_rutas_de_auto_read_existen(): void
    {
        $routes = collect(Route::getRoutes()->getRoutes())->map->getName()->filter()->all();

        foreach (config('notifications.auto_read') as $routeName => $types) {
            $this->assertContains(
                $routeName,
                $routes,
                "config/notifications.php#auto_read referencia la ruta inexistente '{$routeName}'"
            );
            $this->assertNotEmpty($types, "la ruta '{$routeName}' no declara ningún tipo");
            $this->assertSame(
                array_values(array_unique($types)),
                array_values($types),
                "la ruta '{$routeName}' repite tipos en auto_read"
            );
        }
    }

    public function test_las_rutas_de_actividades_lo_tienen_las_cuatro_variantes(): void
    {
        foreach ([
            'app.planning.activities.index',
            'app.coordinacion.activities',
            'app.leadership.activities',
            'app.director.activities',
        ] as $routeName) {
            $this->assertContains(
                'activity_created',
                (array) ((array) config('notifications.auto_read'))[$routeName],
                "'{$routeName}' debe marcar activity_created"
            );
        }
    }

    public function test_la_config_no_tiene_rutas_duplicadas(): void
    {
        // Una clave repetida en el array PHP pisa a la anterior en silencio y
        // se perderían tipos: se comprueba contra el archivo.
        $contents = (string) file_get_contents(config_path('notifications.php'));
        preg_match_all("/'(app\.[a-z0-9._]+)' =>/i", $contents, $matches);

        $this->assertSame(
            array_values(array_unique($matches[1])),
            array_values($matches[1]),
            'hay rutas repetidas en auto_read: la segunda machaca a la primera'
        );
    }
}
