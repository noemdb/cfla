<?php

namespace Tests\Feature\Binnacle;

use App\Exceptions\Handler;
use App\Models\app\Blog\Post;
use App\Models\app\Learner\Representant;
use App\Models\BinnacleEntry;
use App\Models\User;
use App\Services\Binnacle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Spec BINNACLE-001 — criterios de aceptación Fases 2, 3 y 4.
 */
class BinnacleFase2Test extends TestCase
{
    use DatabaseTransactions;

    public function test_unhandled_exception_writes_critical_entry(): void
    {
        $this->app[Handler::class]->report(new \RuntimeException('boom inesperado'));

        $entry = BinnacleEntry::where('event_type', 'exception_thrown')
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('error', $entry->event_category);
        $this->assertSame('critical', $entry->event_severity);
        $this->assertStringContainsString('boom inesperado', (string) $entry->description);
        $this->assertSame(\RuntimeException::class, ($entry->metadata ?? [])['exception'] ?? null);
    }

    public function test_validation_exception_is_not_logged(): void
    {
        $before = BinnacleEntry::where('event_type', 'exception_thrown')->count();

        $this->app[Handler::class]->report(ValidationException::withMessages(['campo' => 'requerido']));

        $this->assertSame($before, BinnacleEntry::where('event_type', 'exception_thrown')->count());
    }

    public function test_exception_is_attributed_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->app[Handler::class]->report(new \RuntimeException('boom con actor'));

