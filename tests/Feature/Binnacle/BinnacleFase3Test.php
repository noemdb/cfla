<?php

namespace Tests\Feature\Binnacle;

use App\Livewire\Admin\Binnacle\DashboardComponent;
use App\Models\BinnacleEntry;
use App\Models\User;
use App\Services\Binnacle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Spec BINNACLE-001 — Fases 3 y 4: dashboard, integridad de cadena, PDF, carga.
 */
class BinnacleFase3Test extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_access_metrics_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/binnacle/dashboard')
            ->assertOk()
            ->assertSee('Métricas de Auditoría');
    }

    public function test_dashboard_component_renders_metrics_and_integrity(): void
    {
        User::factory()->create();
        Binnacle::log('critical_dash', ['title' => 'Crítico de dashboard', 'category' => 'security', 'severity' => 'critical']);

        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(DashboardComponent::class)
            ->assertSee('Métricas de Auditoría')
            ->assertSee('Crítico de dashboard');
    }

    public function test_chain_integrity_is_valid_after_critical_entries(): void
    {
        DB::statement('SET @binnacle_archive_process = 1');
        DB::table('binnacle_entries')->whereIn('event_severity', ['critical', 'alert'])->delete();
        DB::statement('SET @binnacle_archive_process = NULL');

        Binnacle::log('critical_c', ['title' => 'C', 'category' => 'security', 'severity' => 'critical']);
        Binnacle::log('critical_d', ['title' => 'D', 'category' => 'security', 'severity' => 'critical']);

        $integrity = Binnacle::verifyChainIntegrity();

        $this->assertSame(2, $integrity['total']);
        $this->assertSame(0, $integrity['broken_links']);
        $this->assertTrue($integrity['valid']);
    }

    public function test_chain_integrity_detects_removed_eslabon(): void
    {
        DB::statement('SET @binnacle_archive_process = 1');
        DB::table('binnacle_entries')->whereIn('event_severity', ['critical', 'alert'])->delete();
        DB::statement('SET @binnacle_archive_process = NULL');

        Binnacle::log('critical_e', ['title' => 'E', 'category' => 'security', 'severity' => 'critical']);
        Binnacle::log('critical_f', ['title' => 'F', 'category' => 'security', 'severity' => 'critical']);

        // Simula un eslabón removido (ej. vía el proceso de archivado).
        $removed = BinnacleEntry::where('event_type', 'critical_e')->firstOrFail();
        DB::statement('SET @binnacle_archive_process = 1');
        DB::table('binnacle_entries')->where('id', $removed->id)->delete();
        DB::statement('SET @binnacle_archive_process = NULL');

        $integrity = Binnacle::verifyChainIntegrity();

        $this->assertSame(1, $integrity['total']);
        $this->assertSame(1, $integrity['broken_links']);
        $this->assertFalse($integrity['valid']);
    }

    public function test_pdf_export_returns_pdf_document(): void
    {
        User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/binnacle/export/pdf');

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->baseResponse->getContent());
    }

    public function test_seed_test_and_clean_commands(): void
    {
        $this->artisan('binnacle:seed-test', ['--count' => 100])
            ->assertSuccessful();

        $this->assertSame(100, BinnacleEntry::where('subject_identifier', 'seeded-benchmark')->count());

        $this->artisan('binnacle:seed-test', ['--clean' => true])
            ->assertSuccessful();

        $this->assertSame(0, BinnacleEntry::where('subject_identifier', 'seeded-benchmark')->count());
    }

    public function test_benchmark_command_runs_against_real_data(): void
    {
        $this->artisan('binnacle:benchmark', ['--iterations' => 1])
            ->assertSuccessful();
    }

    public function test_editing_a_user_in_admin_logs_model_viewed(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $target = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Users\IndexComponent::class)
            ->call('edit', $target->id);

        $entry = BinnacleEntry::where('event_type', 'model_viewed')
            ->where('object_type', User::class)
            ->where('object_id', $target->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('security', $entry->event_category);
        $this->assertSame($admin->id, $entry->subject_id);
    }

    public function test_timeline_renders_with_selected_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['username' => 'tester_timeline']);

        // Actuando como $user para atribuirle un evento.
        $this->actingAs($user);
        User::factory()->create();
        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Binnacle\UserActivityTimeline::class)
            ->set('userId', $user->id)
            ->assertSee('tester_timeline')
            ->assertSee('1 eventos');
    }

    public function test_admin_access_logs_access_event_with_metadata(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/binnacle')
            ->assertOk();

        $entry = BinnacleEntry::where('event_type', 'access')
            ->where('subject_id', $admin->id)
            ->where('request_url', 'like', '%/admin/binnacle%')
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('security', $entry->event_category);
        $this->assertSame(User::class, $entry->subject_type);
        $this->assertSame($admin->id, (int) $entry->subject_id);
        $this->assertSame(200, ($entry->metadata ?? [])['response_status'] ?? null);
        $this->assertArrayHasKey('response_ms', $entry->metadata ?? []);
    }

    public function test_guest_access_to_admin_is_logged_as_system(): void
    {
        $this->get('/admin/binnacle');

        $entry = BinnacleEntry::where('event_type', 'access')
            ->whereNull('subject_id')
            ->where('request_url', 'like', '%/admin/binnacle%')
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('system', $entry->subject_identifier);
        $this->assertNull($entry->subject_id);
    }

    public function test_timeline_filters_by_event_type_severity_and_search(): void
    {
        $user = User::factory()->create(['username' => 'tester_filters']);
        $this->actingAs($user);

        Binnacle::log('access', [
            'title' => 'Acceso al panel admin',
            'category' => 'security',
            'severity' => 'info',
            'subject' => $user,
        ]);
        Binnacle::log('user_login', [
            'title' => 'Inicio de sesión',
            'category' => 'authentication',
            'severity' => 'warning',
            'subject' => $user,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Binnacle\UserActivityTimeline::class)
            ->set('userId', $user->id)
            ->assertSet('eventType', null)
            ->set('eventType', 'access')
            ->assertSee('Acceso al panel admin')
            ->assertDontSee('Inicio de sesión')
            ->set('eventType', null)
            ->set('severity', 'warning')
            ->assertSee('Inicio de sesión')
            ->assertDontSee('Acceso al panel admin')
            ->set('severity', null)
            ->set('search', 'panel')
            ->assertSee('Acceso al panel admin')
            ->assertDontSee('Inicio de sesión');
    }
}
