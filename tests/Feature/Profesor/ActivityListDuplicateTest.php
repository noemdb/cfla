<?php

namespace Tests\Feature\Profesor;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresión: el listado de actividades del profesor (/app/profesors/activities)
 * se renderizaba DOS veces porque el layout chain imprimía el contenido tanto
 * por $slot como por @yield('content').
 */
class ActivityListDuplicateTest extends TestCase
{
    use DatabaseTransactions;

    private static int $chainCounter = 0;

    public function test_profesor_activities_index_renders_content_once(): void
    {
        [$pevaluacionId, $profesorId, $s] = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($profesorId);

        $response = $this->actingAs($user)->get(route('app.profesors.activities.index'));

        $response->assertOk();

        $html = $response->getContent();

        $this->assertSame(
            1,
            substr_count($html, '>Plan de Actividades</h1>'),
            'El título "Plan de Actividades" debe aparecer exactamente 1 vez.'
        );

        $this->assertSame(
            1,
            substr_count($html, 'Módulo Planificación Académica'),
            'El módulo de planificación debe aparecer exactamente 1 vez.'
        );
    }

    public function test_livewire_components_not_duplicated(): void
    {
        [$pevaluacionId, $profesorId, $s] = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($profesorId);

        $response = $this->actingAs($user)->get(route('app.profesors.activities.index'));
        $html = $response->getContent();

        // Cada componente Livewire del listado debe montarse exactamente 1 vez.
        // Antes de la corrección, el @yield('content') duplicaba la página entera
        // y cada wire:id aparecía 2 veces.
        $this->assertSame(
            1,
            substr_count($html, 'profesor.activity.pevaluacion-list'),
            'El componente pevaluacion-list no debe duplicarse.'
        );

        $this->assertSame(
            1,
            substr_count($html, 'profesor.activity.listado-global-dialog'),
            'El componente listado-global-dialog no debe duplicarse.'
        );

        $this->assertSame(
            1,
            substr_count($html, 'profesor.activity.competencias-dialog'),
            'El componente competencias-dialog no debe duplicarse.'
        );
    }

    // ─── Helpers (misma cadena FK que ActivityImprovementTest) ────────────

    private function createProfesorUser(int $profesorId): User
    {
        $user = User::factory()->create(['is_profesor' => true]);

        DB::table('profesors')->where('id', $profesorId)->update(['user_id' => $user->id]);

        return $user;
    }

    private function createEvaluacionChain(): array
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

        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => $code('PEST-TEST'),
            'name' => 'Test Plan de Estudio '.$s,
            'scale' => $escalaId,
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
            'objetivo' => 'Test objetivo '.$s,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$pevaluacionId, $profesorId, $s];
    }
}