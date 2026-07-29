<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use App\Services\Lms\CommentModerationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use DatabaseTransactions;

    private User $profesorUser;
    private Profesor $profesor;
    private User $otherProfesorUser;
    private Profesor $otherProfesor;
    private Activity $activity;
    private Activity $otherActivity;

    protected function setUp(): void
    {
        parent::setUp();

        // ─── Profesor 1 ──────────────────────────────────────────
        $this->profesorUser = User::factory()->create([
            'is_profesor' => true,
        ]);
        $this->profesor = Profesor::create([
            'user_id'      => $this->profesorUser->id,
            'name'         => 'Carlos',
            'lastname'     => 'Méndez',
            'ci_profesor'  => '12345678',
            'status_active' => 'true',
        ]);

        // ─── Profesor 2 ──────────────────────────────────────────
        $this->otherProfesorUser = User::factory()->create([
            'is_profesor' => true,
        ]);
        $this->otherProfesor = Profesor::create([
            'user_id'      => $this->otherProfesorUser->id,
            'name'         => 'María',
            'lastname'     => 'López',
            'ci_profesor'  => '87654321',
            'status_active' => 'true',
        ]);

        // ─── Estructura académica compartida ─────────────────────
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id'  => $pestudio->id,
            'grado_id'     => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        // ─── Pevaluacion + Activity para Profesor 1 ────────────
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $this->profesor->id,
            'pensum_id'   => $pensum->id,
            'seccion_id'  => $seccion->id,
            'lapso_id'    => $lapso->id,
        ]);

        $this->activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic' => 'Actividad de Carlos',
        ]);

        // ─── Pevaluacion + Activity para Profesor 2 ────────────
        $pevaluacion2 = Pevaluacion::create([
            'profesor_id' => $this->otherProfesor->id,
            'pensum_id'   => $pensum->id,
            'seccion_id'  => $seccion->id,
            'lapso_id'    => $lapso->id,
        ]);

        $this->otherActivity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion2->id,
            'topic' => 'Actividad de María',
        ]);
    }

    /**
     * Helper para crear un comentario pendiente en una actividad.
     */
    private function createPendingComment(Activity $activity, array $extra = []): ActivityComment
    {
        $student = User::factory()->create(['is_student' => true]);

        return ActivityComment::create(array_merge([
            'activity_id' => $activity->id,
            'user_id'     => $student->id,
            'body'        => 'Este es un comentario de prueba.',
            'is_approved' => false,
        ], $extra));
    }

    // ─── HTTP Tests ─────────────────────────────────────────────

    /** @test */
    public function profesor_can_access_moderation_page(): void
    {
        $this->actingAs($this->profesorUser)
            ->get('/app/profesors/lms/comments')
            ->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_users_are_redirected(): void
    {
        $this->get('/app/profesors/lms/comments')
            ->assertRedirect('/login');
    }

    /** @test */
    public function non_profesor_gets_403(): void
    {
        $nonProfesor = User::factory()->create([
            'is_profesor' => false,
            'is_admin'    => false,
        ]);

        $this->actingAs($nonProfesor)
            ->get('/app/profesors/lms/comments')
            ->assertForbidden();
    }

    // ─── Livewire Tests ─────────────────────────────────────────

    /** @test */
    public function profesor_sees_only_own_pending_comments(): void
    {
        $myComment = $this->createPendingComment($this->activity, [
            'body' => 'Comentario de mi actividad',
        ]);
        $otherComment = $this->createPendingComment($this->otherActivity, [
            'body' => 'Comentario de otra actividad',
        ]);

        Livewire::actingAs($this->profesorUser)
            ->test(\App\Livewire\Profesor\Lms\CommentModeration::class)
            ->assertSee('Comentario de mi actividad')
            ->assertDontSee('Comentario de otra actividad');
    }

    /** @test */
    public function admin_sees_all_comments(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->createPendingComment($this->activity, [
            'body' => 'Comentario profe 1',
        ]);
        $this->createPendingComment($this->otherActivity, [
            'body' => 'Comentario profe 2',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Profesor\Lms\CommentModeration::class)
            ->assertSee('Comentario profe 1')
            ->assertSee('Comentario profe 2');
    }

    /** @test */
    public function profesor_can_approve_a_comment(): void
    {
        $comment = $this->createPendingComment($this->activity);

        Livewire::actingAs($this->profesorUser)
            ->test(\App\Livewire\Profesor\Lms\CommentModeration::class)
            ->call('approveComment', $comment->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('activity_comments', [
            'id' => $comment->id,
            'is_approved' => 1,
        ]);
        $this->assertNotNull($comment->fresh()->approved_at);
    }

    /** @test */
    public function profesor_can_reject_a_comment(): void
    {
        $comment = $this->createPendingComment($this->activity);

        Livewire::actingAs($this->profesorUser)
            ->test(\App\Livewire\Profesor\Lms\CommentModeration::class)
            ->call('confirmReject', $comment->id)
            ->set('rejectReason', 'Contenido inapropiado')
            ->call('rejectComment')
            ->assertHasNoErrors();

        $comment->refresh();
        $this->assertNotNull($comment->rejected_at);
        $this->assertEquals('Contenido inapropiado', $comment->rejected_reason);
    }

    /** @test */
    public function pending_scope_excludes_approved_and_rejected(): void
    {
        $this->createPendingComment($this->activity);
        $approved = $this->createPendingComment($this->activity, [
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $this->profesorUser->id,
        ]);
        $rejected = $this->createPendingComment($this->activity, [
            'rejected_at' => now(),
            'rejected_by' => $this->profesorUser->id,
        ]);

        $pendings = ActivityComment::pending()->get();

        $this->assertCount(1, $pendings);
    }

    /** @test */
    public function moderator_service_canModerate_returns_false_for_other(): void
    {
        $service = new CommentModerationService($this->profesorUser);
        $otherComment = $this->createPendingComment($this->otherActivity);

        $this->assertFalse($service->canModerate($otherComment));
    }

    /** @test */
    public function moderator_service_canModerate_returns_true_for_own(): void
    {
        $service = new CommentModerationService($this->profesorUser);
        $ownComment = $this->createPendingComment($this->activity);

        $this->assertTrue($service->canModerate($ownComment));
    }

    /** @test */
    public function moderator_service_countPending_returns_correct_count(): void
    {
        $this->createPendingComment($this->activity);
        $this->createPendingComment($this->activity);
        // This one belongs to other profesor, should NOT be counted
        $this->createPendingComment($this->otherActivity);

        $service = new CommentModerationService($this->profesorUser);

        $this->assertEquals(2, $service->countPending());
    }
}
