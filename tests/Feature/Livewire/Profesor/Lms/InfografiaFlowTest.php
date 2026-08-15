<?php

namespace Tests\Feature\Livewire\Profesor\Lms;

use App\Livewire\Profesor\Lms\LessonWizard;
use App\Models\app\Academy\Activity;
use App\Models\User;
use App\Services\KimiService;
use App\Services\NvidiaService;
use App\Services\OpenRouterService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class InfografiaFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function createProfesorUser(): array
    {
        $user = User::factory()->create(['is_admin' => false]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-12345678',
            'ci_profesor' => '12345678',
            'name' => 'Profesor Test',
            'lastname' => 'Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user);

        return ['user' => $user, 'profesor_id' => $profesorId];
    }

    private function buildFkChain(): array
    {
        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-TEST-'.uniqid(),
            'code_sm' => 'LT',
            'name' => 'Test Lapso',
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA',
            'name' => 'Test Scale',
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Institution',
            'legalname' => 'Test Institution Legal',
            'rif_institution' => 'J-'.uniqid(),
            'email_institution' => 'test-'.uniqid().'@institution.test',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año Escolar',
            'description' => 'Test',
            'finicial' => now(),
            'ffinal' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId,
            'name' => 'Test PE',
            'description' => 'Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => 'PEST-TEST-'.uniqid(),
            'name' => 'Test Plan de Estudio',
            'scale' => $escalaId,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId,
            'name' => 'Test Grado',
            'code' => 'GR-TEST',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId,
            'name' => 'A',
            'description' => 'Sección A',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => 'ASIG-TEST',
            'name' => 'Test Asignatura',
            'tescala' => $escalaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $pestudioId,
            'grado_id' => $gradoId,
            'asignatura_id' => $asignaturaId,
            'status_component' => true,
            'status_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return compact(
            'lapsoId', 'escalaId', 'institucionId', 'pescolarId',
            'peducativoId', 'pestudioId', 'gradoId', 'seccionId',
            'asignaturaId', 'pensumId'
        );
    }

    private function createActivity(int $profesorId): Activity
    {
        $chain = $this->buildFkChain();

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $chain['pensumId'],
            'profesor_id' => $profesorId,
            'lapso_id' => $chain['lapsoId'],
            'seccion_id' => $chain['seccionId'],
            'objetivo' => 'Test objetivo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Ciclo del Agua',
            'thematic' => 'Tejido Temático Test',
            'description' => 'Lección sobre el ciclo del agua',
            'teaching' => 'Enseñanza Test',
            'learning' => 'Aprendizaje Esperado Test',
            'references' => 'Referencias Test',
            'observations' => 'Observaciones/ODS Test',
        ]);
    }

    private function mockOpenRouter(string $json): void
    {
        $this->mock(OpenRouterService::class, function ($mock) use ($json) {
            $mock->shouldReceive('ask')
                ->andReturn([
                    'success' => true,
                    'content' => $json,
                    'model' => 'test-model',
                    'usage' => null,
                    'error' => null,
                ]);
        });
    }

    private function validInfografiaJson(): string
    {
        return json_encode([
            'estructura' => [
                'tipo' => 'jerarquica',
                'niveles' => 4,
                'nodo_raiz' => [
                    'id' => 'n1',
                    'etiqueta' => 'Ciclo del Agua',
                    'descripcion' => 'Proceso continuo',
                    'color_fondo' => '#10b981',
                    'color_texto' => '#ffffff',
                    'icono_sugerido' => 'atom',
                    'hijos' => [
                        [
                            'id' => 'n2',
                            'etiqueta' => 'Evaporación',
                            'descripcion' => 'Líquido a vapor',
                            'color_fondo' => '#0ea5e9',
                            'color_texto' => '#ffffff',
                            'icono_sugerido' => 'globe',
                            'hijos' => [
                                [
                                    'id' => 'n3',
                                    'etiqueta' => 'Radiación solar',
                                    'descripcion' => 'Energía solar',
                                    'color_fondo' => '#f59e0b',
                                    'color_texto' => '#ffffff',
                                    'icono_sugerido' => 'lightbulb',
                                    'hijos' => [
                                        [
                                            'id' => 'n4',
                                            'etiqueta' => 'Calor',
                                            'descripcion' => 'Transferencia',
                                            'color_fondo' => '#8b5cf6',
                                            'color_texto' => '#ffffff',
                                            'icono_sugerido' => 'flask',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function abre_y_cierra_el_modal_de_configuracion(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->call('openInfografiaModal')
            ->assertSet('infografiaModalOpen', true);

        $component->call('closeInfografiaModal')
            ->assertSet('infografiaModalOpen', false)
            ->assertSet('infografiaPreviewSvg', '')
            ->assertSet('infografiaError', null);
    }

    /** @test */
    public function genera_infografia_valida_y_abre_la_vista_previa(): void
    {
        $this->mockOpenRouter($this->validInfografiaJson());

        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('infografiaConfig', [
            'niveles' => 4,
            'tipoEstructura' => 'jerarquica',
            'direccion' => 'vertical',
            'temaColor' => 'esafe',
        ]);

        $component->call('generateInfografia')
            ->assertSet('infografiaError', null)
            ->assertSet('infografiaPreviewOpen', true)
            ->assertNotSet('infografiaPreviewSvg', '');

        $svg = $component->get('infografiaPreviewSvg');
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('Ciclo del Agua', $svg);
        $this->assertStringContainsString('Evaporación', $svg);
    }

    /** @test */
    public function maneja_errores_de_ia_sin_romper(): void
    {
        $this->mock(OpenRouterService::class, function ($mock) {
            $mock->shouldReceive('ask')
                ->andReturn([
                    'success' => false,
                    'content' => null,
                    'model' => 'test-model',
                    'usage' => null,
                    'error' => 'API error simulado',
                ]);
        });

        $this->mock(NvidiaService::class, function ($mock) {
            $mock->shouldReceive('ask')
                ->andReturn([
                    'success' => false,
                    'content' => null,
                    'model' => 'test-model',
                    'usage' => null,
                    'error' => 'API error simulado',
                ]);
        });

        $this->mock(KimiService::class, function ($mock) {
            $mock->shouldReceive('ask')
                ->andReturn([
                    'success' => false,
                    'content' => null,
                    'model' => 'test-model',
                    'usage' => null,
                    'error' => 'API error simulado',
                ]);
        });

        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->call('generateInfografia')
            ->assertSet('infografiaError', 'Todos los servicios de IA fallaron al generar la infografía.');
    }

    /** @test */
    public function inserta_la_infografia_como_embed_en_el_wizard(): void
    {
        $this->mockOpenRouter($this->validInfografiaJson());

        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('infografiaConfig', [
            'niveles' => 4,
            'tipoEstructura' => 'jerarquica',
            'direccion' => 'vertical',
            'temaColor' => 'esafe',
        ]);

        $component->call('generateInfografia');

        $embedsBefore = count($component->get('wizardHtmlEmbeds'));
        $component->call('insertInfografiaEnEditor')
            ->assertSet('infografiaPreviewOpen', false);

        $embeds = $component->get('wizardHtmlEmbeds');
        $this->assertCount($embedsBefore + 1, $embeds);
        $this->assertEquals('Infografía Jerárquica', $embeds[$embedsBefore]['title']);
        $this->assertStringContainsString('infografia-wrapper', $embeds[$embedsBefore]['html_content']);
    }

    /** @test */
    public function renderiza_tipos_de_estructura_radial_flujo_y_matriz(): void
    {
        foreach (['radial', 'flujo', 'matriz'] as $tipo) {
            $json = $this->validInfografiaJson();
            $decoded = json_decode($json, true);
            $decoded['estructura']['tipo'] = $tipo;
            $this->mockOpenRouter(json_encode($decoded));

            $data = $this->createProfesorUser();
            $activity = $this->createActivity($data['profesor_id']);

            $component = Livewire::test(LessonWizard::class);
            $component->call('startWizard', $activity->id);

            $component->set('infografiaConfig', [
                'niveles' => 4,
                'tipoEstructura' => $tipo,
                'direccion' => 'vertical',
                'temaColor' => 'esafe',
            ]);

            $component->call('generateInfografia')
                ->assertSet('infografiaError', null)
                ->assertSet('infografiaPreviewOpen', true);

            $svg = $component->get('infografiaPreviewSvg');
            $this->assertStringContainsString('<svg', $svg);
            $this->assertStringContainsString('Ciclo del Agua', $svg);
        }
    }
}
