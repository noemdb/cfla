<?php

namespace Tests\Feature\Director;

use App\Livewire\Director\CargaAcademicaList;
use App\Livewire\Director\IndicatorDashboard;
use App\Livewire\Director\ProfesorIndicators;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class DirectorDashboardTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Fixture completo: profesor activo con carga académica.
     * Cualquier profesor de la institución debe ser visible para el director.
     */
    private function makeProfesorConCarga(string $suffix, ?string $lastname = null): Profesor
    {
        $profesor = Profesor::create([
            'ti_teacher'    => 'TI-' . Str::upper($suffix),
            'ci_profesor'   => 'CI-' . rand(100000, 999999),
            'name'          => 'Ana',
            'lastname'      => $lastname ?? 'Ruiz',
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

    public function test_dashboard_counts_whole_institution(): void
    {
        $user = User::factory()->director()->create();
        $this->makeProfesorConCarga('DASH1');

        $component = Livewire::actingAs($user)->test(IndicatorDashboard::class);

        // Los KPIs globales no filtran por área/peducativo del usuario:
        // totalPensums = todos los pensums de la institución.
        $this->assertSame(
            Pensum::count(),
            (int) $component->get('totalPensums')
        );

        // totalProfesoresActivos = todos los profesores activos con carga.
        $this->assertSame(
            Profesor::where('status_active', 'true')->whereHas('pevaluacions')->count(),
            (int) $component->get('totalProfesoresActivos')
        );
    }

    public function test_carga_academica_lists_pevaluacions_of_whole_institution(): void
    {
        $user = User::factory()->director()->create();
        $profesor = $this->makeProfesorConCarga('CARGA1');

        Livewire::actingAs($user)
            ->test(CargaAcademicaList::class)
            ->assertStatus(200)
            ->assertViewHas('pevaluacions', function ($paginator) use ($profesor) {
                return $paginator->contains(
                    fn ($peva) => $peva->profesor_id === $profesor->id
                );
            });
    }

    public function test_profesores_indicators_cover_all_teachers(): void
    {
        $user = User::factory()->director()->create();

        // Apellido único para poder aislar al profesor vía el buscador
        // (el listado paginado se ordena por lastname y la institución
        // tiene muchos docentes, no se puede depender de la página 1).
        $tag = 'RUIZ-' . Str::upper(Str::random(6));
        $profesor = $this->makeProfesorConCarga('PROF1', $tag);

        Livewire::actingAs($user)
            ->test(ProfesorIndicators::class)
            ->set('search', $tag)
            ->assertStatus(200)
            ->assertViewHas('profesores', function ($paginator) use ($profesor) {
                return $paginator->contains(
                    fn ($p) => $p->id === $profesor->id
                );
            });
    }
}
