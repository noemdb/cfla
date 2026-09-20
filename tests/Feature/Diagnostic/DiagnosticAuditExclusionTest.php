<?php

namespace Tests\Feature\Diagnostic;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Learner\Estudiant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DiagnosticAuditExclusionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_diag_answer_no_genera_entradas_de_bitacora(): void
    {
        [$estudiant, $pensum] = $this->makeStudentAndPensum();

        $question = DiagQuestion::create([
            'pensum_id' => $pensum->id,
            'pregunta' => '¿Pregunta?',
            'tipo_pregunta' => 'open',
            'orden' => 1,
            'weighing' => 1,
            'difficulty' => 'easy',
            'activo' => true,
        ]);

        $session = DiagSession::create([
            'estudiant_id' => $estudiant->id,
            'pensum_id' => $pensum->id,
            'iniciado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 0,
            'activo' => true,
        ]);

        $before = $this->countAudit(DiagAnswer::class);

        DiagAnswer::create([
            'estudiant_id' => $estudiant->id,
            'question_id' => $question->id,
            'session_id' => $session->id,
            'respuesta' => 'Una respuesta',
            'valor_numerico' => 0,
            'completado_at' => now(),
        ]);

        $this->assertSame($before, $this->countAudit(DiagAnswer::class));
    }

    public function test_otros_modelos_siguen_auditandose(): void
    {
        [$estudiant, $pensum] = $this->makeStudentAndPensum();

        $before = $this->countAudit(DiagSession::class);

        DiagSession::create([
            'estudiant_id' => $estudiant->id,
            'pensum_id' => $pensum->id,
            'iniciado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 0,
            'activo' => true,
        ]);

        $this->assertSame($before + 1, $this->countAudit(DiagSession::class));
    }

    private function countAudit(string $modelClass): int
    {
        return DB::table('binnacle_entries')
            ->where('event_type', 'model_created')
            ->where('object_type', $modelClass)
            ->count();
    }

    /**
     * @return array{0: Estudiant, 1: Pensum}
     */
    private function makeStudentAndPensum(): array
    {
        if (! DB::table('type_cis')->where('id', 1)->exists()) {
            DB::table('type_cis')->insert(['id' => 1, 'name' => 'V', 'status_active' => 'true']);
        }

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan', 'status_active' => 'true',
        ]);

        $estudiant = Estudiant::factory()->create(['planpago_id' => $planpagoId]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);

        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);

        return [$estudiant, $pensum];
    }
}
