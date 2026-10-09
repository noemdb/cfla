<?php

namespace Tests\Feature\Planning;

use App\Livewire\Planning\Diagnostic\DiagMainViewer;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tab "Por Área Conoc." del visor de diagnósticos (planning/diagnostico):
 * agrega los pensums del DiagMain por AreaConocimiento vía
 * campo_conocimientos.pensum_id. Los pensums sin adscripción caen en la
 * fila "Sin área asignada".
 */
class DiagMainAreaTabTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tab_por_area_conocimiento_agrega_por_campo(): void
    {
        $user = User::factory()->create(['is_active' => 'enable']);
        $this->actingAs($user);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['status_active' => 'true']);
        $pensumConArea = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'status_active' => 1,
        ]);
        $pensumSinArea = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'status_active' => 1,
        ]);

        $area = AreaConocimiento::create([
            'pestudio_id' => $pestudio->id,
            'leader_id' => $user->id,
            'name' => 'Ciencias Naturales',
            'code' => 'CN',
        ]);
        CampoConocimiento::create([
            'area_conocimiento_id' => $area->id,
            'asignatura_id' => $pensumConArea->asignatura_id,
            'pensum_id' => $pensumConArea->id,
        ]);

        $diagMain = DiagMain::factory()->create(['name' => 'Diagnóstico de prueba']);

        // 2 preguntas activas en el pensum adscrito…
        foreach (range(1, 2) as $orden) {
            DiagQuestion::factory()->create([
                'pensum_id' => $pensumConArea->id,
                'diag_main_id' => $diagMain->id,
                'tipo_pregunta' => 'multiple',
                'orden' => $orden,
                'activo' => 1,
            ]);
        }
        // …1 activa + 1 inactiva en el pensum sin área (la inactiva no cuenta).
        DiagQuestion::factory()->create([
            'pensum_id' => $pensumSinArea->id,
            'diag_main_id' => $diagMain->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'activo' => 1,
        ]);
        DiagQuestion::factory()->create([
            'pensum_id' => $pensumSinArea->id,
            'diag_main_id' => $diagMain->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 2,
            'activo' => 0,
        ]);

        // Sesión completada con acierto en el área (sin estudiante: no hace falta).
        $pregunta = DiagQuestion::where('pensum_id', $pensumConArea->id)->first();
        $correcta = DiagOption::factory()->create([
            'question_id' => $pregunta->id,
            'opcion' => 'Correcta',
            'valor' => 1,
            'orden' => 1,
        ]);
        $session = DiagSession::create([
            'pensum_id' => $pensumConArea->id,
            'iniciado_at' => now()->subHour(),
            'completado_at' => now(),
            'total_preguntas' => 2,
            'progreso' => 100,
            'activo' => false,
            'diag_main_id' => null,
        ]);
        DiagAnswer::create([
            'session_id' => $session->id,
            'question_id' => $pregunta->id,
            'option_id' => $correcta->id,
            'completado_at' => now(),
        ]);

        $component = Livewire::test(DiagMainViewer::class)
            ->set('selectedId', $diagMain->id)
            ->call('setTab', 'areas_conocimiento')
            ->assertOk()
            ->assertSee('Resumen por Área de Conocimiento')
            ->assertSee('Ciencias Naturales')
            ->assertSee('Sin área asignada');

        $areaProgress = $component->viewData('areaProgress');
        $this->assertSame(2, $areaProgress->total());

        $filaArea = $areaProgress->firstWhere('fullname', 'CN — Ciencias Naturales');
        $this->assertNotNull($filaArea);
        $this->assertSame(1, $filaArea->pensums_count);
        $this->assertSame(2, $filaArea->total_questions);
        $this->assertSame(1, $filaArea->total_sessions);
        $this->assertSame(1, $filaArea->completed_sessions);
        $this->assertSame(100.0, $filaArea->completion_percentage);
        $this->assertSame(100.0, $filaArea->precision);

        $filaSinArea = $areaProgress->firstWhere('fullname', 'Sin área asignada');
        $this->assertNotNull($filaSinArea);
        $this->assertSame(1, $filaSinArea->total_questions);
        $this->assertSame(0, $filaSinArea->total_sessions);
    }

    public function test_filtro_por_area_angosta_a_una_fila(): void
    {
        $user = User::factory()->create(['is_active' => 'enable']);
        $this->actingAs($user);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['status_active' => 'true']);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'status_active' => 1,
        ]);

        $area = AreaConocimiento::create([
            'pestudio_id' => $pestudio->id,
            'leader_id' => $user->id,
            'name' => 'Matemática',
            'code' => 'MAT',
        ]);
        CampoConocimiento::create([
            'area_conocimiento_id' => $area->id,
            'asignatura_id' => $pensum->asignatura_id,
            'pensum_id' => $pensum->id,
        ]);

        $diagMain = DiagMain::factory()->create();
        DiagQuestion::factory()->create([
            'pensum_id' => $pensum->id,
            'diag_main_id' => $diagMain->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'activo' => 1,
        ]);

        // Un área ajena al diagnóstico se descarta del filtro.
        $otraArea = AreaConocimiento::create([
            'pestudio_id' => $pestudio->id,
            'name' => 'Lengua',
            'code' => 'LEN',
        ]);

        Livewire::test(DiagMainViewer::class)
            ->set('selectedId', $diagMain->id)
            ->call('setTab', 'areas_conocimiento')
            ->set('resumenAreaId', $area->id)
            ->assertOk()
            ->assertSee('Matemática');

        $component = Livewire::test(DiagMainViewer::class)
            ->set('selectedId', $diagMain->id)
            ->call('setTab', 'areas_conocimiento')
            ->set('resumenAreaId', $area->id);

        $this->assertSame(1, $component->viewData('areaProgress')->total());

        // El área ajena no pertenece al alcance: se limpia el filtro.
        Livewire::test(DiagMainViewer::class)
            ->set('selectedId', $diagMain->id)
            ->set('resumenAreaId', $otraArea->id)
            ->assertOk()
            ->assertSet('resumenAreaId', null);
    }

    public function test_diagnostico_sin_areas_no_rompe_el_tab(): void
    {
        $user = User::factory()->create(['is_active' => 'enable']);
        $this->actingAs($user);

        $pensum = Pensum::factory()->create(['status_active' => 1]);
        $diagMain = DiagMain::factory()->create();
        DiagQuestion::factory()->create([
            'pensum_id' => $pensum->id,
            'diag_main_id' => $diagMain->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'activo' => 1,
        ]);

        Livewire::test(DiagMainViewer::class)
            ->set('selectedId', $diagMain->id)
            ->call('setTab', 'areas_conocimiento')
            ->assertOk()
            ->assertSee('Sin área asignada')
            ->assertSee('campo_conocimientos');
    }
}
