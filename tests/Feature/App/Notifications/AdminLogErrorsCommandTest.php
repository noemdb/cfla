<?php

namespace Tests\Feature\App\Notifications;

use App\Models\User;
use App\Notifications\AdminLogErrorNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Vigilante de errores del log (`admin:notify-log-errors`).
 *
 * Lee solo los bytes nuevos del archivo (offset persistido en caché), avisa
 * únicamente del nivel pedido (ERROR por defecto) y usa la huella estable del
 * parser como anti-spam + idempotencia.
 */
class AdminLogErrorsCommandTest extends TestCase
{
    use DatabaseTransactions;

    private const FILE = '_test_admin_errors.log';

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true, 'is_active' => 'enable']);

        config([
            'notifications.superadmin_id' => $this->admin->id,
            'notifications.superadmin_types' => ['laravel_log_error'],
        ]);

        $this->writeLog('');
        Cache::forget($this->stateKey());
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('logs/'.self::FILE));
        Cache::forget($this->stateKey());

        parent::tearDown();
    }

    private function stateKey(): string
    {
        return 'admin_log_errors.offset.'.md5(storage_path('logs/'.self::FILE));
    }

    private function writeLog(string $content, bool $append = false): void
    {
        $path = storage_path('logs/'.self::FILE);

        if ($append) {
            file_put_contents($path, $content, FILE_APPEND);
        } else {
            file_put_contents($path, $content);
        }

        clearstatcache(true, $path);
    }

    private function sampleLog(): string
    {
        return implode("\n", [
            '[2026-09-25 10:00:00] local.INFO: Arranque del sistema',
            '[2026-09-25 10:01:00] local.WARNING: Cola lenta {"jobs":12}',
            '[2026-09-25 10:02:00] local.ERROR: Falló el pago {"order":7}',
            '[2026-09-25 10:03:00] local.ERROR: Excepción no capturada',
            'Stack trace de ejemplo línea 1',
            'línea 2 del trace',
            '',
        ])."\n";
    }

    public function test_solo_avisa_las_entradas_de_nivel_error(): void
    {
        $this->writeLog($this->sampleLog());

        $this->artisan('admin:notify-log-errors', [
            '--file' => self::FILE,
            '--from-start' => true,
        ])->assertExitCode(0);

        $rows = $this->admin->notifications()->get();

        $this->assertSame(2, $rows->count(), 'solo las 2 entradas ERROR, no INFO ni WARNING');
        $this->assertSame(
            ['laravel_log_error', 'laravel_log_error'],
            $rows->pluck('data.type')->all()
        );
        $this->assertTrue(
            $rows->every(fn ($row) => $row->type === AdminLogErrorNotification::class
                && str_contains($row->data['url'], '/admin/logs'))
        );
    }

    public function test_la_segunda_pasada_no_duplica(): void
    {
        $this->writeLog($this->sampleLog());

        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE, '--from-start' => true])
            ->assertExitCode(0);

        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE])
            ->assertExitCode(0);

        $this->assertSame(2, $this->admin->notifications()->count());
    }

    public function test_solo_los_bytes_nuevos_generan_avisos(): void
    {
        $this->writeLog($this->sampleLog());

        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE, '--from-start' => true])
            ->assertExitCode(0);

        $this->writeLog("[2026-09-25 11:00:00] local.ERROR: Nuevo fallo grave\n", append: true);

        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE])
            ->assertExitCode(0);

        $this->assertSame(3, $this->admin->notifications()->count());
    }

    public function test_la_primera_ejecucion_no_inunda_con_el_historico(): void
    {
        $this->writeLog($this->sampleLog());

        // Sin estado previo ni banderas: arranca desde el final, sin avisar.
        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE])
            ->assertExitCode(0);

        $this->assertSame(0, $this->admin->notifications()->count());
    }

    public function test_el_dry_run_no_avisa_ni_avanza_el_offset(): void
    {
        $this->writeLog($this->sampleLog());

        $this->artisan('admin:notify-log-errors', [
            '--file' => self::FILE,
            '--from-start' => true,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('dry-run')
            ->assertExitCode(0);

        $this->assertSame(0, $this->admin->notifications()->count());
        $this->assertNull(Cache::get($this->stateKey()), 'el dry-run no debe guardar offset');

        // Una pasada real después sí procesa todo desde el inicio.
        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE, '--from-start' => true])
            ->assertExitCode(0);

        $this->assertSame(2, $this->admin->notifications()->count());
    }

    public function test_el_reintento_no_duplica_gracias_a_la_idempotencia(): void
    {
        $this->writeLog($this->sampleLog());

        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE, '--from-start' => true])
            ->assertExitCode(0);

        // Simula un reintento con el mismo tramo (p. ej. el comando falló a
        // mitad): se fuerza el reprocesado con --since y la BD lo frena.
        $this->artisan('admin:notify-log-errors', [
            '--file' => self::FILE,
            '--since' => '2026-09-25 00:00:00',
        ])->assertExitCode(0);

        $this->assertSame(2, $this->admin->notifications()->count());
    }

    public function test_con_otro_nivel_vigila_ese_nivel(): void
    {
        $this->writeLog($this->sampleLog());

        $this->artisan('admin:notify-log-errors', [
            '--file' => self::FILE,
            '--from-start' => true,
            '--level' => 'WARNING',
        ])->assertExitCode(0);

        $this->assertSame(1, $this->admin->notifications()->count());
    }

    public function test_reset_olvida_el_offset(): void
    {
        $this->writeLog($this->sampleLog());

        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE, '--from-start' => true])
            ->assertExitCode(0);

        $this->assertNotNull(Cache::get($this->stateKey()));

        // --reset olvida el offset y relee desde el inicio; como los avisos ya
        // existen (no leídos, misma huella), la idempotencia los frena y al
        // final queda guardado el offset nuevo (EOF), no un estado vacío.
        $this->artisan('admin:notify-log-errors', ['--file' => self::FILE, '--reset' => true])
            ->assertExitCode(0);

        $this->assertSame(2, $this->admin->notifications()->count(), 'reset relee pero no re-avisa');
        $this->assertSame(
            (int) filesize(storage_path('logs/'.self::FILE)),
            (int) (Cache::get($this->stateKey())['offset'] ?? -1),
            'tras la pasada el offset apunta al final'
        );
    }
}
