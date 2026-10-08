<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Livewire\Inicial\EifinalkComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Learner\Estudiant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * F3 del módulo de Educación Inicial: CRUD del INFORME FINAL (último documento).
 *
 * Blueprint: blueprint/inicial · F3 · patrón A.6.
 *
 * Es el único informe POR PERSONA, así que las tests fijan sus particularidades:
 *  · no hay `profesor_id`/`grado_id`/`seccion_id`/`lapso_id`: todo se hereda de la
 *    carga académica (`pevaluacion_id`), que es obligatoria;
 *  · la tabla de estudiantes NO se escribe a mano en ningún sitio: se resuelve
 *    con `Estudiant::getTable()`, porque el nombre del blueprint legacy
 *    (`estudiantes`) no es el de cfla;
 *  · las expectativas viven en el pivote `eifinalk_expectation` y se sincronizan
 *    (reemplazo total), no se adjuntan;
 *  · el tipo del informe (oficial / de componente) lo decide
 *    `pevaluacion.status_official` y cambia los campos del formulario.
 *
 * @group inicial
 * @group inicial-f3
 */
class EifinalkComponentTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Fixtures ─────────────────────────────────────────────────

    /**
     * @return array{user: User, profesor_id: int, pevaluacion_id: int, seccion_id: int, grado_id: int}
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
            'lastname' => 'Final Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = (int) Grado::where('pestudio_id', 6)->value('id');
        $pensum = Pensum::where('grado_id', $gradoId)->firstOrFail();
        $seccionId = $this->seccionActiva($gradoId);
        $lapsoId = (int) DB::table('lapsos')->value('id');

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'profesor_id' => $profesorId,
            'pensum_id' => $pensum->id,
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

    private function seccionActiva(int $gradoId): int
    {
        return (int) DB::table('seccions')
            ->where('grado_id', $gradoId)
            ->where('status_active', 'true')
            ->value('id');
    }

    /** Estudiante de la sección, con su inscripción (el vínculo de cfla). */
    private function makeEstudiante(int $seccionId, string $nombre = 'Estudiante'): Estudiant
    {
        // El nombre de la tabla se pide al modelo: en cfla no es el del blueprint
        // legacy, y aquí no tiene sentido repetirlo a mano.
        $tabla = (new Estudiant)->getTable();

        // `planpago_id` es NOT NULL sin default y con FK a `planpagos`; el plan
        // de pago se crea aquí porque la tabla tiene una sola fila real y
        // reutilizarla ataría el fixture a ese registro.
        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Plan fixture '.uniqid(),
            'description' => 'Fixture de pruebas',
            'observations' => 'Fixture de pruebas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // `representant_id` es NOT NULL con un default centinela (1111111111) que
        // NO existe en `representants`: cualquier INSERT que lo omita revienta
        // por FK. Mina del esquema de cfla, no del módulo; el fixture usa un
        // representante real en vez de propagarla.
        $representantId = (int) DB::table('representants')->min('id');
        // La tabla se llama `programacions`, no `programaciones` (cfla nombra en
        // catalán); el nombre sale de information_schema, no de memoria.
        $programacionId = (int) DB::table('programacions')->min('id');

        $id = DB::table($tabla)->insertGetId([
            'ci_estudiant' => (string) random_int(10_000_000, 99_999_999),
            'planpago_id' => $planpagoId,
            'representant_id' => $representantId,
            'lastname' => 'Apellido',
            'name' => $nombre,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // `tipo_id` y `programacion_id` son NOT NULL con FK a tablas cuya fila
        // por defecto (0 / centinela) no existe: se reutiliza una fila real en
        // vez de depender de esos defaults.
        DB::table('inscripcions')->insert([
            'tipo_id' => DB::table('tinscripcions')->value('id'),
            'seccion_id' => $seccionId,
            'estudiant_id' => $id,
            'programacion_id' => $programacionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Estudiant::find($id);
    }

    /**
     * Área + 2 expectativas en el grado. Requiere el seeder de F0 (`EILearningSeeder`)
     * o filas propias; este test las crea para no depender de ese paso.
     */
    private function makeAreaConExpectativas(int $gradoId, int $cuantas = 2): array
    {
        $areaId = DB::table('eilearningareas')->insertGetId([
            'name' => 'Área de prueba '.uniqid(),
            'description' => 'Descripción del área de prueba',
            'grado_id' => $gradoId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ids = [];
        for ($i = 0; $i < $cuantas; $i++) {
            // `eilearningexpectations` NO tiene columna `name`: su texto vive en
            // `description` (NOT NULL). Escribir `name` falla en runtime.
            $ids[] = DB::table('eilearningexpectations')->insertGetId([
                'eilearningarea_id' => $areaId,
                'description' => "Expectativa {$i}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return [$areaId, $ids];
    }

    /** @return array<string, mixed> */
    private function reportAttributes(int $pevaluacionId, int $estudiantId, int $order = 1): array
    {
        return [
            'order' => $order,
            'pevaluacion_id' => $pevaluacionId,
            'estudiant_id' => $estudiantId,
            'title' => 'Informe final del primer momento',
            'context_group' => 'El grupo llega con buena convivencia.',
            'achievements' => 'Leyeron y comentarón el cuento.',
        ];
    }

    /** @return array<string, mixed> */
    private function reportForm(int $pevaluacionId, int $estudiantId): array
    {
        return collect($this->reportAttributes($pevaluacionId, $estudiantId))
            ->mapWithKeys(fn ($value, $key) => ['eifinalk.'.$key => $value])
            ->all();
    }

    private function saltarSiFaltaLaMigracion(): void
    {
        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped('Requiere la columna users.is_inicial.');
        }
    }

    // ─── Listado ──────────────────────────────────────────────────

    /** @test */
    public function el_listado_muestra_solo_los_informes_del_docente(): void
    {
        $docente = $this->makeDocenteInicial();
        $otro = $this->makeDocenteInicial();

        $propio = Eifinalk::create($this->reportAttributes(
            $docente['pevaluacion_id'],
            $this->makeEstudiante($docente['seccion_id'])->id
        ));

        $ajeno = Eifinalk::create($this->reportAttributes(
            $otro['pevaluacion_id'],
            $this->makeEstudiante($otro['seccion_id'])->id
        ));

        $ids = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->viewData('eifinalks')
            ->pluck('id')
            ->all();

        $this->assertContains($propio->id, $ids);
        $this->assertNotContains($ajeno->id, $ids, 'Un docente no debe ver informes de otro.');
    }

    /** @test */
    public function los_informes_se_ordenan_por_el_campo_order(): void
    {
        $docente = $this->makeDocenteInicial();
        $seccion = $docente['seccion_id'];

        $segundo = Eifinalk::create($this->reportAttributes(
            $docente['pevaluacion_id'],
            $this->makeEstudiante($seccion, 'B')->id,
            order: 2
        ));
        $primero = Eifinalk::create($this->reportAttributes(
            $docente['pevaluacion_id'],
            $this->makeEstudiante($seccion, 'A')->id,
            order: 1
        ));

        $ids = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->viewData('eifinalks')
            ->pluck('id')
            ->all();

        // `order` primero: es lo que gobierna la impresión del boletín.
        $this->assertSame([$primero->id, $segundo->id], $ids);
    }

    // ─── Alta y validación ────────────────────────────────────────

    /** @test */
    public function guarda_un_informe_final(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set($this->reportForm($docente['pevaluacion_id'], $estudiante->id))
            ->call('save');

        $component->assertHasNoErrors();

        $report = Eifinalk::where('estudiant_id', $estudiante->id)->first();

        $this->assertNotNull($report, 'El informe no se guardó.');
        $this->assertSame('Informe final del primer momento', $report->title);
        $this->assertSame(1, (int) $report->order);
        $this->assertFalse($component->get('showModal'));
    }

    /** @test */
    public function exige_carga_academica_estudiante_orden_y_titulo(): void
    {
        $docente = $this->makeDocenteInicial();

        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->call('save');

        // La carga es la que ancla grado, sección y lapso, así que sin ella el
        // informe no existe.
        //
        // `order` NO se espera aquí aunque la regla lo exija: el formulario lo
        // prellena con 1 (y `nuevoInformePara()` calcula el siguiente), así que
        // exigirlo obligaría a la docente a escribir un número que el sistema ya
        // sabe. La regla sigue ahí como red de seguridad si el campo llega vacío.
        $component->assertHasErrors([
            'eifinalk.pevaluacion_id',
            'eifinalk.estudiant_id',
            'eifinalk.title',
        ]);

        $this->assertSame(0, Eifinalk::count());
    }

    /** @test */
    public function los_campos_de_texto_son_opcionales(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);

        // El docente redacta a lo largo del periodo: lo que aún no tiene
        // sentido no debe bloquear el guardado.
        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set([
                'eifinalk.order' => 1,
                'eifinalk.pevaluacion_id' => $docente['pevaluacion_id'],
                'eifinalk.estudiant_id' => $estudiante->id,
                'eifinalk.title' => 'Informe en construcción',
            ])
            ->call('save');

        $component->assertHasNoErrors();

        $report = Eifinalk::where('estudiant_id', $estudiante->id)->first();

        $this->assertNotNull($report);
        $this->assertNull($report->conclusions, 'Un campo opcional vacío se guarda como NULL, no como ""');
    }

    // ─── Pivote de expectativas ───────────────────────────────────

    /** @test */
    public function sincroniza_las_expectativas_marcadas_en_el_pivote(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);
        [, $expectativas] = $this->makeAreaConExpectativas($docente['grado_id']);

        Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set($this->reportForm($docente['pevaluacion_id'], $estudiante->id))
            ->set('eifinalk.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('selected_expectations', [$expectativas[0], $expectativas[1]])
            ->call('save')
            ->assertHasNoErrors();

        $report = Eifinalk::where('estudiant_id', $estudiante->id)->firstOrFail();

        $this->assertCount(2, $report->expectations);

        // El pivote desnormaliza área y pevaluación para poder filtrar el
        // boletín sin joins extra.
        $pivot = DB::table('eifinalk_expectation')->where('eifinalk_id', $report->id)->first();

        $this->assertSame($docente['pevaluacion_id'], (int) $pivot->pevaluacion_id);
        $this->assertNotNull($pivot->eilearningarea_id);
    }

    /** @test */
    public function el_sincronizado_reemplaza_las_expectativas_anteriores(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);
        [, $expectativas] = $this->makeAreaConExpectativas($docente['grado_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set($this->reportForm($docente['pevaluacion_id'], $estudiante->id))
            ->set('eifinalk.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('selected_expectations', $expectativas)
            ->call('save')
            ->assertHasNoErrors();

        $report = Eifinalk::where('estudiant_id', $estudiante->id)->firstOrFail();
        $this->assertCount(2, $report->expectations);

        // Se desmarca una y se guarda: con `attach()` se acumularían.
        $component
            ->call('openModal', $report->id)
            ->set('selected_expectations', [$expectativas[0]])
            ->call('update')
            ->assertHasNoErrors();

        $this->assertSame(
            1,
            DB::table('eifinalk_expectation')->where('eifinalk_id', $report->id)->count()
        );
    }

    /** @test */
    public function descarta_las_expectativas_de_otros_grados(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);
        [$areaBuena, $buenas] = $this->makeAreaConExpectativas($docente['grado_id']);

        // Área de OTRO grado: el legacy la adjuntaba igual porque solo hacía
        // `find()` por id, sin comprobar que perteneciera al grado del informe.
        $otroGrado = (int) Grado::where('pestudio_id', 6)->where('id', '!=', $docente['grado_id'])->value('id');
        [$areaAjena, $ajenas] = $this->makeAreaConExpectativas($otroGrado);

        Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set($this->reportForm($docente['pevaluacion_id'], $estudiante->id))
            ->set('eifinalk.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('selected_expectations', [$buenas[0], $ajenas[0]])
            ->call('save')
            ->assertHasNoErrors();

        $report = Eifinalk::where('estudiant_id', $estudiante->id)->firstOrFail();

        $vinculadas = $report->expectations->pluck('id')->all();

        $this->assertContains($buenas[0], $vinculadas);
        $this->assertNotContains(
            $ajenas[0],
            $vinculadas,
            'Una expectativa de otro grado no debe entrar en el informe.'
        );
    }

    /** @test */
    public function el_acordeon_carga_las_areas_del_grado_de_la_carga(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);
        [$areaId, $expectativas] = $this->makeAreaConExpectativas($docente['grado_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set($this->reportForm($docente['pevaluacion_id'], $estudiante->id))
            ->set('eifinalk.pevaluacion_id', $docente['pevaluacion_id']);

        $areas = $component->get('learningAreas');

        $this->assertTrue(
            $areas->contains('id', $areaId),
            'El acordeón debe traer las áreas del grado de la carga elegida.'
        );
        $this->assertCount(2, $areas->firstWhere('id', $areaId)->expectations);
    }

    /** @test */
    public function cambiar_de_carga_limpia_las_expectativas_marcadas(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);
        [, $expectativas] = $this->makeAreaConExpectativas($docente['grado_id']);

        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set('selected_expectations', [$expectativas[0]]);

        // Cambiar de carga deja obsoletas las expectativas del grado anterior.
        $component->set('eifinalk.pevaluacion_id', $docente['pevaluacion_id']);

        $this->assertSame(
            [],
            $component->get('selected_expectations'),
            'Las expectativas del grado anterior no deben sobrevivir al cambio de carga.'
        );
    }

    // ─── Campos condicionales por status_official ─────────────────

    /** @test */
    public function una_carga_oficial_muestra_los_campos_de_informe_oficial(): void
    {
        $docente = $this->makeDocenteInicial(); // status_official = 1
        $estudiante = $this->makeEstudiante($docente['seccion_id']);

        // El modal debe estar ABIERTO: los campos condicionales solo se
        // renderizan cuando `$showModal` es true.
        $html = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->call('openModal')
            ->set($this->reportForm($docente['pevaluacion_id'], $estudiante->id))
            ->set('eifinalk.pevaluacion_id', $docente['pevaluacion_id'])
            ->html();

        $this->assertStringContainsString('Informe OFICIAL', $html);
        $this->assertStringContainsString('Observaciones individuales', $html);
        $this->assertStringContainsString('Aprendizajes esperados', $html);
        // El bloque de componente no se pide en un informe oficial.
        $this->assertStringNotContainsString('Observación del especialista', $html);
    }

    /** @test */
    public function una_carga_de_componente_muestra_la_observacion_del_especialista(): void
    {
        $docente = $this->makeDocenteInicial();

        // La misma carga, ahora como informe de COMPONENTE.
        DB::table('pevaluacions')->where('id', $docente['pevaluacion_id'])->update(['status_official' => 0]);

        $estudiante = $this->makeEstudiante($docente['seccion_id']);

        // El modal debe estar ABIERTO: los campos condicionales solo se
        // renderizan cuando `$showModal` es true.
        $html = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->call('openModal')
            ->set($this->reportForm($docente['pevaluacion_id'], $estudiante->id))
            ->set('eifinalk.pevaluacion_id', $docente['pevaluacion_id'])
            ->html();

        $this->assertStringContainsString('Informe de COMPONENTE', $html);
        $this->assertStringContainsString('Observación del especialista', $html);
        $this->assertStringNotContainsString('Observaciones individuales', $html);
    }

    // ─── Pestaña de estudiantes ───────────────────────────────────

    /** @test */
    public function la_pestana_de_estudiantes_lista_los_matriculados_de_la_seccion(): void
    {
        $docente = $this->makeDocenteInicial();

        $ana = $this->makeEstudiante($docente['seccion_id'], 'Ana');
        $luis = $this->makeEstudiante($docente['seccion_id'], 'Luis');

        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set('filterPevaluacionEstudiantes', $docente['pevaluacion_id']);

        $ids = $component->get('estudiantes')->pluck('id');

        $this->assertTrue($ids->contains($ana->id));
        $this->assertTrue($ids->contains($luis->id));
    }

    /** @test */
    public function la_insignia_de_informa_que_ya_existe_y_el_alta_lo_fija(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id'], 'Ana');

        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            // La insignia se pinta en la pestaña de estudiantes: sin cambiar de
            // pestaña el HTML es la lista de informes y no aparece.
            ->call('setActiveTab', 'estudiantesList')
            ->set('filterPevaluacionEstudiantes', $docente['pevaluacion_id']);

        // Se comprueba por lo que ve la docente, no por el retorno del método:
        // `tieneInforme()` es un helper de la insignia.
        $component->assertSee('○ Sin informe');

        Eifinalk::create($this->reportAttributes($docente['pevaluacion_id'], $estudiante->id));

        // El HTML ya renderizado no se recalcula solo: hay que pedir un refresco
        // para que la insignia vuelva a evaluarse contra la BD.
        $component->call('$refresh')->assertSee('● Informe creado');
    }

    /** @test */
    public function crear_informe_desde_el_estudiante_preselecciona_carga_estudiante_y_orden(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);

        // Ya hay un informe en esa carga: el siguiente debe ir detrás.
        Eifinalk::create($this->reportAttributes($docente['pevaluacion_id'], $estudiante->id, order: 1));

        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set('filterPevaluacionEstudiantes', $docente['pevaluacion_id'])
            ->call('nuevoInformePara', $estudiante->id);

        $form = $component->get('eifinalk');

        $this->assertSame($docente['pevaluacion_id'], $form['pevaluacion_id']);
        $this->assertSame($estudiante->id, $form['estudiant_id']);
        $this->assertSame(2, $form['order']);
        $this->assertTrue($component->get('showModal'));
    }

    /** @test */
    public function el_estudiante_preseleccionado_no_se_pierde_al_cargar_el_acordeon(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);
        $this->makeAreaConExpectativas($docente['grado_id']);

        // `nuevoInformePara()` recarga las áreas (que limpia las expectativas
        // marcadas) DESPUÉS de fijar el estudiante: el orden importa.
        $component = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set('filterPevaluacionEstudiantes', $docente['pevaluacion_id'])
            ->call('nuevoInformePara', $estudiante->id);

        $this->assertSame(
            $estudiante->id,
            $component->get('eifinalk')['estudiant_id'],
            'El estudiante preseleccionado debe sobrevivir a la carga del acordeón.'
        );
    }

    // ─── Borrado ──────────────────────────────────────────────────

    /** @test */
    public function eliminar_un_informe_limpia_su_pivote(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id']);
        [, $expectativas] = $this->makeAreaConExpectativas($docente['grado_id']);

        Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->set($this->reportForm($docente['pevaluacion_id'], $estudiante->id))
            ->set('eifinalk.pevaluacion_id', $docente['pevaluacion_id'])
            ->set('selected_expectations', [$expectativas[0]])
            ->call('save')
            ->assertHasNoErrors();

        $report = Eifinalk::where('estudiant_id', $estudiante->id)->firstOrFail();

        Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->call('deleteReport', $report->id)
            ->assertHasNoErrors();

        $this->assertNull(Eifinalk::find($report->id));
        $this->assertSame(
            0,
            DB::table('eifinalk_expectation')->where('eifinalk_id', $report->id)->count(),
            'El pivote no debe quedar con filas de un informe inexistente.'
        );
    }

    // ─── Acceso ───────────────────────────────────────────────────

    /** @test */
    public function un_informe_de_otro_docente_no_puede_abrirse_ni_borrarse(): void
    {
        $dueno = $this->makeDocenteInicial();
        $invasor = $this->makeDocenteInicial();

        $ajeno = Eifinalk::create($this->reportAttributes(
            $dueno['pevaluacion_id'],
            $this->makeEstudiante($dueno['seccion_id'])->id
        ));

        // El legacy: `edit()` y `delete()` con `findOrFail($id)` a secas. El
        // listado sí filtraba, pero el modal no.
        Livewire::actingAs($invasor['user'])
            ->test(EifinalkComponent::class)
            ->call('openModal', $ajeno->id)
            ->assertStatus(404);

        Livewire::actingAs($invasor['user'])
            ->test(EifinalkComponent::class)
            ->call('deleteReport', $ajeno->id)
            ->assertStatus(404);

        $this->assertNotNull(Eifinalk::find($ajeno->id));
    }

    /** @test */
    public function usuario_sin_permiso_de_inicial_recibe_403(): void
    {
        $sinPermiso = User::factory()->profesor()->create(['is_admin' => false]);

        $this->actingAs($sinPermiso)
            ->get(route('inicials.home'))
            ->assertForbidden();

        Livewire::actingAs($sinPermiso)
            ->test(EifinalkComponent::class)
            ->assertStatus(403);
    }

    /** @test */
    public function un_docente_con_el_flag_entra_al_modulo_y_ve_su_listado(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = $this->makeDocenteInicial(isAdmin: false);
        $report = Eifinalk::create($this->reportAttributes(
            $docente['pevaluacion_id'],
            $this->makeEstudiante($docente['seccion_id'])->id
        ));

        // Primero el componente: si el listado del componente está bien y la
        // página no, el problema es de la ruta/vista, no de los datos.
        $this->assertCount(
            1,
            Livewire::actingAs($docente['user'])
                ->test(EifinalkComponent::class)
                ->viewData('eifinalks')
                ->all(),
            'El componente debe listar el informe del docente.'
        );

        $this->actingAs($docente['user'])
            ->get(route('inicials.eifinalks.index'))
            ->assertOk()
            ->assertSee($report->title)
            ->assertDontSee('Todavía no hay informes finales.');
    }

    // ─── Formato imprimible ───────────────────────────────────────

    /** @test */
    public function el_formato_imprimible_trae_cabecera_expectativas_por_area_y_bloques(): void
    {
        $docente = $this->makeDocenteInicial();
        $estudiante = $this->makeEstudiante($docente['seccion_id'], 'Ana');
        [$areaId, $expectativas] = $this->makeAreaConExpectativas($docente['grado_id']);

        $report = Eifinalk::create($this->reportAttributes($docente['pevaluacion_id'], $estudiante->id));

        $report->expectations()->sync([
            $expectativas[0] => [
                'eilearningarea_id' => $areaId,
                'pevaluacion_id' => $docente['pevaluacion_id'],
            ],
        ]);

        $html = $this->actingAs($docente['user'])
            ->get(route('inicials.eifinalks.format', $report->id))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('INFORME FINAL', $html);
        $this->assertStringContainsString('Expectativas de Aprendizaje', $html);
        $this->assertStringContainsString('Desarrollo', $html);

        // Datos reales, escapados.
        $this->assertStringContainsString('Apellido Ana', $html);
        $this->assertStringContainsString('Expectativa 0', $html);
        $this->assertStringContainsString($report->achievements, $html);

        // El tipo se imprime para que el papel archivado no haya que adivinarlo.
        $this->assertStringContainsString('OFICIAL', $html);
    }

    /** @test */
    public function el_formato_imprimible_no_permite_ver_el_informe_de_otro_docente(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $dueno = $this->makeDocenteInicial(isAdmin: false);
        $otro = $this->makeDocenteInicial(isAdmin: false);

        $report = Eifinalk::create($this->reportAttributes(
            $dueno['pevaluacion_id'],
            $this->makeEstudiante($dueno['seccion_id'])->id
        ));

        $this->actingAs($otro['user'])
            ->get(route('inicials.eifinalks.format', $report->id))
            ->assertNotFound();
    }

    /** @test */
    public function el_modulo_de_informes_finales_arranca_desde_el_indice(): void
    {
        $docente = $this->makeDocenteInicial();

        $this->actingAs($docente['user'])
            ->get(route('inicials.eifinalks.index'))
            ->assertOk()
            ->assertSee('Informes');
    }

    /** @test */
    public function el_acordeon_no_ofrece_areas_antes_de_elegir_carga(): void
    {
        $docente = $this->makeDocenteInicial();
        $this->makeAreaConExpectativas($docente['grado_id']);

        $html = Livewire::actingAs($docente['user'])
            ->test(EifinalkComponent::class)
            ->call('openModal')
            ->html();

        $this->assertStringContainsString(
            'Elige una carga académica para cargar las áreas',
            $html
        );
    }
}
