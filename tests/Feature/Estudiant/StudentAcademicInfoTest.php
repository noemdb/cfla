<?php

namespace Tests\Feature\Estudiant;

use App\Models\User;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Seccion;
use App\Models\app\Learner\Estudiant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentAcademicInfoTest extends TestCase
{
    use DatabaseTransactions;

    public function test_academic_page_shows_pensums(): void
    {
        [$user, $seccion, $asignaturaId] = $this->buildEnrolledStudentWithPensum();

        $response = $this->actingAs($user)
            ->get(route('student.lms.academic'));

        $response->assertStatus(200);
        $response->assertSee('Test Asignatura');
    }

    public function test_academic_page_shows_no_data_for_orphan_user(): void
    {
        $user = User::factory()->create(['is_student' => true]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.academic'));

        $response->assertStatus(200);
        $response->assertSee('No se encontraron datos');
    }

    /**
     * @return array{0: User, 1: Seccion, 2: int}
     */
    private function buildEnrolledStudentWithPensum(): array
    {
        $user = User::factory()->create(['is_student' => true]);
        $uniq = uniqid();

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Inst ' . $uniq, 'legalname' => 'Test ' . $uniq,
            'rif_institution' => 'J-' . $uniq, 'email_institution' => 'test@test.com',
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
        ]);

        $seccion = Seccion::find($seccionId);

        $repId = DB::table('representants')->insertGetId([
            'user_id' => $user->id, 'name' => 'Test Rep',
            'ci_representant' => 'V-' . $user->id, 'status_active' => 'true',
        ]);

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan', 'status_active' => 'true',
        ]);

        $typeCiExists = DB::table('type_cis')->where('id', 1)->exists();
        if (!$typeCiExists) {
            DB::table('type_cis')->insert([
                'id' => 1, 'name' => 'V', 'status_active' => 'true',
            ]);
        }

        $estudiant = Estudiant::create([
            'user_id' => $user->id, 'representant_id' => $repId,
            'planpago_id' => $planpagoId, 'type_ci_id' => 1,
            'ci_estudiant' => 'V-' . $user->id, 'representant_ci' => 'V-' . $user->id,
            'name' => 'Test', 'lastname' => 'Student', 'status_active' => 'true',
        ]);

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

        Pensum::create([
            'pestudio_id' => $pestudioId, 'grado_id' => $grado->id,
            'asignatura_id' => $asignaturaId, 'status_component' => true,
            'status_active' => true,
        ]);

        return [$user, $seccion, $asignaturaId];
    }
}
