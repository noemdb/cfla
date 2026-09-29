<?php

namespace Tests\Feature\Leadership;

use App\Livewire\Leadership\DiagnosticQuestionReview;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Registro de preguntas desde /app/leadership/diagnosticos.
 *
 * El botón "+ Nueva" junto al buscador abre el wizard en modo registro,
 * limitado a los pensums activos del ámbito del líder
 * (AreaConocimiento.leader_id → CampoConocimiento → pensum_id).
 */
class LeadershipQuestionCreateTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAmbito(User $leader): Pensum
    {
        $coord = User::factory()->create(['is_coordinacion' => true, 'is_active' => 'enable']);
        $ped = Peducativo::factory()->create(['manager_id' => $coord->id, 'status_active' => 'true']);
        $pestudio = Pestudio::factory()->create(['peducativo_id' => $ped->id, 'status_active' => 'true', 'planning_module' => 1]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => 1,
        ]);

        $area = AreaConocimiento::create([
            'peducativo_id' => $ped->id,
            'pestudio_id' => $pestudio->id,
            'leader_id' => $leader->id,
            'name' => 'Área test',
            'code' => 'AT'.uniqid(),
            'order' => 1,
        ]);
        CampoConocimiento::create([
            'area_conocimiento_id' => $area->id,
            'asignatura_id' => $asignatura->id,
            'pensum_id' => $pensum->id,
            'order' => 1,
        ]);

        return $pensum;
    }

    private function fillWizard($test, Pensum $pensum): void
    {
        $test->set('pensum_id', $pensum->id)
            ->set('tipo_pregunta', 'multiple')
            ->call('nextStep')
            ->set('pregunta', '¿Cuál es el resultado de sumar dos más dos en este contexto?')
            ->set('options', [
                ['opcion' => 'Tres', 'valor' => 0, 'orden' => 1],
                ['opcion' => 'Cuatro', 'valor' => 0, 'orden' => 2],
            ])
            ->set('correct_option_index', 1)
            ->call('nextStep');
    }

    public function test_leader_registra_pregunta_en_su_ambito(): void
    {
        $leader = User::factory()->leadership()->create();
        $pensum = $this->makeAmbito($leader);
        $this->actingAs($leader);

        $test = Livewire::test(DiagnosticQuestionReview::class)
            ->call('openCreateQuestionModal')
            ->assertSet('showQuestionModal', true)
            ->assertSet('isCreatingQuestion', true);

        $this->fillWizard($test, $pensum);
        $test->call('saveQuestion');

        $question = DiagQuestion::where('pensum_id', $pensum->id)
            ->where('pregunta', 'like', '%sumar dos más dos%')
            ->first();

        $this->assertNotNull($question, 'la pregunta debe existir en el pensum del ámbito');
        $this->assertSame('multiple', $question->tipo_pregunta);
        $this->assertCount(2, $question->options);
        $this->assertSame(1, (int) $question->options->firstWhere('opcion', 'Cuatro')->valor);
        $this->assertSame(0, (int) $question->options->firstWhere('opcion', 'Tres')->valor);
        $test->assertSet('showQuestionModal', false);
    }

    public function test_registro_rechaza_pensum_fuera_del_ambito(): void
    {
        $leader = User::factory()->leadership()->create();
        $this->makeAmbito($leader);
        $ajeno = Pensum::factory()->create();
        $this->actingAs($leader);

        $test = Livewire::test(DiagnosticQuestionReview::class)
            ->call('openCreateQuestionModal');

        $this->fillWizard($test, $ajeno);
        $test->call('saveQuestion');

        $this->assertSame(
            0,
            DiagQuestion::where('pensum_id', $ajeno->id)->count(),
            'no debe crear preguntas fuera del ámbito'
        );
        $test->assertSet('showQuestionModal', true);
    }

    public function test_sin_ambito_no_abre_modal_de_registro(): void
    {
        $leader = User::factory()->leadership()->create();
        $this->actingAs($leader);

        Livewire::test(DiagnosticQuestionReview::class)
            ->call('openCreateQuestionModal')
            ->assertSet('showQuestionModal', false)
            ->assertSet('isCreatingQuestion', false);
    }

    public function test_edicion_existente_sigue_funcionando(): void
    {
        $leader = User::factory()->leadership()->create();
        $pensum = $this->makeAmbito($leader);
        $question = DiagQuestion::create([
            'pensum_id' => $pensum->id,
            'pregunta' => 'Pregunta original para editar con suficiente longitud',
            'tipo_pregunta' => 'open',
            'orden' => 1,
            'weighing' => 1,
            'difficulty' => 'medium',
            'activo' => true,
        ]);
        $this->actingAs($leader);

        $test = Livewire::test(DiagnosticQuestionReview::class)
            ->call('openQuestionModal', $question->id)
            ->assertSet('isCreatingQuestion', false);

        $test->set('pregunta', 'Pregunta editada por el líder con suficiente longitud')
            ->call('nextStep')
            ->call('nextStep')
            ->call('saveQuestion');

        $this->assertSame(
            'Pregunta editada por el líder con suficiente longitud',
            $question->fresh()->pregunta
        );
        $test->assertSet('showQuestionModal', false);
    }
}
