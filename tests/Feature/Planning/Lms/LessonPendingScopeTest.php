<?php

namespace Tests\Feature\Planning\Lms;

use App\Livewire\Planning\Lms\LessonPendingCount;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Peducativo;
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
 * Opción 2 — Contador por rol/scope: el badge de SCHEDULED se scopea según
 * el rol del usuario (Admin/Planner/Director globales; Coordinación por
 * peducativos; Leadership por áreas asignadas).
 */
class LessonPendingScopeTest extends TestCase
{
    use DatabaseTransactions;

    private function makeProfesor(): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '12345678',
            'status_active' => 'true',
        ]);

        return ['id' => $profesor->id, 'user_id' => $user->id];
    }

    /**
     * Crea una lección SCHEDULED en un pestudio dado. Devuelve la Activity.
     */
    private function makeScheduledLesson(Pestudio $pestudio, string $topic): Activity
    {
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $this->makeProfesor()['id'],
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
            'published_by' => User::factory()->create()->id,
            'status' => 'SCHEDULED',
            'publish_at' => now(),
            'published_at' => null,
        ]);

        return $activity;
    }

    private function makePestudio(?User $manager = null): Pestudio
    {
        $peducativo = Peducativo::factory()->create([
            'manager_id' => $manager?->id,
            'status_active' => 'true',
        ]);

        return Pestudio::factory()->create([
            'peducativo_id' => $peducativo->id,
            'status_active' => 'true',
            'planning_module' => 1,
        ]);
    }

    /** @test */
    public function admin_y_planner_y_director_ven_todas_las_scheduled(): void
    {
        $pestudioA = $this->makePestudio();
        $pestudioB = $this->makePestudio();
        $this->makeScheduledLesson($pestudioA, 'Global A '.uniqid());
        $this->makeScheduledLesson($pestudioB, 'Global B '.uniqid());

        foreach (['is_admin' => true, 'is_planner' => true, 'is_director' => true] as $field => $value) {
            $user = User::factory()->create([$field => $value]);

            // Sin leídas previas: el badge cuenta todas las SCHEDULED de la BD
            // (no restringidas por scope).
            $expected = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
                ->count();

            Livewire::actingAs($user)
                ->test(LessonPendingCount::class)
                ->assertSet('count', $expected);
        }
    }

    /** @test */
    public function coordinacion_solo_ve_su_scope_de_peducativos(): void
    {
        $coordinator = User::factory()->coordinacion()->create();

        $myPestudio = $this->makePestudio($coordinator);
        $otherPestudio = $this->makePestudio();

        $this->makeScheduledLesson($myPestudio, 'Mi scope '.uniqid());
        $this->makeScheduledLesson($otherPestudio, 'Fuera de mi scope '.uniqid());

        // Esperado = SCHEDULED dentro del pestudio gestionado por el coordinador.
        $expected = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
            ->whereHas('pevaluacion.pensum', fn ($q) => $q->where('pestudio_id', $myPestudio->id))
            ->count();

        Livewire::actingAs($coordinator)
            ->test(LessonPendingCount::class)
            ->assertSet('count', $expected);
    }

    /** @test */
    public function coordinacion_sin_scope_no_ve_nada(): void
    {
        $coordinator = User::factory()->coordinacion()->create();

        // Lecciones SCHEDULED de pestudios que el coordinador NO gestiona.
        $this->makeScheduledLesson($this->makePestudio(), 'Sin scope '.uniqid());
        $this->makeScheduledLesson($this->makePestudio(), 'Sin scope 2 '.uniqid());

        Livewire::actingAs($coordinator)
            ->test(LessonPendingCount::class)
            ->assertSet('count', 0);
    }

    /** @test */
    public function leadership_solo_ve_sus_areas_asignadas(): void
    {
        $leader = User::factory()->leadership()->create();

        $myPestudio = $this->makePestudio();
        $otherPestudio = $this->makePestudio();

        // Área del líder cubriendo UNA asignatura del pestudio A.
        $myAsignatura = Asignatura::factory()->create(['pestudio_id' => $myPestudio->id]);
        $area = AreaConocimiento::create([
            'leader_id' => $leader->id,
            'name' => 'Área del líder',
            'code' => 'LEAD',
            'peducativo_id' => $myPestudio->peducativo_id,
            'pestudio_id' => $myPestudio->id,
            'order' => 1,
        ]);
        $myAsignatura->areasConocimiento()->attach($area->id);

        // Lección dentro del área del líder (usando esa asignatura).
        $grado = Grado::factory()->create(['pestudio_id' => $myPestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $myPestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $myAsignatura->id,
        ]);
        $lapso = Lapso::factory()->create();
        $peva = Pevaluacion::create([
            'profesor_id' => $this->makeProfesor()['id'],
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);
        $myActivity = Activity::factory()->create(['pevaluacion_id' => $peva->id, 'topic' => 'En mi área', 'status' => true]);
        LmsActivitySection::create(['activity_id' => $myActivity->id, 'title' => 'S', 'sort_order' => 1, 'is_visible' => true]);
        LmsActivityPublication::factory()->create([
            'activity_id' => $myActivity->id,
            'published_by' => User::factory()->create()->id,
            'status' => 'SCHEDULED',
            'publish_at' => now(),
            'published_at' => null,
        ]);

        // Lección fuera del área del líder (pestudio B).
        $this->makeScheduledLesson($otherPestudio, 'Fuera de mi área '.uniqid());

        // Esperado = SCHEDULED de la asignatura cubierta por el área del líder.
        $expected = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
            ->whereHas('pevaluacion.pensum', fn ($q) => $q->where('asignatura_id', $myAsignatura->id))
            ->count();

        Livewire::actingAs($leader)
            ->test(LessonPendingCount::class)
            ->assertSet('count', $expected);
    }

    /** @test */
    public function leadership_sin_areas_asignadas_no_ve_nada(): void
    {
        $leader = User::factory()->leadership()->create();

        $this->makeScheduledLesson($this->makePestudio(), 'Sin área '.uniqid());
        $this->makeScheduledLesson($this->makePestudio(), 'Sin área 2 '.uniqid());

        Livewire::actingAs($leader)
            ->test(LessonPendingCount::class)
            ->assertSet('count', 0);
    }
}
