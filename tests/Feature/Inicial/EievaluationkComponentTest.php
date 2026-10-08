<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EievaluationkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eievaluationp;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * F3 del módulo de Educación Inicial: CRUD del PLAN DE EVALUACIÓN.
 *
 * Blueprint: blueprint/inicial · F3 (CRUD docente resto).
 *
 * Es el documento más distinto del módulo, y las tests lo fijan:
 *  · la cabecera lleva `lapso_id` OBLIGATORIO (único documento con periodo);
 *  · NO tiene `tiempo_ejecucion` ni `diagnostico`: se documenta con
 *    `observaciones`, `asistencia` y `recomendacion`;
 *  · no hay rejilla de estrategias: sus hijas son POSICIONES y solo exigen el
 *    área (el resto es opcional, a diferencia de los resúmenes de los otros
 *    documentos, que exigen 4 campos).
 *  · `fecha` se valida como `date`, no como `string` (divergencia deliberada
 *    respecto al legacy, que perdía el dato en silencio).
 *
 * @group inicial
 * @group inicial-f3
 */
class EievaluationkComponentTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Fixtures ─────────────────────────────────────────────────

    /**
     * @return array{user: User, profesor_id: int, pevaluacion_id: int, lapso_id: int}
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
            'lastname' => 'Evaluacion Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = $this->gradoInicialId();
        $pensum = Pensum::where('grado_id', $gradoId)->firstOrFail();
        $lapsoId = (int) DB::table('lapsos')->value('id');

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'profesor_id' => $profesorId,
            'pensum_id' => $pensum->id,
            'seccion_id' => $this->seccionDe($gradoId),
            'lapso_id' => $lapsoId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'user' => $user,
            'profesor_id' => $profesorId,
            'pevaluacion_id' => $pevaluacionId,
            'lapso_id' => $lapsoId,
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
     * Atributos válidos de un plan, con las CLAVES PLANAS de la tabla.
     *
     * @return array<string, mixed>
     */
    private function planAttributes(int $lapsoId): array
    {
        $gradoId = $this->gradoInicialId();

        return [
            'grado_id' => $gradoId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $this->seccionDe($gradoId),
            'finicial' => '2026-10-01',
            'ffinal' => '2026-10-31',
            'observaciones' => 'Se evaluó la comprensión lectora mediante tres actividades.',
            'asistencia' => 'El grupo mantiene una asistencia del 92 %.',
            'recomendacion' => 'Reforzar la lectura en casa durante el segundo periodo.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function planForm(int $lapsoId): array
    {
        return collect($this->planAttributes($lapsoId))
            ->mapWithKeys(fn ($value, $key) => ['eievaluationk.'.$key => $value])
            ->all();
    }

    private function makePlan(int $profesorId, int $lapsoId): Eievaluationk
    {
        return Eievaluationk::create(
            array_merge($this->planAttributes($lapsoId), ['profesor_id' => $profesorId])
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

        $propio = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);
        $ajeno = $this->makePlan($otro['profesor_id'], $otro['lapso_id']);

        $ids = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->viewData('eievaluationks')
            ->pluck('id')
            ->all();

        $this->assertContains($propio->id, $ids);
        $this->assertNotContains($ajeno->id, $ids);
    }

    /** @test */
    public function guarda_un_plan_de_evaluacion_con_lapso_observaciones_y_asistencia(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->set($this->planForm($docente['lapso_id']))
            ->call('save');

        $component->assertHasNoErrors();

        $plan = Eievaluationk::where('profesor_id', $docente['profesor_id'])->first();

        $this->assertNotNull($plan, 'El plan no se guardó.');
        $this->assertSame($docente['lapso_id'], (int) $plan->lapso_id);
        $this->assertSame(
            'Se evaluó la comprensión lectora mediante tres actividades.',
            $plan->observaciones
        );
        $this->assertFalse($component->get('showModal'));
    }

    /** @test */
    public function el_lapso_es_obligatorio(): void
    {
        $docente = $this->makeDocenteInicial();

        $form = $this->planForm($docente['lapso_id']);
        $form['eievaluationk.lapso_id'] = null;

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->set($form)
            ->call('save');

        // Único documento del módulo anclado a un periodo: sin lapso no hay plan.
        $component->assertHasErrors('eievaluationk.lapso_id');

        $this->assertSame(0, Eievaluationk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function exige_observaciones_de_10_caracteres_y_asistencia(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->set($this->planForm($docente['lapso_id']))
            ->set('eievaluationk.observaciones', 'corta')
            ->set('eievaluationk.asistencia', '')
            ->call('save');

        // R1 (min:10, no min:50) y el `required|string` del runtime legacy.
        $component->assertHasErrors([
            'eievaluationk.observaciones',
            'eievaluationk.asistencia',
        ]);
    }

    /** @test */
    public function rechaza_una_fecha_de_culminacion_anterior_a_la_de_inicio(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->set($this->planForm($docente['lapso_id']))
            ->set('eievaluationk.ffinal', '2026-09-01')
            ->call('save');

        // R2.
        $component->assertHasErrors('eievaluationk.ffinal');
    }

    /** @test */
    public function un_plan_no_puede_abrirse_ni_borrarse_desde_otro_docente(): void
    {
        $dueno = $this->makeDocenteInicial();
        $invasor = $this->makeDocenteInicial();

        $plan = $this->makePlan($dueno['profesor_id'], $dueno['lapso_id']);

        Livewire::actingAs($invasor['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'position', $plan->id)
            ->assertStatus(404);

        Livewire::actingAs($invasor['user'])
            ->test(EievaluationkComponent::class)
            ->call('deletePlan', $plan->id)
            ->assertStatus(404);

        $this->assertNotNull(Eievaluationk::find($plan->id));
    }

    // ─── Posiciones ──────────────────────────────────────────────

    /** @test */
    public function guarda_una_posicion_solo_con_el_area_siendo_el_resto_opcional(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        // A diferencia de los resúmenes de los otros documentos, aquí el legacy
        // solo exigía el área: la docente abre la fila y la completa después.
        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'position', $plan->id)
            ->set('eievaluationp.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('eievaluationp.nombre_ninos', 'Ana, Luis y María')
            ->set('eievaluationp.fecha', '2026-10-15')
            ->set('eievaluationp.instrumento', 'Observación directa')
            ->call('savePosition');

        $component->assertHasNoErrors();

        $position = Eievaluationp::where('eievaluationk_id', $plan->id)->first();

        $this->assertNotNull($position);
        $this->assertSame('Ana, Luis y María', $position->nombre_ninos);
        $this->assertNull($position->componente, 'Un campo opcional sin rellenar queda NULL.');
    }

    /** @test */
    public function la_posicion_exige_el_area_de_aprendizaje(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'position', $plan->id)
            ->call('savePosition');

        $component->assertHasErrors('eievaluationp.pevaluacion_id');

        $this->assertSame(0, Eievaluationp::where('eievaluationk_id', $plan->id)->count());
    }

    /** @test */
    public function la_fecha_de_la_posicion_se_valida_como_fecha_y_no_como_texto(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        // El legacy validaba `nullable|string` sobre una columna DATE: un texto
        // tipo "15/10/2026" se guardaba como NULL con un warning silencioso.
        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'position', $plan->id)
            ->set('eievaluationp.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('eievaluationp.fecha', 'no-es-una-fecha')
            ->call('savePosition');

        $component->assertHasErrors('eievaluationp.fecha');

        $this->assertSame(0, Eievaluationp::where('eievaluationk_id', $plan->id)->count());
    }

    /** @test */
    public function la_posicion_de_otro_docente_no_puede_abrirse_ni_borrarse(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $invasor = $this->makeDocenteInicial(isAdmin: false);

        $positionAjena = Eievaluationp::create([
            'eievaluationk_id' => $this->makePlan($dueno['profesor_id'], $dueno['lapso_id'])->id,
            'pevaluacion_id' => $dueno['pevaluacion_id'],
            'nombre_ninos' => 'Niños del plan ajeno',
        ]);

        $planPropio = $this->makePlan($invasor['profesor_id'], $invasor['lapso_id']);

        Livewire::actingAs($invasor['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'edit-position', $positionAjena->id)
            ->assertStatus(404);

        Livewire::actingAs($invasor['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'position', $planPropio->id)
            ->call('deletePosition', $positionAjena->id)
            ->assertStatus(404);

        $this->assertNotNull(Eievaluationp::find($positionAjena->id));
    }

    /** @test */
    public function el_modal_de_alta_muestra_el_boton_de_guardar_posicion(): void
    {
        // Regresión de UI: el gestor muestra lista + formulario de alta, pero el
        // botón solo se pintaba en `edit-position`. Sin esto la primera posición
        // no tenía dónde guardarse.
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'position', $plan->id)
            ->assertSee('Guardar posición');
    }

    /** @test */
    public function el_gestor_de_posiciones_solo_ofrece_las_areas_del_lapso_del_plan(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'position', $plan->id);

        $areas = $component->get('listPevaluacion');

        $this->assertTrue(
            $areas->isNotEmpty(),
            'El plan debe traer al menos el área del propio docente.'
        );

        // Todas las áreas propuestas son del docente Y del lapso del plan.
        $esperadas = $plan->getPevaluacionsList($docente['profesor_id'], $docente['lapso_id']);

        $this->assertSame(
            $esperadas->keys()->all(),
            $areas->keys()->all()
        );
    }

    // ─── Borrado en cascada ──────────────────────────────────────

    /** @test */
    public function borrar_el_plan_arrastra_sus_posiciones(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        Eievaluationp::create([
            'eievaluationk_id' => $plan->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'nombre_ninos' => 'Ana y Luis',
            'instrumento' => 'Observación directa',
        ]);

        Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('deletePlan', $plan->id)
            ->assertHasNoErrors();

        $this->assertNull(Eievaluationk::find($plan->id));
        // Sin FK declarada hacia las hijas, el borrado debe ser explícito.
        $this->assertSame(0, Eievaluationp::where('eievaluationk_id', $plan->id)->count());
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
            ->test(EievaluationkComponent::class)
            ->assertStatus(403);
    }

    /** @test */
    public function un_docente_con_el_flag_entra_al_modulo_y_ve_su_listado(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = $this->makeDocenteInicial(isAdmin: false);
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        $this->assertTrue($docente['user']->isInicial());

        $this->actingAs($docente['user'])
            ->get(route('inicials.eievaluationks.index'))
            ->assertOk()
            ->assertSee($plan->observaciones);
    }

    // ─── Formato imprimible ──────────────────────────────────────

    /** @test */
    public function el_formato_imprimible_imprime_una_hoja_por_area_con_sus_posiciones(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        Eievaluationp::create([
            'eievaluationk_id' => $plan->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'fecha' => '2026-10-15',
            'nombre_ninos' => 'Ana, Luis y María',
            'aprendizaje_alcanzado' => 'Reconocen los personajes del cuento.',
            'indicadores' => '1. Nombra. 2. Describe.',
            'instrumento' => 'Rúbrica de observación',
            'observacion' => 'Necesitan apoyo con la lectura de Illustrated.',
        ]);

        $html = $this->actingAs($docente['user'])
            ->get(route('inicials.eievaluationks.format', $plan->id))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('PLAN DE EVALUACIÓN', $html);
        $this->assertStringContainsString('Plan de Evaluación', $html);
        $this->assertStringContainsString('Aprendizaje alcanzado', $html);

        // Los datos de la posición, escapados (el legacy los inyectaba con
        // `as_replace` + `{!! !!}`, que es XSS).
        $this->assertStringContainsString('Ana, Luis y María', $html);
        $this->assertStringContainsString('Rúbrica de observación', $html);
        $this->assertStringContainsString('15/10/2026', $html);
        $this->assertStringNotContainsString('No hay datos', $html);
    }

    /** @test */
    public function el_formato_imprimible_no_permite_ver_el_plan_de_otro_docente(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $otro = $this->makeDocenteInicial(isAdmin: false);

        $plan = $this->makePlan($dueno['profesor_id'], $dueno['lapso_id']);

        $this->actingAs($otro['user'])
            ->get(route('inicials.eievaluationks.format', $plan->id))
            ->assertNotFound();
    }

    /** @test */
    public function el_modulo_de_evaluacion_arranca_desde_el_indice_sin_errores_de_vista(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        // `assertOk()` NO basta: una página que monte el componente de otro
        // documento también devuelve 200. El contenido del plan propio delata
        // que se está viendo la lista correcta.
        $this->actingAs($docente['user'])
            ->get(route('inicials.eievaluationks.index'))
            ->assertOk()
            ->assertSee($plan->observaciones)
            ->assertDontSee('Todavía no hay planes');
    }

    /** @test */
    public function el_modal_de_detalle_muestra_cabecera_y_posiciones(): void
    {
        // El partial `plan-details` SOLO se renderiza con `openModal('view')`.
        // Sin esta cobertura el partial queda sin ejecutar.
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id'], $docente['lapso_id']);

        Eievaluationp::create([
            'eievaluationk_id' => $plan->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'nombre_ninos' => 'Ana, Luis y María',
            'instrumento' => 'Observación directa',
        ]);

        Livewire::actingAs($docente['user'])
            ->test(EievaluationkComponent::class)
            ->call('openModal', 'view', $plan->id)
            ->assertOk()
            ->assertSee('Detalle')
            ->assertSee($plan->observaciones)
            ->assertSee('Ana, Luis y María');
    }
}
