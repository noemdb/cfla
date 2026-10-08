<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EiplanningwkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\User;
use App\Services\Inicial\ImportadorEiplanningwk;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Asistente de importación de planificaciones semanales desde s2526.
 *
 * Blueprint: blueprint/inicial · importación por documento (sustituye a la
 * migración masiva F6 para este documento).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * QUÉ SE COMPRUEBA Y QUÉ NO
 * ─────────────────────────────────────────────────────────────────────────────
 * Se lee s2526 (319 planes reales) pero NUNCA se escribe en él: las escrituras
 * van a la BD actual dentro de `DatabaseTransactions`. El emparejamiento es
 * solo por (grado, sección) de la carga vigente, nunca por `profesor_id`.
 *
 * @group inicial
 * @group inicial-import
 */
class EiplanningwkImportTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array{user: User, profesor_id: int, pevaluacion_id: int}
     */
    private function makeDocenteConCarga(int $gradoId, int $seccionId): array
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
            'lastname' => 'Import Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pensum = Pensum::where('grado_id', $gradoId)->firstOrFail();

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'profesor_id' => $profesorId,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccionId,
            'lapso_id' => Lapso::current()?->id ?? DB::table('lapsos')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['user' => $user, 'profesor_id' => $profesorId, 'pevaluacion_id' => $pevaluacionId];
    }

    /** Primer plan legacy (id real más bajo de s2526, nunca hardcodeado). */
    private function primerPlanLegacy(): object
    {
        return DB::connection('s2526')->table('eiplanningwks')->orderBy('id')->first();
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
    public function los_candidatos_son_los_planes_del_mismo_grado_y_seccion_de_la_carga_vigente(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $ids = (new ImportadorEiplanningwk)->candidatos($docente['profesor_id'])
            ->pluck('id')->all();

        $this->assertContains(
            (int) $this->primerPlanLegacy()->id,
            $ids,
            'El primer plan legacy debe ofrecerse a un docente con su mismo grado/sección.'
        );

        // Ningún candidato puede ser de otro grado/sección.
        $fuera = DB::connection('s2526')->table('eiplanningwks')
            ->whereIn('id', $ids)
            ->where(fn ($q) => $q->where('grado_id', '!=', 23)->orWhere('seccion_id', '!=', 58))
            ->count();

        $this->assertSame(0, $fuera, 'Solo entra lo que coincide con la carga vigente.');
    }

    /** @test */
    public function importar_crea_un_plan_nuevo_con_sus_estrategias(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $esperadas = DB::connection('s2526')->table('eiplanningwstrategies')
            ->where('eiplanningwk_id', $legacy->id)->count();
        $this->assertGreaterThan(0, $esperadas);

        $reporte = (new ImportadorEiplanningwk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $reporte['creados']);
        $this->assertSame([], $reporte['omitidos']);

        $creado = $reporte['creados'][0];

        // ID NUEVO, nunca el del legacy.
        $this->assertNotSame((int) $legacy->id, $creado['nuevo_id']);
        $this->assertSame($esperadas, $creado['estrategias']);

        $plan = Eiplanningwk::find($creado['nuevo_id']);

        $this->assertSame($docente['profesor_id'], $plan->profesor_id);
        $this->assertSame((int) $legacy->grado_id, $plan->grado_id);
        $this->assertSame((int) $legacy->seccion_id, $plan->seccion_id);
        $this->assertNull($plan->eiprojectk_id, 'El proyecto del período viejo no se copia.');
        $this->assertSame($esperadas, $plan->eiplanningwstrategies()->count());
    }

    /**
     * Anti-duplicado: importar dos veces el mismo plan legacy NO crea dos
     * planes. La segunda vez se omite con motivo, porque la cabecera completa
     * (profesor, grado, sección, finicial, ffinal, diagnostico) ya existe.
     *
     * @test
     */
    public function importar_dos_veces_no_duplica_el_plan(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);
        $importador = new ImportadorEiplanningwk;

        $primero = $importador->importar([$legacy->id], $docente['profesor_id']);

        $this->assertCount(1, $primero['creados']);
        $this->assertSame([], $primero['omitidos']);

        $segundo = $importador->importar([$legacy->id], $docente['profesor_id']);

        $this->assertSame([], $segundo['creados']);
        $this->assertCount(1, $segundo['omitidos']);
        $this->assertStringContainsString('duplicado', strtolower($segundo['omitidos'][0]['motivo']));
        $this->assertSame(1, Eiplanningwk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function un_plan_fuera_de_la_carga_vigente_se_omite_sin_escribir(): void
    {
        $this->saltarSiNoHayLegacy();

        // Carga en el primer grado de Inicial: el primer plan legacy (de otro
        // grado) no encaja.
        $primerGrado = (int) Grado::where('pestudio_id', 6)->orderBy('id')->value('id');
        $legacy = $this->primerPlanLegacy();

        if ((int) $legacy->grado_id === $primerGrado) {
            $this->markTestSkipped('El primer plan legacy ya es del primer grado.');
        }

        $docente = $this->makeDocenteConCarga(
            (int) Grado::where('pestudio_id', 6)->orderBy('id')->value('id'),
            (int) DB::table('seccions')->where('grado_id', Grado::where('pestudio_id', 6)->orderBy('id')->value('id'))->where('status_active', 'true')->value('id')
        );

        $antes = Eiplanningwk::count();

        $reporte = (new ImportadorEiplanningwk)->importar([$legacy->id], $docente['profesor_id']);

        $this->assertSame([], $reporte['creados']);
        $this->assertCount(1, $reporte['omitidos']);
        $this->assertSame($antes, Eiplanningwk::count());
    }

    /** @test */
    public function el_resumen_se_ancla_a_la_pevaluacion_vigente_de_la_misma_asignatura_y_seccion(): void
    {
        $this->saltarSiNoHayLegacy();

        // Pevaluación legacy 814: sección 58, asignatura 235.
        $docente = $this->makeDocenteConCarga(23, 58);

        // La fixture usa el primer pensum del grado 23; se fuerza la misma
        // asignatura del resumen legacy para que haya equivalente vigente.
        $pensum235 = Pensum::where('grado_id', 23)->where('asignatura_id', 235)->first();

        if (! $pensum235) {
            $this->markTestSkipped('Sin pensum de asignatura 235 en grado 23.');
        }

        DB::table('pevaluacions')->where('id', $docente['pevaluacion_id'])->update(['pensum_id' => $pensum235->id]);

        $metodo = new \ReflectionMethod(ImportadorEiplanningwk::class, 'pevaluacionVigenteParaResumen');
        $metodo->setAccessible(true);

        $carga = ['pevaluacion_id' => $docente['pevaluacion_id'], 'grado_id' => 23, 'seccion_id' => 58, 'asignatura_id' => 235];

        $this->assertSame(
            $docente['pevaluacion_id'],
            $metodo->invoke(new ImportadorEiplanningwk, 814, $carga, $docente['profesor_id'])
        );

        // Pevaluación legacy inexistente → sin equivalente → null (se salta).
        $this->assertNull(
            $metodo->invoke(new ImportadorEiplanningwk, 999_999_999, $carga, $docente['profesor_id'])
        );
    }

    /** @test */
    public function importar_sin_seleccion_no_escribe_nada(): void
    {
        $this->saltarSiNoHayLegacy();

        $this->expectException(\InvalidArgumentException::class);

        (new ImportadorEiplanningwk)->importar([], 1);
    }

    /** @test */
    public function el_componente_abre_el_asistente_e_importa_lo_marcado(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openImport');

        $component->assertSet('showImport', true);
        $component->assertSee('Planes del período anterior');

        $component
            ->set('importSeleccionados', [$legacy->id])
            ->call('importarSeleccionados')
            ->assertHasNoErrors();

        $this->assertSame(1, Eiplanningwk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_asistente_filtra_pagina_y_alterna_la_vista(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openImport');

        $this->assertGreaterThan(0, $component->get('importCandidatos')->count());

        // Búsqueda que no coincide con nada → mensaje de vacío, sin error.
        $component->set('importSearch', 'zzz-sin-coincidencia');
        $component->assertSee('Ningún plan coincide con los filtros.');

        // Búsqueda por el grado del plan legacy → vuelve a aparecer.
        $grado = DB::connection('s2526')->table('grados')->where('id', $legacy->grado_id)->value('name');
        $component->set('importSearch', (string) $grado);
        $component->assertSee((string) $grado);

        // Alternar la vista cambia el modo y el marcado.
        $component->call('toggleImportView');
        $component->assertSet('importViewMode', 'table');
        $component->assertSee('Período');
        // La tabla trae el mismo detalle que las tarjetas: diagnóstico,
        // conteos separados y origen con texto completo en `title`.
        $component->assertSee('Diagnóstico');
        $component->assertSee('Estrategias');
        $component->assertSee('Resúmenes');
        $component->assertSee('Origen');
        $component->call('toggleImportView');
        $component->assertSet('importViewMode', 'grid');

        // La selección sobrevive al cambio de página.
        $component->set('importSeleccionados', [(int) $legacy->id]);
        $component->call('importPaginaSiguiente');
        $this->assertSame([(int) $legacy->id], $component->get('importSeleccionados'));

        // Filtrar resetea la paginación a la primera página.
        $component->call('importPaginaSiguiente');
        $component->set('importSearch', 'zzz-otro');
        $this->assertSame(1, $component->get('importPage'));
    }

    /** @test */
    public function el_asistente_ordena_muestra_detalle_e_importa_un_solo_plan(): void
    {
        $this->saltarSiNoHayLegacy();

        $legacy = $this->primerPlanLegacy();
        $docente = $this->makeDocenteConCarga((int) $legacy->grado_id, (int) $legacy->seccion_id);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openImport');

        // Ordenamiento por fecha y por grado.
        $component->assertSee('Fecha');
        $component->call('sortImport', 'grado');
        $component->assertSet('importSort', 'grado');
        $component->assertSet('importPage', 1);

        // Paginación numerada.
        $component->assertSee('Pág.');

        // Menú por tarjeta: ver detalle expande la información del plan.
        $component->call('verImportDetalle', (int) $legacy->id);
        $component->assertSet('importDetalleId', (int) $legacy->id);
        $component->assertSee('Ver detalle');
        $component->assertSee('se copian íntegras');

        // El botón visible "Detalle" de cada tarjeta hace lo mismo.
        $component->assertSee('Detalle');

        // Importar este plan: un solo plan nuevo, sin duplicar.
        $component->call('importarPlan', (int) $legacy->id);
        $component->assertHasNoErrors();

        $this->assertSame(1, Eiplanningwk::where('profesor_id', $docente['profesor_id'])->count());

        // Reimportar el mismo plan desde su tarjeta → omitido (anti-duplicado).
        $component->call('importarPlan', (int) $legacy->id);

        $this->assertSame(1, Eiplanningwk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_componente_exige_marcar_al_menos_un_plan(): void
    {
        $this->saltarSiNoHayLegacy();

        $docente = $this->makeDocenteConCarga(23, 58);
        $antes = Eiplanningwk::count();

        Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openImport')
            ->call('importarSeleccionados')
            ->assertHasErrors('importSeleccionados');

        $this->assertSame($antes, Eiplanningwk::count());
    }

    /**
     * Filtro por docente origen: se prueba con candidatos fabricados. Lo que
     * se verifica es la lógica del componente, no los datos.
     *
     * @test
     */
    public function el_asistente_filtra_por_docente_origen(): void
    {
        $this->saltarSiNoHayLegacy();

        $docente = $this->makeDocenteConCarga(23, 58);

        $candidatos = collect([
            ['id' => 1, 'profesor_origen_id' => 10, 'profesor_origen' => 'Perez Ana', 'grado' => '1ER GRUPO', 'seccion' => 'U', 'finicial' => '2025-09-01', 'ffinal' => '2025-09-05', 'diagnostico' => 'DIAG-ALFA', 'estrategias' => 5, 'resumenes' => 2, 'asignatura_ids' => [235], 'asignaturas' => ['Lengua']],
            ['id' => 2, 'profesor_origen_id' => 11, 'profesor_origen' => 'Gomez Luis', 'grado' => '1ER GRUPO', 'seccion' => 'U', 'finicial' => '2025-09-08', 'ffinal' => '2025-09-12', 'diagnostico' => 'DIAG-BETA', 'estrategias' => 3, 'resumenes' => 0, 'asignatura_ids' => [], 'asignaturas' => []],
            ['id' => 3, 'profesor_origen_id' => 10, 'profesor_origen' => 'Perez Ana', 'grado' => '2DO GRUPO', 'seccion' => 'U', 'finicial' => '2025-09-15', 'ffinal' => '2025-09-19', 'diagnostico' => 'DIAG-GAMMA', 'estrategias' => 0, 'resumenes' => 1, 'asignatura_ids' => [237], 'asignaturas' => ['Matemática']],
        ]);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openImport')
            ->set('importCandidatos', $candidatos);

        // Opciones de ambos selects salen de los candidatos.
        $component->assertSee('value="10"', false);

        // Filtro por docente origen (planes 1 y 3, no el 2).
        $component->set('importProfesor', '10');
        $component->assertSee('DIAG-ALFA');
        $component->assertSee('DIAG-GAMMA');
        $component->assertDontSee('DIAG-BETA');

        // El select de áreas se eliminó: solo quedan búsqueda, grado y docente.
        $component->assertDontSee('Todas las áreas');
    }
}
