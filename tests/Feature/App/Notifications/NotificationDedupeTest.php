<?php

namespace Tests\Feature\App\Notifications;

use App\Models\User;
use App\Notifications\ActivityCreatedNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Anti-spam genérico (`NotificationService::shouldNotify`/`filterByDedupe`).
 *
 * Reglas: solo cuentan los avisos SIN LEER (si el usuario ya abrió el aviso,
 * un nuevo registro del mismo asunto sí merece volver a avisarle); el
 * agrupamiento es por tipo + asunto; y la ventana es por acción.
 */
class NotificationDedupeTest extends TestCase
{
    use DatabaseTransactions;

    private function service(): NotificationService
    {
        return app(NotificationService::class);
    }

    private function notify(User $user, string $type, array $data = [], bool $read = false): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => ActivityCreatedNotification::class,
            'data' => array_merge(['type' => $type, 'message' => 'aviso'], $data),
            'read_at' => $read ? now() : null,
            'created_at' => now(),
        ]);
    }

    public function test_avisa_si_no_hay_aviso_previo(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($this->service()->shouldNotify($user, 'activity_created'));
    }

    public function test_omite_si_ya_hay_un_aviso_sin_leer_del_mismo_asunto(): void
    {
        $user = User::factory()->create();
        $this->notify($user, 'activity_created', ['pevaluacion_id' => 10]);

        $this->assertFalse($this->service()->shouldNotify($user, 'activity_created', ['pevaluacion_id' => 10]));
    }

    public function test_avisa_otro_asunto_del_mismo_tipo(): void
    {
        $user = User::factory()->create();
        $this->notify($user, 'activity_created', ['pevaluacion_id' => 10]);

        $this->assertTrue($this->service()->shouldNotify($user, 'activity_created', ['pevaluacion_id' => 11]));
    }

    public function test_avisa_otro_tipo_del_mismo_asunto(): void
    {
        $user = User::factory()->create();
        $this->notify($user, 'peducativo_updated', ['pevaluacion_id' => 10]);

        $this->assertTrue($this->service()->shouldNotify($user, 'activity_created', ['pevaluacion_id' => 10]));
    }

    public function test_un_aviso_ya_leido_no_bloquea(): void
    {
        $user = User::factory()->create();
        $this->notify($user, 'activity_created', ['pevaluacion_id' => 10], read: true);

        $this->assertTrue($this->service()->shouldNotify($user, 'activity_created', ['pevaluacion_id' => 10]));
    }

    public function test_respeta_la_ventana_horaria(): void
    {
        $user = User::factory()->create();
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => ActivityCreatedNotification::class,
            'data' => ['type' => 'activity_created', 'pevaluacion_id' => 10],
            'read_at' => null,
            'created_at' => now()->subHours(3),
        ]);

        // Ventana de 1h: el aviso tiene 3h, no bloquea.
        $this->assertTrue($this->service()->shouldNotify($user, 'activity_created', ['pevaluacion_id' => 10], 1));
        // Ventana de 24h: sí bloquea.
        $this->assertFalse($this->service()->shouldNotify($user, 'activity_created', ['pevaluacion_id' => 10], 24));
    }

    public function test_el_filtro_por_lote_deja_solo_a_quien_no_tiene_aviso(): void
    {
        $conAviso = User::factory()->create();
        $sinAviso = User::factory()->create();
        $this->notify($conAviso, 'peducativo_updated', ['peducativo_id' => 7]);

        $result = $this->service()->filterByDedupe([$conAviso, $sinAviso], 'peducativo_updated', ['peducativo_id' => 7]);

        $this->assertCount(1, $result);
        $this->assertSame($sinAviso->id, $result->first()->id);
    }

    public function test_es_por_usuario_no_global(): void
    {
        $uno = User::factory()->create();
        $otro = User::factory()->create();
        $this->notify($uno, 'activity_created', ['pevaluacion_id' => 10]);

        $this->assertFalse($this->service()->shouldNotify($uno, 'activity_created', ['pevaluacion_id' => 10]));
        $this->assertTrue($this->service()->shouldNotify($otro, 'activity_created', ['pevaluacion_id' => 10]));
    }
}
