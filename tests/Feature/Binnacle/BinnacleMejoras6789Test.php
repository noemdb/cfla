<?php

namespace Tests\Feature\Binnacle;

use App\Models\BinnacleEntry;
use App\Models\User;
use App\Notifications\BinnacleAnchorNotification;
use App\Services\Binnacle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Spec BINNACLE-001 — Mejoras #6 (ancla externa), #8 (gate de particionado)
 * y #9 (revisión de retención / binnacle:archive --dry-run).
 */
class BinnacleMejoras6789Test extends TestCase
{
    use DatabaseTransactions;

    private string $anchorPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->anchorPath = storage_path('logs/test-binnacle-anchor-'.uniqid().'.log');
        @unlink($this->anchorPath);
    }

    protected function tearDown(): void
    {
        @unlink($this->anchorPath);
        parent::tearDown();
    }

    // ─── Mejora #6: ancla externa del hash-chain ────────────────────────────

    public function test_anchor_publishes_last_critical_hash_to_file(): void
    {
        User::factory()->create();
        Binnacle::log('critical_anchor', [
            'title' => 'Crítico a anclar', 'category' => 'security', 'severity' => 'critical',
        ]);

        $last = BinnacleEntry::whereIn('event_severity', ['critical', 'alert'])
            ->orderByDesc('id')->firstOrFail();

        $this->artisan('binnacle:anchor', ['--path' => $this->anchorPath])
            ->expectsOutputToContain('Ancla publicada')
            ->assertSuccessful();

        $this->assertFileExists($this->anchorPath);
        $line = trim((string) file_get_contents($this->anchorPath));
        $this->assertStringEndsWith('|'.$last->event_type.'|'.$last->entry_hash, $line);

        // El anclaje queda auditado en la propia bitácora.
        $entry = BinnacleEntry::where('event_type', 'binnacle_anchor_sent')
            ->orderByDesc('id')->firstOrFail();
        $this->assertSame($last->id, ($entry->metadata ?? [])['entry_id'] ?? null);
    }

    public function test_anchor_is_verified_as_valid(): void
    {
        Binnacle::log('critical_a', ['title' => 'A', 'category' => 'security', 'severity' => 'critical']);

        $this->artisan('binnacle:anchor', ['--path' => $this->anchorPath])->assertSuccessful();

        $integrity = Binnacle::verifyAnchorIntegrity($this->anchorPath);

        $this->assertTrue($integrity['anchored']);
        $this->assertTrue($integrity['valid']);
        $this->assertSame(64, strlen((string) $integrity['last_anchor']['hash']));

        $this->artisan('binnacle:anchor', ['--path' => $this->anchorPath, '--check' => true])
            ->assertSuccessful();
    }

    public function test_anchor_detects_rollback_of_anchored_entry(): void
    {
        Binnacle::log('critical_b', ['title' => 'B', 'category' => 'security', 'severity' => 'critical']);

        $this->artisan('binnacle:anchor', ['--path' => $this->anchorPath])->assertSuccessful();

        // Simula manipulación: se elimina el eslabón anclado (vía proceso de archivado).
        $anchored = BinnacleEntry::where('event_type', 'critical_b')->firstOrFail();
        DB::statement('SET @binnacle_archive_process = 1');
        DB::table('binnacle_entries')->where('id', $anchored->id)->delete();
        DB::statement('SET @binnacle_archive_process = NULL');

        $integrity = Binnacle::verifyAnchorIntegrity($this->anchorPath);

        $this->assertTrue($integrity['anchored']);
        $this->assertFalse($integrity['valid']);

        $this->artisan('binnacle:anchor', ['--path' => $this->anchorPath, '--check' => true])
            ->assertFailed();
    }

    public function test_anchor_notify_sends_email_to_admin_and_director(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $director = User::factory()->director()->create();
        $leadership = User::factory()->leadership()->create();

        Binnacle::log('critical_n', ['title' => 'N', 'category' => 'security', 'severity' => 'critical']);

        $this->artisan('binnacle:anchor', ['--path' => $this->anchorPath, '--notify' => true])
            ->assertSuccessful();

        Notification::assertSentTo(
            [$admin, $director],
            BinnacleAnchorNotification::class
        );
        Notification::assertNotSentTo($leadership, BinnacleAnchorNotification::class);
    }

    // ─── Mejora #8: gate de particionado (Spec §9) ──────────────────────────

    public function test_check_growth_reports_ok_below_threshold(): void
    {
        config(['binnacle.partition_threshold' => 10_000_000]);

        $this->artisan('binnacle:check-growth')
            ->expectsOutputToContain('Sin necesidad de particionado')
            ->assertSuccessful();

        $this->artisan('binnacle:check-growth', ['--check' => true])->assertSuccessful();
    }

    public function test_check_growth_recommends_partitioning_above_threshold(): void
    {
        // Un solo registro basta para superar un umbral de 1 fila.
        config(['binnacle.partition_threshold' => 1]);
        BinnacleEntry::forceCreate([
            'uuid' => fake()->uuid(),
            'event_type' => 'growth_probe',
            'event_category' => 'system',
            'event_severity' => 'info',
            'title' => 'Proyección',
        ]);

        $this->artisan('binnacle:check-growth')
            ->expectsOutputToContain('Particionado recomendado')
            ->assertFailed();

        // En --check no se audita, solo exit code.
        $this->artisan('binnacle:check-growth', ['--check' => true])->assertFailed();
    }

    public function test_projected_growth_math(): void
    {
        config([
            'binnacle.partition_threshold' => 100,
            'binnacle.partition_lookahead_months' => 12,
        ]);

        BinnacleEntry::forceCreate([
            'uuid' => fake()->uuid(),
            'event_type' => 'growth_math',
            'event_category' => 'system',
            'event_severity' => 'info',
            'title' => 'Math',
            'created_at' => now()->subDays(10),
        ]);

        $g = Binnacle::projectedGrowth();

        $this->assertSame(12, $g['lookahead_months']);
        $this->assertSame(100, $g['threshold']);
        $this->assertGreaterThanOrEqual(1, $g['total']);
        $this->assertTrue($g['projected'] >= $g['total']);
    }

    // ─── Mejora #9: revisión de retención / --dry-run ───────────────────────

    public function test_dashboard_renders_anchor_and_growth_cards(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Binnacle\DashboardComponent::class)
            ->assertSee('Ancla externa (hash-chain §8.3)')
            ->assertSee('Crecimiento y particionado (Spec §9)')
            ->assertSee('Umbral');
    }

    public function test_archive_dry_run_does_not_move_rows(): void
    {
        // Categoría system (retención 6 meses), entrada vieja de 7 meses.
        BinnacleEntry::forceCreate([
            'uuid' => fake()->uuid(),
            'event_type' => 'system_probe',
            'event_category' => 'system',
            'event_severity' => 'info',
            'title' => 'Antigua',
            'created_at' => now()->subMonths(7),
        ]);

        $before = BinnacleEntry::count();

        $this->artisan('binnacle:archive', ['--dry-run' => true])
            ->expectsOutputToContain('Vista previa')
            ->expectsOutputToContain('system')
            ->assertSuccessful();

        // Nada se movió: ni a archivo ni borrado.
        $this->assertSame($before, BinnacleEntry::count());
        $this->assertSame(0, DB::table('binnacle_entries_archive')->count());
    }

    public function test_archive_moves_after_dry_run_preview(): void
    {
        BinnacleEntry::forceCreate([
            'uuid' => fake()->uuid(),
            'event_type' => 'system_probe_2',
            'event_category' => 'system',
            'event_severity' => 'info',
            'title' => 'Antigua 2',
            'created_at' => now()->subMonths(7),
        ]);

        $this->artisan('binnacle:archive', ['--dry-run' => true])->assertSuccessful();
        $this->assertSame(0, DB::table('binnacle_entries_archive')->count());

        $this->artisan('binnacle:archive')->assertSuccessful();

        $this->assertSame(0, BinnacleEntry::where('event_type', 'system_probe_2')->count());
        $this->assertSame(1, DB::table('binnacle_entries_archive')->count());
    }
}
