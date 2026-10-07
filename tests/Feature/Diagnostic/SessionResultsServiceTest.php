<?php

namespace Tests\Feature\Diagnostic;

use App\Models\app\Academy\Pensum;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Services\Diagnostic\SessionResultsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Regresión de "Sin responder" en el detalle de sesión.
 *
 * El universo de preguntas de una sesión es el de SU ÁREA (las activas del
 * pensum, ver Diagnostic::startDiagnostic). Antes se usaba el total de
 * preguntas del DiagMain —transversal a todas las áreas y grados—, así que una
 * sesión de 10 preguntas de Física.Reportaba "945 sin responder" con un
 * diagnóstico de 955 preguntas.
 */
class SessionResultsServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_el_universo_es_el_del_area_y_no_el_del_diagnostico(): void
    {
        $diagMain = DiagMain::factory()->create();
        $area = Pensum::factory()->create(['status_active' => 1]);
        $otraArea = Pensum::factory()->create(['status_active' => 1]);

        $areaConSesion = DiagQuestion::factory()->create([
            'pensum_id' => $area->id,
            'diag_main_id' => $diagMain->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'activo' => 1,
        ]);

        // 40 preguntas más del mismo diagnóstico en OTRAS áreas: el diagnóstico
        // es transversal y no puede ser el denominador.
        for ($i = 0; $i < 40; $i++) {
            DiagQuestion::factory()->create([
                'pensum_id' => $otraArea->id,
                'diag_main_id' => $diagMain->id,
                'tipo_pregunta' => 'multiple',
                'orden' => $i + 2,
                'activo' => 1,
            ]);
        }

        $session = DiagSession::create([
            'pensum_id' => $area->id,
            'iniciado_at' => now(),
            'completado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 100,
            'activo' => false,
            'diag_main_id' => null,
        ]);

        $correcta = DiagOption::factory()->create([
            'question_id' => $areaConSesion->id,
            'opcion' => 'Correcta',
            'valor' => 1,
            'orden' => 1,
        ]);
        DiagAnswer::create([
            'session_id' => $session->id,
            'question_id' => $correcta->question_id,
            'option_id' => $correcta->id,
            'completado_at' => now(),
        ]);

        // 41 preguntas en el diagnóstico; solo 1 pertenece al área evaluada.
        $this->assertSame(41, DiagQuestion::where('diag_main_id', $diagMain->id)->count());

        $summary = app(SessionResultsService::class)->summarize($session->fresh());

        $this->assertSame(1, $summary['total'], 'El total es el de preguntas activas del área, no el del diagnóstico.');
        $this->assertSame(1, $summary['answered']);
        $this->assertSame(1, $summary['correct']);
        $this->assertSame(0, $summary['incorrect']);
        $this->assertSame(0, $summary['unanswered'], 'Una sesión completa no debe reportar preguntas sin responder.');
        $this->assertSame(100, $summary['percentage']);

        // Reproduce el defecto reportado: 41 - 1 = 40 "sin responder" sin sentido.
        $this->assertNotSame(40, $summary['unanswered']);
    }

    public function test_cuenta_las_no_contestadas_del_area(): void
    {
        $diagMain = DiagMain::factory()->create();
        $area = Pensum::factory()->create(['status_active' => 1]);

        // 3 preguntas activas del área; el estudiante solo contesta 2.
        $preguntas = collect(range(1, 3))->map(fn ($orden) => DiagQuestion::factory()->create([
            'pensum_id' => $area->id,
            'diag_main_id' => $diagMain->id,
            'tipo_pregunta' => 'multiple',
            'orden' => $orden,
            'activo' => 1,
        ]));

        $session = DiagSession::create([
            'pensum_id' => $preguntas->first()->pensum_id,
            'iniciado_at' => now(),
            'completado_at' => now(),
            'total_preguntas' => 3,
            'progreso' => 66,
            'activo' => false,
            'diag_main_id' => null,
        ]);

        foreach ($preguntas->take(2) as $pregunta) {
            $correcta = DiagOption::factory()->create([
                'question_id' => $pregunta->id,
                'opcion' => 'Correcta',
                'valor' => 1,
                'orden' => 1,
            ]);
            DiagOption::factory()->create([
                'question_id' => $pregunta->id,
                'opcion' => 'Incorrecta',
                'valor' => 0,
                'orden' => 2,
            ]);
            DiagAnswer::create([
                'session_id' => $session->id,
                'question_id' => $pregunta->id,
                'option_id' => $correcta->id,
                'completado_at' => now(),
            ]);
        }

        $summary = app(SessionResultsService::class)->summarize($session->fresh());

        $this->assertSame(3, $summary['total']);
        $this->assertSame(2, $summary['answered']);
        $this->assertSame(1, $summary['unanswered']);
        $this->assertSame(2, $summary['correct']);
        $this->assertSame(0, $summary['incorrect']);
        $this->assertSame(100, $summary['percentage']);
    }

    public function test_una_incorrecta_no_es_correcta(): void
    {
        $area = Pensum::factory()->create(['status_active' => 1]);
        $pregunta = DiagQuestion::factory()->create([
            'pensum_id' => $area->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'activo' => 1,
        ]);
        $incorrecta = DiagOption::factory()->create([
            'question_id' => $pregunta->id,
            'opcion' => 'Incorrecta',
            'valor' => 0,
            'orden' => 1,
        ]);

        $session = DiagSession::create([
            'pensum_id' => $area->id,
            'iniciado_at' => now(),
            'completado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 100,
            'activo' => false,
            'diag_main_id' => null,
        ]);
        DiagAnswer::create([
            'session_id' => $session->id,
            'question_id' => $pregunta->id,
            'option_id' => $incorrecta->id,
            'completado_at' => now(),
        ]);

        $summary = app(SessionResultsService::class)->summarize($session->fresh());

        $this->assertSame(0, $summary['correct']);
        $this->assertSame(1, $summary['incorrect']);
        $this->assertSame(0, $summary['percentage']);
    }

    public function test_una_pregunta_contestada_una_sola_vez(): void
    {
        $area = Pensum::factory()->create(['status_active' => 1]);
        $pregunta = DiagQuestion::factory()->create([
            'pensum_id' => $area->id,
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'activo' => 1,
        ]);
        $correcta = DiagOption::factory()->create([
            'question_id' => $pregunta->id,
            'opcion' => 'Correcta',
            'valor' => 1,
            'orden' => 1,
        ]);

        $session = DiagSession::create([
            'pensum_id' => $area->id,
            'iniciado_at' => now(),
            'completado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 100,
            'activo' => false,
            'diag_main_id' => null,
        ]);

        // Dos filas para la misma pregunta (p. ej. reintento): cuenta una vez.
        DiagAnswer::create([
            'session_id' => $session->id,
            'question_id' => $pregunta->id,
            'option_id' => null,
            'completado_at' => now(),
        ]);
        DiagAnswer::create([
            'session_id' => $session->id,
            'question_id' => $pregunta->id,
            'option_id' => $correcta->id,
            'completado_at' => now(),
        ]);

        $summary = app(SessionResultsService::class)->summarize($session->fresh());

        $this->assertSame(1, $summary['answered']);
        $this->assertSame(1, $summary['correct']);
        $this->assertSame(1, $summary['total']);
        $this->assertSame(0, $summary['unanswered']);
    }
}
