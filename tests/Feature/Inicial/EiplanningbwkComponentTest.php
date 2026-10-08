<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EiplanningbwkComponent;
use App\Livewire\Inicial\EiplanningwkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningbwstrategy;
use App\Models\app\Inicial\Eiplanningbwsummary;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * F3 del módulo de Educación Inicial: CRUD de la planificación QUINCENAL.
 *
 * Blueprint: blueprint/inicial · fase F3.
 *
 * ─────────────────────────────────────────────────────────────────
 * CÓMO SE CUBRE EL ACCESO
 * ─────────────────────────────────────────────────────────────────
 * `is_admin` deja pasar el middleware y el `authorizeInicial()` a propósito, así
 * que los tests del CRUD no dependen del flag de módulo.
 *
 * Los tests de PROPIEDAD necesitan lo contrario —docentes NO admin, para que
 * corra el chequeo de `profesor_id`— y un docente no admin solo entra al módulo
 * con `users.is_inicial`. Esos tests usan fixtures con el flag y se saltan
 * solos (`saltarSiFaltaLaMigracion()`) si la columna no existiera, en vez de
 * fallar por algo que no depende del código.
 *
 * @group inicial
 * @group inicial-f3
 */
class EiplanningbwkComponentTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Fixtures ─────────────────────────────────────────────────

    /**
     * Docente de Inicial: usuario + ficha `profesors` + una `pevaluacions` en un
     * pensum del pestudio 6, que es lo que hace que `loadGrados()` liste ese
     * grado.
     *
     * @return array{user: User, profesor_id: int, pevaluacion_id: int}
     */
    private function makeDocenteInicial(bool $isAdmin = true): array
    {
        $attributes = [
            'is_admin' => $isAdmin,
            'is_profesor' => true,
        ];

        // Solo se puede marcar `is_inicial` si la columna existe; si no, el
        // INSERT revienta con "Unknown column".
        if (Schema::hasColumn('users', 'is_inicial')) {
            $attributes['is_inicial'] = ! $isAdmin;
        }

        $user = User::factory()->create($attributes);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-99999999',
            'ci_profesor' => '99999999',
            'name' => 'Docente',
            'lastname' => 'Quincenal Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
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
     * ⚠️ No usar para `->set()` de Livewire: ahí hacen falta las rutas con punto.
     * `Eiplanningbwk::create()` con claves con punto descarta los atributos en
     * silencio (no están en `$fillable`) y deja el plan con todo a NULL.
     *
     * @return array<string, mixed>
     */
    private function planAttributes(): array
    {
        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');

        return [
            'grado_id' => $gradoId,
            'seccion_id' => $this->seccionDe($gradoId),
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-16',
            'tiempo_ejecucion' => 2,
            'diagnostico' => 'El grupo llega de la quincena anterior con buena convivencia.',
            'observacion' => 'Buena participación del grupo.',
        ];
    }

    /**
     * El mismo formulario como rutas de propiedad de Livewire, para `->set()`.
     *
     * @return array<string, mixed>
     */
    private function planForm(): array
    {
        return collect($this->planAttributes())
            ->mapWithKeys(fn ($value, $key) => ['eiplanningbwk.'.$key => $value])
            ->all();
    }

    /** Crea un plan directamente en BD (fixtures). */
    private function makePlan(int $profesorId): Eiplanningbwk
    {
        return Eiplanningbwk::create(
            array_merge($this->planAttributes(), ['profesor_id' => $profesorId])
        );
    }

    /**
     * Crea un resumen completo sobre el plan del docente indicado.
     *
     * @param  array{user: User, profesor_id: int, pevaluacion_id: int}  $docente
     */
    private function makeResumen(array $docente, string $componente): Eiplanningbwsummary
    {
        $plan = $this->makePlan($docente['profesor_id']);

        return Eiplanningbwsummary::create([
            'eiplanningbwk_id' => $plan->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'componente' => $componente,
            'objetivo' => 'Cuenta objetos del entorno.',
            'aprendizaje_esperado' => 'Cuenta hasta cinco elementos.',
            'indicadores' => '1. Cuenta. 2. Agrupa.',
        ]);
    }

    /**
     * Los tests de PROPIEDAD necesitan docentes NO admin, y un docente no admin
     * solo entra al módulo con `users.is_inicial`. Si la migración sigue
     * pendiente el middleware corta antes: se omite con un motivo explícito en
     * vez de fallar por algo que aún no puede darse.
     */
    private function saltarSiFaltaLaMigracion(): void
    {
        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped(
                'Requiere la columna users.is_inicial (migración add_is_inicial_to_users_table pendiente).'
            );
        }
    }

    // ─── Listado y carga en cascada ──────────────────────────────

    /** @test */
    public function el_listado_muestra_solo_los_planes_del_docente(): void
    {
        $docente = $this->makeDocenteInicial();
        $otro = $this->makeDocenteInicial();

        $propio = $this->makePlan($docente['profesor_id']);
        $ajeno = $this->makePlan($otro['profesor_id']);

        $ids = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->viewData('eiplanningbwks')
            ->pluck('id')
            ->all();

        $this->assertContains($propio->id, $ids);
        $this->assertNotContains($ajeno->id, $ids, 'Un docente no debe ver el plan de otro.');
    }

    /** @test */
    public function al_elegir_grado_carga_solo_sus_secciones_activas(): void
    {
        $docente = $this->makeDocenteInicial();
        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->set('eiplanningbwk.grado_id', $gradoId);

        $secciones = $component->get('listSeccion');

        $this->assertNotEmpty($secciones);
        $this->assertTrue(
            $secciones->keys()->every(
                fn ($id) => (int) DB::table('seccions')->where('id', $id)->value('grado_id') === $gradoId
            ),
            'Las secciones deben ser del grado elegido.'
        );
        $this->assertNull(
            $component->get('eiplanningbwk')['seccion_id'],
            'Al cambiar de grado la sección elegida debe limpiarse.'
        );
    }

    // ─── Cabecera: guardar y validar ─────────────────────────────

    /** @test */
    public function guarda_un_plan_quincenal_nuevo_con_el_profesor_autenticado(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->set($this->planForm())
            ->call('save');

        $component->assertHasNoErrors();

        $plan = Eiplanningbwk::where('profesor_id', $docente['profesor_id'])->first();

        $this->assertNotNull($plan, 'El plan no se guardó.');
        $this->assertSame('2026-10-05', $plan->finicial->format('Y-m-d'));
        $this->assertSame(2, (int) $plan->tiempo_ejecucion);
        $this->assertFalse($component->get('showModal'), 'El modal debe cerrarse tras guardar.');
    }

    /** @test */
    public function rechaza_una_fecha_de_culminacion_anterior_a_la_de_inicio(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->set($this->planForm())
            ->set('eiplanningbwk.ffinal', '2026-10-01')
            ->call('save');

        // R2 del blueprint: la regla documentada SÍ se adoptó en F3.
        $component->assertHasErrors('eiplanningbwk.ffinal');

        $this->assertSame(0, Eiplanningbwk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function exige_un_diagnostico_minimo_de_10_caracteres(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->set($this->planForm())
            // R1: el runtime legacy era min:10 (el "≥50" documentado NO bloquea).
            ->set('eiplanningbwk.diagnostico', 'corto')
            ->call('save');

        $component->assertHasErrors('eiplanningbwk.diagnostico');
    }

    /** @test */
    public function un_plan_no_puede_abrirse_desde_otro_docente(): void
    {
        $dueno = $this->makeDocenteInicial();
        $invasor = $this->makeDocenteInicial();

        $plan = $this->makePlan($dueno['profesor_id']);

        // `findPlan()` filtra por `profesor_id` → 404, no un 500 ni la carga
        // silenciosa del plan ajeno.
        Livewire::actingAs($invasor['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->assertStatus(404);
    }

    /** @test */
    public function no_puede_borrar_el_plan_de_otro_docente(): void
    {
        $dueno = $this->makeDocenteInicial();
        $invasor = $this->makeDocenteInicial();

        $plan = $this->makePlan($dueno['profesor_id']);

        // El legacy hacía `Eiplanningbwk::findOrFail($id)` sin filtro: cualquier
        // docente autenticado podía borrar el plan de otro con su id.
        Livewire::actingAs($invasor['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('deletePlan', $plan->id)
            ->assertStatus(404);

        $this->assertNotNull(Eiplanningbwk::find($plan->id), 'El plan ajeno no debe borrarse.');
    }

    // ─── Wizard de estrategias (5 × 10) ──────────────────────────

    /** @test */
    public function crea_la_rejilla_completa_de_50_celdas_vacias(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class);

        $estrategias = $component->get('strategies');

        $this->assertCount(5, $estrategias, 'Debe haber 5 días.');
        $this->assertCount(10, $estrategias['lunes'], 'Debe haber 10 momentos por día.');

        // La rejilla se indexa 0..9 (los nombres de momento tienen espacios y
        // dos puntos, que romperían `wire:model`) y el orden es el de la rutina.
        $this->assertSame(range(0, 9), array_keys($estrategias['lunes']));
        $this->assertSame(
            array_values(Eiplanningbwstrategy::LIST_MOMENT),
            array_values($component->get('moments'))
        );
    }

    /** @test */
    public function la_estrategia_se_persiste_en_la_columna_lunes_del_legacy(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->call('setActiveDay', 'martes')
            ->call('setActiveMoment', 3)
            ->set('strategies.martes.3.estrategia', 'El docente modela la saludo del día.')
            ->call('saveCurrentStrategy');

        $component->assertHasNoErrors();

        $fila = Eiplanningbwstrategy::where('eiplanningbwk_id', $plan->id)
            ->where('day_of_week', 'martes')
            ->where('momento_rutina_diaria', 'Periodo: Planificación')
            ->first();

        $this->assertNotNull($fila, 'La estrategia no se guardó en la celda martes × Periodo: Planificación.');

        // Quirk D3: el texto SIEMPRE vive en `lunes`, con independencia del día.
        $this->assertSame('El docente modela la saludo del día.', $fila->getRawOriginal('lunes'));
        $this->assertSame('El docente modela la saludo del día.', $fila->estrategia);
        $this->assertNull($fila->martes, 'Las columnas martes…viernes son residuo del esquema legacy.');
    }

    /** @test */
    public function guarda_todas_las_celdas_que_tengan_texto(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->set('strategies.lunes.0.estrategia', 'Recibimiento con canción de bienvenida.')
            ->set('strategies.viernes.9.estrategia', 'Cierre en círculo y reflexión grupal.')
            ->call('saveStrategies');

        $this->assertSame(2, Eiplanningbwstrategy::where('eiplanningbwk_id', $plan->id)->count());
    }

    /** @test */
    public function la_estrategia_se_borra_con_la_firma_dia_indice_del_momento(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->set('strategies.jueves.7.estrategia', 'Trabajo por estaciones.')
            ->call('saveStrategies');

        $this->assertSame(1, Eiplanningbwstrategy::where('eiplanningbwk_id', $plan->id)->count());

        $component->call('deleteStrategy', 'jueves', 7)->assertHasNoErrors();

        $this->assertSame(
            0,
            Eiplanningbwstrategy::where('eiplanningbwk_id', $plan->id)->count(),
            'El borrado debe ser efectivo.'
        );

        $celda = $component->get('strategies')['jueves'][7];
        $this->assertNull($celda['id']);
        $this->assertSame('', $celda['estrategia']);
    }

    /** @test */
    public function la_estrategia_guardada_reabre_en_su_celda_dia_momento_correcta(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Eiplanningbwstrategy::create([
            'eiplanningbwk_id' => $plan->id,
            'day_of_week' => 'viernes',
            'momento_rutina_diaria' => 'Periodo: Despedida',
            'lunes' => 'Puesta en común final.',
            'order' => 2,
        ]);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->call('closeModal')
            ->call('openModal', 'strategy', $plan->id);

        $celda = $component->get('strategies')['viernes'][9];

        $this->assertSame('Puesta en común final.', $celda['estrategia']);
        $this->assertNotNull($celda['id']);
        $this->assertSame(2, $celda['order']);
    }

    /** @test */
    public function abrir_una_celda_concreta_desde_la_tarjeta_enfoca_el_momento_indicado(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        // Regresión: `setActiveMoment()` espera el ÍNDICE. Pasándole el nombre
        // del momento (`indexForMoment` no se aplicaba) el docente caía siempre
        // en el primer momento del día.
        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openStrategyCell', $plan->id, 'jueves', 'Periodo: Despedida');

        $this->assertSame('jueves', $component->get('activeDay'));
        $this->assertSame(9, $component->get('activeMomentIndex'));
        $this->assertSame('Periodo: Despedida', $component->get('activeMoment'));
    }

    // ─── Resúmenes por área ──────────────────────────────────────

    /** @test */
    public function guarda_un_resumen_por_area_con_los_cuatro_campos_obligatorios(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'summary', $plan->id)
            ->set('eiplanningbwsummary.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('eiplanningbwsummary.componente', 'Lenguaje')
            ->set('eiplanningbwsummary.objetivo', 'Participa en conversaciones cotidianas.')
            ->set('eiplanningbwsummary.aprendizaje_esperado', 'Conversa con sus pares sobre temas cotidianos.')
            ->set('eiplanningbwsummary.indicadores', '1. Inicia la conversación. 2. Escucha al otro.')
            ->call('saveSummary');

        $component->assertHasNoErrors();

        $resumen = Eiplanningbwsummary::where('eiplanningbwk_id', $plan->id)->first();

        $this->assertNotNull($resumen);
        $this->assertSame('Lenguaje', $resumen->componente);
    }

    /** @test */
    public function el_resumen_exige_los_cuatro_campos_de_informacion_general(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'summary', $plan->id)
            ->set('eiplanningbwsummary.pevaluacion_id', $docente['pevaluacion_id'])
            ->call('saveSummary');

        // R5 del blueprint.
        $component->assertHasErrors([
            'eiplanningbwsummary.componente',
            'eiplanningbwsummary.objetivo',
            'eiplanningbwsummary.aprendizaje_esperado',
            'eiplanningbwsummary.indicadores',
        ]);

        $this->assertSame(0, Eiplanningbwsummary::where('eiplanningbwk_id', $plan->id)->count());
    }

    /** @test */
    public function el_resumen_de_otro_docente_no_puede_abrirse_para_editarlo(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $invasor = $this->makeDocenteInicial(isAdmin: false);

        $resumen = $this->makeResumen($dueno, 'Matemática');

        // El legacy hacía `Eiplanningbwsummary::find($id)` sin filtro: con solo
        // conocer el id, `openModal('edit-summary')` cargaba el resumen de otro
        // docente y `saveSummary()` lo escribía encima.
        Livewire::actingAs($invasor['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'edit-summary', $resumen->id)
            ->assertStatus(404);
    }

    /** @test */
    public function el_resumen_de_otro_docente_no_puede_borrarse_desde_un_plan_propio(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $invasor = $this->makeDocenteInicial(isAdmin: false);

        $resumenAjeno = $this->makeResumen($dueno, 'Matemática');

        // El invasor abre SU plan (sin problema) y desde ahí intenta borrar el
        // resumen ajeno. El legacy lo permitía: `deleteSummary()` hacía
        // `findOrFail($id)` sin mirar de qué plan venía.
        $planPropio = $this->makePlan($invasor['profesor_id']);

        Livewire::actingAs($invasor['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'summary', $planPropio->id)
            ->call('deleteSummary', $resumenAjeno->id)
            ->assertStatus(404);

        $this->assertNotNull(
            Eiplanningbwsummary::find($resumenAjeno->id),
            'El resumen ajeno no debe borrarse.'
        );
    }

    /** @test */
    public function un_docente_con_el_flag_entra_al_modulo_y_ve_su_listado(): void
    {
        // El camino REAL de producción: `is_admin` NO lo abre nadie; lo abre
        // `is_inicial` (o el middleware corta con 403). Este test es el que
        // fallaría si el flag no se guardara o el middleware lo ignorara.
        $this->saltarSiFaltaLaMigracion();

        $docente = $this->makeDocenteInicial(isAdmin: false);
        $plan = $this->makePlan($docente['profesor_id']);

        $this->assertFalse((bool) $docente['user']->is_admin, 'El fixture no debe ser admin.');
        $this->assertTrue($docente['user']->isInicial(), 'El fixture debe tener is_inicial = 1.');

        $this->actingAs($docente['user'])
            ->get(route('inicials.eiplanningbwks.index'))
            ->assertOk()
            ->assertSee($plan->diagnostico);

        Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'edit', $plan->id)
            ->assertOk();
    }

    // ─── Borrado en cascada ──────────────────────────────────────

    /** @test */
    public function borrar_el_plan_arrastra_sus_estrategias_y_resumenes(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Eiplanningbwstrategy::create([
            'eiplanningbwk_id' => $plan->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Canción de bienvenida.',
        ]);

        Eiplanningbwsummary::create([
            'eiplanningbwk_id' => $plan->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'componente' => 'Lenguaje',
            'objetivo' => 'Escucha y relata.',
            'aprendizaje_esperado' => 'Relata un cuento breve.',
            'indicadores' => '1. Escucha. 2. Relata.',
        ]);

        Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('deletePlan', $plan->id)
            ->assertHasNoErrors();

        $this->assertNull(Eiplanningbwk::find($plan->id));
        // Sin FK declarada hacia las hijas, el borrado debe ser explícito.
        $this->assertSame(0, Eiplanningbwstrategy::where('eiplanningbwk_id', $plan->id)->count());
        $this->assertSame(0, Eiplanningbwsummary::where('eiplanningbwk_id', $plan->id)->count());
    }

    // ─── Acceso ──────────────────────────────────────────────────

    /** @test */
    public function usuario_sin_permiso_de_inicial_recibe_403(): void
    {
        // Docente legítimo pero de OTRO nivel (Bachillerato): tiene
        // `is_profesor`, no `is_admin` y no es de Inicial. NO se pasa
        // `is_inicial` a la factory porque la columna puede no existir en un
        // entorno sin la migración.
        $sinPermiso = User::factory()->profesor()->create(['is_admin' => false]);

        $this->actingAs($sinPermiso)
            ->get(route('inicials.home'))
            ->assertForbidden();

        Livewire::actingAs($sinPermiso)
            ->test(EiplanningbwkComponent::class)
            ->assertStatus(403);
    }

    // ─── Formato imprimible ──────────────────────────────────────

    /** @test */
    public function el_formato_imprimible_se_renderiza_con_las_tres_secciones(): void
    {
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Eiplanningbwstrategy::create([
            'eiplanningbwk_id' => $plan->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Símbolo patrio en círculo.',
        ]);

        $response = $this->actingAs($docente['user'])
            ->get(route('inicials.eiplanningbwks.format', $plan->id));

        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('PLAN QUINCENAL', $html);
        $this->assertStringContainsString('Tabla Resumen', $html);
        $this->assertStringContainsString('Estrategias del Docente', $html);
        // El texto del docente se imprime escapado (XSS del legacy).
        $this->assertStringContainsString('Símbolo patrio en círculo.', $html);

        // El legacy imprimía el membrete/título de estrategias DOS veces.
        $this->assertSame(
            1,
            substr_count($html, 'Estrategias del Docente'),
            'El título de estrategias debe aparecer una sola vez.'
        );
    }

    /** @test */
    public function el_formato_imprimible_repite_las_columnas_por_resumen(): void
    {
        $docente = $this->makeDocenteInicial();

        $plan = $this->makePlan($docente['profesor_id']);

        // Misma regresión que en la semanal: `linea_investigacion` y
        // `enfasis_curriculares` son columnas POR RESUMEN, no del proyecto.
        foreach ([['Matemática', 'Línea UNO'], ['Lenguaje', 'Línea DOS']] as [$componente, $linea]) {
            Eiplanningbwsummary::create([
                'eiplanningbwk_id' => $plan->id,
                'pevaluacion_id' => $docente['pevaluacion_id'],
                'componente' => $componente,
                'objetivo' => 'Objetivo de '.$componente,
                'aprendizaje_esperado' => 'Aprendizaje esperado de '.$componente,
                'indicadores' => 'Indicador de '.$componente,
                'linea_investigacion' => $linea,
                'enfasis_curriculares' => 'Énfasis de '.$componente,
            ]);
        }

        $html = $this->actingAs($docente['user'])
            ->get(route('inicials.eiplanningbwks.format', $plan->id))
            ->assertOk()
            ->getContent();

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
            ->get(route('inicials.eiplanningbwks.format', $plan->id))
            ->assertNotFound();
    }

    /** @test */
    public function el_modulo_quincenal_arranca_desde_el_indice_sin_errores_de_vista(): void
    {
        $docente = $this->makeDocenteInicial();
        $this->makePlan($docente['profesor_id']);

        $this->actingAs($docente['user'])
            ->get(route('inicials.eiplanningbwks.index'))
            ->assertOk();
    }

    /** @test */
    public function la_semanal_no_rompe_tras_mover_el_wizard_a_un_partial_compartido(): void
    {
        // El wizard de estrategias pasó a `livewire/inicial/shared/`: si su
        // ruta o sus variables se rompen, el listado sigue verde y el modal
        // revienta al abrirlo. Este test abre el modal de verdad.
        $docente = $this->makeDocenteInicial();
        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');

        $plan = Eiplanningwk::create([
            'profesor_id' => $docente['profesor_id'],
            'grado_id' => $gradoId,
            'seccion_id' => $this->seccionDe($gradoId),
            'finicial' => '2026-10-05',
            'ffinal' => '2026-10-09',
            'tiempo_ejecucion' => 1,
            'diagnostico' => 'Diagnóstico de la planificación semanal.',
            'observacion' => null,
        ]);

        Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->assertOk()
            ->assertSee('Estrategias')
            ->assertSee('Recibimiento');
    }

    /** @test */
    public function el_modal_de_detalle_muestra_la_cabecera_del_plan(): void
    {
        // El partial `plan-details` SOLO se renderiza con `openModal('view')`.
        // Sin esta cobertura el partial queda sin ejecutar.
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Livewire::actingAs($docente['user'])
            ->test(EiplanningbwkComponent::class)
            ->call('openModal', 'view', $plan->id)
            ->assertOk()
            ->assertSee('Detalle')
            ->assertSee($plan->diagnostico);
    }
}
