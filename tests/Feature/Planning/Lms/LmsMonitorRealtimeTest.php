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
use ReflectionMethod;
use Tests\TestCase;

/**
 * Opción 6 — Vista en vivo del monitor: el listado se re-renderiza cuando
 * llega el broadcast de lección programada (sin recargar la página).
 */
class LmsMonitorRealtimeTest extends TestCase
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

    /** @test */
    public function listeners_incluyen_refresh_en_tiempo_real(): void
    {
        $component = Livewire::actingAs($this->planner)->test(LmsMonitor::class);

        $listeners = (new ReflectionMethod(LmsMonitor::class, 'getListeners'))->invoke($component->instance());

        $expected = 'echo-private:App.Models.User.'.$this->planner->id.',.lesson.scheduled';
        $this->assertArrayHasKey($expected, $listeners);
        $this->assertSame('refreshFromRealtimeEvent', $listeners[$expected]);
        $this->assertArrayHasKey('lesson-scheduled', $listeners);
        $this->assertSame('refreshFromRealtimeEvent', $listeners['lesson-scheduled']);
    }

    /** @test */
    public function refresh_from_realtime_event_muestra_la_leccion_nueva(): void
    {
        $topic = 'Lección que llega en vivo '.uniqid();

        $component = Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->set('filterStatus', 'SCHEDULED');

        // Antes del broadcast, la lección aún no existe → no se ve
        $component->assertDontSee($topic);

        // El profesor programa la lección (SCHEDULED) y llega el broadcast
        $this->createScheduledLesson($topic);

        // El listener re-renderiza el listado con los datos frescos
        $component->call('refreshFromRealtimeEvent')
            ->assertSee($topic);
    }
}