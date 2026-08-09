<?php

namespace Tests\Feature\Livewire\Student\Lms;

use App\Livewire\Student\Lms\LessonList;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El listado de lecciones del estudiante separa en dos grupos claramente
 * diferenciados: primero las lecciones del lapso actual (Lapso::current())
 * y después las de los demás lapsos ("Lecciones anteriores").
 *
 * @group student-lms
 * @group student-lms-lessons
 */
class LessonListGroupingTest extends TestCase
{
    use DatabaseTransactions;

    private function buildChain(): array
    {
        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-CUR-'.uniqid(), 'code_sm' => 'LC', 'name' => 'Lapso Actual',
            'finicial' => now()->subMonth(), 'ffinal' => now()->addMonth(),
            'status_last' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $oldLapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-OLD-'.uniqid(), 'code_sm' => 'LO', 'name' => 'Lapso Pasado',
            'finicial' => now()->subYear(), 'ffinal' => now()->subMonths(6),
            'status_last' => 'false', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA', 'name' => 'Scale', 'minimo' => '1', 'maximo' => '20',
            'aprobacion' => '10', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Inst', 'legalname' => 'Inst Legal', 'rif_institution' => 'J-'.uniqid(),
            'email_institution' => 'inst-'.uniqid().'@test.com',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId, 'name' => 'PE', 'description' => 'x',
            'finicial' => now(), 'ffinal' => now()->addYear(),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'name' => 'PEd', 'description' => 'x', 'pescolar_id' => $pescolarId,
            'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'code' => 'PES-'.uniqid(), 'name' => 'PEst', 'peducativo_id' => $peducativoId,
            'scale' => $escalaId, 'planning_module' => 'true', 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId, 'name' => 'Grado', 'code' => 'G',
            'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId, 'name' => 'A', 'description' => 'Sec A',
            'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId, 'code' => 'ASIG', 'name' => 'Matemática',
            'tescala' => $escalaId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $pestudioId, 'grado_id' => $gradoId, 'asignatura_id' => $asignaturaId,
            'status_component' => true, 'status_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return compact('lapsoId', 'oldLapsoId', 'escalaId', 'institucionId', 'pescolarId', 'peducativoId', 'pestudioId', 'gradoId', 'seccionId', 'asignaturaId', 'pensumId');
    }

    private function activityWithLapso(int $profesorId, array $chain, int $lapsoId, string $topic): Activity
    {
        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $chain['pensumId'], 'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId, 'seccion_id' => $chain['seccionId'],
            'objetivo' => 'obj', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(), 'ffinal' => now()->addDays(7),
            'status' => true,
            'topic' => $topic, 'thematic' => 'Tejido', 'description' => 'Desc',
            'teaching' => 'T', 'learning' => 'L', 'references' => 'R', 'observations' => 'O',
        ]);
    }

    private function setupUser(int $seccionId): \App\Models\User
    {
        $user = \App\Models\User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $representantId = DB::table('representants')->insertGetId([
            'ci_representant' => 'V-'.uniqid(), 'name' => 'Rep', 'phone' => '0000',
            'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Plan '.uniqid(), 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $estudiantId = DB::table('estudiants')->insertGetId([
            'user_id' => $user->id, 'representant_id' => $representantId,
            'planpago_id' => $planpagoId,
            'name' => 'Estudiante', 'lastname' => 'Test',
            'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $tipoId = DB::table('tinscripcions')->insertGetId([
            'name' => 'Ordinaria', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $programacionId = DB::table('programacions')->insertGetId([
            'name' => 'Programación '.uniqid(), 'description' => 'x',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('inscripcions')->insert([
            'estudiant_id' => $estudiantId, 'seccion_id' => $seccionId,
            'tipo_id' => $tipoId, 'programacion_id' => $programacionId,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return $user;
    }

    private function publish(int $activityId, int $userId): void
    {
        DB::table('lms_activity_publications')->insert([
            'activity_id' => $activityId, 'published_by' => $userId, 'status' => 'PUBLISHED',
            'publish_at' => now()->subDay(), 'published_at' => now(),
            'allow_comments' => true, 'allow_downloads' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** @test */
    public function agrupa_lapso_actual_primero_y_resto_despues(): void
    {
        // El lapso actual lo define Lapso::current() (hoy cae en su rango y es el
        // de menor id que lo cumple) — puede existir data real en la BD compartida.
        $currentLapso = \App\Models\app\Academy\Lapso::current();
        $this->assertNotNull($currentLapso);
        $currentLapsoId = $currentLapso->id;

        $profesorUser = \App\Models\User::factory()->create(['is_admin' => true]);
        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-1', 'ci_profesor' => '1', 'name' => 'P', 'lastname' => 'P',
            'user_id' => $profesorUser->id, 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $chain = $this->buildChain();
        $this->setupUser($chain['seccionId']);

        $cur = $this->activityWithLapso($profesorId, $chain, $currentLapsoId, 'Lección del Lapso Actual');
        $old = $this->activityWithLapso($profesorId, $chain, $chain['oldLapsoId'], 'Lección de un Lapso Pasado');

        $this->publish($cur->id, $profesorUser->id);
        $this->publish($old->id, $profesorUser->id);

        // Verifico además que el oldLapso NO es el actual (para que caiga en el grupo resto)
        $this->assertNotSame($currentLapsoId, $chain['oldLapsoId']);

        $component = Livewire::test(LessonList::class);

        $html = $component->html();
        $this->assertStringContainsString('Lapso actual', $html);
        $this->assertStringContainsString('Lecciones anteriores', $html);

        // El grupo "Lapso actual" aparece antes que "Lecciones anteriores"
        $posCurrent = strpos($html, 'Lapso actual');
        $posOthers = strpos($html, 'Lecciones anteriores');
        $this->assertGreaterThan($posCurrent, $posOthers);
    }
}
