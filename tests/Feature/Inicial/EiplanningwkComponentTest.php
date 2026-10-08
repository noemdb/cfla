<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EiplanningwkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiplanningwsummary;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * F2 del módulo de Educación Inicial: CRUD de la planificación semanal.
 *
 * Blueprint: blueprint/inicial · fase F2 (entidad P0).
 *
 * ─────────────────────────────────────────────────────────────────
 * POR QUÉ LA MAYORÍA DE ESTOS TESTS USA `is_admin`
 * ─────────────────────────────────────────────────────────────────
 * `is_admin` deja pasar el middleware y el `authorizeInicial()` del componente
 * a propósito (ver IsInicial::handle), así que los tests del CRUD no dependen
 * del flag de módulo y son estables en cualquier entorno.
 *
 * Los tests que necesitan el camino real de docente (los de propiedad) usan
 * fixtures con `is_inicial = 1` y se saltan solos si la columna no existe; la
 * cobertura del camino real está en
 * `EiplanningbwkComponentTest::un_docente_con_el_flag_entra_al_modulo_*`.
 *
 * @group inicial
 * @group inicial-f2
 */
class EiplanningwkComponentTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Fixtures ─────────────────────────────────────────────────

    /**
     * Docente de Inicial completo: usuario + ficha `profesors` + una
     * `pevaluacions` en un pensum del pestudio 6 (grado 22 "1ER GRUPO").
     *
     * Sin esa pevaluación, `loadGrado()` —que lista los grados donde el
     * docente tiene carga— devolvería vacío y el select quedaría en blanco.
     *
     * @param  bool  $isAdmin  Los tests de propiedad usan docentes NO admin
     *                         para que corra el chequeo de `profesor_id`.
     * @return array{user: User, profesor_id: int, pevaluacion_id: int}
     */
    private function makeDocenteInicial(bool $isAdmin = true): array
    {
        $attributes = [
            'is_admin' => $isAdmin,
            'is_profesor' => true,
        ];

        // Solo se puede marcar `is_inicial` si la columna existe: en un entorno
        // sin la migración el INSERT revienta con "Unknown column". Al no
        // marcarla, el docente queda fuera del módulo y los tests de propiedad
        // se saltan (ver saltarSiFaltaLaMigracion()).
        if (Schema::hasColumn('users', 'is_inicial')) {
            $attributes['is_inicial'] = ! $isAdmin;
        }

        $user = User::factory()->create($attributes);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-99999999',
            'ci_profesor' => '99999999',
            'name' => 'Docente',
            'lastname' => 'Inicial Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pensum = Pensum::where('grado_id', Grado::where('pestudio_id', 6)->value('id'))
            ->firstOrFail();

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'profesor_id' => $profesorId,
            'pensum_id' => $pensum->id,
            'seccion_id' => DB::table('seccions')
                ->where('grado_id', $pensum->grado_id)
                ->where('status_active', 'true')
                ->value('id'),
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

    /** Sección activa del grado de la fixture. */
    private function seccionDe(int $gradoId): int
    {
        return (int) DB::table('seccions')
            ->where('grado_id', $gradoId)
            ->where('status_active', 'true')
            ->value('id');
    }

    /**
     * Los tests de PROPIEDAD necesitan docentes NO admin (para que corra el
     * chequeo de `profesor_id`), y un docente no admin solo entra en el módulo
     * con `users.is_inicial`. Si la migración sigue pendiente, el middleware
     * devuelve 403 antes de llegar a la lógica: se omite el test con un motivo
     * explícito en lugar de fallar por algo que aún no se puede dar.
     */
    private function saltarSiFaltaLaMigracion(): void
    {
        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped(
                'Requiere la columna users.is_inicial (migración add_is_inicial_to_users_table pendiente).'
            );
        }
    }

    /**
     * Atributos válidos de un plan, con las CLAVES PLANAS de la tabla.
     *
     * ⚠️ No se puede usar para `->set()` de Livewire: allí hacen falta las
     * rutas con punto (`eiplanningwk.grado_id`) porque son propiedades del
     * componente. `Eiplanningwk::create()` con claves con punto descarta los
     * atributos en silencio (no están en `$fillable`) y deja el plan con todo
     * a NULL.
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
            'ffinal' => '2026-10-09',
            'tiempo_ejecucion' => 1,
            'diagnostico' => 'El grupo se muestra interesado en las rutinas de conversación.',
            'observacion' => 'Se observó alta participación.',
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
            ->mapWithKeys(fn ($value, $key) => ['eiplanningwk.'.$key => $value])
            ->all();
    }

    /** Crea un plan directamente en BD (fixtures). */
    private function makePlan(int $profesorId): Eiplanningwk
    {
        return Eiplanningwk::create(
            array_merge($this->planAttributes(), ['profesor_id' => $profesorId])
        );
    }

    // ─── Listado y carga en cascada ──────────────────────────────

    /** @test */
    public function el_select_de_grado_solo_ofrece_los_grados_de_inicial_del_docente(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class);

        $grados = $component->get('listGrado');

        $this->assertNotEmpty($grados, 'El docente con carga en pestudio 6 debe ver al menos un grado.');
        $this->assertTrue(
            // `every()` pasa (valor, clave): el valor de un pluck('name','id')
            // es el NOMBRE, la clave es el id. `pestudio_id` además llega como
            // string desde PDO, de ahí el cast.
            $grados->every(fn ($nombre, $id) => (int) Grado::find($id)?->pestudio_id === 6),
            'No debe colar ningún grado de otro nivel (Bachillerato, etc.).'
        );
    }

    /** @test */
    public function al_elegir_grado_carga_solo_sus_secciones_activas(): void
    {
        $docente = $this->makeDocenteInicial();
        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->set('eiplanningwk.grado_id', $gradoId);

        $secciones = $component->get('listSeccion');

        $this->assertNotEmpty($secciones);
        $this->assertTrue(
            $secciones->keys()->every(
                fn ($id) => (int) DB::table('seccions')->where('id', $id)->value('grado_id') === $gradoId
            ),
            'Las secciones deben ser del grado elegido.'
        );
        $this->assertNull(
            $component->get('eiplanningwk')['seccion_id'],
            'Al cambiar de grado la sección elegida debe limpiarse.'
        );
    }

    // ─── Modal limpio: título único, min real y selects vinculados ──

    /**
     * Regresión: el modal usaba `@switch`/`@case` SIN `@break`, así que renderizaba
     * TODOS los títulos; y la fecha de culminación llevaba un `@min(...)` malformado
     * que salía como texto. Además los selects de grado/sección deben salir de la
     * carga académica del profesor (pevaluacion → pensum → grado → seccion).
     *
     * @test
     */
    public function el_modal_de_creacion_es_limpio_y_vinculado_a_la_carga(): void
    {
        $docente = $this->makeDocenteInicial();
        // La fixture crea carga en el primer grado de pestudio 6: ese es el que
        // el select debe mostrar (y ningún otro grado sin carga).
        $gradoId = (int) \App\Models\app\Academy\Grado::where('pestudio_id', config('inicial.pestudio_id'))->value('id');

        $html = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'create')
            ->html();

        // 1 · Título único: no aparecen los demás títulos del modal.
        $this->assertStringContainsString('Nueva planificación semanal', $html);
        $this->assertStringNotContainsString('Editar planificación', $html);
        $this->assertStringNotContainsString('Detalle de la planificación', $html);

        // 2 · La fecha de culminación lleva `min` (atributo real, no `@min` roto).
        $this->assertMatchesRegularExpression('/id="p-ffinal"[^>]*min="/', $html);

        // 3 · El select de grado solo trae los grados con carga del docente.
        //     (En el fixture el docente tiene carga en un único grado de pestudio 6,
        //     así que no debe ofrecer un 3.er grupo sin carga.)
        $this->assertStringContainsString('value="'.$gradoId.'"', $html);
        $this->assertStringNotContainsString('>3ER GRUPO<', $html);
    }

    // ─── Cabecera: guardar y validar ─────────────────────────────

    /** @test */
    public function guarda_un_plan_semanal_nuevo_con_el_profesor_autenticado(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->set($this->planForm())
            ->call('save');

        $component->assertHasNoErrors();

        $plan = Eiplanningwk::where('profesor_id', $docente['profesor_id'])->first();

        $this->assertNotNull($plan, 'El plan no se guardó.');
        // `finicial` tiene cast de fecha en el modelo: se compara como fecha.
        $this->assertSame('2026-10-05', $plan->finicial->format('Y-m-d'));
        $this->assertSame(1, (int) $plan->tiempo_ejecucion);
        $this->assertFalse($component->get('showModal'), 'El modal debe cerrarse tras guardar.');
    }

    /** @test */
    public function rechaza_una_fecha_de_culminacion_anterior_a_la_de_inicio(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->set($this->planForm())
            ->set('eiplanningwk.ffinal', '2026-10-01')
            ->call('save');

        // R2 del blueprint: la regla documentada SÍ se adoptó en F2.
        $component->assertHasErrors('eiplanningwk.ffinal');

        $this->assertSame(
            0,
            Eiplanningwk::where('profesor_id', $docente['profesor_id'])->count(),
            'No debe persistirse nada cuando la validación falla.'
        );
    }

    /** @test */
    public function exige_un_diagnostico_minimo_de_10_caracteres(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->set($this->planForm())
            // R1: el runtime legacy era min:10 (el "≥50" documentado NO bloquea).
            ->set('eiplanningwk.diagnostico', 'corto')
            ->call('save');

        $component->assertHasErrors('eiplanningwk.diagnostico');
    }

    /** @test */
    public function un_plan_no_puede_abrirse_desde_otro_docente(): void
    {
        $dueno = $this->makeDocenteInicial();
        $invasor = $this->makeDocenteInicial();

        $plan = $this->makePlan($dueno['profesor_id']);

        // `findPlan()` filtra por `profesor_id` autenticado → 404, no un 500
        // ni la carga silenciosa del plan ajeno. (Nivel componente: aquí no
        // corre el middleware, por eso bastan usuarios admin.)
        Livewire::actingAs($invasor['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->assertStatus(404);
    }

    // ─── Wizard de estrategias (5 × 10) ──────────────────────────

    /** @test */
    public function crea_la_rejilla_completa_de_50_celdas_vacias(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class);

        $estrategias = $component->get('strategies');

        $this->assertCount(5, $estrategias, 'Debe haber 5 días.');
        $this->assertCount(10, $estrategias['lunes'], 'Debe haber 10 momentos por día.');

        // La rejilla se indexa 0..9 (los nombres de momento contienen espacios
        // y dos puntos, que romperían `wire:model`), y el índice traduce a la
        // constante del modelo en ese mismo orden: el wizard recorre los
        // momentos EN SU ORDEN REAL, no alfabético.
        $this->assertSame(
            range(0, count(Eiplanningwstrategy::LIST_MOMENT) - 1),
            array_keys($estrategias['lunes']),
            'La rejilla debe indexarse 0..9.'
        );
        $this->assertSame(
            array_values(Eiplanningwstrategy::LIST_MOMENT),
            array_values($component->get('moments')),
            'El orden de los momentos debe coincidir con la constante del modelo.'
        );
    }

    /** @test */
    public function la_estrategia_se_persiste_en_la_columna_lunes_del_legacy(): void
    {
        $docente = $this->makeDocenteInicial();

        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->call('setActiveDay', 'martes')
            ->call('setActiveMoment', 3)
            ->set('strategies.martes.3.estrategia', 'El docente modela la saludo del día.')
            ->call('saveCurrentStrategy');

        $component->assertHasNoErrors();

        $fila = Eiplanningwstrategy::where('eiplanningwk_id', $plan->id)
            ->where('day_of_week', 'martes')
            ->where('momento_rutina_diaria', 'Periodo: Planificación')
            ->first();

        $this->assertNotNull($fila, 'La estrategia no se guardó en la celda martes × Periodo: Planificación.');

        // Quirk D3: el texto SIEMPRE vive en `lunes`, con independencia del día.
        $this->assertSame('El docente modela la saludo del día.', $fila->getRawOriginal('lunes'));
        $this->assertSame('El docente modela la saludo del día.', $fila->estrategia);
    }

    /** @test */
    public function guarda_todas_las_celdas_que_tengan_texto(): void
    {
        $docente = $this->makeDocenteInicial();

        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->set('strategies.lunes.0.estrategia', 'Recibimiento con canción de bienvenida.')
            ->set('strategies.lunes.1.estrategia', 'Himno nacional y lectura del lema.')
            ->set('strategies.viernes.9.estrategia', 'Cierre en círculo y reflexión grupal.')
            // Celda vacía: no debe generar fila.
            ->set('strategies.martes.0.estrategia', '')
            ->call('saveStrategies');

        $this->assertSame(3, Eiplanningwstrategy::where('eiplanningwk_id', $plan->id)->count());
    }

    /** @test */
    public function la_estrategia_se_borra_con_la_firma_dia_indice_del_momento(): void
    {
        $docente = $this->makeDocenteInicial();

        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->set('strategies.jueves.7.estrategia', 'Trabajo por estaciones.')
            ->call('saveStrategies');

        $this->assertSame(1, Eiplanningwstrategy::where('eiplanningwk_id', $plan->id)->count());

        // REGRESIÓN del legacy: `delete-strategy` pasaba un id a un método que
        // esperaba (día, momento), dejando el borrado como no-op silencioso.
        $component
            ->call('deleteStrategy', 'jueves', 7)
            ->assertHasNoErrors();

        $this->assertSame(
            0,
            Eiplanningwstrategy::where('eiplanningwk_id', $plan->id)->count(),
            'El borrado debe ser efectivo (era un no-op en el legacy).'
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

        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $plan->id,
            'day_of_week' => 'viernes',
            'momento_rutina_diaria' => 'Periodo: Despedida',
            'lunes' => 'Puesta en común final.',
            'order' => 2,
        ]);

        // Cierra y reabre: los datos deben volver a SU celda.
        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'strategy', $plan->id)
            ->call('closeModal')
            ->call('openModal', 'strategy', $plan->id);

        $celda = $component->get('strategies')['viernes'][9];

        $this->assertSame('Puesta en común final.', $celda['estrategia']);
        $this->assertNotNull($celda['id']);
        $this->assertSame(2, $celda['order']);
    }

    // ─── Resúmenes por área ──────────────────────────────────────

    /** @test */
    public function guarda_un_resumen_por_area_con_los_cuatro_campos_obligatorios(): void
    {
        $docente = $this->makeDocenteInicial();

        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'summary', $plan->id)
            ->set('eiplanningwsummary.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('eiplanningwsummary.componente', 'Lenguaje')
            ->set('eiplanningwsummary.objetivo', 'Participa en conversaciones cotidianas.')
            ->set('eiplanningwsummary.aprendizaje_esperado', 'Conversa con sus pares sobre temas cotidianos.')
            ->set('eiplanningwsummary.indicadores', '1. Inicia la conversación. 2. Escucha al otro.')
            ->call('saveSummary');

        $component->assertHasNoErrors();

        $resumen = Eiplanningwsummary::where('eiplanningwk_id', $plan->id)->first();

        $this->assertNotNull($resumen);
        $this->assertSame('Lenguaje', $resumen->componente);
    }

    /** @test */
    public function el_resumen_exige_los_cuatro_campos_de_informacion_general(): void
    {
        $docente = $this->makeDocenteInicial();

        $plan = $this->makePlan($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'summary', $plan->id)
            ->set('eiplanningwsummary.pevaluacion_id', $docente['pevaluacion_id'])
            ->call('saveSummary');

        // R5 del blueprint.
        $component->assertHasErrors([
            'eiplanningwsummary.componente',
            'eiplanningwsummary.objetivo',
            'eiplanningwsummary.aprendizaje_esperado',
            'eiplanningwsummary.indicadores',
        ]);

        $this->assertSame(0, Eiplanningwsummary::where('eiplanningwk_id', $plan->id)->count());
    }

    // ─── Acceso ──────────────────────────────────────────────────

    /** @test */
    public function usuario_sin_permiso_de_inicial_recibe_403(): void
    {
        // Un docente legítimo pero de OTRO nivel (Bachillerato): tiene
        // `is_profesor`, no `is_admin` y no es de Inicial. NO se pasa
        // `is_inicial` a la factory porque en un entorno sin la migración esa
        // columna no existe y el INSERT revienta con "Unknown column".
        $sinPermiso = User::factory()->profesor()->create([
            'is_admin' => false,
        ]);

        // Capa 1 — el middleware `isInicial` del grupo de rutas: es lo que el
        // usuario ve al pegar la URL en el navegador.
        $this->actingAs($sinPermiso)
            ->get(route('inicials.home'))
            ->assertForbidden();

        // Capa 2 — el gate propio del componente (defensa en profundidad, se
        // ejecuta aunque alguien monte el componente fuera del grupo de rutas).
        Livewire::actingAs($sinPermiso)
            ->test(EiplanningwkComponent::class)
            ->assertStatus(403);
    }

    // ─── Formato imprimible ──────────────────────────────────────

    /** @test */
    public function el_formato_imprimible_se_renderiza_con_las_tres_secciones(): void
    {
        $docente = $this->makeDocenteInicial();

        $plan = $this->makePlan($docente['profesor_id']);

        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $plan->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Símbolo patrio en círculo.',
        ]);

        $response = $this->actingAs($docente['user'])
            ->get(route('inicials.eiplanningwks.format', $plan->id));

        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('Tabla Resumen', $html);
        $this->assertStringContainsString('Estrategias del Docente', $html);
        // El texto del docente se imprime escapado (XSS del legacy: `as_replace`
        // + `{!! !!}` inyectaba HTML cruto).
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

        // Dos resúmenes con línea de investigación DISTINTA. El legacy las
        // pintaba solo en la primera fila con `rowspan="{{ $rowspan }}"`, así que
        // el segundo salía con celdas vacías.
        foreach ([['Matemática', 'Línea UNO'], ['Lenguaje', 'Línea DOS']] as [$componente, $linea]) {
            Eiplanningwsummary::create([
                'eiplanningwk_id' => $plan->id,
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
            ->get(route('inicials.eiplanningwks.format', $plan->id))
            ->assertOk()
            ->getContent();

        // Ambas filas con sus datos: si la asignación del array se rompe, la
        // tabla cae al `@empty` y "No hay datos" lo delata.
        $this->assertStringContainsString('Línea UNO', $html);
        $this->assertStringContainsString('Línea DOS', $html);
        $this->assertStringContainsString('Énfasis de Lenguaje', $html);
        $this->assertStringNotContainsString('No hay datos', $html);
    }

    /** @test */
    public function el_formato_imprimible_no_permite_ver_el_plan_de_otro_docente(): void
    {
        // Necesita docentes NO admin: si fueran admin, el chequeo de
        // propiedad se salta a propósito (un coordinador revisa planes ajenos).
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $otro = $this->makeDocenteInicial(isAdmin: false);

        $plan = $this->makePlan($dueno['profesor_id']);

        $this->actingAs($otro['user'])
            ->get(route('inicials.eiplanningwks.format', $plan->id))
            ->assertNotFound();
    }

    /** @test */
    public function el_modal_de_detalle_muestra_la_cabecera_del_plan(): void
    {
        // El partial `plan-details` SOLO se renderiza con `openModal('view')`.
        // Si ninguna test abre ese tipo de modal, el partial queda sin cubrir y
        // sus errores (relaciones inexistentes, variables.Fatalf) pasan
        // inadvertidos.
        $docente = $this->makeDocenteInicial();
        $plan = $this->makePlan($docente['profesor_id']);

        Livewire::actingAs($docente['user'])
            ->test(EiplanningwkComponent::class)
            ->call('openModal', 'view', $plan->id)
            ->assertOk()
            ->assertSee('Detalle')
            ->assertSee($plan->diagnostico);
    }
}
