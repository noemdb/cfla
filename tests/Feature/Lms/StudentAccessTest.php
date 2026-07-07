<?php

namespace Tests\Feature\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_student_can_see_home(): void
    {
        $student = User::factory()->create(['is_student' => true]);

        $response = $this->actingAs($student)
            ->get(route('student.lms.home'));

        $response->assertStatus(200);
    }

    public function test_non_student_cannot_access_student_routes(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(403);
    }

    public function test_student_cannot_access_unpublished_activity(): void
    {
        $student = User::factory()->create(['is_student' => true]);

        // Create a minimal activity with required FK chain
        $activity = $this->createMinimalActivity();

        $response = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity));

        $response->assertStatus(404);
    }

    public function test_student_can_access_published_activity(): void
    {
        $student = User::factory()->create(['is_student' => true]);

        // Create minimal activity
        $activity = $this->createMinimalActivity();

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity));

        $response->assertStatus(200);
    }

    private function createMinimalActivity(): Activity
    {
        // Build the FK chain manually since the deep factory chain has missing factories.
        // pevaluacion -> pensum -> pestudio -> peducativo -> pescolar
        //              -> grado       -> escala
        //              -> seccion
        //              -> lapso

        // Only create records if they don't already exist (for transaction safety)
        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-TEST',
            'code_sm' => 'LT',
            'name' => 'Test Lapso',
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA',
            'name' => 'Test Scale',
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Institution',
            'legalname' => 'Test Institution Legal',
            'rif_institution' => 'J-12345678-9',
            'email_institution' => 'test@institution.test',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año Escolar',
            'description' => 'Test',
            'finicial' => now(),
            'ffinal' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId,
            'name' => 'Test PE',
            'description' => 'Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => 'PEST-TEST',
            'name' => 'Test Plan de Estudio',
            'scale' => $escalaId,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId,
            'name' => 'Test Grado',
            'code' => 'GR-TEST',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId,
            'name' => 'A',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => 'ASIG-TEST',
            'name' => 'Test Asignatura',
            'tescala' => $escalaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $pestudioId,
            'grado_id' => $gradoId,
            'asignatura_id' => $asignaturaId,
            'status_component' => true,
            'status_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-12345678',
            'ci_profesor' => '12345678',
            'name' => 'Profesor Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'objetivo' => 'Test objetivo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Test Activity',
            'references' => 'Refs',
            'teaching' => 'Teaching',
            'learning' => 'Learning',
            'observations' => 'Obs',
        ]);
    }
}
