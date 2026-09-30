<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Activity\PevaluacionList;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * En el listado del profesor (/app/profesors/activities, modos grid y tabla)
 * la pevaluación señaliza con el badge "C" si alguna de sus actividades
 * tiene información complementaria (texto o imagen en activity_supplements).
 */
class ActivityListSupplementBadgeTest extends TestCase
{
    use DatabaseTransactions;

    private static int $chainCounter = 0;

    public function test_muestra_badge_si_hay_informacion_complementaria(): void
    {
        [$profesorId, $lapsoId, $pevaluacionId] = $this->createChainWithActivity();
        $activityId = DB::table('activities')->insertGetId([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Tema con suplemento',
            'status' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('activity_supplements')->insert([
            'activity_id' => $activityId,
            'text' => 'Texto complementario',
            'image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = $this->createProfesorUser($profesorId);

        Livewire::actingAs($user)
            ->test(PevaluacionList::class, ['lapsoId' => $lapsoId])
            ->assertOk()
            ->assertSee('Contiene información complementaria', false);
    }

    public function test_no_muestra_badge_sin_informacion_complementaria(): void
    {
        [$profesorId, $lapsoId, $pevaluacionId] = $this->createChainWithActivity();
        DB::table('activities')->insert([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Tema sin suplemento',
            'status' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = $this->createProfesorUser($profesorId);

        Livewire::actingAs($user)
            ->test(PevaluacionList::class, ['lapsoId' => $lapsoId])
            ->assertOk()
            ->assertDontSee('Contiene información complementaria', false);
    }

    private function createProfesorUser(int $profesorId): User
    {
        $user = User::factory()->create(['is_profesor' => true]);

        DB::table('profesors')->where('id', $profesorId)->update(['user_id' => $user->id]);

        return $user;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function createChainWithActivity(): array
    {
        self::$chainCounter++;
        $s = self::$chainCounter;
        $code = fn (string $base) => "{$base}-SUP{$s}";

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
            'email_institution' => 'testsup'.$s.'@institution.test',
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

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'grupo_estable_id' => null,
            'objetivo' => 'Test objetivo '.$s,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$profesorId, $lapsoId, $pevaluacionId];
    }
}
