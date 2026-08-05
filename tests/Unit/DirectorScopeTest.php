<?php

namespace Tests\Unit\Director;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use App\Services\Director\DirectorScopeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class DirectorScopeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Crea un profesor activo con una carga académica (pevaluación) completa.
     * Fixture verificado contra el esquema MySQL s2627.
     */
    private function makeProfesorConCarga(string $suffix): Profesor
    {
        $profesor = Profesor::create([
            'ti_teacher'    => 'TI-' . Str::upper($suffix),
            'ci_profesor'   => 'CI-' . rand(100000, 999999),
            'name'          => 'Ana',
            'lastname'      => 'Ruiz',
            'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id'   => $pestudio->id,
            'grado_id'      => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id'   => $pensum->id,
            'seccion_id'  => $seccion->id,
            'lapso_id'    => $lapso->id,
        ]);

        return $profesor;
    }

    public function test_query_pensums_returns_all_pensums_unfiltered(): void
    {
        $user = User::factory()->director()->create();
        $service = new DirectorScopeService($user);

        $before = Pensum::count();

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();
        Pensum::factory()->count(2)->create([
            'pestudio_id'   => $pestudio->id,
            'grado_id'      => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);

        // El director supervisa TODA la institución: queryPensums no filtra nada.
        $this->assertSame($before + 2, $service->queryPensums()->count());
    }

    public function test_query_profesores_returns_only_active_teachers_with_carga(): void
    {
        $user = User::factory()->director()->create();
        $service = new DirectorScopeService($user);

        $conCarga = $this->makeProfesorConCarga('ACT1'); // activo + carga → incluido
        $sinCarga = Profesor::create([                   // activo sin carga → excluido
            'ti_teacher'    => 'TI-SIN-' . Str::upper(Str::random(4)),
            'ci_profesor'   => 'CI-' . rand(100000, 999999),
            'name'          => 'Luis',
            'lastname'      => 'Perez',
            'status_active' => 'true',
        ]);
        $inactivo = $this->makeProfesorConCarga('INAC'); // inactivo con carga → excluido
        $inactivo->update(['status_active' => 'false']);

        $ids = $service->queryProfesores()->pluck('id');

        $this->assertContains($conCarga->id, $ids);
        $this->assertNotContains($sinCarga->id, $ids);
        $this->assertNotContains($inactivo->id, $ids);
    }

    public function test_director_factory_state_sets_is_director(): void
    {
        $user = User::factory()->director()->create();

        $this->assertTrue($user->is_director);
        $this->assertTrue($user->isDirector());
    }

    public function test_role_label_returns_direccion(): void
    {
        $user = User::factory()->director()->create();

        $this->assertEquals('Dirección', $user->role_label);
    }
}
