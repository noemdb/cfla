<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EiplanningbwkComponent;
use App\Livewire\Inicial\EispecialkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eispecialact;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Inicial\Eispecialstrategy;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * F3 del módulo de Educación Inicial: CRUD del PLAN ESPECIAL.
 *
 * Blueprint: blueprint/inicial · F3 (CRUD docente resto).
 *
 * Cubre lo que NO existe en la semanal ni en la quincenal:
 *  · la cabecera justifica con `justificacion`, no con `diagnostico`;
 *  · no hay columna `eiprojectk_id` (el plan especial no se subordina a nada);
 *  · las hijas se llaman **actividades** (`eispecialacts`) y no tienen columna
 *    `estrategias` (esa solo existe en el proyecto de aula).
 *
 * @group inicial
 * @group inicial-f3
 */
class EispecialkComponentTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Fixtures ─────────────────────────────────────────────────

    /**
     * @return array{user: User, profesor_id: int, pevaluacion_id: int}
     */
    private function makeDocenteInicial(bool $isAdmin = true): array
    {
        $attributes = [
            'is_admin' => $isAdmin,
            'is_profesor' => true,
        ];

        if (Schema::hasColumn('users', 'is_inicial')) {
            $attributes['is_inicial'] = ! $isAdmin;
        }

        $user = User::factory()->create($attributes);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-99999999',
            'ci_profesor' => '99999999',
            'name' => 'Docente',
            'lastname' => 'Especial Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = $this->gradoInicialId();
        $pensum = Pensum::where('grado_id', $gradoId)->firstOrFail();

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'profesor_id' => $profesorId,
            'pensum_id' => $pensum->id,
            'seccion_id' => $this->seccionDe($gradoId),
            'lapso_id' => DB::table('lapsos')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'user' => $user,
            'profesor_id' => $profesorId,
            'pevaluacion_id' => $pevaluacionId,
        ];
    }

    private function gradoInicialId(): int
    {
        return (int) Grado::where('pestudio_id', 6)->value('id');
    }

    private function seccionDe(int $gradoId): int
    {
        return (int) DB::table('seccions')
            ->where('grado_id', $gradoId)
            ->where('status_active', 'true')
            ->value('id');
    }

    /**
     * Atributos válidos de un plan especial, con las CLAVES PLANAS de la tabla.
     *
     * @return array<string, mixed>
     */
    private function planAttributes(): array
    {
        $gradoId = $this->gradoInicialId();

        return [
            'grado_id' => $gradoId,
            'seccion_id' => $this->seccionDe($gradoId),
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-23',
            'tiempo_ejecucion' => 3,
            'justificacion' => 'El grupo necesita ampliar la mirada sobre su entorno cotidiano.',
            'observacion' => 'Se valora la participación del grupo.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function planForm(): array
    {
        return collect($this->planAttributes())
            ->mapWithKeys(fn ($value, $key) => ['eispecialk.'.$key => $value])
            ->all();
    }

    private function makePlan(int $profesorId): Eispecialk
    {
        return Eispecialk::create(
            array_merge($this->planAttributes(), ['profesor_id' => $profesorId])
        );
    }

    private function saltarSiFaltaLaMigracion(): void
    {
        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped(
                'Requiere la columna users.is_inicial (migración add_is_inicial_to_users_table pendiente).'
            );
        }
    }

    // ─── Cabecera ─────────────────────────────────────────────────

    /** @test */
    public function el_listado_muestra_solo_los_planes_del_docente(): void
    {
        $docente = $this->makeDocenteInicial();
        $otro = $this->makeDocenteInicial();

        $propio = $this->makePlan($docente['profesor_id']);
        $ajeno = $this->makePlan($otro['profesor_id']);

        $ids = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->viewData('eispecialks')
            ->pluck('id')
            ->all();

        $this->assertContains($propio->id, $ids);
        $this->assertNotContains($ajeno->id, $ids);
    }

    /** @test */
    public function guarda_un_plan_especial_con_la_justificacion(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->set($this->planForm())
            ->call('save');

        $component->assertHasNoErrors();

        $plan = Eispecialk::where('profesor_id', $docente['profesor_id'])->first();

        $this->assertNotNull($plan, 'El plan no se guardó.');
        $this->assertSame(
            'El grupo necesita ampliar la mirada sobre su entorno cotidiano.',
            $plan->justificacion
        );
        $this->assertSame(3, (int) $plan->tiempo_ejecucion);
        $this->assertFalse($component->get('showModal'));
    }

    /** @test */
    public function exige_una_justificacion_minima_de_10_caracteres(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->set($this->planForm())
            ->set('eispecialk.justificacion', 'corta')
            ->call('save');

        // R1: el runtime legacy era min:10, no min:50.
        $component->assertHasErrors('eispecialk.justificacion');

        $this->assertSame(0, Eispecialk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function rechaza_una_fecha_de_culminacion_anterior_a_la_de_inicio(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->set($this->planForm())
            ->set('eispecialk.ffinal', '2026-10-01')
            ->call('save');

        $component->assertHasErrors('eispecialk.ffinal');
    }

    /** @test */
    public function un_plan_no_puede_abrirse_ni_borrarse_desde_otro_docente(): void
    {
        $dueno = $this->makeDocenteInicial();
        $invasor = $this->makeDocenteInicial();

        $plan = $this->makePlan($dueno['profesor_id']);

        Livewire::actingAs($invasor['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->assertStatus(404);

        Livewire::actingAs($invasor['user'])
            ->test(EispecialkComponent::class)
            ->call('deletePlan', $plan->id)
            ->assertStatus(404);

        $this->assertNotNull(Eispecialk::find($plan->id));
    }

    // ─── Actividades ──────────────────────────────────────────────

    /** @test */
    public function guarda_una_actividad_con_los_cuatro_campos_obligatorios(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'activity', $plan->id)
            ->set('eispecialact.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('eispecialact.componente', 'Ciencia social')
            ->set('eispecialact.objetivo', 'Reconoce el entorno como fuente de información.')
            ->set('eispecialact.aprendizaje_esperado', 'Relaciona elementos de su barrio con su función.')
            ->set('eispecialact.indicadores', '1. Observa. 2. Relaciona.')
            ->call('saveActivity');

        $component->assertHasNoErrors();

        $actividad = Eispecialact::where('eispecialk_id', $plan->id)->first();

        $this->assertNotNull($actividad);
        $this->assertSame('Ciencia social', $actividad->componente);
    }

    /** @test */
    public function la_actividad_exige_los_cuatro_campos_de_informacion_general(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'activity', $plan->id)
            ->set('eispecialact.pevaluacion_id', $docente['pevaluacion_id'])
            ->call('saveActivity');

        // R5.
        $component->assertHasErrors([
            'eispecialact.componente',
            'eispecialact.objetivo',
            'eispecialact.aprendizaje_esperado',
            'eispecialact.indicadores',
        ]);

        $this->assertSame(0, Eispecialact::where('eispecialk_id', $plan->id)->count());
    }

    /** @test */
    public function la_actividad_de_otro_docente_no_puede_abrirse(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $invasor = $this->makeDocenteInicial(isAdmin: false);

        $actividadAjena = Eispecialact::create([
            'eispecialk_id' => $this->makePlan($dueno['profesor_id'])->id,
            'pevaluacion_id' => $dueno['pevaluacion_id'],
            'componente' => 'Matemática',
            'objetivo' => 'Cuenta elementos.',
            'aprendizaje_esperado' => 'Cuenta hasta cinco.',
            'indicadores' => '1. Cuenta.',
        ]);

        Livewire::actingAs($invasor['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'edit-activity', $actividadAjena->id)
            ->assertStatus(404);
    }

    /** @test */
    public function el_modal_de_alta_muestra_el_boton_de_guardar_actividad(): void
    {
        // REGRESIÓN de UI: el gestor muestra lista + formulario de alta, pero el
        // botón de guardar solo se pintaba en `edit-activity`. El docente veía el
        // formulario de alta y no tenía dónde pulsarlo: la primera actividad no
        // se podía crear. Las tests que llamaban a `saveActivity()` directamente
        // no lo detectaban.
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'activity', $plan->id)
            ->assertSee('Guardar actividad');
    }

    /** @test */
    public function el_boton_de_guardar_resumen_esta_disponible_al_crear_en_los_otros_documentos(): void
    {
        // El mismo hueco existía en la semanal y la quincenal (y en el
        // proyecto para resumen y revisión).
        $docente = $this->makeDocenteInicial();

        $planSemanal = \App\Models\app\Inicial\Eiplanningwk::create(array_merge(
            [
                'profesor_id' => $docente['profesor_id'],
                'grado_id' => $this->gradoInicialId(),
                'seccion_id' => $this->seccionDe($this->gradoInicialId()),
                'finicial' => '2026-10-05',
                'ffinal' => '2026-10-09',
                'tiempo_ejecucion' => 1,
                'diagnostico' => 'Diagnóstico de la planificación semanal.',
                'observacion' => null,
            ]
        ));

        Livewire::actingAs($docente['user'])
            ->test(\App\Livewire\Inicial\EiplanningwkComponent::class)
            ->call('openModal', 'summary', $planSemanal->id)
            ->assertSee('Guardar resumen');

        $planQuincenal = \App\Models\app\Inicial\Eiplanningbwk::create(array_merge(
            [
                'profesor_id' => $docente['profesor_id'],
                'grado_id' => $this->gradoInicialId(),
                'seccion_id' => $this->seccionDe($this->gradoInicialId()),
                'finicial' => '2026-10-05',
                'ffinal' => '2026-10-16',
                'tiempo_ejecucion' => 2,
                'diagnostico' => 'Diagnóstico de la planificación quincenal.',
                'observacion' => null,
            ]
        ));

        Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'summary', $planQuincenal->id)
            ->assertSee('Guardar resumen');
    }

    // ─── Estrategias ─────────────────────────────────────────────

    /** @test */
    public function la_estrategia_se_persiste_en_la_columna_lunes_del_legacy(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $indice = 4;
        $momento = array_keys(Eispecialstrategy::LIST_MOMENT)[$indice];

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->call('setActiveDay', 'martes')
            ->call('setActiveMoment', $indice)
            ->set("strategies.martes.{$indice}.estrategia", 'Observamos el patio desde la ventana.')
            ->call('saveCurrentStrategy');

        $component->assertHasNoErrors();

        $fila = Eispecialstrategy::where('eispecialk_id', $plan->id)
            ->where('day_of_week', 'martes')
            ->where('momento_rutina_diaria', $momento)
            ->first();

        $this->assertNotNull($fila, "La estrategia no se guardó en la celda martes × {$momento}.");

        // Quirk D3.
        $this->assertSame('Observamos el patio desde la ventana.', $fila->getRawOriginal('lunes'));
        $this->assertNull($fila->martes);
    }

    /** @test */
    public function la_estrategia_se_borra_con_la_firma_dia_indice_del_momento(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->set('strategies.jueves.7.estrategia', 'Mural colaborativo del barrio.')
            ->call('saveStrategies');

        $this->assertSame(1, Eispecialstrategy::where('eispecialk_id', $plan->id)->count());

        $component->call('deleteStrategy', 'jueves', 7)->assertHasNoErrors();

        $this->assertSame(0, Eispecialstrategy::where('eispecialk_id', $plan->id)->count());
    }

    /** @test */
    public function abrir_una_celda_concreta_enfoca_el_momento_indicado(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openStrategyCell', $plan->id, 'viernes', 'Periodo: Despedida');

        $this->assertSame('viernes', $component->get('activeDay'));
        $this->assertSame(9, $component->get('activeMomentIndex'));
    }

    // ─── Borrado en cascada ──────────────────────────────────────

    /** @test */
    public function borrar_el_plan_arrastra_actividades_y_estrategias(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Eispecialstrategy::create([
            'eispecialk_id' => $plan->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Cancion de bienvenida.',
        ]);

        Eispecialact::create([
            'eispecialk_id' => $plan->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'componente' => 'Lenguaje',
            'objetivo' => 'Escucha y relata.',
            'aprendizaje_esperado' => 'Relata un cuento breve.',
            'indicadores' => '1. Escucha. 2. Relata.',
        ]);

        Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('deletePlan', $plan->id)
            ->assertHasNoErrors();

        $this->assertNull(Eispecialk::find($plan->id));
        $this->assertSame(0, Eispecialstrategy::where('eispecialk_id', $plan->id)->count());
        $this->assertSame(0, Eispecialact::where('eispecialk_id', $plan->id)->count());
    }

    // ─── Acceso ──────────────────────────────────────────────────

    /** @test */
    public function usuario_sin_permiso_de_inicial_recibe_403(): void
    {
        $sinPermiso = User::factory()->profesor()->create(['is_admin' => false]);

        $this->actingAs($sinPermiso)
            ->get(route('inicials.home'))
            ->assertForbidden();

        Livewire::actingAs($sinPermiso)
            ->test(EispecialkComponent::class)
            ->assertStatus(403);
    }

    /** @test */
    public function un_docente_con_el_flag_entra_al_modulo_y_ve_su_listado(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = $this->makeDocenteInicial(isAdmin: false);
        $plan = $this->makePlan($docente['profesor_id']);

        $this->assertTrue($docente['user']->isInicial());

        $this->actingAs($docente['user'])
            ->get(route('inicials.eispecialks.index'))
            ->assertOk()
            ->assertSee($plan->justificacion);
    }

    // ─── Formato imprimible ──────────────────────────────────────

    /** @test */
    public function el_formato_imprimible_trae_justificacion_actividades_y_estrategias(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        foreach ([['Matemática', 'Línea UNO'], ['Lenguaje', 'Línea DOS']] as [$componente, $linea]) {
            Eispecialact::create([
                'eispecialk_id' => $plan->id,
                'pevaluacion_id' => $docente['pevaluacion_id'],
                'componente' => $componente,
                'objetivo' => 'Objetivo de '.$componente,
                'aprendizaje_esperado' => 'Aprendizaje esperado de '.$componente,
                'indicadores' => 'Indicador de '.$componente,
                'linea_investigacion' => $linea,
                'enfasis_curriculares' => 'Énfasis de '.$componente,
            ]);
        }

        Eispecialstrategy::create([
            'eispecialk_id' => $plan->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Observamos el patio.',
        ]);

        $html = $this->actingAs($docente['user'])
            ->get(route('inicials.eispecialks.format', $plan->id))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('PLAN ESPECIAL', $html);
        $this->assertStringContainsString('Justificación', $html);
        $this->assertStringContainsString('Tabla de Actividades', $html);
        $this->assertStringContainsString('Estrategias del Docente', $html);

        $this->assertStringContainsString($this->planAttributes()['justificacion'], $html);
        $this->assertStringContainsString('Observamos el patio.', $html);

        // Regresión del `rowspan`: línea de investigación y énfasis son columnas
        // POR actividad, no del plan.
        $this->assertStringContainsString('Línea UNO', $html);
        $this->assertStringContainsString('Línea DOS', $html);
        $this->assertStringNotContainsString('No hay datos', $html);
    }

    /** @test */
    public function el_formato_imprimible_no_permite_ver_el_plan_de_otro_docente(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $otro = $this->makeDocenteInicial(isAdmin: false);

        $plan = $this->makePlan($dueno['profesor_id']);

        $this->actingAs($otro['user'])
            ->get(route('inicials.eispecialks.format', $plan->id))
            ->assertNotFound();
    }

    /** @test */
    public function el_modulo_de_planes_especiales_arranca_desde_el_indice_sin_errores_de_vista(): void
    {
        $docente = $this->makeDocenteInicial();
        $this->makePlan($docente['profesor_id']);

        $this->actingAs($docente['user'])
            ->get(route('inicials.eispecialks.index'))
            ->assertOk();
    }

    /** @test */
    public function el_modal_de_detalle_muestra_la_cabecera_del_plan(): void
    {
        // El partial `plan-details` SOLO se renderiza con `openModal('view')`.
        // Sin esta cobertura el partial queda sin ejecutar.
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Livewire::actingAs($docente['user'])
            ->test(EispecialkComponent::class)
            ->call('openModal', 'view', $plan->id)
            ->assertOk()
            ->assertSee('Detalle')
            ->assertSee($plan->justificacion);
    }
}
