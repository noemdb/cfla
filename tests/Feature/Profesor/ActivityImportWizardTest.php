<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Activity\ActivityImportWizard;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Lapso;
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
 * Wizard de importación de actividades desde otra sección del mismo grado.
 *
 * Paso 1: secciones hermanas del grado.
 * Paso 2: selección una a una de las actividades origen.
 * Paso 3: vista previa y guardado (clona actividades + indicadores).
 */
class ActivityImportWizardTest extends TestCase
{
    use DatabaseTransactions;

    private static int $counter = 0;

    /**
     * @return array{user: User, pevTarget: Pevaluacion, pevSource: Pevaluacion, seccionA: Seccion, seccionB: Seccion, activity1: Activity, activity2: Activity}
     */
    private function fixture(): array
    {
        self::$counter++;
        $n = self::$counter;

        $user = User::factory()->create(['is_profesor' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true', 'planning_module' => true]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '880'.$n, 'status_active' => 'true',
        ]);
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);

        $pevTarget = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccionA->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);
        $pevSource = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccionB->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $activity1 = Activity::create([
            'pevaluacion_id' => $pevSource->id, 'finicial' => now(), 'ffinal' => now()->addDays(7),
            'topic' => 'Tema 1', 'thematic' => 'Tejido 1', 'description' => 'Desc 1',
            'teaching' => 'INICIO DESARROLLO CIERRE', 'learning' => 'Aprendizaje 1',
        ]);
        $activity1->achievements()->create(['name' => 'Indicador 1', 'weighting' => 50]);

        $activity2 = Activity::create([
            'pevaluacion_id' => $pevSource->id, 'finicial' => now(), 'ffinal' => now()->addDays(7),
            'topic' => 'Tema 2', 'thematic' => 'Tejido 2', 'description' => 'Desc 2',
            'teaching' => 'INICIO DESARROLLO CIERRE', 'learning' => 'Aprendizaje 2',
        ]);
        $activity2->achievements()->create(['name' => 'Indicador 2', 'weighting' => 50]);

        return compact('user', 'pevTarget', 'pevSource', 'seccionA', 'seccionB', 'activity1', 'activity2');
    }

    public function test_paso_1_lista_las_secciones_hermanas_del_grado(): void
    {
        $f = $this->fixture();

        $component = Livewire::actingAs($f['user'])->test(ActivityImportWizard::class)
            ->call('open', $f['pevTarget']->id)
            ->assertSet('showModal', true)
            ->assertSet('step', 1);

        $sections = $component->get('sections');

        // Solo la sección hermana (no la del destino), con su conteo.
        $this->assertCount(1, $sections);
        $this->assertSame($f['seccionB']->id, $sections[0]['id']);
        $this->assertSame(2, $sections[0]['activities_count']);
    }

    public function test_paso_2_selecciona_actividades_una_por_una(): void
    {
        $f = $this->fixture();

        $component = Livewire::actingAs($f['user'])->test(ActivityImportWizard::class)
            ->call('open', $f['pevTarget']->id)
            ->call('selectSection', $f['seccionB']->id)
            ->assertSet('step', 2);

        $this->assertCount(2, $component->get('sourceActivities'));
        // Preseleccionadas todas.
        $this->assertCount(2, $component->get('selectedActivityIds'));

        // Quitar todas y dejar solo una.
        $component->call('toggleAll');
        $this->assertSame([], $component->get('selectedActivityIds'));

        $component->set('selectedActivityIds', [$f['activity1']->id]);
        $this->assertSame([$f['activity1']->id], $component->get('selectedActivityIds'));
    }

    public function test_paso_3_vista_previa_y_guardado_clona_seleccionadas(): void
    {
        $f = $this->fixture();

        $component = Livewire::actingAs($f['user'])->test(ActivityImportWizard::class)
            ->call('open', $f['pevTarget']->id)
            ->call('selectSection', $f['seccionB']->id)
            ->set('selectedActivityIds', [$f['activity1']->id])
            ->call('goToPreview')
            ->assertSet('step', 3);

        $preview = $component->get('preview');
        $this->assertSame(1, $preview['activities_count']);
        $this->assertSame(1, $preview['achievements_count']);
        $this->assertSame('B', $preview['source_section']);

        $component->call('save')
            ->assertSet('showModal', false)
            ->assertDispatched('activity-imported');

        $targetActivities = $f['pevTarget']->fresh()->activities()->with('achievements')->get();
        $this->assertCount(1, $targetActivities);
        $this->assertSame('Tema 1', $targetActivities->first()->topic);
        $this->assertNull($targetActivities->first()->comments);
        $this->assertCount(1, $targetActivities->first()->achievements);
        $this->assertSame('Indicador 1', $targetActivities->first()->achievements->first()->name);

        // No se toca la sección origen.
        $this->assertSame(2, $f['pevSource']->fresh()->activities()->count());
    }

    public function test_no_permite_previsualizar_sin_seleccion(): void
    {
        $f = $this->fixture();

        Livewire::actingAs($f['user'])->test(ActivityImportWizard::class)
            ->call('open', $f['pevTarget']->id)
            ->call('selectSection', $f['seccionB']->id)
            ->call('toggleAll')
            ->call('goToPreview')
            ->assertSet('step', 2);
    }

    public function test_seccion_hermana_sin_carga_academica_queda_en_cero(): void
    {
        $f = $this->fixture();

        // Tercera sección del mismo grado, sin Pevaluación de la asignatura.
        $seccionC = Seccion::factory()->create([
            'grado_id' => $f['seccionA']->grado_id, 'name' => 'C', 'status_active' => 'true',
        ]);

        $component = Livewire::actingAs($f['user'])->test(ActivityImportWizard::class)
            ->call('open', $f['pevTarget']->id);

        $sections = collect($component->get('sections'))->keyBy('id');
        $this->assertSame(0, $sections[$seccionC->id]['activities_count']);
        $this->assertSame(2, $sections[$f['seccionB']->id]['activities_count']);
    }

    public function test_destino_sin_grupo_estable_no_importa_desde_un_componente(): void
    {
        $f = $this->fixture();

        // En la sección hermana existe también un componente de formación con
        // la misma asignatura y más actividades. El destino es de sección
        // completa (grupo_estable_id = null), por lo que NO debe considerarlo.
        $grupo = GrupoEstable::factory()->create();
        $componente = Pevaluacion::factory()->create([
            'profesor_id' => $f['pevSource']->profesor_id,
            'seccion_id' => $f['seccionB']->id,
            'pensum_id' => $f['pevSource']->pensum_id,
            'lapso_id' => $f['pevSource']->lapso_id,
            'grupo_estable_id' => $grupo->id,
        ]);
        $this->createActivities($componente, 5);

        $sections = collect(
            Livewire::actingAs($f['user'])->test(ActivityImportWizard::class)
                ->call('open', $f['pevTarget']->id)
                ->get('sections')
        )->keyBy('id');

        // Solo cuenta la Pevaluación de sección completa (2), no el componente (5).
        $this->assertSame(2, $sections[$f['seccionB']->id]['activities_count']);
    }

    public function test_destino_con_grupo_estable_importa_desde_el_mismo_componente(): void
    {
        $f = $this->fixture();

        $grupo = GrupoEstable::factory()->create(['name' => 'Robótica']);

        // Destino: componente de formación en la sección A.
        $target = Pevaluacion::factory()->create([
            'profesor_id' => $f['pevTarget']->profesor_id,
            'seccion_id' => $f['seccionA']->id,
            'pensum_id' => $f['pevTarget']->pensum_id,
            'lapso_id' => $f['pevTarget']->lapso_id,
            'grupo_estable_id' => $grupo->id,
        ]);

        // Origen: el MISMO componente en la sección B (3 actividades).
        $sourceComponente = Pevaluacion::factory()->create([
            'profesor_id' => $f['pevSource']->profesor_id,
            'seccion_id' => $f['seccionB']->id,
            'pensum_id' => $f['pevSource']->pensum_id,
            'lapso_id' => $f['pevSource']->lapso_id,
            'grupo_estable_id' => $grupo->id,
        ]);
        $this->createActivities($sourceComponente, 3, 'Componente');

        // La Pevaluación de sección completa (2 actividades) no debe contarse.
        $component = Livewire::actingAs($f['user'])->test(ActivityImportWizard::class)
            ->call('open', $target->id)
            ->assertSet('step', 1);

        $sections = collect($component->get('sections'))->keyBy('id');
        $this->assertSame(3, $sections[$f['seccionB']->id]['activities_count']);

        $component->call('selectSection', $f['seccionB']->id)
            ->assertSet('step', 2);

        $this->assertCount(3, $component->get('sourceActivities'));
        $this->assertSame('Componente 1', $component->get('sourceActivities')[0]['topic']);

        $component->call('goToPreview')
            ->assertSet('step', 3)
            ->call('save')
            ->assertSet('showModal', false)
            ->assertDispatched('activity-imported');

        $this->assertSame(3, $target->fresh()->activities()->count());
        // El componente origen conserva sus actividades.
        $this->assertSame(3, $sourceComponente->fresh()->activities()->count());
    }

    private function createActivities(Pevaluacion $pevaluacion, int $count, string $prefix = 'Actividad'): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $activity = Activity::create([
                'pevaluacion_id' => $pevaluacion->id,
                'finicial' => now(),
                'ffinal' => now()->addDays(7),
                'topic' => "{$prefix} {$i}",
                'thematic' => "Temática {$i}",
                'description' => "Descripción {$i}",
                'teaching' => 'INICIO DESARROLLO CIERRE',
                'learning' => "Aprendizaje {$i}",
            ]);
            $activity->achievements()->create(['name' => "Indicador {$i}", 'weighting' => 50]);
        }
    }
}
