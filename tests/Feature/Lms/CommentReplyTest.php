<?php

namespace Tests\Feature\Lms;

use App\Events\BinnacleEntryRequested;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Learner\Estudiant;
use App\Models\User;
use App\Notifications\CommentRepliedNotification;
use App\Policies\ActivityCommentPolicy;
use App\Services\Lms\CommentModerationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class CommentReplyTest extends TestCase
{
    use DatabaseTransactions;

    private User $profesorUser;

    private Profesor $profesor;

    private User $otherProfesorUser;

    private Profesor $otherProfesor;

    private Activity $activity;

    private Activity $otherActivity;

    private User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        // La réplica ahora emite notificación + email transaccional (HTTP a
        // SendPulse/Resend): se evita cualquier llamada de red real en tests.
        Http::fake();

        // ─── Profesor 1 ──────────────────────────────────────────
        $this->profesorUser = User::factory()->create(['is_profesor' => true]);
        $this->profesor = Profesor::create([
            'user_id' => $this->profesorUser->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '12345678',
            'status_active' => 'true',
        ]);

        // ─── Profesor 2 ──────────────────────────────────────────
        $this->otherProfesorUser = User::factory()->create(['is_profesor' => true]);
        $this->otherProfesor = Profesor::create([
            'user_id' => $this->otherProfesorUser->id,
            'name' => 'María',
            'lastname' => 'López',
            'ci_profesor' => '87654321',
            'status_active' => 'true',
        ]);

        // ─── Estructura académica compartida ─────────────────────
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        // ─── Pevaluacion + Activity para Profesor 1 ────────────
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $this->profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        $this->activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'status' => true,
            'topic' => 'Actividad de Carlos',
        ]);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $this->activity->id,
        ]);

        // ─── Pevaluacion + Activity para Profesor 2 ────────────
        $pevaluacion2 = Pevaluacion::create([
            'profesor_id' => $this->otherProfesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        $this->otherActivity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion2->id,
            'topic' => 'Actividad de María',
        ]);

        // ─── Estudiante con inscripción en la sección ──────────
        $this->studentUser = User::factory()->create(['is_student' => true]);

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan',
            'description' => 'Test plan description',
            'observations' => 'Test observations',
            'status_active' => 'true',
        ]);

        $estudiant = Estudiant::factory()->create([
            'user_id' => $this->studentUser->id,
            'planpago_id' => $planpagoId,
            'date_birth' => now()->subYears(12)->toDateString(),
        ]);
        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
        ]);
    }

    /**
     * Comentario raíz aprobado de un estudiante en la actividad del profesor 1.
     */
    private function createApprovedRootComment(?Activity $activity = null): ActivityComment
    {
        return ActivityComment::create([
            'activity_id' => ($activity ?? $this->activity)->id,
            'user_id' => $this->studentUser->id,
            'body' => 'Comentario del estudiante.',
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $this->profesorUser->id,
        ]);
    }

    // ─── Servicio: reply() ───────────────────────────────────────

    /** @test */
    public function profesor_can_reply_to_root_comment(): void
    {
        $root = $this->createApprovedRootComment();

        $service = new CommentModerationService($this->profesorUser);
        $reply = $service->reply($root, 'Respuesta del profesor.');

        $this->assertNotNull($reply->id);
        $this->assertEquals($root->id, $reply->parent_id);
        $this->assertEquals($this->activity->id, $reply->activity_id);
        $this->assertEquals($this->profesorUser->id, $reply->user_id);
        $this->assertEquals('Respuesta del profesor.', $reply->body);

        // Autoaprobada (ADR-002)
        $this->assertTrue($reply->is_approved);
        $this->assertTrue($reply->is_instructor_reply);
        $this->assertNotNull($reply->approved_at);
        $this->assertEquals($this->profesorUser->id, $reply->approved_by);

        // Model helpers
        $this->assertTrue($reply->isReply());
        $this->assertTrue($reply->isInstructorReply());
        $this->assertFalse($root->isReply());
    }

    /** @test */
    public function reply_is_auto_approved_and_never_pending(): void
    {
        $root = $this->createApprovedRootComment();
        (new CommentModerationService($this->profesorUser))->reply($root, 'Gracias por tu aporte.');

        $pendings = ActivityComment::where('activity_id', $this->activity->id)
            ->pending()
            ->get();

        $this->assertCount(0, $pendings);
        $this->assertEquals(1, ActivityComment::where('parent_id', $root->id)->count());
    }

    /** @test */
    public function cannot_reply_to_a_reply(): void
    {
        $root = $this->createApprovedRootComment();
        $reply = (new CommentModerationService($this->profesorUser))->reply($root, 'Primera réplica.');

        $this->expectException(\InvalidArgumentException::class);

        (new CommentModerationService($this->profesorUser))->reply($reply, 'Segunda réplica.');
    }

    /** @test */
    public function cannot_reply_to_rejected_comment(): void
    {
        $root = ActivityComment::create([
            'activity_id' => $this->activity->id,
            'user_id' => $this->studentUser->id,
            'body' => 'Comentario rechazado.',
            'is_approved' => false,
            'rejected_at' => now(),
            'rejected_by' => $this->profesorUser->id,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        (new CommentModerationService($this->profesorUser))->reply($root, 'No debería poder.');
    }

    /** @test */
    public function profesor_can_reply_to_pending_comment(): void
    {
        $root = ActivityComment::create([
            'activity_id' => $this->activity->id,
            'user_id' => $this->studentUser->id,
            'body' => 'Comentario pendiente.',
            'is_approved' => false,
        ]);

        $reply = (new CommentModerationService($this->profesorUser))->reply($root, 'Gracias, lo apruebo.');

        $this->assertEquals($root->id, $reply->parent_id);
        $this->assertTrue($reply->is_approved);
    }

    /** @test */
    public function profesor_cannot_reply_to_other_profesor_comment(): void
    {
        $root = $this->createApprovedRootComment($this->otherActivity);

        $this->expectException(AuthorizationException::class);

        (new CommentModerationService($this->profesorUser))->reply($root, 'No debería poder.');
    }

    /** @test */
    public function admin_can_reply_to_any_comment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $root = $this->createApprovedRootComment($this->otherActivity);

        $reply = (new CommentModerationService($admin))->reply($root, 'Respuesta del admin.');

        $this->assertEquals($root->id, $reply->parent_id);
        $this->assertTrue($reply->is_instructor_reply);
    }

    // ─── Policy: reply() ─────────────────────────────────────────

    /** @test */
    public function policy_reply_allows_moderator_roles(): void
    {
        $root = $this->createApprovedRootComment();
        $policy = new ActivityCommentPolicy;

        $leadership = User::factory()->create(['is_leadership' => true]);

        $this->assertTrue($policy->reply($this->profesorUser, $root));
        $this->assertTrue($policy->reply(User::factory()->create(['is_admin' => true]), $root));
        $this->assertTrue($policy->reply($leadership, $root));
    }

    /** @test */
    public function policy_reply_denies_student(): void
    {
        $root = $this->createApprovedRootComment();
        $policy = new ActivityCommentPolicy;

        $this->assertFalse($policy->reply($this->studentUser, $root));
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /** @test */
    public function root_scope_excludes_replies(): void
    {
        $root = $this->createApprovedRootComment();
        (new CommentModerationService($this->profesorUser))->reply($root, 'Réplica.');

        $roots = ActivityComment::where('activity_id', $this->activity->id)
            ->root()
            ->get();

        $this->assertCount(1, $roots);
        $this->assertEquals($root->id, $roots->first()->id);
    }

    /** @test */
    public function approved_scope_excludes_soft_deleted(): void
    {
        $root = $this->createApprovedRootComment();
        $root->delete();

        $approved = ActivityComment::where('activity_id', $this->activity->id)
            ->approved()
            ->get();

        $this->assertCount(0, $approved);
    }

    /** @test */
    public function replies_scope_returns_only_direct_replies(): void
    {
        $root = $this->createApprovedRootComment();
        $otherRoot = $this->createApprovedRootComment();
        $reply1 = (new CommentModerationService($this->profesorUser))->reply($root, 'Réplica 1');
        $reply2 = (new CommentModerationService($this->profesorUser))->reply($root, 'Réplica 2');
        (new CommentModerationService($this->profesorUser))->reply($otherRoot, 'Réplica otro hilo');

        $replies = ActivityComment::repliesOf($root->id)->get();

        $this->assertCount(2, $replies);
        $this->assertTrue($replies->pluck('id')->contains($reply1->id));
        $this->assertTrue($replies->pluck('id')->contains($reply2->id));
        $this->assertFalse($replies->pluck('id')->contains($root->id));
        $this->assertFalse($replies->pluck('id')->contains($otherRoot->id));
    }

    // ─── Integridad referencial ───────────────────────────────────

    /** @test */
    public function replies_are_cascade_deleted_with_parent(): void
    {
        $root = $this->createApprovedRootComment();
        (new CommentModerationService($this->profesorUser))->reply($root, 'Réplica 1');
        (new CommentModerationService($this->profesorUser))->reply($root, 'Réplica 2');

        $root->forceDelete();

        $this->assertDatabaseMissing('activity_comments', ['parent_id' => $root->id]);
        $this->assertSame(
            0,
            ActivityComment::withTrashed()->where('parent_id', $root->id)->count()
        );
    }

    /** @test */
    public function soft_deleting_parent_keeps_replies_but_hides_thread(): void
    {
        $root = $this->createApprovedRootComment();
        (new CommentModerationService($this->profesorUser))->reply($root, 'Réplica 1');

        $root->delete();

        $this->assertDatabaseHas('activity_comments', ['parent_id' => $root->id]);
        $this->assertSame(
            1,
            ActivityComment::withTrashed()->where('parent_id', $root->id)->count()
        );

        $visibleThreads = ActivityComment::where('activity_id', $this->activity->id)
            ->approved()
            ->root()
            ->get();

        $this->assertCount(0, $visibleThreads);
    }

    // ─── Auditabilidad ────────────────────────────────────────────

    /** @test */
    public function creating_a_reply_is_audited(): void
    {
        // Acotado al evento de bitácora: Event::fake() completo también desactiva
        // los eventos elocuentes (eloquent.*), lo que silenciaría al observer.
        Event::fake([BinnacleEntryRequested::class]);

        $root = $this->createApprovedRootComment();
        $reply = (new CommentModerationService($this->profesorUser))->reply($root, 'Réplica auditada.');

        Event::assertDispatched(BinnacleEntryRequested::class, function (BinnacleEntryRequested $event) use ($root, $reply) {
            $newValues = $event->context['new_values'] ?? [];
            $changed = $event->context['changed_fields'] ?? [];

            return $event->eventType === 'model_created'
                && $event->objectType() === ActivityComment::class
                && $event->objectId() == $reply->id
                && ($newValues['parent_id'] ?? null) == $root->id
                && ($newValues['is_instructor_reply'] ?? null) == true
                && in_array('parent_id', $changed, true)
                && in_array('is_instructor_reply', $changed, true);
        });
    }

    // ─── Livewire: CommentModeration ─────────────────────────────

    /** @test */
    public function moderation_component_can_reply_via_livewire(): void
    {
        $root = $this->createApprovedRootComment();

        Livewire::actingAs($this->profesorUser)
            ->test(\App\Livewire\Profesor\Lms\CommentModeration::class)
            ->set('tab', 'approved')
            ->call('openReply', $root->id)
            ->set('replyBody', 'Respuesta desde la moderación.')
            ->call('saveReply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('activity_comments', [
            'parent_id' => $root->id,
            'user_id' => $this->profesorUser->id,
            'body' => 'Respuesta desde la moderación.',
            'is_approved' => 1,
            'is_instructor_reply' => 1,
        ]);
    }

    /** @test */
    public function moderation_reply_requires_body(): void
    {
        $root = $this->createApprovedRootComment();

        Livewire::actingAs($this->profesorUser)
            ->test(\App\Livewire\Profesor\Lms\CommentModeration::class)
            ->call('openReply', $root->id)
            ->call('saveReply')
            ->assertHasErrors(['replyBody']);

        $this->assertDatabaseMissing('activity_comments', ['parent_id' => $root->id]);
    }

    // ─── Livewire: ActivityEditor inline ─────────────────────────

    /** @test */
    public function activity_editor_can_reply_inline(): void
    {
        $root = $this->createApprovedRootComment();

        Livewire::actingAs($this->profesorUser)
            ->test(\App\Livewire\Profesor\Lms\ActivityEditor::class, ['activity' => $this->activity])
            ->set('commentsTab', 'approved')
            ->call('openActivityReply', $root->id)
            ->set('activityReplyBody', 'Réplica inline del editor.')
            ->call('saveActivityReply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('activity_comments', [
            'parent_id' => $root->id,
            'body' => 'Réplica inline del editor.',
            'is_approved' => 1,
            'is_instructor_reply' => 1,
        ]);
    }

    /** @test */
    public function other_profesor_cannot_open_activity_editor(): void
    {
        Livewire::actingAs($this->otherProfesorUser)
            ->test(\App\Livewire\Profesor\Lms\ActivityEditor::class, ['activity' => $this->activity])
            ->assertStatus(403);
    }

    /** @test */
    public function moderation_reply_to_rejected_comment_is_blocked(): void
    {
        $root = ActivityComment::create([
            'activity_id' => $this->activity->id,
            'user_id' => $this->studentUser->id,
            'body' => 'Comentario rechazado.',
            'is_approved' => false,
            'rejected_at' => now(),
            'rejected_by' => $this->profesorUser->id,
        ]);

        Livewire::actingAs($this->profesorUser)
            ->test(\App\Livewire\Profesor\Lms\CommentModeration::class)
            ->set('tab', 'rejected')
            ->call('openReply', $root->id)
            ->set('replyBody', 'Intento de réplica.')
            ->call('saveReply')
            ->assertHasNoErrors()
            ->assertNotDispatched('comment-approved');

        $this->assertDatabaseMissing('activity_comments', ['parent_id' => $root->id]);
    }

    // ─── Livewire: ActivityView (estudiante) ─────────────────────

    /** @test */
    public function student_view_shows_professor_replies_nested(): void
    {
        $root = $this->createApprovedRootComment();
        (new CommentModerationService($this->profesorUser))->reply($root, 'Respuesta visible para el estudiante.');

        Livewire::actingAs($this->studentUser)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $this->activity])
            ->assertSee('Comentario del estudiante.')
            ->assertSee('Respuesta visible para el estudiante.')
            ->assertSee('Profesor');
    }

    /** @test */
    public function student_view_does_not_show_replies_of_rejected_roots(): void
    {
        $root = ActivityComment::create([
            'activity_id' => $this->activity->id,
            'user_id' => $this->studentUser->id,
            'body' => 'Comentario rechazado.',
            'is_approved' => false,
            'rejected_at' => now(),
            'rejected_by' => $this->profesorUser->id,
        ]);
        // La réplica se crea directo (vía servicio está bloqueado) para
        // verificar que la vista no expone raíces rechazadas ni sus réplicas.
        \Database\Factories\ActivityCommentFactory::new()
            ->replyTo($root, $this->profesorUser)
            ->create(['body' => 'Réplica oculta.']);

        Livewire::actingAs($this->studentUser)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $this->activity])
            ->assertDontSee('Comentario rechazado.')
            ->assertDontSee('Réplica oculta.');
    }

    // ─── Notificación al autor ────────────────────────────────────

    /** @test */
    public function replying_creates_database_notification_for_author(): void
    {
        $root = $this->createApprovedRootComment();

        (new CommentModerationService($this->profesorUser))->reply($root, 'Respuesta del profesor.');

        $this->assertDatabaseHas('notifications', [
            'type' => CommentRepliedNotification::class,
            'notifiable_id' => $this->studentUser->id,
            'notifiable_type' => User::class,
        ]);
    }

    /** @test */
    public function notification_payload_contains_reply_context(): void
    {
        $root = $this->createApprovedRootComment();
        (new CommentModerationService($this->profesorUser))->reply($root, 'Respuesta del profesor.');

        $notification = DB::table('notifications')
            ->where('type', CommentRepliedNotification::class)
            ->where('notifiable_id', $this->studentUser->id)
            ->first();

        $this->assertNotNull($notification);

        $data = json_decode($notification->data, true);

        $this->assertEquals('comment_replied', $data['type']);
        $this->assertEquals($this->activity->id, $data['activity_id']);
        $this->assertStringContainsString('Respuesta del profesor', $data['reply_body']);
        $this->assertEquals(route('student.lms.activity', $this->activity->id), $data['url']);
    }

    // ─── Rate limiting (anti-spam, mejora #9) ────────────────────

    /** @test */
    public function moderation_replies_are_rate_limited_per_user(): void
    {
        $root = $this->createApprovedRootComment();

        $test = Livewire::actingAs($this->profesorUser)
            ->test(\App\Livewire\Profesor\Lms\CommentModeration::class)
            ->set('tab', 'approved');

        for ($i = 0; $i < 15; $i++) {
            $test->call('openReply', $root->id)
                ->set('replyBody', 'Réplica '.$i)
                ->call('saveReply');
        }

        // La 16ª intentona supera el límite → se bloquea sin crear réplica.
        $test->call('openReply', $root->id)
            ->set('replyBody', 'Réplica bloqueada')
            ->call('saveReply')
            ->assertHasNoErrors();

        $this->assertSame(15, ActivityComment::where('parent_id', $root->id)->count());
        $this->assertDatabaseMissing('activity_comments', [
            'parent_id' => $root->id,
            'body' => 'Réplica bloqueada',
        ]);
    }

    /** @test */
    public function student_comments_are_rate_limited_per_user(): void
    {
        $this->createApprovedRootComment();

        $test = Livewire::actingAs($this->studentUser)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $this->activity]);

        for ($i = 0; $i < 10; $i++) {
            $test->set('newComment', 'Comentario '.$i)
                ->call('saveComment');
        }

        // La 11ª intentona supera el límite → se bloquea sin crear comentario.
        $test->set('newComment', 'Comentario bloqueado')
            ->call('saveComment')
            ->assertHasNoErrors();

        $this->assertSame(
            10,
            ActivityComment::where('activity_id', $this->activity->id)
                ->where('user_id', $this->studentUser->id)
                ->where('is_approved', false)
                ->count()
        );
        $this->assertDatabaseMissing('activity_comments', [
            'activity_id' => $this->activity->id,
            'user_id' => $this->studentUser->id,
            'body' => 'Comentario bloqueado',
        ]);
    }
}
