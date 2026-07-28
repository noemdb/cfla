<?php

namespace Tests\Unit\Estudiant;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Learner\Estudiant;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Seccion;
use App\Services\Estudiant\StudentScopeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentScopeServiceTest extends TestCase
{
    use DatabaseTransactions;

    // ── Sin inscripción ──────────────────────────────────────────

    public function test_user_without_estudiant_returns_null_estudiant(): void
    {
        $user = User::factory()->create();
        $service = new StudentScopeService($user);

        $this->assertNull($service->getEstudiant());
    }

    public function test_unenrolled_student_returns_empty_seccion_ids(): void
    {
        $user = $this->buildUserWithEstudiant();
        // No creamos inscripción a propósito

        $service = new StudentScopeService($user);
        $seccionIds = $service->getSeccionIds();

        $this->assertTrue($seccionIds->isEmpty(), 'Estudiante sin inscripción debe tener seccionIds vacío');
    }

    public function test_unenrolled_student_returns_empty_grado_ids(): void
    {
        $user = $this->buildUserWithEstudiant();
        $service = new StudentScopeService($user);

        $this->assertTrue($service->getGradoIds()->isEmpty());
    }

    public function test_unenrolled_student_scope_pevaluacions_no_results(): void
    {
        $user = $this->buildUserWithEstudiant();
        $service = new StudentScopeService($user);

        $query = Pevaluacion::query();
        $scoped = $service->scopePevaluacions($query);

        // whereRaw('1 = 0') significa que no trae nada
        $this->assertEquals(0, $scoped->count());
    }

    public function test_unenrolled_student_scope_activities_no_results(): void
    {
        $user = $this->buildUserWithEstudiant();
        $service = new StudentScopeService($user);

        $query = Activity::query();
        $scoped = $service->scopeActivities($query);

        $this->assertEquals(0, $scoped->count());
    }

    public function test_unenrolled_student_scope_resources_no_results(): void
    {
        $user = $this->buildUserWithEstudiant();
        $service = new StudentScopeService($user);

        $query = LmsActivityResource::query();
        $scoped = $service->scopeResources($query);

        $this->assertEquals(0, $scoped->count());
    }

    public function test_unenrolled_student_get_pensum_ids_empty(): void
    {
        $user = $this->buildUserWithEstudiant();
        $service = new StudentScopeService($user);

        $this->assertTrue($service->getPensumIds()->isEmpty());
    }

    public function test_unenrolled_student_get_inscripcion_data_null(): void
    {
        $user = $this->buildUserWithEstudiant();
        $service = new StudentScopeService($user);

        $this->assertNull($service->getInscripcionData());
    }

    // ── Con inscripción activa ───────────────────────────────────

    public function test_enrolled_student_returns_seccion_id(): void
    {
        [$user, $seccion] = $this->buildEnrolledStudent();
        $service = new StudentScopeService($user);

        $seccionIds = $service->getSeccionIds();

        $this->assertCount(1, $seccionIds);
        $this->assertEquals($seccion->id, $seccionIds->first());
    }

    public function test_enrolled_student_returns_grado_id(): void
    {
        [$user, $seccion] = $this->buildEnrolledStudent();
        $service = new StudentScopeService($user);

        $gradoIds = $service->getGradoIds();

        $this->assertCount(1, $gradoIds);
        $this->assertEquals($seccion->grado_id, $gradoIds->first());
    }

    public function test_enrolled_student_scope_pevaluacions_filters_by_seccion(): void
    {
        [$user, $seccion] = $this->buildEnrolledStudent();
        $service = new StudentScopeService($user);

        $pensum = Pensum::where('grado_id', $seccion->grado_id)->first();

        // Crear pevaluacion en la sección del estudiante
        $pev = $this->createPevaluacion($seccion, $pensum);

        // Crear pevaluacion en OTRA sección (mismo grado, distinta sección)
        $otraSeccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $seccion->grado_id,
            'name' => 'B',
            'status_active' => 'true',
        ]);
        $otraSeccion = Seccion::find($otraSeccionId);
        $pevOtra = $this->createPevaluacion($otraSeccion, $pensum);

        $query = Pevaluacion::query();
        $scopedQuery = $service->scopePevaluacions($query);
        $ids = $scopedQuery->pluck('id');

        $this->assertTrue($ids->contains($pev->id), 'Debe incluir pevaluacion de su sección');
        $this->assertFalse($ids->contains($pevOtra->id), 'No debe incluir pevaluacion de otra sección');
    }

    public function test_enrolled_student_scope_activities_only_visible(): void
    {
        [$user, $seccion] = $this->buildEnrolledStudent();
        $service = new StudentScopeService($user);

        $pensum = Pensum::where('grado_id', $seccion->grado_id)->first();
        $pev = $this->createPevaluacion($seccion, $pensum);

        // Actividad publicada
        $pubActivity = Activity::factory()->create(['pevaluacion_id' => $pev->id]);
        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $pubActivity->id,
        ]);

        // Actividad NO publicada (draft)
        $draftActivity = Activity::factory()->create(['pevaluacion_id' => $pev->id]);
        LmsActivityPublication::factory()->create([
            'activity_id' => $draftActivity->id,
            'status' => 'DRAFT',
        ]);

        $query = Activity::query();
        $scopedQuery = $service->scopeActivities($query);
        $ids = $scopedQuery->pluck('id');

        $this->assertTrue($ids->contains($pubActivity->id), 'Actividad publicada debe ser visible');
        $this->assertFalse($ids->contains($draftActivity->id), 'Actividad draft NO debe ser visible');
    }

    // ── Memoización ──────────────────────────────────────────────

    public function test_memoization_prevents_duplicate_queries(): void
    {
        $user = $this->buildUserWithEstudiant();
        $service = new StudentScopeService($user);

        $first = $service->getSeccionIds();
        $second = $service->getSeccionIds();

        $this->assertSame($first, $second);
    }

    public function test_get_inscripcion_data_structure(): void
    {
        [$user, $seccion] = $this->buildEnrolledStudent();
        $service = new StudentScopeService($user);

        $data = $service->getInscripcionData();

        $this->assertNotNull($data);
        $this->assertArrayHasKey('estudiant', $data);
        $this->assertArrayHasKey('inscripcion', $data);
        $this->assertArrayHasKey('seccion', $data);
        $this->assertArrayHasKey('grado', $data);
        $this->assertArrayHasKey('pestudio', $data);
        $this->assertEquals($seccion->id, $data['seccion']->id);
    }

    /**
     * Crear una pevaluacion manualmente (evitando factories rotas).
     */
    private function createPevaluacion(Seccion $seccion, Pensum $pensum): Pevaluacion
    {
        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-TEST-' . uniqid(),
            'code_sm' => 'LT',
            'name' => 'Test Lapso',
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-TEST-' . uniqid(),
            'ci_profesor' => substr(uniqid(), 0, 8),
            'name' => 'Profesor Test',
            'status_active' => 'true',
        ]);

        $pevId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensum->id,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccion->id,
            'objetivo' => 'Test Unidad',
        ]);

        return Pevaluacion::find($pevId);
    }

    // ── Helpers ──────────────────────────────────────────────────

    /**
     * Crear un User + Estudiant sin inscripción.
     */
    private function buildUserWithEstudiant(): User
    {
        $user = User::factory()->create();

        $this->createMinimalEstudiant($user, 'Test', 'Student');

        return $user;
    }

    /**
     * Crear Estudiant con todos los FK requeridos y valores únicos.
     */
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

        // type_ci con ID 1 suele existir, pero por si acaso
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
     * Crear un User + Estudiant + Inscripcion con cadena completa.
     * @return array{0: User, 1: Seccion}
     */
    private function buildEnrolledStudent(): array
    {
        $user = User::factory()->create();

        // Cadena FK: institucion → pescolar → peducativo → pestudio → grado → seccion
        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Inst',
            'legalname' => 'Test Inst Legal',
            'rif_institution' => 'J-11111111-1',
            'email_institution' => 'test@test.com',
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
        $programacionId = DB::table('programacions')->insertGetId([
            'name' => 'Test Programacion',
        ]);

        Inscripcion::create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipoInscripcionId,
            'programacion_id' => $programacionId,
        ]);

        // Pensum base para tests de pevaluacion
        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => 'ASIG-TEST',
            'name' => 'Test Asig',
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
}
