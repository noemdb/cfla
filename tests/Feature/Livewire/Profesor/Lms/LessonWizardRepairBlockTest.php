<?php

namespace Tests\Feature\Livewire\Profesor\Lms;

use App\Livewire\Profesor\Lms\LessonWizard;
use App\Models\app\Academy\Activity;
use App\Services\OpenRouterService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Pruebas del botón "Repair" del paso 2 (editor de diapositivas):
 * `LessonWizard::repairSlideBlock()` — mejora el contenido de un bloque
 * con IA respetando las reglas de calidad establecidas.
 *
 * @group lesson-wizard
 * @group lesson-wizard-repair
 */
class LessonWizardRepairBlockTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Helpers de datos (mismo patrón que el test de caracterización) ──

    private function createProfesorUser(): array
    {
        $user = \App\Models\User::factory()->create(['is_admin' => false]);

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
            'name' => 'Test P.Escolar',
            'description' => 'Test',
            'finicial' => now(),
            'ffinal' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'name' => 'Test P.Educativo',
            'description' => 'Test',
            'pescolar_id' => $pescolarId,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'code' => 'PE-'.uniqid(),
            'name' => 'Test P.Estudio',
            'peducativo_id' => $peducativoId,
            'scale' => $escalaId,
            'planning_module' => 'true',
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

    private function createActivity(int $profesorId, array $overrides = []): Activity
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

        return Activity::create(array_merge([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Tema Generador Test',
            'thematic' => 'Tejido Temático Test',
            'description' => 'Actividad Evaluativa Test',
            'teaching' => 'Enseñanza Test',
            'learning' => 'Aprendizaje Esperado Test',
            'references' => 'Referencias Test',
            'observations' => 'Observaciones/ODS Test',
        ], $overrides));
    }

    /**
     * Mockea OpenRouterService para que siempre devuelva contenido predecible.
     */
    private function mockOpenRouter(string $content): void
    {
        $this->mock(OpenRouterService::class, function ($mock) use ($content) {
            $mock->shouldReceive('ask')
                ->andReturn([
                    'success' => true,
                    'content' => $content,
                    'model' => 'test-model',
                    'usage' => null,
                    'error' => null,
                ]);
        });
    }

    /** Abre el wizard y prepara una sección con un bloque de contenido. */
    private function wizardWithBlock(Activity $activity, string $body, string $type = 'TEXT')
    {
        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->call('addWizardSection');
        $component->call('addWizardFirstBlock', 0);

        $sections = $component->get('wizardSections');
        $sections[0]['contents'][0]['body'] = $body;
        $sections[0]['contents'][0]['type'] = $type;
        $component->set('wizardSections', $sections);

        return $component;
    }

    // ─── Tests ─────────────────────────────────────────────────────

    /** @test */
    public function repair_slide_block_reemplaza_el_body_con_contenido_mejorado(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('## Título Reparado'."\n\n".'Contenido mejorado por la IA.');

        $component = $this->wizardWithBlock($activity, 'contenido original con errores');

        $component->call('repairSlideBlock', 0, 0);

        $sections = $component->get('wizardSections');
        $this->assertStringContainsString('Título Reparado', $sections[0]['contents'][0]['body']);
        $this->assertStringContainsString('Contenido mejorado por la IA.', $sections[0]['contents'][0]['body']);
        $this->assertSame('TEXT', $sections[0]['contents'][0]['type']);
        $component->assertSet('generatingSection', null);
    }

    /** @test */
    public function repair_slide_block_conserva_el_tipo_html(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('<h2>Mejorado</h2><p>Contenido HTML pulido.</p>');

        $component = $this->wizardWithBlock($activity, '<p>contenido html original</p>', 'HTML');

        $component->call('repairSlideBlock', 0, 0);

        $sections = $component->get('wizardSections');
        $this->assertStringContainsString('<h2>Mejorado</h2>', $sections[0]['contents'][0]['body']);
        $this->assertSame('HTML', $sections[0]['contents'][0]['type']);
    }

    /** @test */
    public function repair_slide_block_procesa_svg_sin_danos_y_mantiene_el_tipo_imagen(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('contenido que no deberia usarse');

        $component = $this->wizardWithBlock($activity, '<svg><rect width="10" height="10"/></svg>', 'IMAGE');

        $component->call('repairSlideBlock', 0, 0);

        $sections = $component->get('wizardSections');
        $this->assertStringContainsString('<svg>', $sections[0]['contents'][0]['body']);
        $this->assertStringNotContainsString('no deberia usarse', $sections[0]['contents'][0]['body']);
        $this->assertSame('IMAGE', $sections[0]['contents'][0]['type']);
    }

    /** @test */
    public function repair_slide_block_repara_un_diagrama_mermaid_envuelto(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('graph TD'."\n".'A[Inicio] --> B[Final]'."\n".'B --> C[Resultado]');

        $body = '<div class="w-full bg-white rounded-xl border border-gray-200">'
            .'<div class="p-3 overflow-x-auto"><div class="mermaid">'
            ."graph TD\nA[Inicio] --> B[Fin]"
            .'</div></div></div>';

        $component = $this->wizardWithBlock($activity, $body, 'HTML');

        $component->call('repairSlideBlock', 0, 0);

        $sections = $component->get('wizardSections');
        $this->assertStringContainsString('class="mermaid"', $sections[0]['contents'][0]['body']);
        $this->assertStringContainsString('C[Resultado]', $sections[0]['contents'][0]['body']);
        $this->assertSame('HTML', $sections[0]['contents'][0]['type']);
    }

    /** @test */
    public function repair_slide_block_repara_notacion_matematica(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('<div id="math-block"><p>\(x = \frac{2}{4}\)</p></div>');

        $component = $this->wizardWithBlock(
            $activity,
            '<div id="math-block"><p>\(x = \frac{1}{2}\)</p></div>',
            'MATH'
        );

        $component->call('repairSlideBlock', 0, 0);

        $sections = $component->get('wizardSections');
        $this->assertStringContainsString('math-block', $sections[0]['contents'][0]['body']);
        $this->assertStringContainsString('frac{2}{4}', $sections[0]['contents'][0]['body']);
        $this->assertSame('MATH', $sections[0]['contents'][0]['type']);
    }

    /** @test */
    public function repair_slide_block_salta_si_el_bloque_esta_vacio(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('contenido generado');

        $component = $this->wizardWithBlock($activity, '');

        $component->call('repairSlideBlock', 0, 0);

        $sections = $component->get('wizardSections');
        $this->assertSame('', $sections[0]['contents'][0]['body']);
    }

    /** @test */
    public function repair_slide_block_no_modifica_si_la_leccion_esta_publicada(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        DB::table('lms_activity_publications')->insert([
            'activity_id' => $activity->id,
            'published_by' => $data['user']->id,
            'status' => 'PUBLISHED',
            'publish_at' => now(),
            'published_at' => now(),
            'allow_comments' => true,
            'allow_downloads' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->mockOpenRouter('contenido generado');
        $component = $this->wizardWithBlock($activity, 'contenido original publicado');

        $component->call('repairSlideBlock', 0, 0);

        $sections = $component->get('wizardSections');
        $this->assertSame('contenido original publicado', $sections[0]['contents'][0]['body']);
    }

    /** @test */
    public function repair_slide_block_salta_si_los_indices_no_existen(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('contenido generado');

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->call('addWizardSection');

        $component->call('repairSlideBlock', 99, 99);

        $component->assertSet('generatingSection', null);
    }

    /** @test */
    public function validate_mermaid_diagram_detecta_palabras_concatenadas_sin_espacios(): void
    {
        $this->createProfesorUser();
        $component = Livewire::test(LessonWizard::class);

        $method = new \ReflectionMethod(LessonWizard::class, 'validateMermaidDiagram');
        $method->setAccessible(true);

        // Label con palabras concatenadas (ej. el bug "AutoconocimientoVocacioal")
        $bad = $method->invoke(
            $component->instance(),
            "graph TD\nA[\"AutoconocimientoVocacional\"] --> B[\"OrientaciónProfesional\"]"
        );
        $this->assertFalse($bad['ok']);
        $this->assertStringContainsString('concatenadas', implode(' ', $bad['issues']));

        // Labels correctamente espaciados
        $good = $method->invoke(
            $component->instance(),
            "graph TD\nA[\"Autoconocimiento Vocacional\"] --> B[\"Orientación Profesional\"]"
        );
        $this->assertTrue($good['ok']);
    }

    /** @test */
    public function post_process_mermaid_reinserta_espacios_en_labels_concatenados(): void
    {
        $this->createProfesorUser();
        $component = Livewire::test(LessonWizard::class);

        $method = new \ReflectionMethod(LessonWizard::class, 'postProcessMermaid');
        $method->setAccessible(true);

        // Mismo diagrama del bug reportado (captura mermaid.png):
        // "La dimensión del autoconocimiento vocacional"
        $code = "graph TD\n"
            ."A[\"AutoconocimientoVocacional\"] --> B[\"InteresesVocacionales\"]\n"
            ."B --> C[\"Curiosidad yMotivaciónInterna\"]\n"
            ."A --> D[\"HabilidadesPersonales\"]\n"
            ."D --> E[\"CapacidadesNaturalesyDesarrolladas\"]\n"
            ."A --> F[\"ValoresFundamentales\"]\n"
            ."F --> G[\"Fortalezas yÁreasdeMejora\"]\n"
            ."C --> H[\"DecisionesVocacionalesyAcertadas\"]\n"
            ."E --> H\n"
            ."G --> H";

        $fixed = $method->invoke($component->instance(), $code);

        $this->assertStringContainsString('Autoconocimiento Vocacional', $fixed);
        $this->assertStringContainsString('Intereses Vocacionales', $fixed);
        $this->assertStringContainsString('Curiosidad y Motivación Interna', $fixed);
        $this->assertStringContainsString('Habilidades Personales', $fixed);
        $this->assertStringContainsString('Capacidades Naturales y', $fixed);
        $this->assertStringContainsString('Desarrolladas', $fixed);
        $this->assertStringContainsString('Valores Fundamentales', $fixed);
        $this->assertStringContainsString('Fortalezas y Áreas de Mejora', $fixed);
        $this->assertStringContainsString('Decisiones Vocacionales y', $fixed);
        $this->assertStringContainsString('Acertadas', $fixed);
        $this->assertStringNotContainsString('AutoconocimientoVocacional', $fixed);
        $this->assertStringNotContainsString('yMotivación', $fixed);
    }
}
