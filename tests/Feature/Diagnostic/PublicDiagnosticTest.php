<?php

namespace Tests\Feature\Diagnostic;

use App\Livewire\Diagnostic;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Seccion;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Learner\Estudiant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class PublicDiagnosticTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pagina_diagnostico_carga(): void
    {
        $this->get(route('diagnostico'))
            ->assertOk()
            ->assertSee('Ingresa tu número de cédula');
    }

    public function test_verifica_estudiante_activo_y_muestra_dashboard(): void
    {
        $scenario = $this->createScenario();

        Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->assertHasNoErrors()
            ->assertSet('currentView', 'dashboard')
            ->assertSet('isStudentVerified', true)
            ->assertSee($scenario['estudiant']->full_name);
    }

    public function test_rechaza_estudiante_inactivo(): void
    {
        $scenario = $this->createScenario(['status_active' => 'false']);

        Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->assertHasErrors('studentCi')
            ->assertSet('currentView', 'student-identification')
            ->assertSet('isStudentVerified', false);
    }

    public function test_rechaza_cedula_inexistente(): void
    {
        Livewire::test(Diagnostic::class)
            ->set('studentCi', '99999999')
            ->call('verifyStudent')
            ->assertHasErrors('studentCi')
            ->assertSet('currentView', 'student-identification');
    }

    public function test_estudiante_sin_usuario_asociado_no_lanza_error(): void
    {
        $scenario = $this->createScenario(['user_id' => null]);

        Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->assertHasNoErrors()
            ->assertSet('currentView', 'dashboard')
            ->assertSet('isStudentVerified', true);
    }

    public function test_dashboard_muestra_informacion_academica(): void
    {
        $scenario = $this->createScenario();

        Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->assertSee('Información Académica')
            ->assertSee($scenario['pestudio']->full_name)
            ->assertSee($scenario['grado']->name)
            ->assertSee($scenario['seccion']->name);
    }

    public function test_estudiante_inscrito_sin_registro_administrativo_ve_sus_areas(): void
    {
        $scenario = $this->createScenario();

        $this->assertDatabaseMissing('administrativas', [
            'estudiant_id' => $scenario['estudiant']->id,
        ]);

        Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->assertSee('Test Asignatura');
    }

    public function test_flujo_inicia_responde_y_finaliza(): void
    {
        $scenario = $this->createScenario();
        $pensum = $scenario['pensum'];

        $component = Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('startDiagnostic', $pensum->id)
            ->assertSet('currentView', 'wizard')
            ->assertSet('currentQuestionId', $scenario['question']->id);

        $component->set('selectedAnswer', 'Opción A')
            ->call('nextQuestion')
            ->assertSet('currentView', 'summary');

        $this->assertDatabaseHas('diag_answers', [
            'estudiant_id' => $scenario['estudiant']->id,
            'question_id' => $scenario['question']->id,
            'respuesta' => 'Opción A',
            'valor_numerico' => 1,
        ]);

        $session = DiagSession::where('estudiant_id', $scenario['estudiant']->id)
            ->where('pensum_id', $pensum->id)
            ->first();

        $this->assertNotNull($session);
        $this->assertNotNull($session->completado_at);
        $this->assertFalse((bool) $session->activo);
    }

    public function test_confirmar_finalizacion_muestra_dialogo_y_finaliza_al_aceptar(): void
    {
        $scenario = $this->createScenario();

        $component = Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('startDiagnostic', $scenario['pensum']->id);

        $component->set('selectedAnswer', 'Opción A')->call('confirmFinish');

        // El diálogo no finaliza por sí solo.
        $component->assertSet('currentView', 'wizard');

        $component->call('finalizeDiagnostic')->assertSet('currentView', 'summary');

        $this->assertDatabaseHas('diag_answers', [
            'estudiant_id' => $scenario['estudiant']->id,
            'question_id' => $scenario['question']->id,
            'respuesta' => 'Opción A',
        ]);
    }

    public function test_confirmar_sin_respuesta_no_finaliza(): void
    {
        $scenario = $this->createScenario();

        $component = Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('startDiagnostic', $scenario['pensum']->id);

        $component->set('selectedAnswer', null)->call('confirmFinish');

        $component->assertSet('currentView', 'wizard');

        $this->assertDatabaseMissing('diag_answers', [
            'estudiant_id' => $scenario['estudiant']->id,
            'question_id' => $scenario['question']->id,
        ]);
    }

    public function test_respuesta_se_guarda_con_option_id_y_valor_numerico(): void
    {
        $scenario = $this->createScenario();

        $component = Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('startDiagnostic', $scenario['pensum']->id);

        $component->set('selectedAnswer', 'Opción B')->call('saveAnswer');

        $answer = DiagAnswer::where('estudiant_id', $scenario['estudiant']->id)
            ->where('question_id', $scenario['question']->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertSame('Opción B', $answer->respuesta);
        $this->assertSame(0, (int) $answer->valor_numerico);
        $this->assertSame($scenario['optionB']->id, $answer->option_id);
    }

    public function test_no_avanza_en_pregunta_abierta_invalida(): void
    {
        $scenario = $this->createScenario([], 'open');

        $component = Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('startDiagnostic', $scenario['pensum']->id);

        $component->set('selectedAnswer', 'a')
            ->call('nextQuestion')
            ->assertSet('currentView', 'wizard');

        $this->assertDatabaseMissing('diag_answers', [
            'estudiant_id' => $scenario['estudiant']->id,
            'question_id' => $scenario['question']->id,
        ]);
    }

    public function test_progreso_refleja_el_total_del_area(): void
    {
        $scenario = $this->createScenario([], 'multiple', 2);

        $component = Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('startDiagnostic', $scenario['pensum']->id);

        $component->set('selectedAnswer', 'Opción A')->call('nextQuestion');

        $this->assertSame(50, $component->get('progress'));
    }

    public function test_summary_calcula_resultados(): void
    {
        $scenario = $this->createScenario();

        $component = Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('startDiagnostic', $scenario['pensum']->id);

        $component->set('selectedAnswer', 'Opción A')
            ->call('nextQuestion')
            ->assertSet('currentView', 'summary');

        $results = $component->get('results');

        $this->assertSame(1, $results['correct_answers']);
        $this->assertSame(1, $results['total_answered']);
        $this->assertSame(100.0, (float) $results['precision']);
        $this->assertArrayHasKey('easy', $results['by_difficulty']);
    }

    public function test_respuestas_se_acotan_a_la_sesion_actual(): void
    {
        $scenario = $this->createScenario([], 'multiple', 2);

        $component = Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('startDiagnostic', $scenario['pensum']->id);

        $currentQuestionId = $component->get('currentQuestionId');

        $component->set('selectedAnswer', 'Opción A')->call('saveAnswer');

        // Respuesta de un intento anterior (otra sesión) que NO debe aparecer.
        $otherSession = DiagSession::create([
            'estudiant_id' => $scenario['estudiant']->id,
            'pensum_id' => $scenario['pensum']->id,
            'iniciado_at' => now()->subDay(),
            'completado_at' => now()->subDay(),
            'progreso' => 100,
            'total_preguntas' => 2,
            'activo' => false,
        ]);

        DiagAnswer::create([
            'estudiant_id' => $scenario['estudiant']->id,
            'question_id' => $scenario['questions'][1]->id,
            'session_id' => $otherSession->id,
            'respuesta' => 'Opción B',
            'valor_numerico' => 0,
            'completado_at' => now()->subDay(),
        ]);

        $answered = $component->instance()->getAnsweredQuestionsWithAnswers();

        $this->assertCount(1, $answered);
        $this->assertSame($currentQuestionId, $answered->first()['question']->id);
        $this->assertSame('Opción A', $answered->first()['answer']);
    }

    public function test_guia_de_participacion_se_renderiza_y_cambia_de_tab(): void
    {
        $scenario = $this->createScenario();

        Livewire::test(Diagnostic::class)
            ->set('studentCi', $scenario['estudiant']->ci_estudiant)
            ->call('verifyStudent')
            ->call('showGuide')
            ->assertSet('currentView', 'guide')
            ->assertSet('activeTab', 'overview')
            ->assertSee('Guía de Participación')
            ->assertSee('Antes de empezar')
            ->set('activeTab', 'questions')
            ->assertSee('Tipos de Preguntas');
    }

    /**
     * Construye el escenario académico completo: institución → pestudio →
     * grado → sección → estudiante inscrito → pensum con preguntas activas.
     *
     * @param  array<string, mixed>  $studentOverrides
     * @return array<string, mixed>
     */
    private function createScenario(
        array $studentOverrides = [],
        string $tipoPregunta = 'multiple',
        int $questionCount = 1
    ): array {
        $uniq = uniqid();
        $user = User::factory()->create();

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Inst '.$uniq, 'legalname' => 'Test '.$uniq,
            'rif_institution' => 'J-'.$uniq, 'email_institution' => 'test@test.com',
            'status_dont_allow_registration_if_insolvency' => 'false',
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId, 'name' => 'Test Año',
            'description' => 'Test', 'finicial' => now()->subYear(),
            'ffinal' => now()->addYear(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId, 'name' => 'Test PE',
            'description' => 'Test', 'status_active' => 'true',
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId, 'code' => 'PEST-TEST',
            'name' => 'Test Plan', 'status_active' => 'true',
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA', 'name' => 'Test Scale',
            'minimo' => '1', 'maximo' => '20', 'aprobacion' => '10',
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId, 'name' => 'Test Grado',
            'code' => 'GR-TEST', 'status_active' => 'true',
        ]);

        $grado = Grado::find($gradoId);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId, 'name' => 'A', 'status_active' => 'true',
            'status_inscription_affects' => 'true',
        ]);

        $seccion = Seccion::find($seccionId);

        $repId = DB::table('representants')->insertGetId([
            'user_id' => $user->id, 'name' => 'Test Rep',
            'ci_representant' => 'V-'.$user->id, 'status_active' => 'true',
        ]);

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan', 'status_active' => 'true',
        ]);

        if (! DB::table('type_cis')->where('id', 1)->exists()) {
            DB::table('type_cis')->insert([
                'id' => 1, 'name' => 'V', 'status_active' => 'true',
            ]);
        }

        $estudiant = Estudiant::create(array_merge([
            'user_id' => $user->id, 'representant_id' => $repId,
            'planpago_id' => $planpagoId, 'type_ci_id' => 1,
            'ci_estudiant' => 'V-'.$uniq, 'representant_ci' => 'V-'.$user->id,
            'name' => 'Test', 'lastname' => 'Student', 'status_active' => 'true',
        ], $studentOverrides));

        $tipoInscripcionId = DB::table('tinscripcions')->insertGetId(['name' => 'Test']);
        $programacionId = DB::table('programacions')->insertGetId(['name' => 'Test']);

        Inscripcion::create([
            'estudiant_id' => $estudiant->id, 'seccion_id' => $seccion->id,
            'tipo_id' => $tipoInscripcionId, 'programacion_id' => $programacionId,
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId, 'code' => 'ASIG-TEST',
            'name' => 'Test Asignatura', 'tescala' => $escalaId,
        ]);

        $pensum = Pensum::create([
            'pestudio_id' => $pestudioId, 'grado_id' => $grado->id,
            'asignatura_id' => $asignaturaId, 'status_component' => true,
            'status_active' => true, 'status_active_diagnostic' => true,
        ]);

        $questions = collect();
        $optionA = null;
        $optionB = null;

        for ($i = 1; $i <= $questionCount; $i++) {
            $question = DiagQuestion::create([
                'pensum_id' => $pensum->id, 'pregunta' => "¿Pregunta de prueba {$i}?",
                'tipo_pregunta' => $tipoPregunta, 'orden' => $i, 'weighing' => 1,
                'difficulty' => 'easy', 'activo' => true,
            ]);

            if ($tipoPregunta === 'multiple') {
                $a = DiagOption::create([
                    'question_id' => $question->id, 'opcion' => 'Opción A',
                    'valor' => 1, 'orden' => 1,
                ]);

                $b = DiagOption::create([
                    'question_id' => $question->id, 'opcion' => 'Opción B',
                    'valor' => 0, 'orden' => 2,
                ]);

                if ($i === 1) {
                    $optionA = $a;
                    $optionB = $b;
                }
            }

            $questions->push($question);
        }

        $pestudio = \App\Models\app\Academy\Pestudio::find($pestudioId);

        return [
            'user' => $user,
            'estudiant' => $estudiant,
            'grado' => $grado,
            'seccion' => $seccion,
            'pestudio' => $pestudio,
            'pensum' => $pensum,
            'questions' => $questions,
            'question' => $questions->first(),
            'optionA' => $optionA,
            'optionB' => $optionB,
        ];
    }
}
