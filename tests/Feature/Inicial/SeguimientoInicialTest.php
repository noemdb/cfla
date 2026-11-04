<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicialManager;
use App\Livewire\Admin\Users\IndexComponent as UsersIndexComponent;
use App\Livewire\Inicial\SeguimientoInicialComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eievaluationp;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiplanningwsummary;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eiprojectkstrategy;
use App\Models\app\Inicial\Eispecialact;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Inicial\Eispecialstrategy;
use App\Models\app\Learner\Estudiant;
use App\Models\User;
use App\Notifications\InicialSeguimientoNotification;
use App\Services\Inicial\RegistroInicial;
use App\Services\Inicial\SeguimientoInicial;
use App\Services\MenuBuilder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Quinta perspectiva del módulo de Educación Inicial: SEGUIMIENTO.
 *
 * Blueprint: blueprint/inicial · doc 11 · §11.
 *
 * Cubre acceso, rutas de solo lectura, filtros, menú y administración del flag.
 * La regresión de las otras perspectivas vive en `PerspectivasInicialTest` y
 * `MenuInicialTest`; aquí solo se comprueba que el perfil nuevo no las cambia.
 *
 * @group inicial
 * @group inicial-seguimiento
 */
class SeguimientoInicialTest extends TestCase
{
    use DatabaseTransactions;

    /** Secuencia para que dos planes del mismo docente no choquen con la clave única. */
    private int $secuenciaPlan = 0;

    // ═══════════════════════════════════════════════════════════════
    //  FIXTURES
    // ═══════════════════════════════════════════════════════════════

