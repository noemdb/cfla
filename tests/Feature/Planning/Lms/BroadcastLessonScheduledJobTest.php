<?php

namespace Tests\Feature\Planning\Lms;

use App\Events\Lms\LessonScheduled;
use App\Jobs\BroadcastLessonScheduled;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Opción 9 — Cola diferida + reintentos: el job de respaldo BroadcastLessonScheduled
 * re-emite el broadcast a Reverb cuando el push inmediato falló (Reverb caído).
 * Verifica los reintentos/backoff configurados y que handle() re-despacha el evento.
 */
class BroadcastLessonScheduledJobTest extends TestCase
{
    use DatabaseTransactions;

    private function makeLesson(array $options = []): array
    {
        $peducativo = Peducativo::factory()->create(['status_active' => 'true']);

        $pestudio = Pestudio::factory()->create([
            'peducativo_id' => $peducativo->id,
            'status_active' => 'true',
            'planning_module' => 1,
        ]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);

        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '87654321',
            'status_active' => 'true',
        ]);

        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic' => 'Lección de prueba',
            'status' => true,
        ]);

        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Sección 1',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        return [$activity, $peducativo, $asignatura];
    }

    public function test_job_tiene_reintentos_y_backoff_configurados(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);
        [$activity] = $this->makeLesson();

        $job = new BroadcastLessonScheduled($activity, [$planner], 'Carlos Méndez', '12/08/2026 10:00');

        $this->assertSame(3, $job->tries);
        $this->assertSame([10, 60, 300], $job->backoff);
    }

    public function test_handle_reemite_evento_broadcast(): void
    {
        Event::fake();
        Queue::fake();

        $planner = User::factory()->create(['is_planner' => true]);
        [$activity] = $this->makeLesson();

        $job = new BroadcastLessonScheduled($activity, [$planner], 'Carlos Méndez', '12/08/2026 10:00');
        $job->handle();

        // Re-emite el mismo evento de broadcast a Reverb.
        Event::assertDispatched(LessonScheduled::class);
    }
}
