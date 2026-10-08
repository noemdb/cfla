<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EispecialkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eispecialk;
use App\Models\User;
use App\Services\Inicial\ImportadorEispecialk;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Clon de bwk: importación de planes especiales desde s2526 + `pensum_id`
 * opcional + edición en ambos modos.
 *
 * Replica `EiplanningbwkImportTest` adaptado a `eispecialks` (cabecera con
 * `justificacion`, hijas: `eispecialstrategies` sin pevaluación y
 * `eispecialacts` a remapear).
 *
 * @group inicial
 * @group inicial-import
 */
class EispecialkImportTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @param  array<int, int>  $asignaturas
     * @return array{user: User, profesor_id: int, pevaluacion_id: int, pensum_id: int}
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
            'lastname' => 'Special Import Test',
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
        $pensumId = null;

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
            $pensumId ??= $pensum->id;
        }

        return [
            'user' => $user,
            'profesor_id' => $profesorId,
            'pevaluacion_id' => $primera,
            'pensum_id' => $pensumId,
        ];
    }

    /** Primer plan especial legacy (id real de s2526, nunca hardcodeado). */
    private function primerPlanLegacy(): object
    {
        return DB::connection('s2526')->table('eispecialks')->orderBy('id')->first();
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
    public function importar_crea_un_plan_especial_con_sus_actividades_remapeadas(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();

        // Las 3 áreas de sus actividades (asignaturas 234/236/238, sección 57).
        $docente = $this->makeDocenteConCarga(
            (int) $legacy->grado_id,
            (int) $legacy->seccion_id,
            [234, 236, 238]
        );

        $reporte = (new ImportadorEispecialk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $reporte['creados']);
        $this->assertSame([], $reporte['omitidos']);

        $creado = $reporte['creados'][0];

        $this->assertNotSame((int) $legacy->id, $creado['nuevo_id']);

        $plan = Eispecialk::find($creado['nuevo_id']);

        $this->assertSame($docente['profesor_id'], $plan->profesor_id);
        $this->assertSame(3, $creado['resumenes_ok']);
        $this->assertSame(0, $creado['resumenes_omitidos']);
        $this->assertSame(3, $plan->activities()->count());
    }

    /** @test */
    public function importar_dos_veces_no_duplica_el_plan_especial(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $primero = (new ImportadorEispecialk)->importar([$legacy->id], $docente['profesor_id']);
        $segundo = (new ImportadorEispecialk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $primero['creados']);
        $this->assertSame([], $segundo['creados']);
        $this->assertCount(1, $segundo['omitidos']);
        $this->assertSame(1, Eispecialk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_componente_abre_el_asistente_e_importa_lo_marcado(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openImport')
            ->assertSet('showImport', true)
            ->set('importSeleccionados', [$legacy->id])
            ->call('importarSeleccionados')
            ->assertHasNoErrors();

        $this->assertSame(1, Eispecialk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function guarda_un_plan_con_el_area_de_aprendizaje_del_docente(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->set([
                'eispecialk.grado_id' => $gradoId,
                'eispecialk.seccion_id' => $seccionId,
                'eispecialk.pensum_id' => $docente['pensum_id'],
                'eispecialk.finicial' => '2026-10-05',
                'eispecialk.ffinal' => '2026-10-23',
                'eispecialk.tiempo_ejecucion' => 3,
                'eispecialk.justificacion' => 'El grupo necesita ampliar la mirada sobre su entorno cotidiano.',
            ])
            ->call('save');

        $component->assertHasNoErrors();

        $plan = Eispecialk::where('profesor_id', $docente['profesor_id'])->first();

        $this->assertNotNull($plan);
        $this->assertSame($docente['pensum_id'], (int) $plan->pensum_id);
    }

    /** @test */
    public function rechaza_un_area_de_aprendizaje_de_otro_docente(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);

        $otroPensum = (int) Pensum::where('grado_id', $gradoId)->where('id', '!=', $docente['pensum_id'])->value('id');

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->set([
                'eispecialk.grado_id' => $gradoId,
                'eispecialk.seccion_id' => $seccionId,
                'eispecialk.pensum_id' => $otroPensum,
                'eispecialk.finicial' => '2026-10-05',
                'eispecialk.ffinal' => '2026-10-23',
                'eispecialk.tiempo_ejecucion' => 3,
                'eispecialk.justificacion' => 'El grupo necesita ampliar la mirada sobre su entorno cotidiano.',
            ])
            ->call('save');

        $component->assertHasErrors('eispecialk.pensum_id');
        $this->assertSame(0, Eispecialk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_filtro_de_area_acota_el_listado_al_pensum_elegido(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);

        $conArea = Eispecialk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'pensum_id' => $docente['pensum_id'],
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-23',
            'tiempo_ejecucion' => 3,
            'justificacion' => 'Justificación del plan especial con área vinculada.',
        ]);

        Eispecialk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-23',
            'tiempo_ejecucion' => 3,
            'justificacion' => 'Justificación del plan especial sin área vinculada.',
        ]);

        $ids = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->set('filterPensum', $docente['pensum_id'])
            ->viewData('eispecialks')
            ->pluck('id')
            ->all();

        $this->assertSame([$conArea->id], $ids);
    }

    /** @test */
    public function el_boton_editar_aparece_en_modo_tarjetas_y_en_modo_tabla(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);

        Eispecialk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-23',
            'tiempo_ejecucion' => 3,
            'justificacion' => 'Justificación del plan especial de prueba.',
        ]);

        $grid = Livewire::actingAs($docente['user'])->test(EispecialkComponent::class);
        $grid->assertSee('Editar plan');

        $grid->call('toggleView');
        $grid->assertSet('viewMode', 'table');
        $grid->assertSee('Editar plan');
    }

    /** @test */
    public function editar_un_plan_actualiza_su_cabecera(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);

        $plan = Eispecialk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-23',
            'tiempo_ejecucion' => 3,
            'justificacion' => 'Justificación original del plan especial.',
        ]);

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'edit', $plan->id);

        $this->assertSame($plan->justificacion, $component->get('eispecialk')['justificacion']);

        $component
            ->set('eispecialk.justificacion', 'Justificación actualizada del plan especial de prueba.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Justificación actualizada del plan especial de prueba.', $plan->fresh()->justificacion);
    }
}
