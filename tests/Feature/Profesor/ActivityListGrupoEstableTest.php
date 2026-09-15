<?php

namespace Tests\Feature\Profesor;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * En el listado de áreas de formación del profesor (/app/profesors/activities)
 * debe verse el nombre del grupo estable asociado a la Pevaluacion, debajo de
 * la asignatura (tanto en modo tabla como grid).
 */
class ActivityListGrupoEstableTest extends TestCase
{
    use DatabaseTransactions;

    private static int $chainCounter = 0;

    public function test_listado_muestra_el_nombre_del_grupo_estable(): void
    {
        [$profesorId, $grupoEstableId, $lapsoId] = $this->createEvaluacionChainWithGrupo('GRUPO ROBOTICA X');
        $user = $this->createProfesorUser($profesorId);

        $response = $this->actingAs($user)->get(
            route('app.profesors.activities.index', ['lapso_id' => $lapsoId]),
        );

        $response->assertOk();
        $response->assertSee('GRUPO ROBOTICA X', false);
        $response->assertSee('Comp. Formación:', false);
        $this->assertGreaterThanOrEqual(1, $grupoEstableId);
    }

    public function test_listado_no_falla_sin_grupo_estable(): void
    {
        [$profesorId, , $lapsoId] = $this->createEvaluacionChainWithGrupo(null);
        $user = $this->createProfesorUser($profesorId);

        $response = $this->actingAs($user)->get(
            route('app.profesors.activities.index', ['lapso_id' => $lapsoId]),
        );

        $response->assertOk();
        $response->assertDontSee('Comp. Formación:', false);
    }

    private function createProfesorUser(int $profesorId): User
    {
        $user = User::factory()->create(['is_profesor' => true]);

        DB::table('profesors')->where('id', $profesorId)->update(['user_id' => $user->id]);

        return $user;
    }

    /**
     * @return array{0: int, 1: int|null}
     */
    private function createEvaluacionChainWithGrupo(?string $grupoName): array
    {
        self::$chainCounter++;
        $s = self::$chainCounter;
        $code = fn (string $base) => "{$base}-{$s}";

        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => $code('LAP-TEST'),
            'code_sm' => 'LT',
            'name' => 'Test Lapso '.$s,
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA',
            'name' => 'Test Scale '.$s,
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Institution '.$s,
            'legalname' => 'Test Institution Legal '.$s,
            'rif_institution' => 'J-'.str_pad((string) $s, 8, '0', STR_PAD_LEFT).'-9',
            'email_institution' => 'test'.$s.'@institution.test',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año Escolar '.$s,
            'description' => 'Test',
            'finicial' => now(),
            'ffinal' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId,
            'name' => 'Test PE '.$s,
            'description' => 'Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // planning_module = true es lo que hace visible la pevaluacion en el
        // listado del profesor.
        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => $code('PEST-TEST'),
            'name' => 'Test Plan de Estudio '.$s,
            'scale' => $escalaId,
            'planning_module' => true,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId,
            'name' => 'Test Grado '.$s,
            'code' => $code('GR-TEST'),
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId,
            'name' => 'A'.$s,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => $code('ASIG-TEST'),
            'name' => 'Test Asignatura '.$s,
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
            'ti_teacher' => 'V-'.str_pad((string) $s, 8, '0', STR_PAD_LEFT),
            'ci_profesor' => str_pad((string) $s, 8, '0', STR_PAD_LEFT),
            'name' => 'Profesor Test '.$s,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $grupoEstableId = null;
        if ($grupoName !== null) {
            $grupoEstableId = DB::table('grupo_estables')->insertGetId([
                'code' => $code('GE-TEST'),
                'code_sm' => 'GE',
                'name' => $grupoName,
                'status_active' => 'true',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'grupo_estable_id' => $grupoEstableId,
            'objetivo' => 'Test objetivo '.$s,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$profesorId, $grupoEstableId, $lapsoId];
    }
}
