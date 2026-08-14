<?php

namespace Tests\Feature\Planning\Lms;

use App\Livewire\Planning\Lms\LessonPendingCount;
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
use App\Models\UserLessonRead;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Opción 5 — Marcar como leída: la persistencia (tabla `user_lesson_reads`),
 * el contador de no-leídas del badge y el marcado masivo al abrir el monitor.
 */
class LessonReadTest extends TestCase
{
    use DatabaseTransactions;

    private User $planner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->planner = User::factory()->create(['is_planner' => true]);
    }

    private function createScheduledLesson(string $topic): Activity
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
            'topic' => $topic,
            'status' => true,
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

        return $activity;
    }

    private function scheduledBaseline(): int
    {
        return Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))->count();
    }

    /** @test */
    public function migracion_y_modelo_de_marcado_de_lectura(): void
    {
        $this->assertTrue(\Schema::hasTable('user_lesson_reads'));

        $activity = $this->createScheduledLesson('Lección para leer');
        UserLessonRead::create([
            'user_id' => $this->planner->id,
            'activity_id' => $activity->id,
            'read_at' => now(),
        ]);

        $this->assertDatabaseHas('user_lesson_reads', [
            'user_id' => $this->planner->id,
            'activity_id' => $activity->id,
        ]);

        // Relaciones: el user ve sus lecturas, la activity sus lectores
        $this->assertTrue($this->planner->lessonReads()->where('activity_id', $activity->id)->exists());
        $this->assertTrue($activity->lessonReads()->where('user_id', $this->planner->id)->exists());
    }

    /** @test */
    public function refresh_count_solo_cuenta_scheduled_no_leidas(): void
    {
        $baseline = $this->scheduledBaseline();

        $lesson = $this->createScheduledLesson('No leída '.uniqid());

        Livewire::actingAs($this->planner)
            ->test(LessonPendingCount::class)
            ->assertSet('count', $baseline + 1);

        // Se marca como leída → el badge baja en 1
        UserLessonRead::create([
            'user_id' => $this->planner->id,
            'activity_id' => $lesson->id,
            'read_at' => now(),
        ]);

        Livewire::actingAs($this->planner)
            ->test(LessonPendingCount::class)
            ->assertSet('count', $baseline);
    }

    /** @test */
    public function mark_as_read_es_batch_y_idempotente(): void
    {
        $lessonA = $this->createScheduledLesson('Batch A '.uniqid());
        $lessonB = $this->createScheduledLesson('Batch B '.uniqid());

        $component = Livewire::actingAs($this->planner)->test(LessonPendingCount::class);

        $component->call('markAsRead', [$lessonA->id, $lessonB->id]);

        $this->assertDatabaseHas('user_lesson_reads', ['user_id' => $this->planner->id, 'activity_id' => $lessonA->id]);
        $this->assertDatabaseHas('user_lesson_reads', ['user_id' => $this->planner->id, 'activity_id' => $lessonB->id]);

        // Idempotente: repetir no duplica filas (clave única user_id+activity_id)
        $component->call('markAsRead', [$lessonA->id, $lessonB->id]);

        $this->assertSame(
            1,
            UserLessonRead::where('user_id', $this->planner->id)
                ->where('activity_id', $lessonA->id)
                ->count()
        );
    }

    /** @test */
    public function mark_as_read_no_afecta_otro_usuario(): void
    {
        $lesson = $this->createScheduledLesson('Privacidad de lectura '.uniqid());
        $other = User::factory()->create(['is_planner' => true]);

        Livewire::actingAs($this->planner)
            ->test(LessonPendingCount::class)
            ->call('markAsRead', [$lesson->id]);

        // El otro planner sigue viéndola como pendiente (la lección ya está
        // incluida en el baseline, pues se creó antes de calcularlo).
        Livewire::actingAs($other)
            ->test(LessonPendingCount::class)
            ->assertSet('count', $this->scheduledBaseline());
    }

    /** @test */
    public function abrir_monitor_marca_scheduled_como_leidas(): void
    {
        $baseline = $this->scheduledBaseline();
        $lesson = $this->createScheduledLesson('Vista al abrir '.uniqid());

        // Antes de abrir el monitor: el badge la cuenta
        Livewire::actingAs($this->planner)
            ->test(LessonPendingCount::class)
            ->assertSet('count', $baseline + 1);

        // Se abre el monitor → la lección queda marcada como leída para este usuario
        Livewire::actingAs($this->planner)->test(LmsMonitor::class)->assertStatus(200);

        $this->assertDatabaseHas('user_lesson_reads', [
            'user_id' => $this->planner->id,
            'activity_id' => $lesson->id,
        ]);

        // Al abrir el monitor se marcan TODAS las SCHEDULED actuales como leídas
        // (incluidas las preexistentes): el badge de este usuario queda en 0.
        Livewire::actingAs($this->planner)
            ->test(LessonPendingCount::class)
            ->assertSet('count', 0);
    }

    /** @test */
    public function abrir_monitor_es_idempotente_y_respeta_lecturas_previas(): void
    {
        $lesson = $this->createScheduledLesson('Reapertura '.uniqid());

        Livewire::actingAs($this->planner)->test(LmsMonitor::class)->assertStatus(200);
        Livewire::actingAs($this->planner)->test(LmsMonitor::class)->assertStatus(200);

        $this->assertSame(
            1,
            UserLessonRead::where('user_id', $this->planner->id)
                ->where('activity_id', $lesson->id)
                ->count()
        );

        Livewire::actingAs($this->planner)
            ->test(LessonPendingCount::class)
            ->assertSet('count', 0);
    }
}
