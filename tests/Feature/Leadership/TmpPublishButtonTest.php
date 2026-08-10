<?php

namespace Tests\Feature\Leadership;

use App\Livewire\Leadership\LessonMonitor;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
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

class TmpPublishButtonTest extends TestCase
{
    use DatabaseTransactions;

    public function test_publish_button_renders_for_scheduled_lesson(): void
    {
        $leader = User::factory()->leadership()->create();

        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '12345678',
            'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true', 'planning_module' => true]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();
        $area = AreaConocimiento::create([
            'leader_id' => $leader->id,
            'name' => 'Área de Ciencias',
            'code' => 'CIEN',
            'peducativo_id' => $pestudio->peducativo_id,
            'pestudio_id' => $pestudio->id,
            'order' => 1,
        ]);
        $asignatura->areasConocimiento()->attach($area->id);
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
            'topic' => 'Lección programada de prueba',
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
            'published_by' => $leader->id,
            'status' => 'SCHEDULED',
            'publish_at' => now(),
            'published_at' => null,
        ]);

        $html = Livewire::actingAs($leader)
            ->test(LessonMonitor::class)
            ->html();

        $this->assertStringContainsString('confirmPublishLesson', $html, 'Falta el botón de publicar en el HTML renderizado.');
        $this->assertStringContainsString('Publicar', $html);
    }
}
