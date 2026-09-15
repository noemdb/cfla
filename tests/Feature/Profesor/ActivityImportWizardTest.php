<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Activity\ActivityImportWizard;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
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
}
