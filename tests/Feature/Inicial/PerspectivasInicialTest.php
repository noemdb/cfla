<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Evaluacion\Inicial\EievaluationkComponent;
use App\Livewire\Evaluacion\Inicial\EifinalkComponent;
use App\Livewire\Evaluacion\Inicial\EiplanningwkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Learner\Estudiant;
use App\Models\User;
use App\Services\Inicial\EducationStatsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * F5 del módulo de Educación Inicial: las TRES perspectivas de revisión.
 *
 * Blueprint: blueprint/inicial · F5 · doc 07.
 *
 * Las tres comparten los mismos datos y hasta las mismas vistas de detalle y
 * formato; lo que las diferencia es el alcance:
 *
 *  · Evaluación  (`isDiagnostic`): 6 pestañas, ÚNICA que escribe
 *                 `observacion` / `recomendacion` (min:5) y con indicadores.
 *  · Planificación (`isPlanner`): 5 documentos, solo lectura, sin Livewire.
 *  · Académico   (`isAdmin`): 2 documentos, solo lectura, resto sin ruta.
 *
 * @group inicial
 * @group inicial-f5
 */
class PerspectivasInicialTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Fixtures ─────────────────────────────────────────────────

    /**
     * @return array{user: User, profesor_id: int, pevaluacion_id: int, seccion_id: int, grado_id: int}
     */
    private function makeDocente(bool $isAdmin = true): array
    {
        $attributes = ['is_profesor' => true, 'is_admin' => $isAdmin];

        if (Schema::hasColumn('users', 'is_inicial')) {
            $attributes['is_inicial'] = ! $isAdmin;
        }

        $user = User::factory()->create($attributes);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-'.random_int(10_000_000, 99_999_999),
            'ci_profesor' => (string) random_int(10_000_000, 99_999_999),
            'name' => 'Docente',
            'lastname' => 'Perspectiva Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $seccionId = $this->seccionActiva($gradoId);
        $lapsoId = (int) DB::table('lapsos')->value('id');

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'profesor_id' => $profesorId,
            'pensum_id' => Pensum::where('grado_id', $gradoId)->value('id'),
            'seccion_id' => $seccionId,
            'lapso_id' => $lapsoId,
            'status_official' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'user' => $user,
            'profesor_id' => $profesorId,
            'pevaluacion_id' => $pevaluacionId,
            'seccion_id' => $seccionId,
            'grado_id' => $gradoId,
        ];
    }

    /** Usuario con un flag de perspectiva concreto y sin los de Inicial. */
    private function makeUsuario(string $flag): User
    {
        $attributes = ['is_profesor' => true, 'is_admin' => false, $flag => true];

        if (Schema::hasColumn('users', 'is_inicial')) {
            $attributes['is_inicial'] = false;
        }

        return User::factory()->create($attributes);
    }

    private function seccionActiva(int $gradoId): int
    {
        return (int) DB::table('seccions')
            ->where('grado_id', $gradoId)
            ->where('status_active', 'true')
            ->value('id');
    }

    /** @return array<string, mixed> */
    private function planAttributes(int $profesorId, int $gradoId, int $seccionId): array
    {
        return [
            'profesor_id' => $profesorId,
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'finicial' => now()->subDays(10),
            'ffinal' => now()->subDay(),
            'tiempo_ejecucion' => 60,
            'diagnostico' => 'El grupo llega con buencomedimiento.',
            'observacion' => null,
        ];
    }

    private function makeEstudiante(int $seccionId, string $nombre = 'Estudiante'): Estudiant
    {
        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Plan '.uniqid(),
            'description' => 'Fixture',
            'observations' => 'Fixture',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $id = DB::table((new Estudiant)->getTable())->insertGetId([
            'ci_estudiant' => (string) random_int(10_000_000, 99_999_999),
            'planpago_id' => $planpagoId,
            'representant_id' => (int) DB::table('representants')->min('id'),
            'lastname' => 'Apellido',
            'name' => $nombre,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('inscripcions')->insert([
            'tipo_id' => DB::table('tinscripcions')->value('id'),
            'seccion_id' => $seccionId,
            'estudiant_id' => $id,
            'programacion_id' => (int) DB::table('programacions')->min('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Estudiant::find($id);
    }

    private function saltarSiFaltaLaMigracion(): void
    {
        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped('Requiere la columna users.is_inicial.');
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  EVALUACIÓN · acceso y escritura
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function la_perspectiva_de_evaluacion_exige_is_diagnostic(): void
    {
        $this->actingAs($this->makeUsuario('is_planner'))
            ->get(route('evaluacions.inicials.index'))
            ->assertForbidden();

        $this->actingAs($this->makeUsuario('is_admin'))
            ->get(route('evaluacions.inicials.index'))
            ->assertForbidden();
    }

    /** @test */
    public function el_coordinador_escribe_la_observacion_del_plan(): void
    {
        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EiplanningwkComponent::class)
            ->call('openRevision', $plan->id)
            ->set('revision', 'Ajustar el tiempo de las actividades ludicas.')
            ->call('saveRevision')
            ->assertHasNoErrors();

        $this->assertSame(
            'Ajustar el tiempo de las actividades ludicas.',
            $plan->fresh()->observacion
        );
    }

    /** @test */
    public function la_observacion_exige_al_menos_cinco_caracteres(): void
    {
        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EiplanningwkComponent::class)
            ->call('openRevision', $plan->id)
            ->set('revision', 'ok')
            ->call('saveRevision')
            ->assertHasErrors('revision');

        $this->assertNull($plan->fresh()->observacion, 'Una revisión inválida no debe escribirse.');
    }

    /** @test */
    public function el_plan_de_evaluacion_se_revisa_con_recomendacion_no_con_observacion(): void
    {
        $docente = $this->makeDocente();

        $plan = Eievaluationk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $docente['grado_id'],
            'seccion_id' => $docente['seccion_id'],
            'lapso_id' => (int) DB::table('lapsos')->value('id'),
            'finicial' => now()->subDays(5),
            'ffinal' => now(),
            'recomendacion' => null,
        ]);

        Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EievaluationkComponent::class)
            ->call('openRevision', $plan->id)
            ->set('revision', 'Mantener el ritmo de lectura en casa.')
            ->call('saveRevision')
            ->assertHasNoErrors();

        $this->assertSame('Mantener el ritmo de lectura en casa.', $plan->fresh()->recomendacion);
    }

    /** @test */
    public function el_informe_final_no_admite_revision_de_la_coordinacion(): void
    {
        $docente = $this->makeDocente();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);

        $informe = Eifinalk::create([
            'order' => 1,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'estudiant_id' => $estudiante->id,
            'title' => 'Informe final del primer momento',
        ]);

        // No hay columna de revisión: la acción debe fallar, no escribir en una
        // columna inexistente.
        Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EifinalkComponent::class)
            ->call('openRevision', $informe->id)
            ->assertStatus(404);

        Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EifinalkComponent::class)
            ->call('saveRevision')
            ->assertStatus(404);
    }

    /** @test */
    public function un_documento_inexistente_no_se_puede_revisar(): void
    {
        Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EiplanningwkComponent::class)
            ->call('openRevision', 999_999_999)
            ->assertStatus(404);
    }

    // ═══════════════════════════════════════════════════════════════
    //  EVALUACIÓN · filtros
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function el_indice_de_evaluacion_carga_con_los_filtros_del_query_string(): void
    {
        // Los filtros son GET: se aplican al pedir la página, no después. Aquí se
        // comprueba que el índice los acepta y devuelve 200 con los desplegables
        // ya marcados (el filtrado de datos se comprueba sobre el componente,
        // en el test siguiente).
        $docente = $this->makeDocente();

        $this->actingAs($this->makeUsuario('is_diagnostic'))
            ->get(route('evaluacions.inicials.index', [
                'profesor_id' => $docente['profesor_id'],
                'grado_id' => $docente['grado_id'],
                'seccion_id' => $docente['seccion_id'],
            ]))
            ->assertOk()
            ->assertSee('Indicadores de planificación')
            ->assertSee('value="'.$docente['grado_id'].'" selected', false);
    }

    /** @test */
    public function el_componente_de_evaluacion_solo_muestra_los_documentos_del_profesor_filtrado(): void
    {
        $docente = $this->makeDocente();
        $otro = $this->makeDocente();

        $propio = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        Eiplanningwk::create($this->planAttributes(
            $otro['profesor_id'],
            $otro['grado_id'],
            $otro['seccion_id']
        ));

        $ids = Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EiplanningwkComponent::class, ['profesorId' => $docente['profesor_id']])
            ->viewData('items')
            ->pluck('id')
            ->all();

        $this->assertSame([$propio->id], $ids);
    }

    /** @test */
    public function el_informe_final_se_filtra_por_profesor_a_traves_de_la_carga(): void
    {
        $docente = $this->makeDocente();
        $otro = $this->makeDocente();

        $propio = Eifinalk::create([
            'order' => 1,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'estudiant_id' => $this->makeEstudiante($docente['seccion_id'])->id,
            'title' => 'Informe propio',
        ]);

        Eifinalk::create([
            'order' => 1,
            'pevaluacion_id' => $otro['pevaluacion_id'],
            'estudiant_id' => $this->makeEstudiante($otro['seccion_id'])->id,
            'title' => 'Informe ajeno',
        ]);

        $ids = Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EifinalkComponent::class, ['profesorId' => $docente['profesor_id']])
            ->viewData('items')
            ->pluck('id')
            ->all();

        $this->assertSame([$propio->id], $ids);
    }

    /**
     * El `groupBy` del legacy devolvía UNA fila arbitraria por estudiante y
     * perdía el resto de sus informes. Aquí se agrupa la colección completa.
     *
     * @test
     */
    public function el_listado_de_informes_finales_agrupa_por_estudiante_sin_perder_informes(): void
    {
        $docente = $this->makeDocente();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);

        // Dos informes del MISMO estudiante, con distinto `order`.
        Eifinalk::create([
            'order' => 2,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'estudiant_id' => $estudiante->id,
            'title' => 'Informe segundo',
        ]);

        Eifinalk::create([
            'order' => 1,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'estudiant_id' => $estudiante->id,
            'title' => 'Informe primero',
        ]);

        $items = Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EifinalkComponent::class, ['profesorId' => $docente['profesor_id']])
            ->viewData('items');

        // Un renglón por estudiante, no uno por informe…
        $this->assertCount(1, $items);

        // …y se muestra el de MENOR `order`, que es el primero que se imprime.
        $this->assertSame('Informe primero', $items->first()->title);

        // El total del estudiante conserva el dato de los 2 informes, en lugar
        // del `count()` por fila que tenía el legacy.
        $this->assertSame(2, (int) $items->first()->informes_total);
    }

    /** @test */
    public function el_plan_de_evaluacion_se_filtra_por_lapso(): void
    {
        $docente = $this->makeDocente();
        $lapsoId = (int) DB::table('lapsos')->value('id');

        $enOtroLapso = Eievaluationk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $docente['grado_id'],
            'seccion_id' => $docente['seccion_id'],
            'lapso_id' => $lapsoId,
            'finicial' => now(),
            'recomendacion' => null,
        ]);

        $ids = Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EievaluationkComponent::class, [
                'profesorId' => $docente['profesor_id'],
                'lapsoId' => $lapsoId,
            ])
            ->viewData('items')
            ->pluck('id')
            ->all();

        $this->assertSame([$enOtroLapso->id], $ids);

        // Sin el filtro de lapso aparece igual; el filtro no lo esconde.
        $this->assertCount(1, Livewire::actingAs($this->makeUsuario('is_diagnostic'))
            ->test(EievaluationkComponent::class, ['profesorId' => $docente['profesor_id']])
            ->viewData('items'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  EVALUACIÓN · indicadores
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function los_indicadores_cuentan_los_documentos_del_filtro(): void
    {
        $docente = $this->makeDocente();

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        $stats = (new EducationStatsService)->getEducationStats($docente['profesor_id']);

        $this->assertSame(1, $stats['eiplanningwks']);
        $this->assertSame(1, $stats['totalRecords']);
        $this->assertSame(0, $stats['eiprojectks']);
    }

    /** @test */
    public function los_indicadores_no_inventan_porcentajes_sin_maximo_configurado(): void
    {
        $indicadores = (new EducationStatsService)->indicadores();

        // Las columnas `peducativos.max_number_*` no existen: el máximo es
        // `null` y el porcentaje también, para que la vista muestre el dato
        // absoluto en lugar de una barra contra un techo inventado.
        $this->assertNull($indicadores[0]['maximo']);
        $this->assertNull($indicadores[0]['porcentaje']);
        $this->assertArrayHasKey('conteo', $indicadores[0]);
    }

    /** @test */
    public function el_indice_muestra_los_indicadores_sin_badges_inventados(): void
    {
        $html = $this->actingAs($this->makeUsuario('is_diagnostic'))
            ->get(route('evaluacions.inicials.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Indicadores de planificación', $html);

        // Los badges del legacy (+12 %, +5, 67 %) eran dato falso.
        $this->assertStringNotContainsString('+12%', $html);
        $this->assertStringNotContainsString('+12 %', $html);
        $this->assertStringNotContainsString('67%', $html);
    }

    // ═══════════════════════════════════════════════════════════════
    //  PLANIFICACIÓN · solo lectura
    // ═══════════════════════════════════════════════════════════════

    /**
     * `IsPlanner` es middleware PREEXISTENTE de cfla y es más amplio que
     * `is_planner`: deja entrar también a `is_admin` e `is_diagnostic`. Esa
     * decisión no es del módulo, así que el test fija el comportamiento real
     * (y lo documenta) en vez de el que convendría.
     *
     * @test
     */
    public function planificacion_exige_alguno_de_los_flags_de_planificacion(): void
    {
        $this->actingAs($this->makeUsuario('is_planner'))
            ->get(route('plannings.inicials.index'))
            ->assertOk();

        // Sin ninguno de los tres, fuera.
        $this->actingAs(User::factory()->create(['is_profesor' => false, 'is_admin' => false]))
            ->get(route('plannings.inicials.index'))
            ->assertForbidden();
    }

    /** @test */
    public function planificacion_muestra_los_documentos_sin_acciones_de_escritura(): void
    {
        $docente = $this->makeDocente();

        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        $this->actingAs($this->makeUsuario('is_planner'))
            ->get(route('plannings.inicials.index', ['pestana' => 'eiplanningwks']))
            ->assertOk()
            ->assertSee($plan->diagnostico)
            // Sin textarea ni botón de escritura: es de consulta.
            ->assertDontSee('Guardar')
            ->assertDontSee('Gestionar observación');
    }

    /** @test */
    public function planificacion_enlaza_el_pdf_de_cada_documento_a_su_propio_formato(): void
    {
        $docente = $this->makeDocente();

        $plan = \App\Models\app\Inicial\Eiplanningbwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        // El legacy tenía aquí un error de copia: el botón del quincenal
        // apuntaba al formato SEMANAL.
        $this->actingAs($this->makeUsuario('is_planner'))
            ->get(route('plannings.inicials.index', ['pestana' => 'eiplanningbwks']))
            ->assertOk()
            ->assertSee(route('plannings.inicials.eiplanningbwks.format', $plan->id), false)
            ->assertDontSee(route('plannings.inicials.eiplanningwks.format', $plan->id), false);
    }

    /** @test */
    public function planificacion_solo_admite_una_pestana_del_modulo(): void
    {
        // `eifinalks` no es una pestaña de Planificación.
        $this->actingAs($this->makeUsuario('is_planner'))
            ->get(route('plannings.inicials.index', ['pestana' => 'eifinalks']))
            ->assertOk()
            ->assertSee('Planificación semanal');
    }

    // ═══════════════════════════════════════════════════════════════
    //  ACADÉMICO · alcance limitado
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function la_perspectiva_academica_exige_is_admin(): void
    {
        $this->actingAs($this->makeUsuario('is_planner'))
            ->get(route('academicos.inicials.index'))
            ->assertForbidden();
    }

    /** @test */
    public function la_perspectiva_academica_solo_muestra_semanal_y_proyecto(): void
    {
        $docente = $this->makeDocente();

        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        $this->actingAs($this->makeUsuario('is_admin'))
            ->get(route('academicos.inicials.index'))
            ->assertOk()
            ->assertSee($plan->diagnostico)
            // Las cuatro pestañas sin acceso, con el motivo en vez del
            // placeholder «Content N» del legacy.
            ->assertSee('Planificación semanal')
            ->assertSee('Proyecto de aula')
            ->assertSee('se revisan en Planificación')
            ->assertSee('se revisan en Coordinación de Evaluación');
    }

    /** @test */
    public function la_perspectiva_academica_no_tiene_ruta_para_los_cuatro_documentos_restantes(): void
    {
        $docente = $this->makeDocente();

        $especial = \App\Models\app\Inicial\Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ) + ['justificacion' => ' Necesita apoyo con el lenguaje.']);

        // No es una ruta de Directoría: el detalle del plan especial NO existe
        // en esta perspectiva.
        $this->actingAs($this->makeUsuario('is_admin'))
            ->get(route('academicos.inicials.index').'/eispecialks/'.$especial->id)
            ->assertNotFound();
    }

    // ═══════════════════════════════════════════════════════════════
    //  DETALLE Y FORMATO COMPARTIDOS
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function el_detalle_es_el_mismo_en_las_tres_perspectivas(): void
    {
        $docente = $this->makeDocente();

        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        foreach ([
            ['evaluacions.inicials', 'is_diagnostic'],
            ['plannings.inicials', 'is_planner'],
            ['academicos.inicials', 'is_admin'],
        ] as [$prefijo, $flag]) {
            $this->actingAs($this->makeUsuario($flag))
                ->get(route($prefijo.'.eiplanningwks.show', $plan->id))
                ->assertOk()
                ->assertSee($plan->diagnostico)
                // Es de SOLO LECTURA en las tres: ni el textarea de Evaluación
                // ni botones de edición.
                ->assertDontSee('wire:model.live="eiplanningwk.diagnostico"');
        }
    }

    /** @test */
    public function el_formato_imprimible_lo_sirven_las_tres_perspectivas(): void
    {
        $docente = $this->makeDocente();

        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        foreach ([
            ['evaluacions.inicials', 'is_diagnostic'],
            ['plannings.inicials', 'is_planner'],
            ['academicos.inicials', 'is_admin'],
        ] as [$prefijo, $flag]) {
            $this->actingAs($this->makeUsuario($flag))
                ->get(route($prefijo.'.eiplanningwks.format', $plan->id))
                ->assertOk()
                ->assertSee($plan->diagnostico);
        }
    }

    /** @test */
    public function una_entidad_desconocida_no_revela_que_documentos_existen(): void
    {
        // 404 y no «documento desconocido»: la ruta no debe servir de catálogo.
        $this->actingAs($this->makeUsuario('is_planner'))
            ->get(route('plannings.inicials.index').'/einventados/1')
            ->assertNotFound();
    }

    /** @test */
    public function un_documento_inexistente_da_404_en_el_detalle(): void
    {
        $this->actingAs($this->makeUsuario('is_planner'))
            ->get(route('plannings.inicials.eiplanningwks.show', 999_999_999))
            ->assertNotFound();
    }
}
