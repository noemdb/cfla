<?php

namespace Tests\Feature\Planning\Lms;

use App\Livewire\Planning\Lms\MonitorStats;
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

class MonitorStatsTest extends TestCase
{
    use DatabaseTransactions;

    private User $planner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->planner = User::factory()->create(['is_planner' => true]);
    }

    private function createLesson(string $status): Activity
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
            'status' => $status,
            'publish_at' => now(),
            'published_at' => $status === 'PUBLISHED' ? now() : null,
        ]);

        return $activity;
    }

    /** @test */
    public function renders_with_counts(): void
    {
        $baselineScheduled = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))->count();

        $this->createLesson('SCHEDULED');

        Livewire::actingAs($this->planner)
            ->test(MonitorStats::class)
            ->assertStatus(200)
            ->assertSet('scheduled', $baselineScheduled + 1)
            ->assertSet('total', Activity::count())
            ->assertSet('withContent', Activity::whereHas('lmsSections')->count())
            ->assertSee('Programadas');
    }

    /** @test */
    public function echo_listener_includes_event_name(): void
    {
        $component = Livewire::actingAs($this->planner)->test(MonitorStats::class);

        $listeners = (new ReflectionMethod(MonitorStats::class, 'getListeners'))->invoke($component->instance());

        $expected = 'echo-private:App.Models.User.'.$this->planner->id.',.lesson.scheduled';
        $this->assertArrayHasKey($expected, $listeners);
        $this->assertSame('refreshStatsFromEcho', $listeners[$expected]);
    }

    /** @test */
    public function refresh_stats_recounts_after_new_scheduled_lesson(): void
    {
        $baselineScheduled = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))->count();

        $component = Livewire::actingAs($this->planner)->test(MonitorStats::class);

        $component->assertSet('scheduled', $baselineScheduled);

        $this->createLesson('SCHEDULED');

        $component->call('refreshStats')
            ->assertSet('scheduled', $baselineScheduled + 1);
    }
}
