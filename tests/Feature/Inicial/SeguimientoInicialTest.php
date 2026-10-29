<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicialManager;
use App\Livewire\Admin\Users\IndexComponent as UsersIndexComponent;
use App\Livewire\Inicial\SeguimientoInicialComponent;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Learner\Estudiant;
use App\Models\User;
use App\Services\Inicial\RegistroInicial;
use App\Services\Inicial\SeguimientoInicial;
use App\Services\MenuBuilder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Quinta perspectiva del módulo de Educación Inicial: SEGUIMIENTO (solo lectura).
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
    public function el_componente_no_tiene_metodos_de_escritura(): void
    {
        foreach (['save', 'update', 'delete', 'create', 'store', 'destroy'] as $metodo) {
            $this->assertFalse(
                method_exists(SeguimientoInicialComponent::class, $metodo),
                "El componente no debe tener {$metodo}()."
            );
        }
    }

    /** @test */
    public function el_indice_no_expone_edicion_de_observacion_ni_recomendacion(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $manager = $this->makeManager();
        $docente = $this->makeDocente();
        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id']
        ));

        $this->actingAs($manager)
            ->get(route('inicials.seguimiento.eiplanningwks.index'))
            ->assertOk()
            ->assertSee('Nivel')
            ->assertDontSee('wire:model="observacion"', false)
            ->assertDontSee('wire:model="recomendacion"', false)
            ->assertDontSee('Nueva planificación')
            ->assertDontSee('Importar de s2526');
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
    public function el_estado_completo_se_ignora_fuera_de_la_planificacion_semanal(): void
    {
        $docente = $this->makeDocente();
        Eiprojectk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Proyecto visible con estado manipulado.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiprojectks'])
            ->set('estado', 'completo')
            ->assertOk()
            ->assertSee('Proyecto visible con estado manipulado.');
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
    public function filtra_por_estado_sin_revision_y_revisado(): void
    {
        $docente = $this->makeDocente();

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Plan sin revisión.', 'observacion' => null]
        ));

        Eiplanningwk::create($this->planAttributes(
            $docente['profesor_id'],
            $docente['grado_id'],
            $docente['seccion_id'],
            ['diagnostico' => 'Plan ya revisado.', 'observacion' => 'Observación registrada.']
        ));

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('estado', 'sin_revision')
            ->assertSee('Plan sin revisión.')
            ->assertDontSee('Plan ya revisado.');

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('estado', 'revisado')
            ->assertSee('Plan ya revisado.')
            ->assertDontSee('Plan sin revisión.');
    }

    /** @test */
    public function el_estado_completo_solo_incluye_semanas_con_las_50_estrategias(): void
    {
        $docente = $this->makeDocente();

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

        $dias = array_keys(config('inicial.dias_semana'));
        $momentos = array_keys(config('inicial.momentos_rutina_diaria'));

        $total = 0;
        foreach ($dias as $dia) {
            foreach ($momentos as $momento) {
                Eiplanningwstrategy::create([
                    'eiplanningwk_id' => $completo->id,
                    'day_of_week' => $dia,
                    'momento_rutina_diaria' => $momento,
                    'lunes' => 'Estrategia '.$dia.' '.$momento,
                    'order' => $total,
                ]);
                $total++;
            }
        }

        // Solo una estrategia en el plan incompleto.
        Eiplanningwstrategy::create([
            'eiplanningwk_id' => $incompleto->id,
            'day_of_week' => 'lunes',
            'momento_rutina_diaria' => 'Recibimiento',
            'lunes' => 'Única estrategia',
            'order' => 0,
        ]);

        $resumen = app(SeguimientoInicial::class)->resumen('eiplanningwks', [
            'profesor_id' => $docente['profesor_id'],
        ]);
        $this->assertSame(1, $resumen['completos']);

        Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->set('profesor_id', $docente['profesor_id'])
            ->set('estado', 'completo')
            ->assertSee('Semana completa.')
            ->assertDontSee('Semana incompleta.');
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
            $docente['seccion_id']
        ));

        $componente = Livewire::actingAs($this->makeManager())
            ->test(SeguimientoInicialComponent::class, ['entidad' => 'eiplanningwks'])
            ->call('abrirDetalle', $plan->id)
            ->assertSet('detalleId', $plan->id)
            ->assertSee('Abrir ficha completa')
            ->assertSee('Formato imprimible');

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
        $this->assertStringContainsString('1/', $estado['detalle']);
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
