<?php

namespace Tests\Feature\Diagnostic;

use App\Livewire\Admin\Diagnostic\IndexComponent;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Instrument\DiagQuestion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class PlanningDiagnosticActivationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_no_activa_area_sin_preguntas(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $pensum = $this->makePensum($pestudio, $grado, withQuestion: false);

        Livewire::test(IndexComponent::class)->call('toggleStatus', $pensum->id);

        $this->assertFalse((bool) $pensum->fresh()->status_active_diagnostic);
    }

    public function test_activa_area_con_preguntas(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $pensum = $this->makePensum($pestudio, $grado, withQuestion: true);

        Livewire::test(IndexComponent::class)->call('toggleStatus', $pensum->id);

        $this->assertTrue((bool) $pensum->fresh()->status_active_diagnostic);
    }

    public function test_activacion_masiva_omite_areas_sin_preguntas(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);

        $conPreguntas = $this->makePensum($pestudio, $grado, withQuestion: true);
        $sinPreguntas = $this->makePensum($pestudio, $grado, withQuestion: false);

        Livewire::test(IndexComponent::class)
            ->call('toggleAllGrado', $pestudio->id, $grado->id, true);

        $this->assertTrue((bool) $conPreguntas->fresh()->status_active_diagnostic);
        $this->assertFalse((bool) $sinPreguntas->fresh()->status_active_diagnostic);
    }

    public function test_activacion_masiva_registra_bitacora(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $this->makePensum($pestudio, $grado, withQuestion: true);

        Livewire::test(IndexComponent::class)
            ->call('toggleAllGrado', $pestudio->id, $grado->id, true);

        $this->assertDatabaseHas('binnacle_entries', [
            'event_type' => 'diagnostic_bulk_activation',
        ]);
    }

    private function makePensum(Pestudio $pestudio, Grado $grado, bool $withQuestion): Pensum
    {
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);

        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => true,
            'status_active_diagnostic' => false,
        ]);

        if ($withQuestion) {
            DiagQuestion::create([
                'pensum_id' => $pensum->id,
                'pregunta' => '¿Pregunta de prueba?',
                'tipo_pregunta' => 'multiple',
                'orden' => 1,
                'weighing' => 1,
                'difficulty' => 'easy',
                'activo' => true,
            ]);
        }

        return $pensum;
    }
}
