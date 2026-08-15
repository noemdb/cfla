<?php

namespace Tests\Feature\Binnacle;

use App\Models\BinnacleEntry;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Spec BINNACLE-001 — criterios de aceptación Fase 1.
 */
class BinnacleTest extends TestCase
{
    use DatabaseTransactions;

    private function createdEntry(User $user, string $eventType): BinnacleEntry
    {
        // Los eventos de modelo guardan el modelo afectado en object_type/object_id
        // y al actor (quien ejecuta la acción) en subject_type/subject_id.
        return BinnacleEntry::where('object_type', User::class)
            ->where('object_id', $user->id)
            ->where('event_type', $eventType)
            ->orderByDesc('id')
            ->firstOrFail();
    }

    public function test_user_created_writes_entry_without_sensitive_fields(): void
    {
        $user = User::factory()->create([
            'email' => 'javier_sanchez@example.com',
        ]);

        $entry = $this->createdEntry($user, 'model_created');

        $newValues = $entry->new_values ?? [];

        $this->assertArrayNotHasKey('password', $newValues);
        $this->assertArrayNotHasKey('remember_token', $newValues);
        $this->assertArrayNotHasKey('number_id', $newValues);
        $this->assertSame('user_action', $entry->event_category);
        $this->assertSame('info', $entry->event_severity);

        // El email se enmascara (maskedAuditFields), nunca en claro.
        $this->assertStringNotContainsString('javier_sanchez', (string) ($newValues['email'] ?? ''));
    }

    public function test_user_updated_writes_diff_of_changed_fields(): void
    {
        $user = User::factory()->create();

        $user->update(['is_profesor' => true]);

        $entry = $this->createdEntry($user, 'model_updated');

        $this->assertContains('is_profesor', $entry->changed_fields ?? []);
        $this->assertFalse((bool) (($entry->old_values ?? [])['is_profesor'] ?? false));
        $this->assertTrue((bool) (($entry->new_values ?? [])['is_profesor'] ?? false));
    }

    public function test_user_deleted_writes_entry(): void
    {
        $user = User::factory()->create();

        $user->delete();

        $entry = $this->createdEntry($user, 'model_deleted');

        $this->assertSame('warning', $entry->event_severity);
        $this->assertArrayHasKey('username', $entry->old_values ?? []);
    }

    public function test_direct_update_on_binnacle_table_is_blocked_by_trigger(): void
    {
        $user = User::factory()->create();
        $entry = $this->createdEntry($user, 'model_created');

        $this->expectException(QueryException::class);

        DB::table('binnacle_entries')->where('id', $entry->id)->update(['title' => 'hack']);
    }

    public function test_direct_delete_on_binnacle_table_is_blocked_by_trigger(): void
    {
        $user = User::factory()->create();
        $entry = $this->createdEntry($user, 'model_created');

        $this->expectException(QueryException::class);

        DB::table('binnacle_entries')->where('id', $entry->id)->delete();
    }

    public function test_eloquent_update_on_binnacle_entry_is_blocked_by_model_guard(): void
    {
        $user = User::factory()->create();
        $entry = $this->createdEntry($user, 'model_created');

        $this->expectException(\RuntimeException::class);

        $entry->update(['title' => 'hack']);
    }

    public function test_login_failure_writes_warning_entry(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'username' => $user->username,
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('username');

        $entry = BinnacleEntry::where('event_type', 'user_login_failed')
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('authentication', $entry->event_category);
        $this->assertSame('warning', $entry->event_severity);
        $this->assertSame($user->username, $entry->subject_identifier);
    }

    public function test_login_success_writes_entry(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ])->assertRedirect('/');

        $entry = BinnacleEntry::where('event_type', 'user_login')
            ->where('subject_id', $user->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame($user->username, $entry->subject_identifier);
    }

    public function test_profesor_cannot_access_admin_binnacle(): void
    {
        $profesor = User::factory()->create(['is_profesor' => true]);

        $this->actingAs($profesor)
            ->get('/admin/binnacle')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_binnacle(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/binnacle')
            ->assertOk();
    }
}
