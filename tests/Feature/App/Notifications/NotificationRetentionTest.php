<?php

namespace Tests\Feature\App\Notifications;

use App\Models\User;
use App\Notifications\ActivityCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Retención de la tabla `notifications` (ítem 14).
 *
 * Se podan las LEÍDAS más antiguas que el umbral; las no leídas son la bandeja
 * de trabajo del usuario y solo se tocan si se piden explícitamente.
 */
class NotificationRetentionTest extends TestCase
{
    use DatabaseTransactions;

    private function makeNotification(User $user, string $id, ?int $ageDays, bool $read): void
    {
        $user->notifications()->create([
            'id' => $id,
            'type' => ActivityCreatedNotification::class,
            'data' => ['type' => 'activity_created', 'message' => 'aviso '.$id, 'url' => '/'],
            'read_at' => $read ? now()->subDays(max(1, $ageDays)) : null,
            'created_at' => now()->subDays($ageDays ?? 0),
        ]);
    }

    public function test_poda_las_leidas_antiguas_y_conserva_el_resto(): void
    {
        $user = User::factory()->create();
        $this->makeNotification($user, 'vieja-leida', 200, true);
        $this->makeNotification($user, 'reciente-leida', 5, true);
        $this->makeNotification($user, 'vieja-sin-leer', 200, false);

        $this->artisan('notifications:prune', ['--days' => 90])->assertExitCode(0);

        $this->assertNull($user->notifications()->find('vieja-leida'), 'la leída caducada se poda');
        $this->assertNotNull($user->notifications()->find('reciente-leida'), 'la leída reciente se conserva');
        $this->assertNotNull($user->notifications()->find('vieja-sin-leer'), 'la no leída nunca se toca por defecto');
    }

    public function test_el_dry_run_no_borra_nada(): void
    {
        $user = User::factory()->create();
        $this->makeNotification($user, 'vieja-leida', 200, true);

        $this->artisan('notifications:prune', ['--days' => 90, '--dry-run' => true])
            ->expectsOutputToContain('Leídas anteriores a')
            ->assertExitCode(0);

        $this->assertNotNull($user->notifications()->find('vieja-leida'));
    }

    public function test_las_no_leidas_solo_se_podan_si_se_piden(): void
    {
        $user = User::factory()->create();
        $this->makeNotification($user, 'vieja-sin-leer', 200, false);

        $this->artisan('notifications:prune', ['--days' => 90, '--purge-unread-days' => 90])->assertExitCode(0);

        $this->assertNull($user->notifications()->find('vieja-sin-leer'));
    }

    public function test_con_dias_cero_no_se_poda_nada(): void
    {
        $user = User::factory()->create();
        $this->makeNotification($user, 'vieja-leida', 200, true);

        $this->artisan('notifications:prune', ['--days' => 0])->assertExitCode(0);

        $this->assertNotNull($user->notifications()->find('vieja-leida'));
    }

    public function test_el_umbral_por_defecto_viene_de_la_configuracion(): void
    {
        config(['notifications.retention_days' => 120]);

        $user = User::factory()->create();
        $this->makeNotification($user, 'de-100-dias', 100, true);
        $this->makeNotification($user, 'de-130-dias', 130, true);

        $this->artisan('notifications:prune')->assertExitCode(0);

        $this->assertNotNull($user->notifications()->find('de-100-dias'), '100 días no supera el umbral de 120');
        $this->assertNull($user->notifications()->find('de-130-dias'));
    }

    public function test_la_poda_por_lotes_no_deja_restos(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 7; $i++) {
            $this->makeNotification($user, 'caducada-'.$i, 200, true);
        }

        $this->artisan('notifications:prune', ['--days' => 90, '--chunk' => 2])->assertExitCode(0);

        $this->assertSame(
            0,
            DB::table('notifications')->where('id', 'like', 'caducada-%')->count(),
            'con lotes de 2 no debe quedar ninguna fila fuera'
        );
    }
}
