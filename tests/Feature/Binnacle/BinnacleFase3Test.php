<?php

namespace Tests\Feature\Binnacle;

use App\Livewire\Admin\Binnacle\DashboardComponent;
use App\Models\BinnacleEntry;
use App\Models\User;
use App\Notifications\BinnacleBacklogNotification;
use App\Services\Binnacle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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
            'title' => 'Sesión iniciada correctamente',
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
            ->assertDontSee('Sesión iniciada correctamente')
            ->set('eventType', null)
            ->set('severity', 'warning')
            ->assertSee('Sesión iniciada correctamente')
            ->assertDontSee('Acceso al panel admin')
            ->set('severity', null)
            ->set('search', 'panel')
            ->assertSee('Acceso al panel admin')
            ->assertDontSee('Sesión iniciada correctamente');
    }

    public function test_director_can_access_binnacle_panel(): void
    {
        $director = User::factory()->director()->create();

        $this->actingAs($director)
            ->get('/admin/binnacle')
            ->assertOk();
    }

    public function test_leadership_can_access_binnacle_panel(): void
    {
        $leadership = User::factory()->leadership()->create();

        $this->actingAs($leadership)
            ->get('/admin/binnacle')
            ->assertOk();
    }

    public function test_estudiante_cannot_access_binnacle_panel(): void
    {
        $student = User::factory()->create(['is_student' => true]);

        $this->actingAs($student)
            ->get('/admin/binnacle')
            ->assertForbidden();
    }

    public function test_director_can_export_but_leadership_cannot(): void
    {
        $director = User::factory()->director()->create();
        $leadership = User::factory()->leadership()->create();

        $this->actingAs($director)->get('/admin/binnacle/export')->assertOk();
        $this->actingAs($leadership)->get('/admin/binnacle/export')->assertForbidden();
    }

    public function test_profesor_can_only_view_own_activity(): void
    {
        $profesor = User::factory()->create(['is_profesor' => true]);
        $other = User::factory()->create(['username' => 'otro_usuario_rbac']);
        $this->actingAs($profesor);

        $this->get('/admin/binnacle/timeline')->assertForbidden();
        $this->get('/admin/binnacle/mi-actividad')->assertOk();

        Livewire::test(\App\Livewire\Admin\Binnacle\UserActivityTimeline::class, ['selfMode' => true])
            ->set('userId', $other->id)
            ->assertSet('userId', $profesor->id);
    }

    public function test_profesor_module_activity_timeline_shows_only_own_entries(): void
    {
        $profesor = User::factory()->create(['is_profesor' => true, 'username' => 'profesor_binnacle']);
        $other = User::factory()->create(['username' => 'otro_profesor_rbac']);

        // Evento del profesor en la bitácora.
        Binnacle::log('user_login', [
            'title' => 'Inicio de sesión del profesor',
            'category' => 'authentication',
            'severity' => 'info',
            'subject' => $profesor,
        ]);

        // Evento de otro usuario (no debe aparecer).
        Binnacle::log('access', [
            'title' => 'Acceso del otro usuario',
            'category' => 'security',
            'severity' => 'info',
            'subject' => $other,
        ]);

        $this->actingAs($profesor);

        $this->get('/app/profesors/binnacle/mi-bitcora')
            ->assertOk()
            ->assertSee('profesor_binnacle')
            ->assertSee('Inicio de sesión del profesor');

        Livewire::test(\App\Livewire\Profesor\Binnacle\ActivityTimeline::class)
            ->assertSet('selfMode', true)
            ->assertSet('userId', $profesor->id)
            ->assertSee('Inicio de sesión del profesor')
            ->assertDontSee('Acceso del otro usuario')
            // Intentar ver la actividad de otro usuario queda bloqueado por selfMode.
            ->set('userId', $other->id)
            ->assertSet('userId', $profesor->id);
    }

    // ─── Mejora #4: severidades síncronas selectivas ────────────────────────

    public function test_user_login_event_is_written_sync(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Binnacle::log('user_login', [
            'title' => 'Inicio de sesión',
            'category' => 'authentication',
            'severity' => 'info',
            'subject' => $user,
        ]);

        // Si se encolara (QUEUE_CONNECTION=sync en tests lo procesa al vuelo,
        // pero en producción con worker caído se perdería). Como está en
        // sync_event_types, debe quedar escrito en la misma request.
        $entry = BinnacleEntry::where('event_type', 'user_login')
            ->where('subject_id', $user->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('authentication', $entry->event_category);
    }

    public function test_access_event_is_written_sync(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        Binnacle::log('access', [
            'title' => 'Acceso al panel admin',
            'category' => 'security',
            'severity' => 'info',
            'subject' => $admin,
        ]);

        $entry = BinnacleEntry::where('event_type', 'access')
            ->where('subject_id', $admin->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('security', $entry->event_category);
    }

    public function test_regular_info_event_is_still_queued(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Queue::fake captura los jobs en vez de ejecutarlos: si el evento
        // va por la cola, el listener no escribe nada en la BD.
        \Illuminate\Support\Facades\Queue::fake();

        $before = BinnacleEntry::where('event_type', 'info_probe')->count();

        Binnacle::log('info_probe', [
            'title' => 'Sonda de sistema',
            'category' => 'system',
            'severity' => 'info',
        ]);

        // No está en sync_event_types ni es severidad critical/alert → cola.
        $this->assertSame($before, BinnacleEntry::where('event_type', 'info_probe')->count());
    }

    // ─── Mejora #1: alerta de backlog (binnacle:watch) ─────────────────────

    private function seedBinnacleJobs(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('jobs')->insert([
                'queue' => 'binnacle',
                'payload' => json_encode(['displayName' => 'App\\Jobs\\FakeJob']),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => now()->getTimestamp(),
                'created_at' => now()->getTimestamp(),
            ]);
        }
    }

    public function test_watch_reports_ok_when_queue_is_healthy(): void
    {
        DB::table('jobs')->where('queue', 'binnacle')->delete();

        $this->artisan('binnacle:watch')
            ->expectsOutputToContain('jobs pendientes')
            ->assertSuccessful();
    }

    public function test_watch_fails_check_mode_on_backlog(): void
    {
        DB::table('jobs')->where('queue', 'binnacle')->delete();
        $this->seedBinnacleJobs(5);

        $this->artisan('binnacle:watch', ['--check' => true, '--threshold' => 3])
            ->expectsOutputToContain('Backlog')
            ->assertFailed();
    }

    public function test_watch_writes_sync_alert_and_notifies_on_backlog(): void
    {
        Notification::fake();

        DB::table('jobs')->where('queue', 'binnacle')->delete();
        $this->seedBinnacleJobs(5);

        $admin = User::factory()->create(['is_admin' => true]);

        $this->artisan('binnacle:watch', ['--threshold' => 3])
            ->assertFailed();

        // La alerta se escribe síncrona en la propia bitácora aunque el worker
        // esté caído (queue_backlog está en sync_event_types).
        $entry = BinnacleEntry::where('event_type', 'queue_backlog')
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame('warning', $entry->event_severity);
        $this->assertSame('system', $entry->event_category);
        $this->assertSame(5, ($entry->metadata ?? [])['pending'] ?? null);

        Notification::assertSentTo($admin, BinnacleBacklogNotification::class);
    }

    public function test_watch_does_not_notify_below_threshold(): void
    {
        Notification::fake();

        DB::table('jobs')->where('queue', 'binnacle')->delete();
        $this->seedBinnacleJobs(2);

        $before = BinnacleEntry::where('event_type', 'queue_backlog')->count();

        $this->artisan('binnacle:watch', ['--threshold' => 5])
            ->assertSuccessful();

        Notification::assertNothingSent();
        $this->assertSame(
            $before,
            BinnacleEntry::where('event_type', 'queue_backlog')->count()
        );
    }

    // ─── Mejora #5: resumen diario por email (binnacle:report) ──────────────

    public function test_report_sends_daily_summary_to_admin_and_director(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $director = User::factory()->director()->create();
        $leadership = User::factory()->leadership()->create();

        $user = User::factory()->create(['username' => 'actor_reporte']);
        $this->actingAs($user);
        Binnacle::log('user_login', [
            'title' => 'Inicio de sesión',
            'category' => 'authentication',
            'severity' => 'warning',
            'subject' => $user,
        ]);
        $this->actingAs($admin);
        Binnacle::log('critical_rep', [
            'title' => 'Crítico del reporte',
            'category' => 'security',
            'severity' => 'critical',
        ]);

        $this->artisan('binnacle:report', ['--date' => now()->toDateString()])
            ->assertSuccessful();

        Notification::assertSentTo(
            [$admin, $director],
            \App\Notifications\BinnacleDailyReportNotification::class
        );
        Notification::assertNotSentTo(
            $leadership,
            \App\Notifications\BinnacleDailyReportNotification::class
        );

        $entry = BinnacleEntry::where('event_type', 'binnacle_report_sent')
            ->orderByDesc('id')
            ->firstOrFail();
        $this->assertSame('system', $entry->event_category);
        $this->assertSame(now()->toDateString(), ($entry->metadata ?? [])['date'] ?? null);
    }

    public function test_report_no_notify_still_generates_summary(): void
    {
        Notification::fake();
        User::factory()->create(['is_admin' => true]);

        $this->artisan('binnacle:report', [
            '--date' => now()->toDateString(),
            '--no-notify' => true,
        ])->assertSuccessful();

        Notification::assertNothingSent();
    }

    // ─── Mejora #7: cobertura Auditable en Academy/LMS ──────────────────────

    private function makePevaluacion(User $user): \App\Models\app\Academy\Pevaluacion
    {
        $pestudio = \App\Models\app\Academy\Pestudio::factory()->create(['status_active' => 'true']);
        $grado = \App\Models\app\Academy\Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = \App\Models\app\Academy\Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = \App\Models\app\Academy\Asignatura::factory()->create();
        $pensum = \App\Models\app\Academy\Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = \App\Models\app\Academy\Lapso::factory()->create();
        $profesor = \App\Models\app\Academy\Profesor::create([
            'user_id' => $user->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '87654321',
            'status_active' => 'true',
        ]);

        return \App\Models\app\Academy\Pevaluacion::create([
            'pensum_id' => $pensum->id,
            'profesor_id' => $profesor->id,
            'lapso_id' => $lapso->id,
            'seccion_id' => $seccion->id,
            'objetivo' => 'Objetivo de prueba',
        ]);
    }

    public function test_activity_create_update_delete_are_audited(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $pevaluacion = $this->makePevaluacion($admin);
        $activity = \App\Models\app\Academy\Activity::factory()->create(['pevaluacion_id' => $pevaluacion->id]);

        $created = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', \App\Models\app\Academy\Activity::class)
            ->where('object_id', $activity->id)
            ->firstOrFail();

        $this->assertSame('user_action', $created->event_category);

        $activity->update(['topic' => 'Tema actualizado']);

        $updated = BinnacleEntry::where('event_type', 'model_updated')
            ->where('object_type', \App\Models\app\Academy\Activity::class)
            ->where('object_id', $activity->id)
            ->firstOrFail();

        $this->assertContains('topic', $updated->changed_fields ?? []);
    }

    public function test_profesor_masks_personal_data_in_audit(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $profesor = \App\Models\app\Academy\Profesor::create([
            'user_id' => $admin->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '12345678',
            'status_active' => 'true',
        ]);

        $entry = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', \App\Models\app\Academy\Profesor::class)
            ->where('object_id', $profesor->id)
            ->firstOrFail();

        $newValues = $entry->new_values ?? [];

        // La cédula se enmascara, nunca en claro.
        $this->assertStringNotContainsString('12345678', (string) ($newValues['ci_profesor'] ?? ''));
        $this->assertArrayHasKey('name', $newValues);
        $this->assertArrayNotHasKey('gspassword', $newValues);
    }

    public function test_lms_activity_section_is_audited(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $pevaluacion = $this->makePevaluacion($admin);
        $activity = \App\Models\app\Academy\Activity::factory()->create(['pevaluacion_id' => $pevaluacion->id]);

        $section = \App\Models\app\Academy\Lms\LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Introducción',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $entry = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', \App\Models\app\Academy\Lms\LmsActivitySection::class)
            ->where('object_id', $section->id)
            ->firstOrFail();

        $this->assertSame('user_action', $entry->event_category);
    }

    // ─── Mejora #7 ampliada: cobertura Educational (debates) ────────────────

    public function test_debate_competition_create_is_audited(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $competition = \App\Models\app\Educational\DebateCompetition::create([
            'user_id' => $admin->id,
            'name' => 'Olimpiada de Debate 2026',
            'token' => \App\Models\app\Educational\DebateCompetition::genToken(),
            'description' => 'Primera edición',
            'motive' => 'Fomento académico',
            'date' => now()->toDateString(),
            'status_active' => true,
        ]);

        $entry = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', \App\Models\app\Educational\DebateCompetition::class)
            ->where('object_id', $competition->id)
            ->firstOrFail();

        $newValues = $entry->new_values ?? [];
        $this->assertSame('user_action', $entry->event_category);
        $this->assertSame('Olimpiada de Debate 2026', $newValues['name'] ?? null);
        $this->assertArrayNotHasKey('token', $newValues);
    }

    public function test_debate_question_update_is_audited(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $competition = \App\Models\app\Educational\DebateCompetition::create([
            'user_id' => $admin->id,
            'name' => 'Competencia Test',
            'token' => \App\Models\app\Educational\DebateCompetition::genToken(),
            'description' => 'd',
            'motive' => 'm',
            'date' => now()->toDateString(),
        ]);

        $debate = \App\Models\app\Educational\Debate::create([
            'competition_id' => $competition->id,
            'token' => 't',
            'name' => 'Debate A',
            'description' => 'Descripción debate',
            'status_active' => true,
        ]);

        $question = \App\Models\app\Educational\DebateQuestion::create([
            'debate_id' => $debate->id,
            'category' => 'Lengua',
            'text' => '¿Qué es la retórica?',
            'time' => 30,
            'weighting' => 10,
        ]);

        $question->update(['weighting' => 20]);

        $entry = BinnacleEntry::where('event_type', 'model_updated')
            ->where('object_type', \App\Models\app\Educational\DebateQuestion::class)
            ->where('object_id', $question->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertContains('weighting', $entry->changed_fields ?? []);
    }

    // ─── Mejora #7 ampliada: cobertura Instrument (diagnóstico) ─────────────

    public function test_diag_main_create_is_audited(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $main = \App\Models\app\Instrument\DiagMain::create([
            'name' => 'Diagnóstico Inicial 2026',
            'description' => 'Evaluación diagnóstica',
            'active' => true,
        ]);

        $entry = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', \App\Models\app\Instrument\DiagMain::class)
            ->where('object_id', $main->id)
            ->firstOrFail();

        $newValues = $entry->new_values ?? [];
        $this->assertSame('user_action', $entry->event_category);
        $this->assertSame('Diagnóstico Inicial 2026', $newValues['name'] ?? null);
    }

    public function test_diag_referent_create_is_audited(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $referent = \App\Models\app\Instrument\DiagReferent::create([
            'name' => 'CNB EPB 2007',
            'code' => 'CNB-2007',
            'version' => '1.0',
            'active' => true,
        ]);

        $entry = BinnacleEntry::where('event_type', 'model_created')
            ->where('object_type', \App\Models\app\Instrument\DiagReferent::class)
            ->where('object_id', $referent->id)
            ->firstOrFail();

        $this->assertSame('user_action', $entry->event_category);
    }
}
