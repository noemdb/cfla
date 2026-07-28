<?php

namespace Tests\Feature\Estudiant;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Learner\Estudiant;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Seccion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentEnrollmentScopeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Un estudiante sin inscripción puede acceder al home pero el scope
     * por sección debe filtrar actividades (TODO: se activa cuando el
     * componente StudentHome integre StudentScopeService).
     *
     * @see tests/Unit/Estudiant/StudentScopeServiceTest::test_unenrolled_student_scope_pevaluacions_no_results
     */
    public function test_unenrolled_student_can_access_home(): void
    {
        $user = $this->buildStudentWithoutEnrollment();

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(200);
    }

    /**
     * Un estudiante con inscripción puede acceder a su home.
     * La verificación de que solo ve sus actividades está a nivel de
     * StudentScopeService (unit test).
     *
     * @see tests/Unit/Estudiant/StudentScopeServiceTest::test_enrolled_student_scope_pevaluacions_filters_by_seccion
     */
    public function test_enrolled_student_can_access_home(): void
    {
        [$user, $seccion] = $this->buildEnrolledStudent();
        $this->createPublishedActivityInSection($seccion);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(200);
    }

    /**
     * El scope por inscripción funciona a nivel de servicio.
     * Este test complementa los unit tests verificando que el middleware
     * deja pasar al estudiante con inscripción.
     */
    public function test_student_middleware_passes_with_enrollment(): void
    {
        [$user, $seccion] = $this->buildEnrolledStudent();

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(200);
    }

    /**
     * El middleware bloquea a usuarios no estudiantes.
     */
    public function test_non_student_is_blocked_by_middleware(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(403);
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function buildStudentWithoutEnrollment(): User
    {
        $user = User::factory()->create(['is_student' => true]);

        $this->createMinimalEstudiant($user, 'Sin', 'Inscripcion');

        return $user;
    }

    private function createMinimalEstudiant(User $user, string $name, string $lastname): Estudiant
    {
        $repId = DB::table('representants')->insertGetId([
            'user_id' => $user->id,
            'name' => 'Test Rep',
            'ci_representant' => 'V-' . $user->id,
            'status_active' => 'true',
        ]);

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan Pago',
            'status_active' => 'true',
        ]);

        $typeCiExists = DB::table('type_cis')->where('id', 1)->exists();
        if (!$typeCiExists) {
            DB::table('type_cis')->insert([
                'id' => 1,
                'name' => 'V',
                'status_active' => 'true',
            ]);
        }

        $estudiant = Estudiant::create([
            'user_id' => $user->id,
            'representant_id' => $repId,
            'planpago_id' => $planpagoId,
            'type_ci_id' => 1,
            'ci_estudiant' => 'TEST-CI-' . $user->id,
            'representant_ci' => 'V-' . $user->id,
            'name' => $name,
            'lastname' => $lastname,
            'status_active' => 'true',
        ]);

        return $estudiant;
    }

    /**
     * @return array{0: User, 1: Seccion}
     */
    private function buildEnrolledStudent(): array
    {
        $user = User::factory()->create(['is_student' => true]);

        $uniq = uniqid();
        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Inst ' . $uniq,
            'legalname' => 'Test Inst Legal ' . $uniq,
            'rif_institution' => 'J-' . $uniq,
            'email_institution' => 'test-' . $uniq . '@test.com',
            'status_dont_allow_registration_if_insolvency' => 'false',
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año Escolar',
            'description' => 'Test',
            'finicial' => now()->subYear(),
            'ffinal' => now()->addYear(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId,
            'name' => 'Test PE',
            'description' => 'Test',
            'status_active' => 'true',
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => 'PEST-TEST',
            'name' => 'Test Plan',
            'status_active' => 'true',
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA',
            'name' => 'Test Scale',
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId,
            'name' => 'Test Grado',
            'code' => 'GR-TEST',
            'status_active' => 'true',
        ]);

        $grado = Grado::find($gradoId);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId,
            'name' => 'A',
            'status_active' => 'true',
        ]);

        $seccion = Seccion::find($seccionId);

        $estudiant = $this->createMinimalEstudiant($user, 'Test', 'Enrolled');

        $tipoInscripcionId = DB::table('tinscripcions')->insertGetId(['name' => 'Test Tipo']);
        $programacionId = DB::table('programacions')->insertGetId(['name' => 'Test Programacion']);

        Inscripcion::create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipoInscripcionId,
            'programacion_id' => $programacionId,
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => 'ASIG-TEST',
            'name' => 'Test Asignatura',
            'tescala' => $escalaId,
        ]);

        Pensum::create([
            'pestudio_id' => $pestudioId,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignaturaId,
            'status_component' => true,
            'status_active' => true,
        ]);

        return [$user, $seccion];
    }

    private function createPublishedActivityInSection(Seccion $seccion): Activity
    {
        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-TEST',
            'code_sm' => 'LT',
            'name' => 'Test Lapso',
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-87654321',
            'ci_profesor' => '87654321',
            'name' => 'Profesor Test',
            'status_active' => 'true',
        ]);

        $pensum = Pensum::where('grado_id', $seccion->grado_id)->first();

        $pevId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensum->id,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccion->id,
            'objetivo' => 'Test Unidad',
        ]);

        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevId,
            'topic' => 'Actividad Publicada',
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
        ]);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        return $activity;
    }
}
