<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EiplanningbwkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\User;
use App\Services\Inicial\ImportadorEiplanningbwk;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Clon de bwk: importación desde s2526 + `pensum_id` opcional + edición.
 *
 * Replica `EiplanningwkImportTest` y los tests de `pensum_id`/edición del
 * semanal, adaptados a las tablas del quincenal (`eiplanningbwks`,
 * `eiplanningbwstrategies`, `eiplanningbwsummaries`).
 *
 * @group inicial
 * @group inicial-import
 */
class EiplanningbwkImportTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @param  array<int, int>  $asignaturas  asignaturas de sus pevaluaciones
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
            'lastname' => 'Bwk Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asignaturas = $asignaturas === []
            ? [Pensum::where('grado_id', $gradoId)->value('asignatura_id')]
            : $asignaturas;

        $pevaluacionId = null;
        $pensumId = null;

        foreach ($asignaturas as $asignaturaId) {
            $pensum = Pensum::where('grado_id', $gradoId)->where('asignatura_id', $asignaturaId)->firstOrFail();

            $pevaluacionId ??= DB::table('pevaluacions')->insertGetId([
                'profesor_id' => $profesorId,
                'pensum_id' => $pensum->id,
                'seccion_id' => $seccionId,
                'lapso_id' => Lapso::current()?->id ?? DB::table('lapsos')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $pensumId ??= $pensum->id;

            if ($pevaluacionId !== null && $pensumId !== (int) $pensum->id) {
                DB::table('pevaluacions')->insert([
                    'profesor_id' => $profesorId,
                    'pensum_id' => $pensum->id,
                    'seccion_id' => $seccionId,
                    'lapso_id' => Lapso::current()?->id ?? DB::table('lapsos')->value('id'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return [
            'user' => $user,
            'profesor_id' => $profesorId,
            'pevaluacion_id' => $pevaluacionId,
            'pensum_id' => $pensumId,
        ];
    }

    /** Primer plan legacy quincenal (id real de s2526, nunca hardcodeado). */
    private function primerPlanLegacy(): object
    {
        return DB::connection('s2526')->table('eiplanningbwks')->orderBy('id')->first();
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
    public function importar_crea_un_plan_quincenal_con_sus_resumenes_remapeados(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();

        // Las 3 áreas de sus resúmenes (asignaturas 235/237/239, sección 58).
        $docente = $this->makeDocenteConCarga(
            (int) $legacy->grado_id,
            (int) $legacy->seccion_id,
            [235, 237, 239]
        );

        $reporte = (new ImportadorEiplanningbwk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $reporte['creados']);
        $this->assertSame([], $reporte['omitidos']);

        $creado = $reporte['creados'][0];

        $this->assertNotSame((int) $legacy->id, $creado['nuevo_id']);

        $plan = Eiplanningbwk::find($creado['nuevo_id']);

        $this->assertSame($docente['profesor_id'], $plan->profesor_id);
        $this->assertNull($plan->eiprojectk_id, 'El proyecto del período viejo no se copia.');
        $this->assertSame(3, $creado['resumenes_ok']);
        $this->assertSame(0, $creado['resumenes_omitidos']);
        $this->assertSame(3, $plan->eiplanningbwsummaries()->count());
    }

    /** @test */
    public function importar_dos_veces_no_duplica_el_plan_quincenal(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $primero = (new ImportadorEiplanningbwk)->importar([$legacy->id], $docente['profesor_id']);
        $segundo = (new ImportadorEiplanningbwk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $primero['creados']);
        $this->assertSame([], $segundo['creados']);
        $this->assertCount(1, $segundo['omitidos']);
        $this->assertSame(1, Eiplanningbwk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_componente_abre_el_asistente_e_importa_lo_marcado(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openImport')
            ->assertSet('showImport', true)
            ->set('importSeleccionados', [$legacy->id])
            ->call('importarSeleccionados')
            ->assertHasNoErrors();

        $this->assertSame(1, Eiplanningbwk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function guarda_un_plan_con_el_area_de_aprendizaje_del_docente(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->set([
                'eiplanningbwk.grado_id' => $gradoId,
                'eiplanningbwk.seccion_id' => $seccionId,
                'eiplanningbwk.pensum_id' => $docente['pensum_id'],
                'eiplanningbwk.finicial' => '2026-10-05',
                'eiplanningbwk.ffinal' => '2026-10-19',
                'eiplanningbwk.tiempo_ejecucion' => 2,
                'eiplanningbwk.diagnostico' => 'El grupo se muestra interesado en las rutinas de conversación.',
            ])
            ->call('save');

        $component->assertHasNoErrors();

        $plan = Eiplanningbwk::where('profesor_id', $docente['profesor_id'])->first();

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
            ->test(EiplanningbwkComponent::class)
            ->set([
                'eiplanningbwk.grado_id' => $gradoId,
                'eiplanningbwk.seccion_id' => $seccionId,
                'eiplanningbwk.pensum_id' => $otroPensum,
                'eiplanningbwk.finicial' => '2026-10-05',
                'eiplanningbwk.ffinal' => '2026-10-19',
                'eiplanningbwk.tiempo_ejecucion' => 2,
                'eiplanningbwk.diagnostico' => 'El grupo se muestra interesado en las rutinas de conversación.',
            ])
            ->call('save');

        $component->assertHasErrors('eiplanningbwk.pensum_id');
        $this->assertSame(0, Eiplanningbwk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_boton_editar_aparece_en_modo_tarjetas_y_en_modo_tabla(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);

        Eiplanningbwk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-19',
            'tiempo_ejecucion' => 2,
            'diagnostico' => 'Diagnóstico del plan quincenal de prueba.',
        ]);

        $grid = Livewire::actingAs($docente['user'])->test(EiplanningbwkComponent::class);
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

        $plan = Eiplanningbwk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-19',
            'tiempo_ejecucion' => 2,
            'diagnostico' => 'Diagnóstico original del plan quincenal.',
        ]);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'edit', $plan->id);

        $this->assertSame($plan->diagnostico, $component->get('eiplanningbwk')['diagnostico']);

        $component
            ->set('eiplanningbwk.diagnostico', 'Diagnóstico actualizado del plan quincenal de prueba.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Diagnóstico actualizado del plan quincenal de prueba.', $plan->fresh()->diagnostico);
    }
}
