<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EievaluationkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\User;
use App\Services\Inicial\ImportadorEievaluationk;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Clon de bwk: importación de planes de evaluación desde s2526 + `pensum_id`
 * opcional + edición en ambos modos.
 *
 * Particularidades del documento: la cabecera trae `lapso_id` (el importado se
 * ancla al lapso en curso, no se copia el viejo) y las hijas son POSICIONES
 * (`eievaluationps`) a remapear. No hay estrategias ni revisiones.
 *
 * @group inicial
 * @group inicial-import
 */
class EievaluationkImportTest extends TestCase
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
            'lastname' => 'Evaluation Import Test',
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

    /** Primer plan legacy con posiciones (id real de s2526, nunca hardcodeado). */
    private function primerPlanLegacy(): object
    {
        $id = DB::connection('s2526')->table('eievaluationks as k')
            ->select('k.id')
            ->selectRaw('(select count(*) from eievaluationps p where p.eievaluationk_id=k.id) as n')
            ->orderBy('k.id')
            ->get()
            ->first(fn ($r) => (int) $r->n > 0)?->id
            ?? DB::connection('s2526')->table('eievaluationks')->orderBy('id')->value('id');

        return DB::connection('s2526')->table('eievaluationks')->where('id', $id)->first();
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
    public function importar_crea_un_plan_con_sus_posiciones_remapeadas(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();

        // Asignaturas de sus posiciones, para que el remapeo tenga equivalente.
        $asignaturas = DB::connection('s2526')->table('eievaluationps as p')
            ->select('pens.asignatura_id')
            ->join('pevaluacions as pev', 'pev.id', '=', 'p.pevaluacion_id')
            ->join('pensums as pens', 'pens.id', '=', 'pev.pensum_id')
            ->where('p.eievaluationk_id', $legacy->id)
            ->distinct()
            ->pluck('asignatura_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $docente = $this->makeDocenteConCarga(
            (int) $legacy->grado_id,
            (int) $legacy->seccion_id,
            $asignaturas
        );

        $reporte = (new ImportadorEievaluationk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $reporte['creados']);
        $this->assertSame([], $reporte['omitidos']);

        $creado = $reporte['creados'][0];

        $this->assertNotSame((int) $legacy->id, $creado['nuevo_id']);

        $plan = Eievaluationk::find($creado['nuevo_id']);

        $this->assertSame($docente['profesor_id'], $plan->profesor_id);
        // El lapso viejo no se copia: nace en el lapso en curso.
        $this->assertSame(Lapso::current()?->id ?? $plan->lapso_id, (int) $plan->lapso_id);
        $this->assertSame(
            DB::connection('s2526')->table('eievaluationps')->where('eievaluationk_id', $legacy->id)->count(),
            $creado['resumenes_ok']
        );
        $this->assertSame(0, $creado['resumenes_omitidos']);
    }

    /** @test */
    public function importar_dos_veces_no_duplica_el_plan_de_evaluacion(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $primero = (new ImportadorEievaluationk)->importar([$legacy->id], $docente['profesor_id']);
        $segundo = (new ImportadorEievaluationk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $primero['creados']);
        $this->assertSame([], $segundo['creados']);
        $this->assertCount(1, $segundo['omitidos']);
        $this->assertSame(1, Eievaluationk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_componente_abre_el_asistente_e_importa_lo_marcado(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('openImport')
            ->assertSet('showImport', true)
            ->set('importSeleccionados', [$legacy->id])
            ->call('importarSeleccionados')
            ->assertHasNoErrors();

        $this->assertSame(1, Eievaluationk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function guarda_un_plan_con_el_area_de_aprendizaje_del_docente(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);
        $lapsoId = Lapso::current()?->id ?? DB::table('lapsos')->value('id');

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->set([
                'eievaluationk.grado_id' => $gradoId,
                'eievaluationk.lapso_id' => $lapsoId,
                'eievaluationk.seccion_id' => $seccionId,
                'eievaluationk.pensum_id' => $docente['pensum_id'],
                'eievaluationk.finicial' => '2026-10-01',
                'eievaluationk.ffinal' => '2026-10-31',
                'eievaluationk.observaciones' => 'Se evaluó la comprensión lectora mediante tres actividades.',
                'eievaluationk.asistencia' => 'El grupo mantiene una asistencia del 92 %.',
            ])
            ->call('save');

        $component->assertHasNoErrors();

        $plan = Eievaluationk::where('profesor_id', $docente['profesor_id'])->first();

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
        $lapsoId = Lapso::current()?->id ?? DB::table('lapsos')->value('id');

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->set([
                'eievaluationk.grado_id' => $gradoId,
                'eievaluationk.lapso_id' => $lapsoId,
                'eievaluationk.seccion_id' => $seccionId,
                'eievaluationk.pensum_id' => $otroPensum,
                'eievaluationk.finicial' => '2026-10-01',
                'eievaluationk.ffinal' => '2026-10-31',
                'eievaluationk.observaciones' => 'Se evaluó la comprensión lectora mediante tres actividades.',
                'eievaluationk.asistencia' => 'El grupo mantiene una asistencia del 92 %.',
            ])
            ->call('save');

        $component->assertHasErrors('eievaluationk.pensum_id');
        $this->assertSame(0, Eievaluationk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_filtro_de_area_acota_el_listado_al_pensum_elegido(): void
    {
        $this->saltarSiNoHayLegacy();

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = (int) DB::table('seccions')->where('grado_id', $gradoId)->where('status_active', 'true')->value('id');
        $docente = $this->makeDocenteConCarga($gradoId, $seccionId);
        $lapsoId = Lapso::current()?->id ?? DB::table('lapsos')->value('id');

        $conArea = Eievaluationk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'pensum_id' => $docente['pensum_id'],
            'finicial' => '2026-10-01',
            'ffinal' => '2026-10-31',
            'observaciones' => 'Observaciones del plan con área vinculada.',
            'asistencia' => 'Asistencia del 92 %.',
        ]);

        Eievaluationk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'finicial' => '2026-10-01',
            'ffinal' => '2026-10-31',
            'observaciones' => 'Observaciones del plan sin área vinculada.',
            'asistencia' => 'Asistencia del 90 %.',
        ]);

        $ids = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->set('filterPensum', $docente['pensum_id'])
            ->viewData('eievaluationks')
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

        Eievaluationk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'lapso_id' => Lapso::current()?->id ?? DB::table('lapsos')->value('id'),
            'seccion_id' => $seccionId,
            'finicial' => '2026-10-01',
            'ffinal' => '2026-10-31',
            'observaciones' => 'Observaciones del plan de prueba.',
            'asistencia' => 'Asistencia del 92 %.',
        ]);

        $grid = Livewire::actingAs($docente['user'])->test(EievaluationkComponent::class);
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

        $plan = Eievaluationk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'lapso_id' => Lapso::current()?->id ?? DB::table('lapsos')->value('id'),
            'seccion_id' => $seccionId,
            'finicial' => '2026-10-01',
            'ffinal' => '2026-10-31',
            'observaciones' => 'Observaciones originales del plan.',
            'asistencia' => 'Asistencia del 92 %.',
        ]);

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'edit', $plan->id);

        $this->assertSame($plan->observaciones, $component->get('eievaluationk')['observaciones']);

        $component
            ->set('eievaluationk.observaciones', 'Observaciones actualizadas del plan de evaluación de prueba.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Observaciones actualizadas del plan de evaluación de prueba.', $plan->fresh()->observaciones);
    }
}
