<?php

namespace Tests\Feature\Binnacle;

use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lms\BroadcastEvent;
use App\Models\app\Academy\Pensum;
use App\Models\app\Learner\Estudiant;
use App\Models\BinnacleEntry;
use App\Models\User;
use App\Services\Binnacle\SqlQueryAuditor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Expansión de cobertura auditable (blueprint/binnacle):
 *   - nuevos modelos Auditable: Inscripcion, Pensum y BroadcastEvent;
 *   - acceso + auditoría SQL (select/insert/update/delete) en las rutas
 *     académicas marcadas (profesor/coordinación/leadership).
 */
class BinnacleExpansionTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Helpers ───────────────────────────────────────────────────────

    private function createInscripcion(array $overrides = []): Inscripcion
    {
        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Plan Auditoría',
            'description' => 'Plan de prueba',
            'observations' => 'Plan de prueba',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estudiant = Estudiant::factory()->create(['planpago_id' => $planpagoId]);

        return Inscripcion::factory()->create(array_merge([
            'estudiant_id' => $estudiant->id,
        ], $overrides));
    }

    // ─── Cobertura de modelos ampliada ────────────────────────────────

    public function test_inscripcion_created_writes_audit_entry_with_allowlist(): void
    {
        $this->actingAs(User::factory()->create());

        $inscripcion = $this->createInscripcion(['observations' => 'inscrito en 4to']);

        $entry = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', Inscripcion::class)
            ->where('object_id', $inscripcion->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $newValues = $entry->new_values ?? [];

        $this->assertSame('user_action', $entry->event_category);
        $this->assertArrayHasKey('estudiant_id', $newValues);
        $this->assertArrayHasKey('observations', $newValues);
    }

    public function test_inscripcion_updated_writes_diff(): void
    {
        $this->actingAs(User::factory()->create());

        $inscripcion = $this->createInscripcion(['observations' => 'estado inicial']);

        $inscripcion->update(['observations' => 'estado final']);

        $entry = BinnacleEntry::where('event_type', 'model_updated')
            ->where('object_type', Inscripcion::class)
            ->where('object_id', $inscripcion->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame(['observations'], $entry->changed_fields ?? []);
        $this->assertSame('estado inicial', ($entry->old_values ?? [])['observations'] ?? null);
        $this->assertSame('estado final', ($entry->new_values ?? [])['observations'] ?? null);
    }

    public function test_pensum_created_writes_audit_entry_with_allowlist(): void
    {
        $this->actingAs(User::factory()->create());

        $pensum = Pensum::factory()->create(['observations' => 'plan base']);

        $entry = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', Pensum::class)
            ->where('object_id', $pensum->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $newValues = $entry->new_values ?? [];

        $this->assertArrayHasKey('grado_id', $newValues);
        $this->assertArrayHasKey('status_active', $newValues);
        $this->assertArrayNotHasKey('created_at', $newValues);
    }

    public function test_broadcast_event_created_writes_audit_entry(): void
    {
        $this->actingAs(User::factory()->create());

        $event = BroadcastEvent::create([
            'event' => 'activity_updated',
            'subject_type' => \App\Models\app\Academy\Activity::class,
            'subject_id' => 1,
            'actor_user_id' => auth()->id(),
            'recipient_ids' => [1, 2],
            'channel_count' => 2,
            'driver' => 'reverb',
            'delivered' => false,
        ]);

        $entry = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', BroadcastEvent::class)
            ->where('object_id', $event->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertArrayHasKey('event', $entry->new_values ?? []);
        $this->assertArrayHasKey('driver', $entry->new_values ?? []);
    }

    // ─── Acceso a rutas académicas marcadas ───────────────────────────

    public function test_coordinacion_activities_route_logs_access(): void
    {
        $coord = User::factory()->create(['is_coordinacion' => true]);
        $this->actingAs($coord)
            ->get('/app/coordinacion/activities')
            ->assertOk();

        $entry = BinnacleEntry::where('event_type', 'access')
            ->where('subject_id', $coord->id)
            ->where('request_url', 'like', '%app/coordinacion/activities%')
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('user_action', $entry->event_category);
        $this->assertSame(200, ($entry->metadata ?? [])['response_status'] ?? null);
    }

    public function test_coordinacion_lessons_route_logs_access_and_sql(): void
    {
        $coord = User::factory()->create(['is_coordinacion' => true]);
        $this->actingAs($coord)
            ->get('/app/coordinacion/lecciones')
            ->assertOk();

        BinnacleEntry::where('event_type', 'access')
            ->where('subject_id', $coord->id)
            ->where('request_url', 'like', '%app/coordinacion/lecciones%')
            ->firstOrFail();

        $this->assertSqlAudited('select');
    }

    public function test_profesor_lesson_wizard_route_logs_access_and_sql(): void
    {
        $user = User::factory()->create(['is_profesor' => true]);
        DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-12345678',
            'ci_profesor' => '12345678',
            'name' => 'Profesor Audit',
            'lastname' => 'Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/app/profesors/lms/activity/lesson/new')
            ->assertOk();

        BinnacleEntry::where('event_type', 'access')
            ->where('subject_id', $user->id)
            ->where('request_url', 'like', '%app/profesors/lms/activity/lesson/new%')
            ->firstOrFail();

        $this->assertSqlAudited('select');
    }

    // ─── Auditor SQL (servicio) ───────────────────────────────────────

    public function test_sql_auditor_records_only_monitored_tables(): void
    {
        $auditor = app(SqlQueryAuditor::class);

        $this->actingAs(User::factory()->create());

        $auditor->start();
        DB::table('profesors')->count();
        DB::table('users')->count();
        $auditor->flush();

        $entries = BinnacleEntry::where('event_type', 'sql_select')->get();

        $this->assertTrue(
            $entries->contains(fn ($e) => ($e->metadata['table'] ?? null) === 'profesors'),
            'Debe registrar selects sobre profesors'
        );
        $this->assertFalse(
            $entries->contains(fn ($e) => ($e->metadata['table'] ?? null) === 'users'),
            'No debe registrar selects sobre tablas fuera de la allowlist'
        );
    }

    public function test_sql_auditor_records_insert_update_delete_operations(): void
    {
        $auditor = app(SqlQueryAuditor::class);
        $user = User::factory()->create();
        $this->actingAs($user);

        $auditor->start();
        $id = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-99999999',
            'ci_profesor' => '99999999',
            'name' => 'Auditor',
            'lastname' => 'Op',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('profesors')->where('id', $id)->update(['name' => 'Auditor Editado']);
        DB::table('profesors')->where('id', $id)->delete();
        $auditor->flush();

        foreach (['sql_insert', 'sql_update', 'sql_delete'] as $eventType) {
            $entry = BinnacleEntry::where('event_type', $eventType)
                ->orderByDesc('id')
                ->firstOrFail();

            $this->assertSame('profesors', $entry->metadata['table'] ?? null);
            $this->assertSame(1, $entry->metadata['count'] ?? null);
            $this->assertSame('user_action', $entry->event_category);
        }
    }

    public function test_sql_audit_aggregates_multiple_selects_per_request(): void
    {
        $auditor = app(SqlQueryAuditor::class);
        $this->actingAs(User::factory()->create());

        $auditor->start();
        DB::table('profesors')->count();
        DB::table('profesors')->count();
        DB::table('pensums')->count();
        $auditor->flush();

        $profesors = BinnacleEntry::where('event_type', 'sql_select')
            ->where('metadata->table', 'profesors')
            ->firstOrFail();
        $pensums = BinnacleEntry::where('event_type', 'sql_select')
            ->where('metadata->table', 'pensums')
            ->firstOrFail();

        $this->assertSame(2, $profesors->metadata['count'] ?? null);
        $this->assertSame(1, $pensums->metadata['count'] ?? null);
        $this->assertNotNull($profesors->metadata['sql'] ?? null);
    }

    // ─── Helpers ───────────────────────────────────────────────────────

    private function assertSqlAudited(string $operation): void
    {
        $entry = BinnacleEntry::where('event_type', 'sql_'.$operation)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('user_action', $entry->event_category);
        $this->assertIsNumeric($entry->metadata['response_ms'] ?? null);
        $this->assertArrayHasKey('table', $entry->metadata ?? []);
        $this->assertArrayHasKey('count', $entry->metadata ?? []);
    }
}
