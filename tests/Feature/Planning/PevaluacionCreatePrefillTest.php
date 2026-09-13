<?php

namespace Tests\Feature\Planning;

use App\Livewire\Planning\Pevaluacion\IndexComponent;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Al abrir "Nueva Asignación" con filtros activos, el formulario debe
 * pre-cargar el plan de estudio, grado, sección, asignatura, profesor y lapso
 * seleccionados.
 */
class PevaluacionCreatePrefillTest extends TestCase
{
    use DatabaseTransactions;

    public function test_create_prefills_from_active_filters(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => true,
        ]);
        $lapso = Lapso::factory()->create();
        $profesor = Profesor::create([
            'user_id' => User::factory()->create()->id,
            'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => 'prefill-'.uniqid(),
            'status_active' => 'true',
        ]);

        $component = Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->set('filter_pestudio', $pestudio->id)
            ->set('filter_grado', $grado->id)
            ->set('filter_seccion', $seccion->id)
            ->set('filter_asignatura', $asignatura->id)
            ->set('filter_profesor', $profesor->id)
            ->set('filter_lapso', $lapso->id)
            ->call('create');

        $this->assertSame($pestudio->id, $component->get('form.pestudio_id'));
        $this->assertSame($grado->id, $component->get('form.grado_id'));
        $this->assertSame($seccion->id, $component->get('form.seccion_id'));
        $this->assertSame($pensum->id, $component->get('form.pensum_id'));
        $this->assertSame($profesor->id, $component->get('form.profesor_id'));
        $this->assertSame($lapso->id, $component->get('form.lapso_id'));
    }
}
