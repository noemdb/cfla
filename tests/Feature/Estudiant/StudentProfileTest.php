<?php

namespace Tests\Feature\Estudiant;

use App\Models\User;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Seccion;
use App\Models\app\Learner\Estudiant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_profile_page_renders_for_student_with_data(): void
    {
        $user = $this->createEnrolledStudentWithProfile();

        $response = $this->actingAs($user)
            ->get(route('student.lms.profile'));

        $response->assertStatus(200);
        $response->assertSee('Carlos');
        $response->assertSee('Pérez');
    }

    public function test_profile_shows_no_data_message_for_orphan_user(): void
    {
        $user = User::factory()->create(['is_student' => true]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.profile'));

        $response->assertStatus(200);
        $response->assertSee('No se encontraron datos');
    }

    private function createEnrolledStudentWithProfile(): User
    {
        $user = User::factory()->create(['is_student' => true]);
        $uniq = uniqid();

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Inst ' . $uniq,
            'legalname' => 'Test Legal ' . $uniq,
            'rif_institution' => 'J-' . $uniq,
            'email_institution' => 'test@test.com',
            'status_dont_allow_registration_if_insolvency' => 'false',
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año', 'description' => 'Test',
            'finicial' => now()->subYear(),
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

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId, 'name' => '1er Año',
            'code' => '1A', 'status_active' => 'true',
        ]);

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
            'user_id' => $user->id,
            'representant_id' => $repId,
            'planpago_id' => $planpagoId,
            'type_ci_id' => 1,
            'ci_estudiant' => 'V-12345678',
            'representant_ci' => 'V-' . $user->id,
            'name' => 'Carlos',
            'lastname' => 'Pérez',
            'date_birth' => '2005-06-15',
            'status_active' => 'true',
        ]);

        $tipoInscripcionId = DB::table('tinscripcions')->insertGetId(['name' => 'Test']);
        $programacionId = DB::table('programacions')->insertGetId(['name' => 'Test']);

        Inscripcion::create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipoInscripcionId,
            'programacion_id' => $programacionId,
        ]);

        return $user;
    }
}
