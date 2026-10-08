<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EiprojectkComponent;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\User;
use App\Services\Inicial\ImportadorEiprojectk;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Clon de bwk: importación de proyectos desde s2526.
 *
 * Replica `EiplanningbwkImportTest` adaptado a `eiprojectks` (hijas extra:
 * `eiprojectreviews`, que se copian tal cual por no llevar pevaluación).
 *
 * @group inicial
 * @group inicial-import
 */
class EiprojectkImportTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @param  array<int, int>  $asignaturas
     * @return array{user: User, profesor_id: int, pevaluacion_id: int}
     */
    private function makeDocenteConCarga(int $gradoId, int $seccionId, array $asignaturas = []): array
    {
        $attributes = ['is_admin' => false, 'is_profesor' => true];

        if (Schema::hasColumn('users', 'is_inicial')) {
            $attributes['is_inicial'] = true;
        }

        $user = User::factory()->create($attributes);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-'.random_int(10_000_000, 99_999_999),
            'ci_profesor' => (string) random_int(10_000_000, 99_999_999),
            'name' => 'Docente',
            'lastname' => 'Project Import Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asignaturas = $asignaturas === []
            ? [Pensum::where('grado_id', $gradoId)->value('asignatura_id')]
            : $asignaturas;

        $lapsoId = Lapso::current()?->id ?? DB::table('lapsos')->value('id');
        $primera = null;

        foreach ($asignaturas as $asignaturaId) {
            $pensum = Pensum::where('grado_id', $gradoId)->where('asignatura_id', $asignaturaId)->firstOrFail();

            $id = DB::table('pevaluacions')->insertGetId([
                'profesor_id' => $profesorId,
                'pensum_id' => $pensum->id,
                'seccion_id' => $seccionId,
                'lapso_id' => $lapsoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $primera ??= $id;
        }

        return ['user' => $user, 'profesor_id' => $profesorId, 'pevaluacion_id' => $primera];
    }

    /** Primer proyecto legacy (id real de s2526, nunca hardcodeado). */
    private function primerProyectoLegacy(): object
    {
        return DB::connection('s2526')->table('eiprojectks')->orderBy('id')->first();
    }

    private function saltarSiNoHayLegacy(): void
    {
        try {
            DB::connection('s2526')->select('SELECT 1');
        } catch (\Throwable $e) {
            $this->markTestSkipped('La conexión legacy s2526 no está disponible en este entorno.');
        }

        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped('Requiere la columna users.is_inicial.');
        }
    }

    /** @test */
    public function importar_crea_un_proyecto_con_estrategias_revisiones_y_resumenes(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerProyectoLegacy();
        $docente = $this->makeDocenteConCarga(
            (int) $legacy->grado_id,
            (int) $legacy->seccion_id,
            [235, 237, 239]
        );

        $esperadasEstrategias = DB::connection('s2526')->table('eiprojectkstrategies')
            ->where('eiprojectk_id', $legacy->id)->count();
        $esperadasRevisiones = DB::connection('s2526')->table('eiprojectreviews')
            ->where('eiprojectk_id', $legacy->id)->count();

        $reporte = (new ImportadorEiprojectk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $reporte['creados']);
        $this->assertSame([], $reporte['omitidos']);

        $creado = $reporte['creados'][0];

        $this->assertNotSame((int) $legacy->id, $creado['nuevo_id']);
        $this->assertSame($esperadasEstrategias, $creado['estrategias']);
        $this->assertSame($esperadasRevisiones, $creado['revisiones']);

        $proyecto = Eiprojectk::find($creado['nuevo_id']);

        $this->assertSame($docente['profesor_id'], $proyecto->profesor_id);
        $this->assertSame($esperadasEstrategias, $proyecto->eiprojectkstrategies()->count());
        $this->assertSame($esperadasRevisiones, $proyecto->eiprojectreviews()->count());
        $this->assertSame(3, $creado['resumenes_ok']);
        $this->assertSame(0, $creado['resumenes_omitidos']);
    }

    /** @test */
    public function importar_dos_veces_no_duplica_el_proyecto(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerProyectoLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $primero = (new ImportadorEiprojectk)->importar([$legacy->id], $docente['profesor_id']);
        $segundo = (new ImportadorEiprojectk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $primero['creados']);
        $this->assertSame([], $segundo['creados']);
        $this->assertCount(1, $segundo['omitidos']);
        $this->assertSame(1, Eiprojectk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_componente_abre_el_asistente_e_importa_lo_marcado(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerProyectoLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openImport')
            ->assertSet('showImport', true)
            ->set('importSeleccionados', [$legacy->id])
            ->call('importarSeleccionados')
            ->assertHasNoErrors();

        $this->assertSame(1, Eiprojectk::where('profesor_id', $docente['profesor_id'])->count());
    }
}
