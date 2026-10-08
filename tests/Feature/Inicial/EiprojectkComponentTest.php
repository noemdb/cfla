<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EiprojectkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eiprojectkstrategy;
use App\Models\app\Inicial\Eiprojectreview;
use App\Models\app\Inicial\Eiprojectsummary;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * F4 del módulo de Educación Inicial: CRUD del PROYECTO DE AULA.
 *
 * Blueprint: blueprint/inicial · fase F4.
 *
 * Cubre además lo que NO existe en la semanal ni en la quincenal:
 *  · el bloque de REVISIÓN (`eiprojectreviews`), cuarta hija del proyecto;
 *  · la columna `estrategias`, propia de `eiprojectsummaries`;
 *  · la ausencia de `eiprojectk_id` en la cabecera (el proyecto no apunta a
 *    ningún plan: son los planes los que lo referencian).
 *
 * @group inicial
 * @group inicial-f4
 */
class EiprojectkComponentTest extends TestCase
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
            'lastname' => 'Proyecto Test',
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
            'pensum_id' => $pensum->id,
        ];
    }

    /** Asigna al docente un pensum distinto al primero de su grado. */
    private function darPensumDistinto(array $docente): array
    {
        $gradoId = $this->gradoInicialId();

        $pensum = Pensum::where('grado_id', $gradoId)
            ->where('id', '!=', $docente['pensum_id'])
            ->first();

        if ($pensum) {
            DB::table('pevaluacions')->where('id', $docente['pevaluacion_id'])
                ->update(['pensum_id' => $pensum->id]);
            $docente['pensum_id'] = (int) $pensum->id;
        }

        return $docente;
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
     * Atributos válidos de un proyecto, con las CLAVES PLANAS de la tabla.
     *
     * ⚠️ No usar para `->set()` de Livewire: ahí hacen falta las rutas con punto.
     *
     * @return array<string, mixed>
     */
    private function projectAttributes(): array
    {
        $gradoId = $this->gradoInicialId();

        return [
            'grado_id' => $gradoId,
            'seccion_id' => $this->seccionDe($gradoId),
            'finicial' => '2026-10-05',
            'ffinal' => '2026-11-27',
            'tiempo_ejecucion' => 8,
            'diagnostico' => 'El grupo muestra interés por los animales de la granja.',
            'observacion' => 'Se valora la participación del grupo.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function projectForm(): array
    {
        return collect($this->projectAttributes())
            ->mapWithKeys(fn ($value, $key) => ['eiprojectk.'.$key => $value])
            ->all();
    }

    /** Crea un proyecto directamente en BD (fixtures). */
    private function makeProject(int $profesorId): Eiprojectk
    {
        return Eiprojectk::create(
            array_merge($this->projectAttributes(), ['profesor_id' => $profesorId])
        );
    }

    /** Datos válidos de una revisión (los 6 campos que el legacy exigía). */
    private function reviewForm(): array
    {
        return [
            'posibles_temas_interes' => 'Los animales de la granja y su cuidado.',
            'eleccion_tema_nombre' => 'La granja Escuela: cuidamos y aprendemos',
            'que_sabe' => 'Reconocen animales domésticos y sus sonidos.',
            'que_desean_aprender' => 'Cómo se alimentan y dónde viven.',
            'que_necesitamos' => 'Materiales reciclados y visita al patio.',
            'quienes_nos_pueden_apoyar' => 'La maestra de educación física.',
            'estrategias' => 'Visita al huerto de la escuela.',
            'order' => 1,
        ];
    }

    /** Los tests de PROPIEDAD necesitan docentes NO admin + `is_inicial`. */
    private function saltarSiFaltaLaMigracion(): void
    {
        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped(
                'Requiere la columna users.is_inicial (migración add_is_inicial_to_users_table pendiente).'
            );
        }
    }

    // ─── Listado y cabecera ──────────────────────────────────────

    /** @test */
    public function el_listado_muestra_solo_los_proyectos_del_docente(): void
    {
        $docente = $this->makeDocenteInicial();
        $otro = $this->makeDocenteInicial();

        $propio = $this->makeProject($docente['profesor_id']);
        $ajeno = $this->makeProject($otro['profesor_id']);

        $ids = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->viewData('eiprojectks')
            ->pluck('id')
            ->all();

        $this->assertContains($propio->id, $ids);
        $this->assertNotContains($ajeno->id, $ids, 'Un docente no debe ver el proyecto de otro.');
    }

    // ─── Área de aprendizaje opcional (pensum_id) ──────────────

    /** @test */
    public function guarda_un_proyecto_con_el_area_de_aprendizaje_del_docente(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->set($this->projectForm())
            ->set('eiprojectk.pensum_id', $docente['pensum_id'])
            ->call('save');

        $component->assertHasNoErrors();

        $proyecto = Eiprojectk::where('profesor_id', $docente['profesor_id'])->first();

        $this->assertNotNull($proyecto);
        $this->assertSame($docente['pensum_id'], (int) $proyecto->pensum_id);
    }

    /** @test */
    public function rechaza_un_area_de_aprendizaje_de_otro_docente(): void
    {
        $docente = $this->makeDocenteInicial();
        $otro = $this->darPensumDistinto($this->makeDocenteInicial(isAdmin: false));

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->set($this->projectForm())
            ->set('eiprojectk.pensum_id', $otro['pensum_id'])
            ->call('save');

        $component->assertHasErrors('eiprojectk.pensum_id');
        $this->assertSame(0, Eiprojectk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function el_filtro_de_area_acota_el_listado_al_pensum_elegido(): void
    {
        $docente = $this->makeDocenteInicial();

        $conArea = $this->makeProject($docente['profesor_id']);
        $conArea->update(['pensum_id' => $docente['pensum_id']]);
        $this->makeProject($docente['profesor_id']);

        $ids = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->set('filterPensum', $docente['pensum_id'])
            ->viewData('eiprojectks')
            ->pluck('id')
            ->all();

        $this->assertSame([$conArea->id], $ids);
    }

    /** @test */
    public function guarda_un_proyecto_nuevo_con_el_profesor_autenticado(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->set($this->projectForm())
            ->call('save');

        $component->assertHasNoErrors();

        $project = Eiprojectk::where('profesor_id', $docente['profesor_id'])->first();

        $this->assertNotNull($project, 'El proyecto no se guardó.');
        $this->assertSame('2026-10-05', $project->finicial->format('Y-m-d'));
        $this->assertSame(8, (int) $project->tiempo_ejecucion);
        $this->assertFalse($component->get('showModal'), 'El modal debe cerrarse tras guardar.');
    }

    /** @test */
    public function rechaza_una_fecha_de_culminacion_anterior_a_la_de_inicio(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->set($this->projectForm())
            ->set('eiprojectk.ffinal', '2026-10-01')
            ->call('save');

        $component->assertHasErrors('eiprojectk.ffinal');

        $this->assertSame(0, Eiprojectk::where('profesor_id', $docente['profesor_id'])->count());
    }

    /** @test */
    public function un_proyecto_no_puede_abrirse_ni_borrarse_desde_otro_docente(): void
    {
        $dueno = $this->makeDocenteInicial();
        $invasor = $this->makeDocenteInicial();

        $project = $this->makeProject($dueno['profesor_id']);

        Livewire::actingAs($invasor['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'strategy', $project->id)
            ->assertStatus(404);

        Livewire::actingAs($invasor['user'])
            ->test(EiprojectkComponent::class)
            ->call('deleteProject', $project->id)
            ->assertStatus(404);

        $this->assertNotNull(Eiprojectk::find($project->id), 'El proyecto ajeno no debe borrarse.');
    }

    // ─── Revisión (bloque propio del proyecto) ────────────────────

    /** @test */
    public function guarda_una_revision_con_los_seis_campos_obligatorios(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'review', $project->id);

        foreach ($this->reviewForm() as $campo => $valor) {
            $component->set('eiprojectreview.'.$campo, $valor);
        }

        $component->call('saveReview')->assertHasNoErrors();

        $review = Eiprojectreview::where('eiprojectk_id', $project->id)->first();

        $this->assertNotNull($review);
        $this->assertSame($this->reviewForm()['que_sabe'], $review->que_sabe);
        $this->assertSame($this->reviewForm()['estrategias'], $review->estrategias);
    }

    /** @test */
    public function la_revision_exige_los_seis_campos(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'review', $project->id)
            ->call('saveReview');

        // El legacy exigía los 6 en `saveReview()`, sin la discrepancia que hubo
        // con `diagnostico` (min:50 documentado vs min:10 en runtime).
        $component->assertHasErrors([
            'eiprojectreview.posibles_temas_interes',
            'eiprojectreview.eleccion_tema_nombre',
            'eiprojectreview.que_sabe',
            'eiprojectreview.que_desean_aprender',
            'eiprojectreview.que_necesitamos',
            'eiprojectreview.quienes_nos_pueden_apoyar',
        ]);

        $this->assertSame(0, Eiprojectreview::where('eiprojectk_id', $project->id)->count());
    }

    /** @test */
    public function la_revision_guardada_reabre_para_editarse(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        $review = Eiprojectreview::create(array_merge(
            ['eiprojectk_id' => $project->id],
            $this->reviewForm()
        ));

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'edit-review', $review->id);

        $this->assertSame($review->id, $component->get('eiprojectreview_id'));
        $this->assertSame($review->que_sabe, $component->get('eiprojectreview')['que_sabe']);
    }

    /** @test */
    public function la_revision_de_otro_docente_no_puede_abrirse_ni_borrarse(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $invasor = $this->makeDocenteInicial(isAdmin: false);

        $reviewAjena = Eiprojectreview::create(array_merge(
            ['eiprojectk_id' => $this->makeProject($dueno['profesor_id'])->id],
            $this->reviewForm()
        ));

        $projectPropio = $this->makeProject($invasor['profesor_id']);

        Livewire::actingAs($invasor['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'edit-review', $reviewAjena->id)
            ->assertStatus(404);

        Livewire::actingAs($invasor['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'review', $projectPropio->id)
            ->call('deleteReview', $reviewAjena->id)
            ->assertStatus(404);

        $this->assertNotNull(Eiprojectreview::find($reviewAjena->id));
    }

    // ─── Resúmenes (con la columna `estrategias`) ─────────────────

    /** @test */
    public function guarda_un_resumen_incluyendo_la_columna_estrategias(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'summary', $project->id)
            ->set('eiprojectsummary.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('eiprojectsummary.componente', 'Ciencia social')
            ->set('eiprojectsummary.objetivo', 'Reconoce la granja como espacio de trabajo.')
            ->set('eiprojectsummary.aprendizaje_esperado', 'Identifica animales de la granja y su función.')
            ->set('eiprojectsummary.indicadores', '1. Nombra. 2. Relaciona.')
            ->set('eiprojectsummary.estrategias', 'Visita al huerto escolar.')
            ->call('saveSummary');

        $component->assertHasNoErrors();

        $resumen = Eiprojectsummary::where('eiprojectk_id', $project->id)->first();

        $this->assertNotNull($resumen);
        // Columna propia de eiprojectsummaries: no existe en los otros documentos.
        $this->assertSame('Visita al huerto escolar.', $resumen->estrategias);
    }

    /** @test */
    public function el_resumen_exige_los_cuatro_campos_de_informacion_general(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'summary', $project->id)
            ->set('eiprojectsummary.pevaluacion_id', $docente['pevaluacion_id'])
            ->call('saveSummary');

        $component->assertHasErrors([
            'eiprojectsummary.componente',
            'eiprojectsummary.objetivo',
            'eiprojectsummary.aprendizaje_esperado',
            'eiprojectsummary.indicadores',
        ]);

        $this->assertSame(0, Eiprojectsummary::where('eiprojectk_id', $project->id)->count());
    }

    /** @test */
    public function el_resumen_de_otro_docente_no_puede_abrirse(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $invasor = $this->makeDocenteInicial(isAdmin: false);

        $resumenAjeno = Eiprojectsummary::create([
            'eiprojectk_id' => $this->makeProject($dueno['profesor_id'])->id,
            'pevaluacion_id' => $dueno['pevaluacion_id'],
            'componente' => 'Matemática',
            'objetivo' => 'Cuenta animales.',
            'aprendizaje_esperado' => 'Cuenta hasta cinco.',
            'indicadores' => '1. Cuenta.',
        ]);

        Livewire::actingAs($invasor['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'edit-summary', $resumenAjeno->id)
            ->assertStatus(404);
    }

    // ─── Estrategias ─────────────────────────────────────────────

    /** @test */
    public function la_estrategia_se_persiste_en_la_columna_lunes_del_legacy(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        // El momento esperado se deriva de la constante del modelo en vez de
        // escribirlo a mano: la rejilla se indexa 0..9 y un literal fijo
        // desincroniza el test en cuanto se inserte un momento en medio.
        $indice = 5;
        $momento = array_keys(Eiprojectkstrategy::LIST_MOMENT)[$indice];

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'strategy', $project->id)
            ->call('setActiveDay', 'miercoles')
            ->call('setActiveMoment', $indice)
            ->set("strategies.miercoles.{$indice}.estrategia", 'Salimos al huerto a sembrar.')
            ->call('saveCurrentStrategy');

        $component->assertHasNoErrors();

        $fila = Eiprojectkstrategy::where('eiprojectk_id', $project->id)
            ->where('day_of_week', 'miercoles')
            ->where('momento_rutina_diaria', $momento)
            ->first();

        $this->assertNotNull($fila, "La estrategia no se guardó en la celda miércoles × {$momento}.");

        // Quirk D3: el texto SIEMPRE vive en `lunes`, con independencia del día.
        $this->assertSame('Salimos al huerto a sembrar.', $fila->getRawOriginal('lunes'));
        $this->assertNull($fila->miercoles, 'Las columnas martes…viernes son residuo del esquema legacy.');
    }

    /** @test */
    public function la_estrategia_se_borra_con_la_firma_dia_indice_del_momento(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'strategy', $project->id)
            ->set('strategies.jueves.7.estrategia', 'Pintamos el corral.')
            ->call('saveStrategies');

        $this->assertSame(1, Eiprojectkstrategy::where('eiprojectk_id', $project->id)->count());

        $component->call('deleteStrategy', 'jueves', 7)->assertHasNoErrors();

        $this->assertSame(0, Eiprojectkstrategy::where('eiprojectk_id', $project->id)->count());

        $celda = $component->get('strategies')['jueves'][7];
        $this->assertNull($celda['id']);
        $this->assertSame('', $celda['estrategia']);
    }

    /** @test */
    public function abrir_una_celda_concreta_enfoca_el_momento_indicado(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openStrategyCell', $project->id, 'viernes', 'Periodo: Despedida');

        $this->assertSame('viernes', $component->get('activeDay'));
        $this->assertSame(9, $component->get('activeMomentIndex'));
    }

    // ─── Borrado en cascada ──────────────────────────────────────

    /** @test */
    public function borrar_el_proyecto_arrastra_revisiones_resumenes_y_estrategias(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        Eiprojectkstrategy::create([
            'eiprojectk_id' => $project->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Cancion de bienvenida.',
        ]);

        Eiprojectsummary::create([
            'eiprojectk_id' => $project->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'componente' => 'Lenguaje',
            'objetivo' => 'Escucha y relata.',
            'aprendizaje_esperado' => 'Relata un cuento breve.',
            'indicadores' => '1. Escucha. 2. Relata.',
        ]);

        Eiprojectreview::create(array_merge(
            ['eiprojectk_id' => $project->id],
            $this->reviewForm()
        ));

        Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('deleteProject', $project->id)
            ->assertHasNoErrors();

        $this->assertNull(Eiprojectk::find($project->id));
        // Sin FK declarada hacia las hijas, el borrado debe ser explícito.
        $this->assertSame(0, Eiprojectkstrategy::where('eiprojectk_id', $project->id)->count());
        $this->assertSame(0, Eiprojectsummary::where('eiprojectk_id', $project->id)->count());
        $this->assertSame(0, Eiprojectreview::where('eiprojectk_id', $project->id)->count());
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
            ->test(EiprojectkComponent::class)
            ->assertStatus(403);
    }

    /** @test */
    public function un_docente_con_el_flag_entra_al_modulo_y_ve_su_listado(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = $this->makeDocenteInicial(isAdmin: false);
        $project = $this->makeProject($docente['profesor_id']);

        $this->assertFalse((bool) $docente['user']->is_admin, 'El fixture no debe ser admin.');
        $this->assertTrue($docente['user']->isInicial(), 'El fixture debe tener is_inicial = 1.');

        $this->actingAs($docente['user'])
            ->get(route('inicials.eiprojectks.index'))
            ->assertOk()
            ->assertSee($project->diagnostico);
    }

    // ─── Formato imprimible ──────────────────────────────────────

    /** @test */
    public function el_formato_imprimible_incluye_cabecera_revision_resumen_y_estrategias(): void
    {
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        Eiprojectreview::create(array_merge(
            ['eiprojectk_id' => $project->id],
            $this->reviewForm()
        ));

        Eiprojectsummary::create([
            'eiprojectk_id' => $project->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'componente' => 'Ciencia social',
            'objetivo' => 'Reconoce la granja.',
            'aprendizaje_esperado' => 'Identifica animales.',
            'indicadores' => '1. Nombra.',
            'linea_investigacion' => 'Línea de investigación del área',
            'enfasis_curriculares' => 'Énfasis del área',
            'estrategias' => 'Visita al huerto.',
        ]);

        Eiprojectkstrategy::create([
            'eiprojectk_id' => $project->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Cancion de la granja.',
        ]);

        $response = $this->actingAs($docente['user'])
            ->get(route('inicials.eiprojectks.format', $project->id));

        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('PROYECTO DE AULA', $html);
        $this->assertStringContainsString('Revisión del Proyecto', $html);
        $this->assertStringContainsString('Tabla Resumen', $html);
        $this->assertStringContainsString('Estrategias del Docente', $html);

        // Los cuatro bloques con datos reales, no solo los encabezados.
        $this->assertStringContainsString($this->reviewForm()['que_sabe'], $html);
        $this->assertStringContainsString('Visita al huerto.', $html);
        $this->assertStringContainsString('Cancion de la granja.', $html);

        // REGRESIÓN del `rowspan` del legacy: línea de investigación y énfasis
        // son columnas POR RESUMEN. Con dos resúmenes, el segundo imprimía
        // celdas vacías porque el legacy las pintaba solo en la primera fila.
        $this->assertStringContainsString('Línea de investigación del área', $html);
        $this->assertStringContainsString('Énfasis del área', $html);
    }

    /** @test */
    public function el_formato_imprimible_no_permite_ver_el_proyecto_de_otro_docente(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $otro = $this->makeDocenteInicial(isAdmin: false);

        $project = $this->makeProject($dueno['profesor_id']);

        $this->actingAs($otro['user'])
            ->get(route('inicials.eiprojectks.format', $project->id))
            ->assertNotFound();
    }

    /** @test */
    public function el_modulo_de_proyectos_arranca_desde_el_indice_sin_errores_de_vista(): void
    {
        $docente = $this->makeDocenteInicial();
        $this->makeProject($docente['profesor_id']);

        $this->actingAs($docente['user'])
            ->get(route('inicials.eiprojectks.index'))
            ->assertOk();
    }

    /** @test */
    public function el_modal_de_detalle_muestra_cabecera_revision_y_resumenes(): void
    {
        // El partial `plan-details` SOLO se renderiza con `openModal('view')`.
        // Es además el único sitio donde se pintan las revisiones, así que sin
        // esta cobertura el bloque entero queda sin ejecutar.
        $docente = $this->makeDocenteInicial();
        $project = $this->makeProject($docente['profesor_id']);

        Eiprojectreview::create(array_merge(
            ['eiprojectk_id' => $project->id],
            $this->reviewForm()
        ));

        Livewire::actingAs($docente['user'])
            ->test(EiprojectkComponent::class)
            ->call('openModal', 'view', $project->id)
            ->assertOk()
            ->assertSee('Detalle')
            ->assertSee($project->diagnostico)
            ->assertSee($this->reviewForm()['que_sabe']);
    }
}
