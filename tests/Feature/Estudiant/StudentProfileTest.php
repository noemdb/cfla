<?php

namespace Tests\Feature\Estudiant;

use App\Models\User;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Seccion;
use App\Models\app\Learner\Estudiant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use DatabaseTransactions;

    private ?int $seccionId = null;

    private ?int $pestudioId = null;

    private ?int $gradoId = null;

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

    public function test_profile_stats_use_home_semantics(): void
    {
        $user = $this->createEnrolledStudentWithProfile();

        // 2 actividades publicadas visibles en la sección del estudiante
        $pevaluacionId = $this->createActivityChainForSeccion($this->seccionId);
        $activityA = $this->createPublishedActivity($pevaluacionId);
        $activityB = $this->createPublishedActivity($pevaluacionId);

        // Progreso: 1 de 2 completadas
        LmsActivityLog::create([
            'activity_id' => $activityA,
            'user_id' => $user->id,
            'event' => 'COMPLETE',
            'created_at' => now(),
        ]);

        // 1 descarga
        LmsActivityLog::create([
            'activity_id' => $activityA,
            'user_id' => $user->id,
            'event' => 'RESOURCE_DOWNLOAD',
            'created_at' => now(),
        ]);

        // Comentarios: 1 del estudiante + 1 de OTRO usuario (no debe contarse)
        ActivityComment::create([
            'activity_id' => $activityA,
            'user_id' => $user->id,
            'body' => 'Mi comentario',
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);
        ActivityComment::create([
            'activity_id' => $activityA,
            'user_id' => User::factory()->create()->id,
            'body' => 'Comentario ajeno',
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Student\Lms\Profile::class)
            // Semántica canónica (StudentHome): conteos reales, no % crudos
            ->assertSet('stats.total', 2)
            ->assertSet('stats.completed', 1)
            ->assertSet('stats.progress_pct', 50)
            ->assertSet('stats.comments', 1)   // solo el del propio estudiante
            ->assertSet('stats.downloads', 1)
            // UI honesta: el % solo aparece como "X% del total"
            ->assertSee('Disponibles para ti')
            ->assertSee('50% del total')
            ->assertSee('Que has dejado')
            ->assertSee('Recursos descargados');
    }

    public function test_profile_stats_empty_state_shows_sin_actividades(): void
    {
        $user = $this->createEnrolledStudentWithProfile();
        // sin actividades publicadas en la sección

        Livewire::actingAs($user)
            ->test(\App\Livewire\Student\Lms\Profile::class)
            ->assertSet('stats.total', 0)
            ->assertSet('stats.completed', 0)
            ->assertSet('stats.progress_pct', 0)
            ->assertSee('Sin actividades');
    }

    private function createActivityChainForSeccion(int $seccionId): int
    {
        $uniq = uniqid();

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA',
            'name' => 'Escala '.$uniq,
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
        ]);

        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-'.$uniq,
            'code_sm' => 'LT',
            'name' => 'Lapso '.$uniq,
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $this->pestudioId,
            'code' => 'ASIG-'.$uniq,
            'name' => 'Asignatura '.$uniq,
            'tescala' => $escalaId,
        ]);

        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $this->pestudioId,
            'grado_id' => $this->gradoId,
            'asignatura_id' => $asignaturaId,
            'status_component' => true,
            'status_active' => true,
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-'.$uniq,
            'ci_profesor' => substr($uniq, 0, 8),
            'name' => 'Profesor '.$uniq,
            'status_active' => 'true',
        ]);

        return DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'objetivo' => 'Objetivo '.$uniq,
        ]);
    }

    private function createPublishedActivity(int $pevaluacionId): int
    {
        $activityId = DB::table('activities')->insertGetId([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now()->subDays(2),
            'ffinal' => now()->addDays(7),
            'topic' => 'Tema '.uniqid(),
            'status' => true,
        ]);

        $publisher = User::factory()->create(['is_profesor' => true]);
        DB::table('lms_activity_publications')->insert([
            'activity_id' => $activityId,
            'published_by' => $publisher->id,
            'status' => 'PUBLISHED',
            'publish_at' => now()->subDay(),
            'published_at' => now()->subDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $activityId;
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
        $this->pestudioId = $pestudioId;

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId, 'name' => '1er Año',
            'code' => '1A', 'status_active' => 'true',
        ]);
        $this->gradoId = $gradoId;

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId, 'name' => 'A', 'status_active' => 'true',
        ]);
        $this->seccionId = $seccionId;

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