    /** @param array<string, mixed> $overrides */
    private function makeUsuario(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'is_admin' => false,
            'is_diagnostic' => false,
            'is_planner' => false,
            'is_profesor' => false,
            'is_inicial' => false,
            'is_inicial_manager' => false,
            'is_leadership' => false,
            'is_coordinacion' => false,
            'is_director' => false,
            'is_student' => false,
        ], $overrides));
    }

    private function makeManager(): User
    {
        return $this->makeUsuario([
            'is_inicial_manager' => true,
            // Sin `is_inicial`: los dos perfiles son excluyentes.
            'is_inicial' => false,
        ]);
    }

    /**
     * @return array{user: User, profesor_id: int, pevaluacion_id: int, seccion_id: int, grado_id: int, lapso_id: int}
     */
    private function makeDocente(bool $isAdmin = false): array
    {
        $user = $this->makeUsuario([
            'is_profesor' => true,
            'is_admin' => $isAdmin,
            'is_inicial' => ! $isAdmin,
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-'.random_int(10_000_000, 99_999_999),
            'ci_profesor' => (string) random_int(10_000_000, 99_999_999),
            'name' => 'Docente',
            'lastname' => 'Seguimiento Test',
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
            'lapso_id' => $lapsoId,
        ];
    }

    private function seccionActiva(int $gradoId): int
    {
        return (int) DB::table('seccions')
            ->where('grado_id', $gradoId)
            ->where('status_active', 'true')
            ->value('id');
    }

    /** @return array<string, mixed> */
    private function planAttributes(int $profesorId, int $gradoId, int $seccionId, array $overrides = []): array
    {
        // La tabla tiene una clave única (profesor, nivel, sección, fecha
        // inicial): cada plan de un mismo docente necesita una fecha distinta.
        $diasAtras = 10 + (3 * $this->secuenciaPlan++);

        return array_merge([
            'profesor_id' => $profesorId,
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'finicial' => now()->subDays($diasAtras)->startOfDay(),
            'ffinal' => now()->subDay()->startOfDay(),
            'tiempo_ejecucion' => 1,
            'diagnostico' => 'Diagnóstico de seguimiento.',
            'observacion' => null,
        ], $overrides);
    }

    /**
     * Llena la rejilla (días × momentos) con texto en `lunes`, que es donde el
     * editor guarda SIEMPRE la estrategia; el día real vive en `day_of_week`.
     *
     * @return int celdas sembradas
     */
    private function sembrarSemanaCompleta(Eiplanningwk $plan): int
    {
        $orden = 0;

        foreach (array_keys(config('inicial.dias_semana')) as $dia) {
            foreach (array_keys(config('inicial.momentos_rutina_diaria')) as $momento) {
                Eiplanningwstrategy::create([
                    'eiplanningwk_id' => $plan->id,
                    'day_of_week' => $dia,
                    'momento_rutina_diaria' => $momento,
                    'lunes' => 'Estrategia '.$dia.' '.$momento,
                    'order' => $orden++,
                ]);
            }
        }

        return $orden;
    }

    private function crearResumenSemanal(Eiplanningwk $plan, int $pevaluacionId): void
    {
        Eiplanningwsummary::create([
            'eiplanningwk_id' => $plan->id,
            'pevaluacion_id' => $pevaluacionId,
            'componente' => 'Lenguaje',
            'objetivo' => 'Participar en conversaciones.',
            'aprendizaje_esperado' => 'Expresa ideas con claridad.',
            'indicadores' => 'Participa y escucha.',
        ]);
    }

    private function sembrarEstrategiasProyecto(Eiprojectk $plan): void
    {
        foreach (array_keys(config('inicial.dias_semana')) as $dia) {
            foreach (array_keys(config('inicial.momentos_rutina_diaria')) as $momento) {
                Eiprojectkstrategy::create([
                    'eiprojectk_id' => $plan->id,
                    'day_of_week' => $dia,
                    'momento_rutina_diaria' => $momento,
                    'lunes' => 'Estrategia '.$dia.' '.$momento,
                ]);
            }
        }
    }

    private function sembrarEstrategiasEspeciales(Eispecialk $plan, int $cantidad): void
    {
        $dias = array_keys(config('inicial.dias_semana'));
        $momentos = array_keys(config('inicial.momentos_rutina_diaria'));

        for ($indice = 0; $indice < $cantidad; $indice++) {
            Eispecialstrategy::create([
                'eispecialk_id' => $plan->id,
                'day_of_week' => $dias[intdiv($indice, count($momentos))],
                'momento_rutina_diaria' => $momentos[$indice % count($momentos)],
                'lunes' => 'Estrategia especial '.$indice,
                'order' => $indice,
            ]);
        }
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
        if (IsInicialManager::migracionPendiente()) {
            $this->markTestSkipped('Requiere la columna users.is_inicial_manager.');
        }
    }

    /**
     * Un documento de cada uno de los seis catálogos, del mismo docente.
     *
     * @return array<string, \Illuminate\Database\Eloquent\Model>
     */
    private function documentosDeLosSeis(): array
    {
        $docente = $this->makeDocente();
        $atributos = $this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        );

        $estudiante = $this->makeEstudiante($docente['seccion_id'], 'Ana');

        return [
            'eiplanningwks' => Eiplanningwk::create($atributos),
            'eiplanningbwks' => Eiplanningbwk::create($atributos),
            'eiprojectks' => Eiprojectk::create($atributos),
            'eispecialks' => Eispecialk::create(array_merge($atributos, [
                'justificacion' => 'Justificación del plan especial.',
            ])),
            'eievaluationks' => Eievaluationk::create(array_merge($atributos, [
                'lapso_id' => $docente['lapso_id'],
            ])),
            'eifinalks' => Eifinalk::create([
                'order' => 1,
                'pevaluacion_id' => $docente['pevaluacion_id'],
                'estudiant_id' => $estudiante->id,
                'title' => 'Informe final de seguimiento',
            ]),
        ];
    }

    // ═══════════════════════════════════════════════════════════════
    //  ACCESO
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function un_invitado_es_redirigido_al_login(): void
    {
        $this->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function un_docente_sin_el_flag_recibe_403(): void
    {
        $docente = $this->makeDocente();

        $this->actingAs($docente['user'])
            ->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertForbidden();
    }

    /** @test */
    public function un_admin_sin_el_flag_recibe_403(): void
    {
        $admin = $this->makeUsuario(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertForbidden();
    }

    /** @test */
    public function el_manager_entra_y_el_panel_redirige_a_la_planificacion_semanal(): void
    {
        $manager = $this->makeManager();

        $this->actingAs($manager)
            ->get(route('inicials.seguimiento.index'))
            ->assertRedirect(route('inicials.seguimiento.eiplanningwks.index'));

        $this->actingAs($manager)
            ->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertOk()
            ->assertSee('Seguimiento de Educación Inicial')
            ->assertSee('Solo lectura');
    }

    /** @test */
    public function los_filtros_se_muestran_en_un_panel_colapsable_y_conservan_su_distribucion(): void
    {
        $component = Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->assertSet('filtrosAbiertos', false)
            ->assertSee('Filtros')
            ->assertSee('Mostrar criterios de búsqueda')
            ->assertSee('aria-expanded="false"', false)
            ->assertDontSee('f-docente', false)
            ->call('toggleFiltros')
            ->assertSet('filtrosAbiertos', true)
            ->assertSee('Ocultar criterios de búsqueda')
            ->assertSee('aria-expanded="true"', false)
            ->assertSee('Plan y período')
            ->assertSee('Búsqueda y seguimiento')
            ->assertSee('xl:grid-cols-12', false)
            ->assertSee('xl:col-span-3', false)
            ->assertSee('min-h-11', false)
            ->assertSee('f-docente', false)
            ->assertSee('f-desde', false)
            ->assertSee('f-estado', false)
            ->assertSee('f-revision', false)
            ->assertSee('f-orden', false);

        $component->call('toggleFiltros')
            ->assertSet('filtrosAbiertos', false)
            ->assertDontSee('f-docente', false);
    }

    /** @test */
    public function la_cabecera_incluye_una_leyenda_con_la_definicion_de_cada_estado(): void
    {
        $this->actingAs($this->makeManager())
            ->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertOk()
            ->assertSee('Leyenda de estados')
            ->assertSee('dos ejes independientes')
            ->assertSee('Sin iniciar')
            ->assertSee('En progreso')
            ->assertSee('Completo')
            ->assertSee('Sin revisión')
            ->assertSee('Revisado')
            ->assertDontSee('Emitido')
            ->assertSee('50 estrategias')
            ->assertSee('al menos un resumen')
            ->assertSee('las actividades no son obligatorias para marcarlo completo');
    }

    /** @test */
    public function un_manager_que_tambien_es_profesor_entra_con_su_layout_propio(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $manager = $this->makeUsuario([
            'is_inicial_manager' => true,
            'is_profesor' => true,
        ]);

        $this->assertEquals('seguimiento_inicial', MenuBuilder::resolveLayoutForUser($manager));

        $this->actingAs($manager)
            ->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertOk();
    }

    /** @test */
    public function un_manager_con_is_inicial_heredado_entra_pero_la_ui_de_admin_bloquea_la_combinacion(): void
    {
        $this->saltarSiFaltaLaMigracion();

        // Data heredada: el middleware prioriza el flag manager y no da 403.
        $manager = $this->makeUsuario([
            'is_inicial_manager' => true,
            'is_inicial' => true,
        ]);

        $this->actingAs($manager)
            ->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertOk();

        $admin = $this->makeUsuario(['is_admin' => true]);

        Livewire::actingAs($admin)->test(UsersIndexComponent::class)
            ->set('username', 'heredado_'.uniqid())
            ->set('email', uniqid().'@example.test')
            ->set('password', 'secret123')
            ->set('is_inicial', true)
            ->set('is_inicial_manager', true)
            ->call('save')
            ->assertHasErrors('is_inicial_manager');
    }

    /** @test */
    public function el_manager_abre_el_detalle_y_el_formato_de_los_seis_documentos(): void
    {
        $manager = $this->makeManager();

        foreach ($this->documentosDeLosSeis() as $entidad => $documento) {
            $this->actingAs($manager)
                ->get(route('inicials.seguimiento.'.$entidad.'.show', $documento->id))
                ->assertOk();

            $this->actingAs($manager)
                ->get(route('inicials.seguimiento.'.$entidad.'.format', $documento->id))
                ->assertOk();
        }
    }

    /** @test */
    public function el_detalle_ofrece_un_boton_para_volver_al_listado_de_su_documento(): void
    {
        $manager = $this->makeManager();

        foreach ($this->documentosDeLosSeis() as $entidad => $documento) {
            $this->actingAs($manager)
                ->get(route('inicials.seguimiento.'.$entidad.'.show', $documento->id))
                ->assertOk()
                ->assertSee('Volver al listado')
                ->assertSee(route('inicials.seguimiento.'.$entidad.'.index'), false);
        }
    }

    /** @test */
    public function un_docente_o_un_admin_sin_el_flag_no_abren_el_detalle_ni_el_formato(): void
    {
        $documentos = $this->documentosDeLosSeis();
        $docente = $this->makeDocente();
        $admin = $this->makeUsuario(['is_admin' => true]);

        foreach ([$docente['user'], $admin] as $usuario) {
            $this->actingAs($usuario)
                ->get(route('inicials.seguimiento.eiplanningwks.show', $documentos['eiplanningwks']->id))
                ->assertForbidden();

            $this->actingAs($usuario)
                ->get(route('inicials.seguimiento.eiplanningwks.format', $documentos['eiplanningwks']->id))
                ->assertForbidden();
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  SOLO LECTURA
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function todas_las_rutas_del_grupo_son_get(): void
    {
        $rutas = collect(Route::getRoutes())
            ->filter(fn ($ruta) => str_starts_with((string) $ruta->getName(), 'inicials.seguimiento.'));

        // 1 panel + 6 documentos × (índice + detalle + formato).
        $this->assertCount(19, $rutas);

        foreach ($rutas as $ruta) {
            $this->assertSame(
                [],
                array_values(array_diff($ruta->methods(), ['GET', 'HEAD'])),
                'La ruta '.$ruta->getName().' no debe admitir escritura.'
            );
        }
    }

    /** @test */
    public function no_existe_ninguna_ruta_de_escritura(): void
    {
        $manager = $this->makeManager();

        $this->actingAs($manager)
            ->post('/app/inicials/seguimiento/eiplanningwks')
            ->assertStatus(405);
    }

    /** @test */
    public function el_componente_no_ofrece_crud_de_documentos(): void
    {
        foreach (['save', 'update', 'delete', 'create', 'store', 'destroy'] as $metodo) {
            $this->assertFalse(
                method_exists(SeguimientoInicialComponent::class, $metodo),
                "El componente no debe tener {$metodo}()."
            );
        }

        // El seguimiento no crea ni modifica documentos salvo las acciones
        // explícitas de revisión y observación semanal.
        $this->assertTrue(method_exists(SeguimientoInicialComponent::class, 'guardarRevision'));
        $this->assertTrue(method_exists(SeguimientoInicialComponent::class, 'guardarObservacion'));
    }

    /** @test */
    public function el_indice_no_expone_los_campos_de_edicion_docente(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $manager = $this->makeManager();
        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        $this->actingAs($manager)
            ->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertOk()
            ->assertSee('Filtros')
            ->assertSee('Mostrar criterios de búsqueda')
            ->assertDontSee('wire:model="observacion"', false)
            ->assertDontSee('wire:model="recomendacion"', false)
            ->assertDontSee('Nueva planificación')
            ->assertDontSee('Importar de s2526');

        // Los dos formularios de seguimiento se abren desde el modal «Ver».
        Livewire::actingAs($manager)
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('abrirDetalle', $plan->id)
            ->assertSee('Registrar revisión')
            ->call('abrirRevision')
            ->assertSee('wire:model="revision"', false)
            ->call('seleccionarPestanaRevision', 'observacion')
            ->assertSee('wire:model="observacion"', false)
            ->assertSee('Guardar');
    }

    /** @test */
    public function el_detalle_no_tiene_botones_de_escritura(): void
    {
        $manager = $this->makeManager();
        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        $this->actingAs($manager)
            ->get(route('inicials.seguimiento.eiplanningwks.show', $plan->id))
            ->assertOk()
            ->assertSee('Solo lectura')
            ->assertDontSee('wire:model', false)
            ->assertDontSee('Guardar');
    }

    // ═══════════════════════════════════════════════════════════════
    //  REVISIÓN Y OBSERVACIÓN · ESCRITURA ACOTADA
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function el_manager_registra_la_revision_independiente_desde_el_modal(): void
    {
        $this->saltarSiFaltaLaMigracion();
        Notification::fake();

        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Diagnóstico intacto.']
        ));

        $componente = Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('revisarDetalle', $plan->id)
            ->assertSet('revisionAbierta', true)
            ->assertSet('detalleId', $plan->id);

        Notification::assertNothingSent();

        $componente
            ->set('revision', '  Ajustar la planificación del martes.  ')
            ->call('guardarRevision')
            ->assertHasNoErrors()
            ->assertSet('revisionAbierta', false)
            ->assertSet('detalleId', null)
            ->assertDispatched('wireui:notification', function ($eventName, $params) {
                return ($params[0]['options']['title'] ?? null) === 'Revisión de seguimiento guardada'
                    && ($params[0]['options']['description'] ?? null) === 'La revisión independiente se actualizó correctamente.'
                    && ($params[0]['options']['icon'] ?? null) === 'success';
            })
            ->assertDontSee('aria-modal="true"', false)
            ->assertSee('Revisado');

        $actualizado = $plan->fresh();

        // La revisión es independiente; el contenido docente no se modifica.
        $this->assertSame('Ajustar la planificación del martes.', $actualizado->revision_seguimiento);
        $this->assertNull($actualizado->observacion);
        $this->assertSame('Diagnóstico intacto.', $actualizado->diagnostico);
        Notification::assertSentTo(
            $docente['user'],
            InicialSeguimientoNotification::class,
            fn (InicialSeguimientoNotification $notification) => $notification->type === 'inicial_followup_review'
                && $notification->documentoId === $plan->id
                && $notification->campo === 'revision_seguimiento'
        );

        // El KPI se recalcula con la misma escritura.
        $resumen = app(SeguimientoInicial::class)->resumen('eiplanningwks', [
            'profesor_id' => $docente['profesor_id'],
        ]);
        $this->assertSame(1, $resumen['revisados']);
        $this->assertSame(0, $resumen['sin_revision']);
    }

    /** @test */
    public function el_boton_de_revision_de_la_tarjeta_abre_el_modal_en_modo_escritura(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['observacion' => 'Observación docente intacta.', 'revision_seguimiento' => 'Revisión previa.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->assertSee('wire:click="revisarDetalle('.$plan->id.')"', false)
            ->call('revisarDetalle', $plan->id)
            ->assertSet('detalleId', $plan->id)
            ->assertSet('revisionAbierta', true)
            ->assertSet('revision', 'Revisión previa.')
            ->assertSee('px-2 py-4 sm:px-4 lg:px-6 2xl:px-8', false)
            ->assertSee('w-full max-w-none max-h-[90vh]', false)
            ->assertSee('wire:model="revision"', false);
    }

    /** @test */
    public function la_pestana_observacion_edita_el_campo_y_notifica_al_docente_asociado(): void
    {
        Notification::fake();

        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['observacion' => 'Observación anterior del plan.']
        ));

        $componente = Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('revisarDetalle', $plan->id)
            ->assertSee('role="tablist"', false)
            ->assertSee('Revisión')
            ->assertSee('Observación')
            ->call('seleccionarPestanaRevision', 'observacion')
            ->assertSet('revisionPestana', 'observacion')
            ->assertSet('observacion', 'Observación anterior del plan.')
            ->assertSee('Eiplanningwk.observacion')
            ->assertSee('wire:model="observacion"', false)
            ->set('observacion', 'Actualizar el registro diario del grupo.')
            ->call('guardarObservacion')
            ->assertHasNoErrors()
            ->assertSet('revisionAbierta', false)
            ->assertSet('observacion', '')
            ->assertDispatched('wireui:notification', function ($eventName, $params) {
                return ($params[0]['options']['title'] ?? null) === 'Observación guardada'
                    && ($params[0]['options']['icon'] ?? null) === 'success';
            });

        $this->assertSame('Actualizar el registro diario del grupo.', $plan->fresh()->observacion);
        Notification::assertSentTo(
            $docente['user'],
            InicialSeguimientoNotification::class,
            fn (InicialSeguimientoNotification $notification) => $notification->type === 'inicial_weekly_observation'
                && $notification->documentoId === $plan->id
                && $notification->campo === 'observacion'
        );
    }

    /** @test */
    public function el_manager_registra_la_revision_del_plan_de_evaluacion(): void
    {
        $this->saltarSiFaltaLaMigracion();
        Notification::fake();

        $docente = $this->makeDocente();
        $plan = Eievaluationk::create(array_merge(
            $this->planAttributes(
                $docente['profesor_id'],
                $docente['grado_id'],
                $docente['seccion_id']
            ),
            ['lapso_id' => $docente['lapso_id'], 'recomendacion' => 'Recomendación de Coordinación.', 'revision_seguimiento' => null]
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eievaluationks'])
            ->call('abrirDetalle', $plan->id)
            ->call('abrirRevision')
            ->assertSee('Revisión de seguimiento')
            ->set('revision', 'Incorporar la evaluación diagnóstica.')
            ->call('guardarRevision')
            ->assertHasNoErrors()
            ->assertSet('revisionAbierta', false)
            ->assertSet('detalleId', null)
            ->assertDispatched('wireui:notification', function ($eventName, $params) {
                return ($params[0]['options']['title'] ?? null) === 'Revisión de seguimiento guardada'
                    && ($params[0]['options']['description'] ?? null) === 'La revisión independiente se actualizó correctamente.'
                    && ($params[0]['options']['icon'] ?? null) === 'success';
            })
            ->assertDontSee('aria-modal="true"', false);

        $this->assertSame('Incorporar la evaluación diagnóstica.', $plan->fresh()->revision_seguimiento);
        $this->assertSame('Recomendación de Coordinación.', $plan->fresh()->recomendacion);
    }

    /** @test */
    public function la_revision_exige_al_menos_cinco_caracteres(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('abrirDetalle', $plan->id)
            ->call('abrirRevision')
            ->set('revision', 'ok')
            ->call('guardarRevision')
            ->assertHasErrors(['revision' => 'min'])
            ->assertSet('revisionAbierta', true);

        $this->assertNull($plan->fresh()->observacion);
        $this->assertNull($plan->fresh()->revision_seguimiento);

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('abrirDetalle', $plan->id)
            ->call('abrirRevision')
            ->set('revision', '     ')
            ->call('guardarRevision')
            ->assertHasErrors(['revision' => 'required'])
            ->assertSet('revisionAbierta', true);
    }

    /** @test */
    public function cerrar_revision_descarta_el_texto_sin_guardar(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            [
                'observacion' => 'Observación de la docente.',
                'revision_seguimiento' => 'Revisión anterior.',
                'diagnostico' => 'Contenido extenso del documento que no debe aparecer al cancelar.',
            ]
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('abrirDetalle', $plan->id)
            // «Editar revisión» precarga el texto existente.
            ->call('abrirRevision')
            ->assertSet('revision', 'Revisión anterior.')
            ->set('revision', 'Texto descartado.')
            ->call('cerrarRevision')
            ->assertSet('revisionAbierta', false)
            ->assertSet('revision', '')
            ->assertSet('detalleId', null)
            ->assertDontSee('aria-modal="true"', false);

        $this->assertSame('Revisión anterior.', $plan->fresh()->revision_seguimiento);
        $this->assertSame('Observación de la docente.', $plan->fresh()->observacion);
    }

    /** @test */
    public function el_informe_final_no_admite_revision(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $documentos = $this->documentosDeLosSeis();

        // El informe final no tiene campo revisable: `abrirRevision()` aborta
        // con 404 en vez de abrir un formulario que no escribiría nada.
        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eifinalks'])
            ->call('abrirDetalle', $documentos['eifinalks']->id)
            ->call('abrirRevision')
            ->assertStatus(404);
    }

    // ═══════════════════════════════════════════════════════════════
    //  DATOS Y FILTROS
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function el_listado_muestra_los_documentos_de_dos_docentes(): void
    {
        $uno = $this->makeDocente();
        $dos = $this->makeDocente();

        Eiplanningwk::create($this->planAttributes(
            $uno['profesor_id'],
            $uno['grado_id'],
            $uno['seccion_id'],
            ['diagnostico' => 'Diagnóstico del docente uno.']
        ));

        Eiplanningwk::create($this->planAttributes(
            $dos['profesor_id'],
            $dos['grado_id'],
            $dos['seccion_id'],
            ['diagnostico' => 'Diagnóstico del docente dos.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->assertOk()
            ->assertSee('Diagnóstico del docente uno.')
            ->assertSee('Diagnóstico del docente dos.');
    }

    /** @test */
    public function filtra_por_docente(): void
    {
        $uno = $this->makeDocente();
        $dos = $this->makeDocente();

        Eiplanningwk::create($this->planAttributes(
            $uno['profesor_id'],
            $uno['grado_id'],
            $uno['seccion_id'],
            ['diagnostico' => 'Solo del docente uno.']
        ));

        Eiplanningwk::create($this->planAttributes(
            $dos['profesor_id'],
            $dos['grado_id'],
            $dos['seccion_id'],
            ['diagnostico' => 'Solo del docente dos.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('profesor_id', $uno['profesor_id'])
            ->assertSee('Solo del docente uno.')
            ->assertDontSee('Solo del docente dos.');
    }

    /** @test */
    public function filtra_por_nivel_y_seccion(): void
    {
        $uno = $this->makeDocente();

        $otroGrado = (int) Grado::where('pestudio_id', 6)
            ->where('id', '<>', $uno['grado_id'])
            ->value('id');
        $otraSeccion = $this->seccionActiva($otroGrado);

        Eiplanningwk::create($this->planAttributes(
            $uno['profesor_id'],
            $uno['grado_id'],
            $uno['seccion_id'],
            ['diagnostico' => 'Plan del nivel uno.']
        ));

        Eiplanningwk::create($this->planAttributes(
            $uno['profesor_id'],
            $otroGrado,
            $otraSeccion,
            ['diagnostico' => 'Plan del nivel dos.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('grado_id', $uno['grado_id'])
            ->set('seccion_id', $uno['seccion_id'])
            ->assertSee('Plan del nivel uno.')
            ->assertDontSee('Plan del nivel dos.');
    }

    /** @test */
    public function filtra_por_rango_de_fechas_con_solapamiento(): void
    {
        $docente = $this->makeDocente();

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            [
                'diagnostico' => 'Plan dentro del rango.',
                'finicial' => now()->subDays(3),
                'ffinal' => now()->addDays(3),
            ]
        ));

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            [
                'diagnostico' => 'Plan fuera del rango.',
                'finicial' => now()->subDays(60),
                'ffinal' => now()->subDays(50),
            ]
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('desde', now()->subDay()->toDateString())
            ->set('hasta', now()->addDay()->toDateString())
            ->assertSee('Plan dentro del rango.')
            ->assertDontSee('Plan fuera del rango.');
    }

    /** @test */
    public function filtra_por_lapso_en_el_plan_de_evaluacion(): void
    {
        $docente = $this->makeDocente();
        $otroLapso = (int) DB::table('lapsos')->where('id', '<>', $docente['lapso_id'])->value('id');

        Eievaluationk::create(array_merge(
            $this->planAttributes($docente['profesor_id'], $docente['grado_id'], $docente['seccion_id']),
            ['lapso_id' => $docente['lapso_id'], 'observaciones' => 'Evaluación del lapso uno.']
        ));

        Eievaluationk::create(array_merge(
            $this->planAttributes($docente['profesor_id'], $docente['grado_id'], $docente['seccion_id']),
            ['lapso_id' => $otroLapso, 'observaciones' => 'Evaluación del lapso dos.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eievaluationks'])
            ->set('lapso_id', $docente['lapso_id'])
            ->assertSee('Evaluación del lapso uno.')
            ->assertDontSee('Evaluación del lapso dos.');
    }

    /** @test */
    public function la_planificacion_semanal_ignora_el_filtro_de_lapso(): void
    {
        $docente = $this->makeDocente();

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Plan semanal visible.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('lapso_id', $docente['lapso_id'])
            ->assertSee('Plan semanal visible.');
    }

    /** @test */
    public function los_estados_completos_se_ofrecen_para_los_cinco_documentos_revisables(): void
    {
        $docente = $this->makeDocente();
        $incompleto = Eiprojectk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Proyecto visible con estado manipulado.']
        ));

        foreach ([
            'eiplanningwks',
            'eiplanningbwks',
            'eiprojectks',
            'eispecialks',
            'eievaluationks',
        ] as $entidad) {
            $this->assertArrayHasKey(
                'completo',
                app(SeguimientoInicial::class)->opcionesEstado($entidad)
            );
        }

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiprojectks'])
            ->set('estado', 'completo')
            ->assertOk()
            ->assertDontSee('Proyecto visible con estado manipulado.');

        $this->sembrarEstrategiasProyecto($incompleto);

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiprojectks'])
            ->set('estado', 'completo')
            ->assertSee('Proyecto visible con estado manipulado.');
    }

    /** @test */
    public function el_estado_clasifica_el_avance_completo_de_cada_tipo_de_documento(): void
    {
        $servicio = app(SeguimientoInicial::class);
        $totalEstrategias = count(config('inicial.dias_semana'))
            * count(config('inicial.momentos_rutina_diaria'));

        foreach ([
            'eiplanningwks' => new Eiplanningwk,
            'eiplanningbwks' => new Eiplanningbwk,
            'eiprojectks' => new Eiprojectk,
            'eispecialks' => new Eispecialk,
        ] as $entidad => $plan) {
            $plan->setAttribute('estrategias_count', $totalEstrategias);

            if ($entidad === 'eiplanningwks') {
                $plan->setAttribute('resumenes_count', 1);
            }

            $this->assertSame('Completo', $servicio->estado($entidad, $plan)['etiqueta'], $entidad);

            $plan->setAttribute('estrategias_count', 1);
            $this->assertSame('En progreso', $servicio->estado($entidad, $plan)['etiqueta'], $entidad);
        }

        $weeklyWithoutSummaries = new Eiplanningwk;
        $weeklyWithoutSummaries->setAttribute('estrategias_count', $totalEstrategias);
        $weeklyWithoutSummaries->setAttribute('resumenes_count', 0);
        $this->assertSame('En progreso', $servicio->estado('eiplanningwks', $weeklyWithoutSummaries)['etiqueta']);

        $evaluation = new Eievaluationk;
        $evaluation->setAttribute('posiciones_count', 2);
        $evaluation->setAttribute('posiciones_completas_count', 1);
        $this->assertSame('En progreso', $servicio->estado('eievaluationks', $evaluation)['etiqueta']);

        $evaluation->setAttribute('posiciones_completas_count', 2);
        $this->assertSame('Completo', $servicio->estado('eievaluationks', $evaluation)['etiqueta']);

        $evaluation->setAttribute('posiciones_count', 0);
        $evaluation->setAttribute('posiciones_completas_count', 0);
        $this->assertSame('Sin iniciar', $servicio->estado('eievaluationks', $evaluation)['etiqueta']);

        $planEspecial = new Eispecialk;
        $planEspecial->setAttribute('estrategias_count', 0);
        $planEspecial->setAttribute('actividades_count', 1);
        $this->assertSame('En progreso', $servicio->estado('eispecialks', $planEspecial)['etiqueta']);
    }

    /** @test */
    public function la_tarjeta_del_plan_especial_muestra_estrategias_y_actividades_por_separado(): void
    {
        $docente = $this->makeDocente();
        $plan = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));
        $this->sembrarEstrategiasEspeciales($plan, 50);

        foreach (range(1, 2) as $orden) {
            Eispecialact::create([
                'eispecialk_id' => $plan->id,
                'pevaluacion_id' => $docente['pevaluacion_id'],
                'componente' => 'Componente '.$orden,
                'objetivo' => 'Objetivo '.$orden,
                'order' => $orden,
            ]);
        }

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eispecialks'])
            ->assertSee('50/50 estrategias')
            ->assertSee('2 actividades')
            ->assertSee('Completo con 50 estrategias; las actividades son complementarias.');
    }

    /** @test */
    public function los_accesos_rapidos_de_plan_especial_filtran_avance_y_revision(): void
    {
        $docente = $this->makeDocente();
        $sinIniciar = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));
        $enProgreso = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));
        Eispecialact::create([
            'eispecialk_id' => $enProgreso->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'componente' => 'Actividad registrada',
        ]);
        $completo = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));
        $this->sembrarEstrategiasEspeciales($completo, 50);
        $completo->update(['revision_seguimiento' => 'Plan revisado.']);

        $component = Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eispecialks'])
            ->assertSee('Accesos rápidos')
            ->assertSee('Filtrar por estado: Sin iniciar')
            ->assertSee('Filtrar por estado: En progreso')
            ->assertSee('Filtrar por estado: Completo')
            ->assertSee('Filtrar por revisión: Sin revisión')
            ->assertSee('Filtrar por revisión: Revisado')
            ->call('aplicarFiltroRapido', 'estado', 'completo')
            ->assertSet('estado', 'completo')
            ->assertSee('N° '.$completo->id)
            ->assertDontSee('N° '.$sinIniciar->id)
            ->call('aplicarFiltroRapido', 'revisionEstado', 'revisado')
            ->assertSet('revisionEstado', 'revisado')
            ->assertSee('N° '.$completo->id)
            ->assertDontSee('N° '.$enProgreso->id);
    }

    /** @test */
    public function la_busqueda_de_plan_especial_incluye_actividades_y_estrategias(): void
    {
        $docente = $this->makeDocente();
        $planActividad = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['justificacion' => 'Plan especial de búsqueda.']
        ));
        Eispecialact::create([
            'eispecialk_id' => $planActividad->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'objetivo' => 'MarcadorActividadEspecial',
        ]);

        $planEstrategia = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['justificacion' => 'Otro plan especial.']
        ));
        Eispecialstrategy::create([
            'eispecialk_id' => $planEstrategia->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'MarcadorEstrategiaEspecial',
        ]);

        $servicio = app(SeguimientoInicial::class);
        $porActividad = $servicio->paginar('eispecialks', [
            'profesor_id' => $docente['profesor_id'],
            'search' => 'MarcadorActividadEspecial',
        ]);
        $porEstrategia = $servicio->paginar('eispecialks', [
            'profesor_id' => $docente['profesor_id'],
            'search' => 'MarcadorEstrategiaEspecial',
        ]);

        $this->assertSame([$planActividad->id], $porActividad->getCollection()->modelKeys());
        $this->assertSame([$planEstrategia->id], $porEstrategia->getCollection()->modelKeys());
    }

    /** @test */
    public function pendientes_de_plan_especial_prioriza_incompletos_y_luego_sin_revision(): void
    {
        $docente = $this->makeDocente();
        $pocasEstrategias = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));
        $this->sembrarEstrategiasEspeciales($pocasEstrategias, 3);

        $completoSinRevision = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));
        $this->sembrarEstrategiasEspeciales($completoSinRevision, 50);

        $incompletoRevisado = Eispecialk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));
        $incompletoRevisado->update(['revision_seguimiento' => 'Revisado.']);
        $this->sembrarEstrategiasEspeciales($incompletoRevisado, 1);
        DB::table('eispecialks')
            ->where('id', $pocasEstrategias->id)
            ->update(['updated_at' => now()]);
        DB::table('eispecialks')
            ->where('id', $completoSinRevision->id)
            ->update(['updated_at' => now()->subDay()]);
        DB::table('eispecialks')
            ->where('id', $incompletoRevisado->id)
            ->update(['updated_at' => now()->subDays(2)]);

        $planes = app(SeguimientoInicial::class)
            ->paginar('eispecialks', [
                'profesor_id' => $docente['profesor_id'],
                'orden' => 'pendientes',
            ])
            ->getCollection();

        $this->assertSame(
            [$pocasEstrategias->id, $completoSinRevision->id, $incompletoRevisado->id],
            $planes->modelKeys()
        );
    }

    /** @test */
    public function el_estado_semanal_explica_que_requisitos_faltan(): void
    {
        $plan = new Eiplanningwk;
        $plan->setAttribute('estrategias_count', 2);
        $plan->setAttribute('resumenes_count', 1);

        $estado = app(SeguimientoInicial::class)->estado('eiplanningwks', $plan);

        $this->assertSame('En progreso', $estado['etiqueta']);
        $this->assertStringContainsString('48 estrategias pendientes', $estado['detalle']);
        $this->assertStringNotContainsString('resumen', $estado['detalle']);
    }

    /** @test */
    public function el_estado_de_evaluacion_se_completa_solo_con_aprendizaje_en_todas_las_posiciones(): void
    {
        $docente = $this->makeDocente();
        $attributes = $this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        );

        $completo = Eievaluationk::create(array_merge($attributes, [
            'lapso_id' => $docente['lapso_id'],
            'observaciones' => 'Se evaluaron los aprendizajes del grupo.',
            'asistencia' => 'Asistencia completa.',
        ]));
        Eievaluationp::create([
            'eievaluationk_id' => $completo->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'aprendizaje_alcanzado' => 'Comunica ideas y participa.',
        ]);

        $incompleto = Eievaluationk::create(array_merge($attributes, [
            'lapso_id' => $docente['lapso_id'],
            'observaciones' => 'Evaluación en proceso.',
            'asistencia' => 'Asistencia registrada.',
        ]));
        Eievaluationp::create([
            'eievaluationk_id' => $incompleto->id,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'aprendizaje_alcanzado' => null,
        ]);

        $sinPosiciones = Eievaluationk::create(array_merge($attributes, [
            'lapso_id' => $docente['lapso_id'],
            'observaciones' => 'Sin posiciones todavía.',
            'asistencia' => 'Asistencia registrada.',
        ]));

        $servicio = app(SeguimientoInicial::class);
        $filtrosBase = ['profesor_id' => $docente['profesor_id']];
        $ids = fn (string $estado): array => $servicio
            ->paginar('eievaluationks', $filtrosBase + ['estado' => $estado])
            ->getCollection()
            ->modelKeys();

        $this->assertSame([$completo->id], $ids('completo'));
        $this->assertSame([$incompleto->id], $ids('en_progreso'));
        $this->assertSame([$sinPosiciones->id], $ids('sin_iniciar'));
    }

    /** @test */
    public function busca_por_diagnostico(): void
    {
        $docente = $this->makeDocente();

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Texto inequívoco de búsqueda.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('search', 'inequívoco')
            ->assertSee('Texto inequívoco de búsqueda.');
    }

    /** @test */
    public function busca_por_estudiante_en_el_informe_final(): void
    {
        $docente = $this->makeDocente();
        $estudiante = $this->makeEstudiante($docente['seccion_id'], 'Anahí');

        Eifinalk::create([
            'order' => 1,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'estudiant_id' => $estudiante->id,
            'title' => 'Informe de Anahí',
        ]);

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eifinalks'])
            ->set('search', 'Anahí')
            ->assertSee('Informe de Anahí');

        // El KPI de docentes del informe final cuenta profesores distintos,
        // no cargas académicas (el informe no tiene profesor_id propio).
        $resumen = app(SeguimientoInicial::class)->resumen('eifinalks', ['search' => 'Anahí']);
        $this->assertSame(1, $resumen['docentes']);
    }

    /** @test */
    public function filtra_por_revision_sin_mezclarla_con_el_avance(): void
    {
        $docente = $this->makeDocente();

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Plan sin revisión.', 'revision_seguimiento' => null]
        ));

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Plan ya revisado.', 'revision_seguimiento' => 'Revisión registrada.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('revisionEstado', 'sin_revision')
            ->assertSee('Plan sin revisión.')
            ->assertDontSee('Plan ya revisado.');

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('revisionEstado', 'revisado')
            ->assertSee('Plan ya revisado.')
            ->assertDontSee('Plan sin revisión.');
    }

    /** @test */
    public function muestra_conteos_rapidos_y_resumen_pendiente_por_docente(): void
    {
        $docente = $this->makeDocente();
        $otroDocente = $this->makeDocente();

        $sinIniciar = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'MarcaConteos Plan completamente vacío.']
        ));
        $enProgreso = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            [
                'diagnostico' => 'MarcaConteos Plan parcialmente completado.',
                'revision_seguimiento' => 'Revisado.',
            ]
        ));
        $otroSinIniciar = Eiplanningwk::create($this->planAttributes(
            $otroDocente['profesor_id'],
            $otroDocente['grado_id'],
            $otroDocente['seccion_id'],
            ['diagnostico' => 'MarcaConteos Plan vacío de otro docente.']
        ));

        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $enProgreso->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Estrategia pendiente.',
            'order' => 0,
        ]);

        $servicio = app(SeguimientoInicial::class);
        $filtros = ['search' => 'MarcaConteos'];
        $conteos = $servicio->conteosRapidos('eiplanningwks', $filtros);

        $this->assertSame(2, $conteos['sin_iniciar']);
        $this->assertSame(1, $conteos['en_progreso']);
        $this->assertSame(0, $conteos['completo']);
        $this->assertSame(2, $conteos['sin_revision']);
        $this->assertSame(1, $conteos['revisado']);

        $docentes = $servicio->resumenDocentes('eiplanningwks', $filtros)
            ->keyBy('profesor_id');
        $this->assertSame(2, $docentes[$docente['profesor_id']]['total']);
        $this->assertSame(2, $docentes[$docente['profesor_id']]['incompletos']);
        $this->assertSame(1, $docentes[$docente['profesor_id']]['sin_revision']);
        $this->assertSame(1, $docentes[$otroDocente['profesor_id']]['total']);

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('search', 'MarcaConteos')
            ->assertSee('Accesos rápidos')
            ->assertSee('Resumen por docente')
            ->call('toggleResumenDocentes')
            ->assertSet('resumenDocentesAbierto', true)
            ->assertSee('Seguimiento Test, Docente')
            ->call('aplicarFiltroRapido', 'estado', 'en_progreso')
            ->assertSet('estado', 'en_progreso')
            ->assertSee('MarcaConteos Plan parcialmente completado.')
            ->assertDontSee('MarcaConteos Plan completamente vacío.')
            ->call('aplicarFiltroRapido', 'revisionEstado', 'sin_revision')
            ->assertSet('estado', 'en_progreso')
            ->assertSet('revisionEstado', 'sin_revision')
            ->assertDontSee('MarcaConteos Plan parcialmente completado.')
            ->call('aplicarFiltroRapido', 'revisionEstado', 'sin_revision')
            ->assertSet('revisionEstado', '')
            ->call('filtrarDocente', $docente['profesor_id'])
            ->assertSet('profesor_id', (string) $docente['profesor_id'])
            ->assertSee('MarcaConteos Plan parcialmente completado.')
            ->assertDontSee('MarcaConteos Plan vacío de otro docente.');
    }

    /** @test */
    public function combina_filtros_de_avance_y_revision_y_prioriza_pendientes(): void
    {
        $docente = $this->makeDocente();
        $sinIniciar = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Plan sin iniciar y sin revisión.']
        ));
        $progresoSinRevision = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Plan en progreso sin revisión.']
        ));
        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $progresoSinRevision->id,
            'day_of_week' => array_key_first(config('inicial.dias_semana')),
            'momento_rutina_diaria' => array_key_first(config('inicial.momentos_rutina_diaria')),
            'lunes' => 'Estrategia pendiente.',
            'order' => 0,
        ]);
        $progresoRevisado = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            [
                'diagnostico' => 'Plan en progreso revisado.',
                'revision_seguimiento' => 'Revisión realizada.',
            ]
        ));
        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $progresoRevisado->id,
            'day_of_week' => array_key_first(config('inicial.dias_semana')),
            'momento_rutina_diaria' => array_key_first(config('inicial.momentos_rutina_diaria')),
            'lunes' => 'Estrategia revisada.',
            'order' => 0,
        ]);

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('profesor_id', (string) $docente['profesor_id'])
            ->set('estado', 'en_progreso')
            ->set('revisionEstado', 'sin_revision')
            ->assertSee('Plan en progreso sin revisión.')
            ->assertDontSee('Plan sin iniciar y sin revisión.')
            ->assertDontSee('Plan en progreso revisado.')
            ->assertSee('Filtros activos')
            ->assertSee('Mostrando')
            ->assertSee('1–1', false)
            ->assertSee('registros');

        $ordenados = app(SeguimientoInicial::class)
            ->paginar('eiplanningwks', [
                'profesor_id' => $docente['profesor_id'],
                'orden' => 'pendientes',
            ])
            ->getCollection()
            ->modelKeys();

        $this->assertSame(
            [$sinIniciar->id, $progresoSinRevision->id, $progresoRevisado->id],
            $ordenados
        );
    }

    /** @test */
    public function ordena_por_ultima_actualizacion_y_por_periodo(): void
    {
        $docente = $this->makeDocente();
        $antiguo = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['finicial' => now()->subDays(30), 'diagnostico' => 'Período antiguo.']
        ));
        $reciente = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['finicial' => now()->subDays(5), 'diagnostico' => 'Período reciente.']
        ));
        $intermedio = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['finicial' => now()->subDays(15), 'diagnostico' => 'Período intermedio.']
        ));

        DB::table('eiplanningwks')->where('id', $antiguo->id)->update([
            'updated_at' => now()->subDays(1),
        ]);
        DB::table('eiplanningwks')->where('id', $reciente->id)->update([
            'updated_at' => now()->subDays(3),
        ]);
        DB::table('eiplanningwks')->where('id', $intermedio->id)->update([
            'updated_at' => now()->subDays(2),
        ]);

        $servicio = app(SeguimientoInicial::class);
        $filtros = ['profesor_id' => $docente['profesor_id']];
        $porActualizacion = $servicio->paginar(
            'eiplanningwks',
            $filtros + ['orden' => 'actualizacion']
        )->getCollection()->modelKeys();
        $porPeriodo = $servicio->paginar(
            'eiplanningwks',
            $filtros + ['orden' => 'periodo']
        )->getCollection()->modelKeys();

        $this->assertSame([$antiguo->id, $intermedio->id, $reciente->id], $porActualizacion);
        $this->assertSame([$reciente->id, $intermedio->id, $antiguo->id], $porPeriodo);
    }

    /** @test */
    public function la_busqueda_y_el_quitado_de_chips_conservan_los_filtros_independientes(): void
    {
        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            [
                'diagnostico' => 'Plan localizable desde el filtro activo.',
                'revision_seguimiento' => 'Revisado por seguimiento.',
            ]
        ));
        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $plan->id,
            'day_of_week' => array_key_first(config('inicial.dias_semana')),
            'momento_rutina_diaria' => array_key_first(config('inicial.momentos_rutina_diaria')),
            'lunes' => 'Estrategia encontrada.',
            'order' => 0,
        ]);

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('search', 'localizable')
            ->set('estado', 'en_progreso')
            ->set('revisionEstado', 'revisado')
            ->assertSee('Búsqueda:')
            ->assertSee('Avance:')
            ->assertSee('Revisión:')
            ->call('quitarFiltro', 'estado')
            ->assertSet('estado', '')
            ->assertSet('revisionEstado', 'revisado')
            ->call('quitarFiltro', 'revisionEstado')
            ->assertSet('revisionEstado', '')
            ->assertSet('search', 'localizable')
            ->assertSee('Plan localizable desde el filtro activo.');
    }

    /** @test */
    public function quitar_chip_de_revision_tambien_limpia_el_estado_legacy_de_la_url(): void
    {
        $docente = $this->makeDocente();
        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Registro legacy sin revisión.']
        ));
        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            [
                'diagnostico' => 'Registro legacy revisado.',
                'revision_seguimiento' => 'Revisión existente.',
            ]
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('profesor_id', (string) $docente['profesor_id'])
            ->set('estado', 'sin_revision')
            ->assertSee('Registro legacy sin revisión.')
            ->assertDontSee('Registro legacy revisado.')
            ->call('quitarFiltro', 'revisionEstado')
            ->assertSet('estado', '')
            ->assertSet('revisionEstado', '')
            ->assertSee('Registro legacy sin revisión.')
            ->assertSee('Registro legacy revisado.');
    }

    /** @test */
    public function una_planificacion_semanal_completa_con_un_resumen_sin_exigir_uno_por_area(): void
    {
        $docente = $this->makeDocente();
        $pensumBase = Pensum::where('grado_id', $docente['grado_id'])
            ->where('id', DB::table('pevaluacions')->where('id', $docente['pevaluacion_id'])->value('pensum_id'))
            ->firstOrFail();
        $segundoPensum = Pensum::where('grado_id', $docente['grado_id'])
            ->where('id', '<>', $pensumBase->id)
            ->firstOrFail();
        $segundaPevaluacionId = DB::table('pevaluacions')->insertGetId([
            'profesor_id' => $docente['profesor_id'],
            'pensum_id' => $segundoPensum->id,
            'seccion_id' => $docente['seccion_id'],
            'lapso_id' => $docente['lapso_id'],
            'status_official' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $completo = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Semana completa.']
        ));

        $incompleto = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Semana incompleta.']
        ));

        $sinResumenes = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Semana completa sin resumen.']
        ));

        $this->sembrarSemanaCompleta($completo);
        $this->sembrarSemanaCompleta($sinResumenes);
        $this->crearResumenSemanal($completo, $docente['pevaluacion_id']);

        // Una sola estrategia impide completar la rejilla, aunque tenga resumen.
        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $incompleto->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Única estrategia',
            'order' => 0,
        ]);
        $this->crearResumenSemanal($incompleto, $docente['pevaluacion_id']);

        $resumen = app(SeguimientoInicial::class)->resumen('eiplanningwks', [
            'profesor_id' => $docente['profesor_id'],
        ]);
        $this->assertSame(1, $resumen['completos'], 'Una sola síntesis basta aunque haya varias áreas de aprendizaje.');

        $this->crearResumenSemanal($completo, $segundaPevaluacionId);
        $resumen = app(SeguimientoInicial::class)->resumen('eiplanningwks', [
            'profesor_id' => $docente['profesor_id'],
        ]);
        $this->assertSame(1, $resumen['completos']);

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('profesor_id', $docente['profesor_id'])
            ->set('estado', 'completo')
            ->assertSee('Semana completa.')
            ->assertDontSee('Semana completa sin resumen.')
            ->assertDontSee('Semana incompleta.');
    }

    /** @test */
    public function los_estados_de_contenido_particionan_la_semana_sin_solaparse(): void
    {
        $docente = $this->makeDocente();

        $completo = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Semana completa.']
        ));

        $enProgreso = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Semana en progreso.']
        ));

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Semana sin iniciar.']
        ));

        $this->sembrarSemanaCompleta($completo);
        $this->crearResumenSemanal($completo, $docente['pevaluacion_id']);

        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $enProgreso->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Única estrategia',
            'order' => 0,
        ]);

        $servicio = app(SeguimientoInicial::class);
        $base = ['profesor_id' => $docente['profesor_id']];

        $diagnosticos = fn (string $estado): array => $servicio
            ->paginar('eiplanningwks', $base + ['estado' => $estado])
            ->getCollection()
            ->pluck('diagnostico')
            ->all();

        // Cada estado devuelve SOLO su tramo: 0, 1..49 y >= 50.
        $this->assertSame(['Semana sin iniciar.'], $diagnosticos('sin_iniciar'));
        $this->assertSame(['Semana en progreso.'], $diagnosticos('en_progreso'));
        $this->assertSame(['Semana completa.'], $diagnosticos('completo'));

        // Los tres tramos suman el total: partición, no solape.
        $this->assertSame(3, $servicio->paginar('eiplanningwks', $base)->total());

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('profesor_id', $docente['profesor_id'])
            ->set('estado', 'en_progreso')
            ->assertSee('Semana en progreso.')
            ->assertDontSee('Semana completa.')
            ->assertDontSee('Semana sin iniciar.');
    }

    /** @test */
    public function la_paginacion_usa_quince_por_pagina(): void
    {
        $docente = $this->makeDocente();

        foreach (range(1, 16) as $numero) {
            Eiplanningwk::create($this->planAttributes(
                $docente['profesor_id'],
                $docente['grado_id'],
                $docente['seccion_id'],
                ['diagnostico' => 'Plan '.$numero]
            ));
        }

        $servicio = app(SeguimientoInicial::class);
        $filtros = ['profesor_id' => $docente['profesor_id']];

        $primera = $servicio->paginar('eiplanningwks', $filtros, 15);
        $this->assertCount(15, $primera->items());
        $this->assertSame(16, $primera->total());

        request()->merge(['page' => 2]);
        $segunda = $servicio->paginar('eiplanningwks', $filtros, 15);
        $this->assertSame(2, $segunda->currentPage());
        $this->assertCount(1, $segunda->items());
    }

    /** @test */
    public function cada_pestana_carga_solo_su_documento(): void
    {
        $this->documentosDeLosSeis();

        $componente = Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiprojectks'])
            ->assertSet('entidad', 'eiprojectks')
            ->assertSee('Proyecto de aula');

        $documentos = $componente->viewData('documentos');

        $this->assertNotEmpty($documentos->items());
        $this->assertTrue(
            collect($documentos->items())->every(fn ($documento) => $documento instanceof Eiprojectk),
            'La pestaña de proyectos no debe cargar documentos de otro catálogo.'
        );
    }

    /** @test */
    public function la_vista_de_tabla_tambien_es_de_solo_lectura(): void
    {
        $docente = $this->makeDocente();
        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('alternarVista')
            ->assertSet('viewMode', 'table')
            ->assertSee('Acciones')
            ->assertDontSee('Nueva planificación');
    }

    /** @test */
    public function la_tabla_agrupa_la_informacion_y_no_necesita_scroll_horizontal(): void
    {
        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('alternarVista')
            ->assertSee('Documento')
            ->assertSee('Diagnóstico')
            ->assertSee('N° '.$plan->id)
            ->assertSee('Diagnóstico de seguimiento.')
            ->assertSee('0 estrategias')
            ->assertSee('table-fixed', false)
            ->assertDontSee('min-w-full', false);
    }

    /** @test */
    public function la_tabla_del_informe_final_usa_estudiante_y_titulo(): void
    {
        $docente = $this->makeDocente();
        $estudiante = $this->makeEstudiante($docente['seccion_id'], 'Anahí');

        $informe = Eifinalk::create([
            'order' => 1,
            'pevaluacion_id' => $docente['pevaluacion_id'],
            'estudiant_id' => $estudiante->id,
            'title' => 'Informe final de Anahí',
        ]);

        // El informe no tiene campo de revisión: la tabla se renderiza con
        // cuatro columnas y la etiqueta de contenido es «Título».
        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eifinalks'])
            ->call('alternarVista')
            ->assertSee('Documento')
            ->assertSee('Título')
            ->assertSee('Anahí')
            ->assertSee('Informe final de Anahí')
            ->assertSee('N° '.$informe->id)
            ->assertSee('table-fixed', false)
            ->assertDontSee('min-w-full', false);
    }

    /** @test */
    public function el_modal_de_detalle_reutiliza_el_parcial_del_docente(): void
    {
        $docente = $this->makeDocente();
        $plan = Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            [
                'diagnostico' => str_repeat('Diagnóstico extenso de la situación inicial del grupo. ', 8),
                'observacion' => 'Observación independiente de la docente.',
            ]
        ));

        $componente = Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('abrirDetalle', $plan->id)
            ->assertSet('detalleId', $plan->id)
            ->assertSee('Abrir ficha completa')
            ->assertSee('Formato imprimible')
            ->assertSee('<details', false)
            ->assertSee('Diagnóstico inicial ·', false)
            ->assertSee('Observación de la docente');

        $componente->call('cerrarDetalle')->assertSet('detalleId', null);

        // El estado del modal se calcula sin `withCount`: cae a la relación
        // cargada por `RegistroInicial::consulta()`.
        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $plan->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Estrategia del modal',
            'order' => 0,
        ]);

        $completo = RegistroInicial::consulta('eiplanningwks')->whereKey($plan->id)->first();
        $estado = app(SeguimientoInicial::class)->estado('eiplanningwks', $completo);

        $this->assertSame('En progreso', $estado['etiqueta']);
        $this->assertStringContainsString('49 estrategias pendientes', $estado['detalle']);
        $this->assertStringContainsString('falta el resumen requerido', $estado['detalle']);
    }

    /** @test */
    public function el_estado_vacio_ofrece_limpiar_los_filtros(): void
    {
        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('search', 'texto-que-no-existe-'.uniqid())
            ->assertSee('No hay registros de')
            ->assertSee('Limpiar filtros');
    }

    /** @test */
    public function el_listado_respeta_el_presupuesto_de_consultas(): void
    {
        $docente = $this->makeDocente();

        foreach (range(1, 15) as $numero) {
            Eiplanningwk::create($this->planAttributes(
                $docente['profesor_id'],
                $docente['grado_id'],
                $docente['seccion_id'],
                ['diagnostico' => 'Plan de presupuesto '.$numero]
            ));
        }

        $servicio = app(SeguimientoInicial::class);

        DB::flushQueryLog();
        DB::enableQueryLog();

        // Los tres bloques que ejecuta una carga del índice.
        $servicio->profesores();
        $servicio->paginar('eiplanningwks', [], 15);
        $servicio->resumen('eiplanningwks', []);

        $consultas = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertLessThan(
            30,
            $consultas,
            "El listado de seguimiento ejecutó {$consultas} consultas; el presupuesto de fase 1 es 30."
        );
    }

    // ═══════════════════════════════════════════════════════════════
    //  MENÚ Y ROL
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function el_manager_ve_su_grupo_con_los_siete_items(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $manager = $this->makeManager();
        $this->actingAs($manager);

        $grupo = collect((new MenuBuilder('seguimiento_inicial'))->resolveGroups())
            ->firstWhere('label', 'Seguimiento Inicial');

        $this->assertNotNull($grupo);
        $this->assertSame([
            'Panel',
            'Planificación semanal',
            'Planificación quincenal',
            'Proyecto de aula',
            'Plan especial',
            'Plan de evaluación',
            'Informe final',
        ], collect($grupo['items'])->pluck('label')->all());
    }

    /** @test */
    public function el_manager_multirrol_ve_los_dropdowns_de_todos_sus_flags(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $manager = $this->makeUsuario([
            'is_inicial_manager' => true,
            'is_profesor' => true,
            'is_leadership' => true,
        ]);

        $this->actingAs($manager);

        $html = app(MenuBuilder::class, ['layout' => 'seguimiento_inicial'])->renderDesktop();

        $this->assertStringContainsString(route('inicials.seguimiento.index'), $html);
        $this->assertStringContainsString(route('app.profesors.home'), $html);
        $this->assertStringContainsString(route('app.leadership.dashboard'), $html);
    }

    /** @test */
    public function el_manager_sin_otros_flags_no_ve_los_dropdowns_ajenos(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $manager = $this->makeManager();
        $this->actingAs($manager);

        $html = app(MenuBuilder::class, ['layout' => 'seguimiento_inicial'])->renderDesktop();

        $this->assertStringContainsString(route('inicials.seguimiento.index'), $html);
        $this->assertStringNotContainsString(route('app.profesors.home'), $html);
        $this->assertStringNotContainsString(route('app.leadership.dashboard'), $html);
    }

    /** @test */
    public function un_docente_o_un_admin_sin_el_flag_no_ven_el_grupo(): void
    {
        $this->saltarSiFaltaLaMigracion();

        foreach ([$this->makeDocente()['user'], $this->makeUsuario(['is_admin' => true])] as $usuario) {
            $this->actingAs($usuario);

            $grupos = collect((new MenuBuilder('seguimiento_inicial'))->resolveGroups())->pluck('label');

            $this->assertNotContains('Seguimiento Inicial', $grupos);
        }
    }

    /** @test */
    public function el_layout_y_el_subtitulo_del_manager_son_los_propios(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $manager = $this->makeManager();

        $this->assertSame('seguimiento_inicial', MenuBuilder::resolveLayoutForUser($manager));
        $this->assertSame('Seguimiento · Educación Inicial', MenuBuilder::subtitleForLayout('seguimiento_inicial'));
    }

    /** @test */
    public function el_modelo_expone_el_flag_su_etiqueta_y_la_auditoria(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $manager = $this->makeManager();
        $docente = $this->makeDocente()['user'];

        $this->assertTrue($manager->isInicialManager());
        $this->assertFalse($docente->isInicialManager());

        $this->assertSame('Seguimiento Inicial', $manager->role_label);
        $this->assertContains('is_inicial_manager', $manager->auditableAttributes());
    }

    // ═══════════════════════════════════════════════════════════════
    //  ADMINISTRACIÓN DEL FLAG
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function un_admin_crea_un_usuario_manager_y_el_flag_se_guarda(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $admin = $this->makeUsuario(['is_admin' => true]);
        $username = 'manager_'.uniqid();

        Livewire::actingAs($admin)->test(UsersIndexComponent::class)
            ->set('username', $username)
            ->set('email', $username.'@example.test')
            ->set('password', 'secret123')
            ->set('is_active', 'enable')
            ->set('is_inicial_manager', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'username' => $username,
            'is_inicial_manager' => 1,
            'is_inicial' => 0,
        ]);
    }

    /** @test */
    public function al_editar_un_manager_el_checkbox_queda_marcado(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $admin = $this->makeUsuario(['is_admin' => true]);
        $manager = $this->makeManager();

        Livewire::actingAs($admin)->test(UsersIndexComponent::class)
            ->call('edit', $manager->id)
            ->assertSet('is_inicial_manager', true)
            ->assertSet('is_inicial', false);
    }

    /** @test */
    public function el_filtro_por_inicial_manager_solo_devuelve_managers(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $admin = $this->makeUsuario(['is_admin' => true]);
        $manager = $this->makeUsuario([
            'is_inicial_manager' => true,
            'username' => 'manager_filtro_'.uniqid(),
        ]);
        $otro = $this->makeUsuario([
            'is_profesor' => true,
            'username' => 'docente_filtro_'.uniqid(),
        ]);

        Livewire::actingAs($admin)->test(UsersIndexComponent::class)
            ->set('filter_role', 'inicial_manager')
            ->assertSee($manager->username)
            ->assertDontSee($otro->username);
    }

    /** @test */
    public function el_filtro_usuario_estandar_no_incluye_managers(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $admin = $this->makeUsuario(['is_admin' => true]);
        $manager = $this->makeUsuario([
            'is_inicial_manager' => true,
            'username' => 'manager_estandar_'.uniqid(),
        ]);

        Livewire::actingAs($admin)->test(UsersIndexComponent::class)
            ->set('filter_role', 'standard')
            ->assertDontSee($manager->username);
    }
}
