<?php

namespace Tests\Feature\Diagnostic;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Learner\Estudiant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Cobertura de diag:deduplicate-questions: duplicadas = misma pregunta +
 * mismo pensum. Se conserva 1 por grupo y se borran las demás con sus
 * opciones; una pregunta con respuestas nunca se elimina, y si todas las
 * del grupo tienen respuestas no se toca nada.
 */
class DiagQuestionDeduplicateTest extends TestCase
{
    use DatabaseTransactions;

    private function makePensum(): Pensum
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);

        return Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
    }

    private function makeQuestion(int $pensumId, string $pregunta): DiagQuestion
    {
        $q = DiagQuestion::create([
            'pensum_id' => $pensumId,
            'pregunta' => $pregunta,
            'tipo_pregunta' => 'opcion',
            'orden' => 1,
            'weighing' => 1,
            'difficulty' => 'easy',
            'activo' => true,
        ]);

        DiagOption::create(['question_id' => $q->id, 'opcion' => 'A', 'valor' => 1, 'orden' => 1]);
        DiagOption::create(['question_id' => $q->id, 'opcion' => 'B', 'valor' => 0, 'orden' => 2]);

        return $q;
    }

    private function answerQuestion(DiagQuestion $q): void
    {
        if (! DB::table('type_cis')->where('id', 1)->exists()) {
            DB::table('type_cis')->insert(['id' => 1, 'name' => 'V', 'status_active' => 'true']);
        }

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan', 'status_active' => 'true',
        ]);

        $estudiant = Estudiant::factory()->create(['planpago_id' => $planpagoId]);

        $session = DiagSession::create([
            'estudiant_id' => $estudiant->id,
            'pensum_id' => $q->pensum_id,
            'iniciado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 0,
            'activo' => true,
        ]);

        DiagAnswer::create([
            'estudiant_id' => $estudiant->id,
            'question_id' => $q->id,
            'session_id' => $session->id,
            'respuesta' => 'A',
            'valor_numerico' => 1,
            'completado_at' => now(),
        ]);
    }

    public function test_dry_run_no_borra_nada(): void
    {
        $pensum = $this->makePensum();
        $this->makeQuestion($pensum->id, '¿Duplicada?');
        $this->makeQuestion($pensum->id, '¿Duplicada?');

        $this->artisan('diag:deduplicate-questions', ['--dry-run' => true])
            ->assertExitCode(0);

        $this->assertSame(2, DiagQuestion::where('pensum_id', $pensum->id)->count());
        $this->assertSame(4, DiagOption::whereHas('question', fn ($q) => $q->where('pensum_id', $pensum->id))->count());
    }

    public function test_borra_duplicada_sin_respuestas_y_sus_opciones(): void
    {
        $pensum = $this->makePensum();
        $keep = $this->makeQuestion($pensum->id, '¿Duplicada?');
        $dup = $this->makeQuestion($pensum->id, '¿Duplicada?');
        $other = $this->makeQuestion($pensum->id, '¿Distinta?');

        $this->artisan('diag:deduplicate-questions', ['--force' => true])
            ->assertExitCode(0);

        // Se conserva la de ID menor; la distinta no se toca.
        $this->assertDatabaseHas('diag_questions', ['id' => $keep->id]);
        $this->assertDatabaseHas('diag_questions', ['id' => $other->id]);
        $this->assertDatabaseMissing('diag_questions', ['id' => $dup->id]);
        $this->assertSame(0, DiagOption::where('question_id', $dup->id)->count());
        $this->assertSame(2, DiagOption::where('question_id', $keep->id)->count());
    }

    public function test_no_borra_duplicada_con_respuestas(): void
    {
        $pensum = $this->makePensum();
        $answered = $this->makeQuestion($pensum->id, '¿Duplicada?');
        $this->answerQuestion($answered);
        $dup = $this->makeQuestion($pensum->id, '¿Duplicada?');

        $this->artisan('diag:deduplicate-questions', ['--force' => true])
            ->assertExitCode(0);

        // La respondida se conserva (preferida) y la otra se elimina.
        $this->assertDatabaseHas('diag_questions', ['id' => $answered->id]);
        $this->assertDatabaseMissing('diag_questions', ['id' => $dup->id]);
        $this->assertDatabaseHas('diag_answers', ['question_id' => $answered->id]);
    }

    public function test_si_ambas_tienen_respuestas_no_elimina_nada(): void
    {
        $pensum = $this->makePensum();
        $first = $this->makeQuestion($pensum->id, '¿Duplicada?');
        $second = $this->makeQuestion($pensum->id, '¿Duplicada?');
        $this->answerQuestion($first);
        $this->answerQuestion($second);

        $this->artisan('diag:deduplicate-questions', ['--force' => true])
            ->assertExitCode(0);

        $this->assertDatabaseHas('diag_questions', ['id' => $first->id]);
        $this->assertDatabaseHas('diag_questions', ['id' => $second->id]);
        $this->assertSame(4, DiagOption::whereIn('question_id', [$first->id, $second->id])->count());
    }

    public function test_filtro_pensum_acota_el_alcance(): void
    {
        $pensumA = $this->makePensum();
        $pensumB = $this->makePensum();
        $this->makeQuestion($pensumA->id, '¿Duplicada?');
        $this->makeQuestion($pensumA->id, '¿Duplicada?');
        $this->makeQuestion($pensumB->id, '¿Duplicada?');
        $this->makeQuestion($pensumB->id, '¿Duplicada?');

        $this->artisan('diag:deduplicate-questions', ['--pensum' => $pensumA->id, '--force' => true])
            ->assertExitCode(0);

        $this->assertSame(1, DiagQuestion::where('pensum_id', $pensumA->id)->count());
        $this->assertSame(2, DiagQuestion::where('pensum_id', $pensumB->id)->count());
    }
}
