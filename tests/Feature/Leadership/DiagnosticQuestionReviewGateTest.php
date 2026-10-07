<?php

namespace Tests\Feature\Leadership;

use App\Livewire\Leadership\DiagnosticQuestionReview;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Learner\Estudiant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regresión del gate por diagnóstico en leadership/diagnosticos:
 * sin filterDiagMain solo se muestra el div informativo (sin cifras,
 * sin tabs, sin "Resultados por estudiante"); al elegir uno aparece
 * el bloque de resultados por estudiante.
 */
class DiagnosticQuestionReviewGateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sin_diagnostigo_solo_muestra_el_aviso(): void
    {
        $user = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);
        $this->actingAs($user);

        Livewire::test(DiagnosticQuestionReview::class)
            ->assertOk()
            ->assertSee('No hay diagnóstico seleccionado')
            ->assertDontSee('Resultados por estudiante');
    }

    public function test_el_bloque_de_resultados_por_estudiante_aparece_con_diagnostico(): void
    {
        $user = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);
        $this->actingAs($user);

        // Un id inexistente mantiene el flujo de render sin tocar la BD.
        Livewire::test(DiagnosticQuestionReview::class)
            ->set('filterDiagMain', '999999')
            ->assertOk()
            ->assertSee('Resultados por estudiante')
            ->assertDontSee('No hay diagnóstico seleccionado');
    }

    public function test_limpiar_diagnostico_vuelve_al_aviso(): void
    {
        $user = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);
        $this->actingAs($user);

        Livewire::test(DiagnosticQuestionReview::class)
            ->set('filterDiagMain', '999999')
            ->set('filterDiagMain', '')
            ->assertOk()
            ->assertSee('No hay diagnóstico seleccionado')
            ->assertDontSee('Resultados por estudiante');
    }

    public function test_reset_de_filtros_de_sesiones(): void
    {
        $user = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);
        $this->actingAs($user);

        Livewire::test(DiagnosticQuestionReview::class)
            ->set('filterDiagMain', '999999')
            ->set('searchSessions', 'ana')
            ->set('filterDateFrom', '2026-01-01')
            ->set('filterDateTo', '2026-12-31')
            ->call('resetSessionFilters')
            ->assertOk()
            ->assertSet('searchSessions', '')
            ->assertSet('filterDateFrom', '')
            ->assertSet('filterDateTo', '');
    }

    /**
     * Réplica del tab Sesiones con datos reales del ámbito del líder:
     * la sesión se crea sin `diag_main_id` (como hace el estudiante), así que
     * el filtro por diagnóstico debeResolverla por el pensum de sus preguntas.
     */
    public function test_resultados_por_estudiante_con_datos_del_ambito(): void
    {
        $user = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['status_active' => 'true']);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'status_active' => 1,
        ]);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        // Ámbito del líder: AreaConocimiento.leader_id → CampoConocimiento.pensum_id.
        $area = AreaConocimiento::create([
            'pestudio_id' => $pestudio->id,
            'leader_id' => $user->id,
            'name' => 'Área de prueba',
            'code' => 'AP',
        ]);
        CampoConocimiento::create([
            'area_conocimiento_id' => $area->id,
            'asignatura_id' => $pensum->asignatura_id,
            'pensum_id' => $pensum->id,
        ]);

        // La pevaluación da las secciones → estudiantes del scope (KPI "Estudiantes").
        $profesor = Profesor::factory()->create([
            'user_id' => User::factory()->create(['is_active' => 'enable'])->id,
            'status_active' => 'true',
        ]);
        Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => Lapso::current()->id,
        ]);

        $diagMain = DiagMain::factory()->create(['name' => 'Diagnóstico de prueba']);
        $question = DiagQuestion::factory()->create([
            'pensum_id' => $pensum->id,
            'diag_main_id' => $diagMain->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'activo' => 1,
        ]);
        $correcta = DiagOption::factory()->create([
            'question_id' => $question->id,
            'opcion' => 'Alternativa correcta',
            'valor' => 1,
            'orden' => 1,
        ]);

        $planpagoId = DB::table('planpagos')->insertGetId(['name' => 'Plan de prueba', 'status_active' => 'true']);
        $estudiant = Estudiant::factory()->create(['planpago_id' => $planpagoId]);
        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
        ]);

        $session = DiagSession::create([
            'estudiant_id' => $estudiant->id,
            'pensum_id' => $pensum->id,
            'iniciado_at' => now()->subHours(2),
            'completado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 100,
            'activo' => false,
            'diag_main_id' => null,
        ]);
        DiagAnswer::create([
            'estudiant_id' => $estudiant->id,
            'session_id' => $session->id,
            'question_id' => $correcta->question_id,
            'option_id' => $correcta->id,
            'respuesta' => 'Alternativa correcta',
            'completado_at' => now(),
        ]);

        $this->actingAs($user);

        $component = Livewire::test(DiagnosticQuestionReview::class)
            ->set('filterDiagMain', (string) $diagMain->id)
            ->assertOk()
            ->assertSee('Resultados por estudiante')
            ->assertSee($estudiant->full_name);

        $sessions = $component->viewData('sessions');
        $this->assertCount(1, $sessions->items(), 'La sesión debe aparecer aunque diag_main_id sea NULL.');

        $stats = $component->viewData('sessionStats');
        $this->assertSame(1, $stats['total_students']);
        $this->assertSame(1, $stats['students_with_sessions']);
        $this->assertSame(0, $stats['students_without_sessions']);

        // Modal de detalle: respuesta y acierto resueltos desde la opción elegida.
        Livewire::test(DiagnosticQuestionReview::class)
            ->set('filterDiagMain', (string) $diagMain->id)
            ->call('openSessionDetail', $session->id)
            ->assertOk()
            ->assertSee('Alternativa correcta')
            ->assertSee('Correcto')
            ->call('closeSessionDetail')
            ->assertOk();
    }

    public function test_sesiones_de_areas_ajenas_no_se_muestran(): void
    {
        $user = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);

        // Área sin líder: el pensum queda fuera del ámbito del usuario.
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['status_active' => 'true']);
        $pensumAjeno = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'status_active' => 1,
        ]);

        $diagMainAjeno = DiagMain::factory()->create(['name' => 'Diagnóstico ajeno']);
        DiagQuestion::factory()->create([
            'pensum_id' => $pensumAjeno->id,
            'diag_main_id' => $diagMainAjeno->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'activo' => 1,
        ]);

        $planpagoId = DB::table('planpagos')->insertGetId(['name' => 'Plan ajeno', 'status_active' => 'true']);
        $estudiant = Estudiant::factory()->create(['planpago_id' => $planpagoId]);

        $sessionAjena = DiagSession::create([
            'estudiant_id' => $estudiant->id,
            'pensum_id' => $pensumAjeno->id,
            'iniciado_at' => now()->subHours(2),
            'completado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 100,
            'activo' => false,
            'diag_main_id' => null,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(DiagnosticQuestionReview::class)
            ->set('filterDiagMain', (string) $diagMainAjeno->id)
            ->assertOk();

        // Sin pensums en el ámbito, el filtro ni siquiera ofrece el diagnóstico.
        $this->assertStringNotContainsString(
            'value="'.$diagMainAjeno->id.'"',
            $component->html(),
            'Un diagnóstico de un área ajena no debe ofrecerse en el selector.'
        );
        $this->assertCount(0, $component->viewData('sessions')->items());

        // Y la sesión ajena no se puede abrir por id.
        $this->actingAs($user);
        $component->call('openSessionDetail', $sessionAjena->id)->assertOk();
        $this->assertNull($component->viewData('sessionDetail'));
    }
}
