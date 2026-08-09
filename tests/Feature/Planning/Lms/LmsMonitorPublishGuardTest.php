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
 * Prohibición de publicación en el monitor LMS de PLANIFICACIÓN:
 * no se puede publicar/programar una lección cuya activity asociada
 * esté en revisión (status=0). Misma regla que Coordinación/LessonList.
 */
class LmsMonitorPublishGuardTest extends TestCase
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
    public function publish_es_bloqueado_cuando_la_activity_esta_en_revision(): void
    {
        $lessonId = $this->createLesson(approved: false);

        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->call('publish', $lessonId);

        // La publicación NO pasó a PUBLISHED: sigue SCHEDULED.
        $pub = LmsActivityPublication::where('activity_id', $lessonId)->first();
        $this->assertSame('SCHEDULED', $pub->status);
        $this->assertNull($pub->published_at);
    }

    /** @test */
    public function publish_permite_cuando_la_activity_esta_aprobada(): void
    {
        $lessonId = $this->createLesson(approved: true);

        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->call('publish', $lessonId);

        $pub = LmsActivityPublication::where('activity_id', $lessonId)->first();
        $this->assertSame('PUBLISHED', $pub->status);
        $this->assertNotNull($pub->published_at);
    }

    /** @test */
    public function save_schedule_es_bloqueado_cuando_la_activity_esta_en_revision(): void
    {
        $lessonId = $this->createLesson(approved: false);

        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->set('scheduleActivityId', $lessonId)
            ->set('schedulePublishAt', now()->addDay()->format('Y-m-d H:i'))
            ->call('saveSchedule');

        $pub = LmsActivityPublication::where('activity_id', $lessonId)->first();
        $this->assertSame('SCHEDULED', $pub->status);
        $this->assertNull($pub->published_at);
    }

    /** @test */
    public function bulk_publish_omite_las_activities_en_revision(): void
    {
        $approvedId = $this->createLesson(approved: true);
        $inReviewId = $this->createLesson(approved: false);

        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->set('selectedIds', [$approvedId, $inReviewId])
            ->call('bulkPublish');

        $this->assertSame(
            'PUBLISHED',
            LmsActivityPublication::where('activity_id', $approvedId)->value('status')
        );
        // La no aprobada NO se publicó.
        $this->assertSame(
            'SCHEDULED',
            LmsActivityPublication::where('activity_id', $inReviewId)->value('status')
        );
    }
}
