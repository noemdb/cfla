<?php

namespace Tests\Feature\Planning\Lms;

use App\Livewire\Planning\Lms\LmsMonitor;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Modal "Actividad asociada" (revisión / aprobación) del monitor LMS de
 * PLANIFICACIÓN (/app/planning/lms/monitor): permite al planificador revisar
 * y aprobar la actividad asociada a una lección.
 */
class LmsMonitorActivityReviewTest extends TestCase
{
    use DatabaseTransactions;

    private User $planner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->planner = User::factory()->create(['is_planner' => true]);
    }

    private function createLesson(bool $approved): int
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '12345678',
            'status_active' => 'true',
        ]);

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

        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic' => 'Lección de prueba',
            'status' => $approved,
        ]);

        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Sección 1',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        LmsActivityPublication::factory()->create([
            'activity_id' => $activity->id,
            'published_by' => $this->planner->id,
            'status' => 'SCHEDULED',
            'publish_at' => now(),
            'published_at' => null,
        ]);

        return $activity->id;
    }

    /** @test */
    public function activity_modal_opens_with_activity_data(): void
    {
        $lessonId = $this->createLesson(approved: false);

        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->call('openActivityReview', $lessonId)
            ->assertSet('showActivityModal', true)
            ->assertSet('activity_id', $lessonId)
            ->assertSet('activity_status', 0)
            ->assertSet('comments', '');
    }

    /** @test */
    public function activity_review_can_approve_associated_activity(): void
    {
        $lessonId = $this->createLesson(approved: false);

        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->call('openActivityReview', $lessonId)
            ->set('activity_status', 1)
            ->set('comments', 'Aprobada tras revisar el contenido.')
            ->call('saveActivityReview')
            ->assertSet('showActivityModal', false);

        $this->assertTrue((bool) Activity::find($lessonId)->status);
        $this->assertSame('Aprobada tras revisar el contenido.', Activity::find($lessonId)->comments);
    }

    /** @test */
    public function activity_review_keeps_activity_in_revision_when_not_approved(): void
    {
        $lessonId = $this->createLesson(approved: false);

        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->call('openActivityReview', $lessonId)
            ->set('activity_status', 0)
            ->call('saveActivityReview')
            ->assertSet('showActivityModal', false);

        $this->assertFalse((bool) Activity::find($lessonId)->status);
    }
}