        $entry = BinnacleEntry::where('event_type', 'exception_thrown')
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame(User::class, $entry->subject_type);
        $this->assertSame($user->id, (int) $entry->subject_id);
        $this->assertSame($user->username, $entry->subject_identifier);
    }

    public function test_representant_created_writes_entry_via_generic_observer_with_masked_pii(): void
    {
        $user = User::factory()->create();

        Representant::create([
            'user_id' => $user->id,
            'ci_representant' => '87654321',
            'name' => 'María López',
            'email' => 'maria_representante@example.com',
        ]);

        $entry = BinnacleEntry::where('object_type', Representant::class)
            ->where('event_type', 'model_created')
            ->orderByDesc('id')
            ->firstOrFail();

        $new = $entry->new_values ?? [];

        $this->assertSame('user_action', $entry->event_category);
        // El CI se enmascara: nunca en claro.
        $this->assertSame('87****21', $new['ci_representant'] ?? null);
        $this->assertStringNotContainsString('87654321', json_encode($new));
    }

    public function test_representant_updated_writes_diff(): void
    {
        $user = User::factory()->create();
        $representant = Representant::create([
            'user_id' => $user->id,
            'ci_representant' => '87654321',
            'status_adviders' => false,
        ]);

        $representant->update(['status_adviders' => true]);

        $entry = BinnacleEntry::where('object_type', Representant::class)
            ->where('event_type', 'model_updated')
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertContains('status_adviders', $entry->changed_fields ?? []);
        $this->assertFalse((bool) (($entry->old_values ?? [])['status_adviders'] ?? false));
        $this->assertTrue((bool) (($entry->new_values ?? [])['status_adviders'] ?? false));
    }

    public function test_admin_binnacle_access_logs_meta_audit(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/binnacle')->assertOk();

        $entry = BinnacleEntry::where('event_type', 'binnacle_accessed')
            ->where('subject_id', $admin->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('security', $entry->event_category);
    }

    public function test_csv_export_returns_streaming_csv(): void
    {
        User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/binnacle/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('model_created', $response->streamedContent());
    }

    public function test_timeline_endpoint_returns_user_entries(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // Actuando como $admin para que la creación quede atribuida a él.
        $this->actingAs($admin);
        User::factory()->create();

        $this->getJson("/api/binnacle/user/{$admin->id}/timeline")
            ->assertOk()
            ->assertJsonStructure(['user_id', 'total', 'entries'])
            ->assertJsonPath('total', 1)
            ->assertJsonPath('entries.0.event_type', 'model_created');
    }

    public function test_archive_moves_expired_entries_to_archive_table(): void
    {
        Binnacle::log('critical_probe', [
            'title' => 'Evento crítico de prueba',
            'category' => 'security',
            'severity' => 'critical',
        ]);

        $entry = BinnacleEntry::where('event_type', 'critical_probe')->firstOrFail();

        // La tabla es inmutable: solo el proceso de archivado puede reescribir.
        DB::statement('SET @binnacle_archive_process = 1');
        DB::table('binnacle_entries')->where('id', $entry->id)->update(['created_at' => now()->subYears(3)]);
        DB::statement('SET @binnacle_archive_process = NULL');

        $this->artisan('binnacle:archive')->assertSuccessful();

        $this->assertDatabaseMissing('binnacle_entries', ['id' => $entry->id]);
        $this->assertDatabaseHas('binnacle_entries_archive', [
            'id' => $entry->id,
            'event_type' => 'critical_probe',
        ]);
    }

    public function test_hash_chain_links_consecutive_critical_entries(): void
    {
        // Aisla la cadena: borra critical/alert previos dentro de la transacción
        // del test (se revierte al final). La BD dev compartida puede tener filas.
        DB::statement('SET @binnacle_archive_process = 1');
        DB::table('binnacle_entries')->whereIn('event_severity', ['critical', 'alert'])->delete();
        DB::statement('SET @binnacle_archive_process = NULL');

        Binnacle::log('critical_a', ['title' => 'A', 'category' => 'security', 'severity' => 'critical']);
        Binnacle::log('critical_b', ['title' => 'B', 'category' => 'security', 'severity' => 'critical']);

        $a = BinnacleEntry::where('event_type', 'critical_a')->firstOrFail();
        $b = BinnacleEntry::where('event_type', 'critical_b')->firstOrFail();

        $this->assertNotNull($a->entry_hash);
        $this->assertNotNull($b->entry_hash);
        $this->assertSame($a->entry_hash, $b->previous_hash);
        // Primera entrada de la cadena: genesis implícito (previous_hash null).
        $this->assertNull($a->previous_hash);
    }

    public function test_info_entries_are_not_part_of_hash_chain(): void
    {
        Binnacle::log('info_probe', ['title' => 'X', 'category' => 'user_action', 'severity' => 'info']);

        $entry = BinnacleEntry::where('event_type', 'info_probe')->firstOrFail();

        $this->assertNull($entry->entry_hash);
        $this->assertNull($entry->previous_hash);
    }

    public function test_timeline_page_loads_for_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/binnacle/timeline')
            ->assertOk();
    }

    public function test_model_viewed_is_restricted_by_allowlist(): void
    {
        // Post NO está en config('binnacle.viewed_models'): se ignora.
        Binnacle::logView(new Post);

        $this->assertDatabaseMissing('binnacle_entries', ['event_type' => 'model_viewed']);

        // User sí está en la allowlist: se registra con categoría security.
        $user = User::factory()->create();
        Binnacle::logView($user);

        $entry = BinnacleEntry::where('event_type', 'model_viewed')
            ->where('object_id', $user->id)
            ->firstOrFail();

        $this->assertSame('security', $entry->event_category);
    }

    public function test_entry_detail_modal_shows_event_data(): void
    {
        $entry = BinnacleEntry::forceCreate([
            'uuid' => fake()->uuid(),
            'event_type' => 'detail_probe',
            'event_category' => 'user_action',
            'event_severity' => 'warning',
            'title' => 'Evento de detalle',
            'description' => 'Descripción completa del evento',
            'subject_type' => User::class,
            'subject_identifier' => 'admin_demo',
            'ip_address' => '127.0.0.1',
            'request_method' => 'POST',
            'request_url' => 'https://colegio.test/app/probe',
            'request_id' => 'req-123',
            'changed_fields' => ['status', 'monto'],
            'old_values' => ['status' => 'pendiente'],
            'new_values' => ['status' => 'pagado', 'monto' => 500],
            'metadata' => ['origen' => 'test'],
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        $component = Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Binnacle\IndexComponent::class);

        $component->call('openEntryDetails', $entry->id)
            ->assertSet('showEntryDetails', true)
            ->assertSet('viewingEntryId', $entry->id);

        $html = $component->html();

        $this->assertStringContainsString('Evento de detalle', $html);
        $this->assertStringContainsString('Descripción completa del evento', $html);
        $this->assertStringContainsString('req-123', $html);
        $this->assertStringContainsString('status', $html);

        $component->call('closeEntryDetails')
            ->assertSet('showEntryDetails', false)
            ->assertSet('viewingEntryId', null);
    }
}
