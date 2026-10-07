<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Diagnostics\IndexComponent;
use App\Models\User;
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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regresión del tab "Sesiones" del diagnóstico del profesor.
 *
 * Cubre tres defectos que dejaron la vista vacía o incompleta:
 *  1. `diag_sessions.diag_main_id` nunca se persiste (Diagnostic::startDiagnostic
 *     no lo escribe), por lo que filtrar solo por esa columna devolvía 0 filas.
 *  2. El modelo hidratado se pasaba a la vista bajo la clave 'selectedSession',
 *     que Livewire pisa con la propiedad pública (int) del mismo nombre.
 *  3. No existe columna `is_correct`: el acierto lo resuelve DiagAnswer::isCorrect()
 *     a partir de la opción seleccionada.
 */
class DiagnosticsSessionsTabTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Profesor $profesor;

    protected Lapso $lapso;

    protected DiagMain $diagMain;

    protected Pensum $pensum;

    protected Seccion $seccion;

    protected Estudiant $estudiant;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->lapso = Lapso::factory()->create([
            'finicial' => Carbon::now()->subDays(10)->toDateString(),
            'ffinal' => Carbon::now()->addDays(10)->toDateString(),
        ]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['status_active' => 'true']);

        $this->pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'status_active' => 1,
        ]);

        $this->seccion = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'status_active' => 'true',
        ]);

        $this->user = User::factory()->create(['is_active' => 'enable']);
        $this->profesor = Profesor::factory()->create([
            'user_id' => $this->user->id,
            'status_active' => 'true',
        ]);

        Pevaluacion::factory()->create([
            'profesor_id' => $this->profesor->id,
            'pensum_id' => $this->pensum->id,
            'seccion_id' => $this->seccion->id,
            'lapso_id' => $this->lapso->id,
        ]);

        $this->diagMain = DiagMain::factory()->create(['name' => 'Diagnóstico de prueba']);

        // estudiantes.planpago_id es FK obligatoria y no la crea la factory.
        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Plan de prueba',
            'status_active' => 'true',
        ]);

        $this->estudiant = Estudiant::factory()->create([
            'planpago_id' => $planpagoId,
        ]);

        // La factory resuelve tipo/escolaridad/programacion/grupo estable; se
        // sobrescribe lo que define el ambito (estudiante y seccion).
        Inscripcion::factory()->create([
            'estudiant_id' => $this->estudiant->id,
            'seccion_id' => $this->seccion->id,
        ]);
    }

    /** Crea una pregunta de opción múltiple con su alternativa correcta. */
    protected function crearPreguntaMultiple(int $orden): array
    {
        $question = DiagQuestion::factory()->create([
            'pensum_id' => $this->pensum->id,
            'diag_main_id' => $this->diagMain->id,
            'tipo_pregunta' => 'multiple',
            'orden' => $orden,
            'activo' => 1,
        ]);

        $correcta = DiagOption::factory()->create([
            'question_id' => $question->id,
            'opcion' => 'Alternativa correcta',
            'valor' => 1,
            'orden' => 1,
        ]);

        DiagOption::factory()->create([
            'question_id' => $question->id,
            'opcion' => 'Alternativa incorrecta',
            'valor' => 0,
            'orden' => 2,
        ]);

        return [$question, $correcta];
    }

    /**
     * La sesión se crea sin `diag_main_id`, igual que hace el estudiante, y la
     * columna `is_correct` no existe en diag_answers.
     */
    protected function crearSesion(bool $conAciertos = true): DiagSession
    {
        [, $correcta] = $this->crearPreguntaMultiple(1);

        $session = DiagSession::create([
            'estudiant_id' => $this->estudiant->id,
            'pensum_id' => $this->pensum->id,
            'iniciado_at' => now()->subHours(2),
            'completado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 100,
            'activo' => false,
            'diag_main_id' => null,
        ]);

        DiagAnswer::create([
            'estudiant_id' => $this->estudiant->id,
            'session_id' => $session->id,
            'question_id' => $correcta->question_id,
            'option_id' => $conAciertos ? $correcta->id : null,
            'respuesta' => 'Alternativa correcta',
            'completado_at' => now(),
        ]);

        return $session->fresh();
    }

    public function test_sessions_tab_muestra_sesiones_del_profesor(): void
    {
        $session = $this->crearSesion();

        $this->actingAs($this->user);

        Livewire::test(IndexComponent::class)
            ->set('activeTab', 'sessions')
            ->assertOk()
            ->assertSee($session->id);
    }

    /**
     * Regresión principal: con diag_main_id NULL en la sesión, filtrar por
     * diagnóstico debe seguir devolviendo sus sesiones.
     */
    public function test_filtrar_por_diagnostico_no_oculta_sesiones(): void
    {
        $this->crearSesion();

        $this->actingAs($this->user);

        $component = Livewire::test(IndexComponent::class)
            ->set('activeTab', 'sessions')
            ->set('filterDiagMainId', (string) $this->diagMain->id)
            ->assertOk();

        $this->assertSame(
            1,
            $component->viewData('stats')['total_sessions'],
            'El filtro por diagnóstico debe contar la sesión aunque diag_main_id sea NULL.'
        );

        $this->assertCount(1, $component->viewData('sessions')->items());
    }

    public function test_modal_muestra_respuesta_y_no_hereda_la_propiedad_publica(): void
    {
        $session = $this->crearSesion();

        $this->actingAs($this->user);

        $html = Livewire::test(IndexComponent::class)
            ->set('activeTab', 'sessions')
            ->call('viewSession', $session->id)
            ->assertOk()
            ->html();

        // La respuesta vive en `respuesta`; `respuesta_texto` no existe.
        $this->assertStringContainsString('Alternativa correcta', $html);
        $this->assertStringNotContainsString('>—</div>', $html);

        // Diagnóstico resuelto por las preguntas aunque diag_main_id sea NULL.
        $this->assertStringContainsString('Diagnóstico de prueba', $html);
    }

    public function test_modal_no_falla_leyendo_propiedades_sobre_un_int(): void
    {
        $session = $this->crearSesion();

        $this->actingAs($this->user);

        // El id es un int: si la vista lo tratara como modelo, exploited al
        // leer `iniciado_at` (bug "Attempt to read property on int").
        Livewire::test(IndexComponent::class)
            ->set('activeTab', 'sessions')
            ->set('selectedSession', $session->id)
            ->set('SessionModalReport', true)
            ->assertOk()
            ->assertDontSee('iniciado_at');
    }

    public function test_aciertos_se_resuelven_con_la_opcion_seleccionada(): void
    {
        $session = $this->crearSesion();

        $this->actingAs($this->user);

        $component = Livewire::test(IndexComponent::class)
            ->set('activeTab', 'sessions')
            ->set('filterDiagMainId', (string) $this->diagMain->id);

        $row = $component->viewData('sessions')->first();

        // is_correct no es una columna; el acierto sale de la opción elegida.
        $this->assertSame(1, $row->answers->filter(fn ($a) => $a->isCorrect())->count());
        $this->assertSame(1, $row->resolvedDiagMain->id);
        $this->assertSame($this->diagMain->id, $row->resolvedDiagMain->id);
    }

    public function test_sesiones_de_otros_profesores_no_se_muestran(): void
    {
        $this->crearSesion();

        $otroUser = User::factory()->create(['is_active' => 'enable']);
        $otroProfesor = Profesor::factory()->create([
            'user_id' => $otroUser->id,
            'status_active' => 'true',
        ]);

        // El otro profesor tiene carga académica, pero en otro pensum.
        $otroPensum = Pensum::factory()->create([
            'pestudio_id' => $this->pensum->pestudio_id,
            'grado_id' => $this->pensum->grado_id,
            'status_active' => 1,
        ]);

        Pevaluacion::factory()->create([
            'profesor_id' => $otroProfesor->id,
            'pensum_id' => $otroPensum->id,
            'seccion_id' => $this->seccion->id,
            'lapso_id' => $this->lapso->id,
        ]);

        $this->actingAs($otroUser);

        $component = Livewire::test(IndexComponent::class)
            ->set('activeTab', 'sessions')
            ->set('filterDiagMainId', (string) $this->diagMain->id);

        $this->assertSame(0, $component->viewData('stats')['total_sessions']);
    }
}