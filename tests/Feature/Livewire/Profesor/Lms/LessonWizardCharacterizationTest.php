<?php

namespace Tests\Feature\Livewire\Profesor\Lms;

use App\Livewire\Profesor\Lms\LessonWizard;
use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Models\app\Academy\Lms\LmsActivityLink;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsHtmlEmbed;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Services\NvidiaService;
use App\Services\OpenRouterService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Caracterización del LessonWizard para el refactor SPEC-REFACTOR-LESSONWIZARD-001.
 *
 * Estos tests capturan el comportamiento ACTUAL del componente, bugs incluidos.
 * NO se deben modificar durante el refactor — solo añadir nuevos tests si es necesario.
 * Sirven como red de seguridad (Phase 0) antes de cualquier extracción de código.
 *
 * @group characterization
 * @group lesson-wizard
 */
class LessonWizardCharacterizationTest extends TestCase
{
    use DatabaseTransactions;

    // ─── Helpers de datos ────────────────────────────────────────────

    private function createProfesorUser(bool $isAdmin = false, array $extraUserAttrs = []): array
    {
        $user = User::factory()->create(array_merge(['is_admin' => $isAdmin], $extraUserAttrs));

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
     * Crea una segunda sección en el mismo grado de una actividad.
     * Necesaria para tests de exportación/importación que requieren
     * al menos dos secciones en el mismo grado.
     */
    private function createSecondSectionInGrade($activity): int
    {
        $gradeId = $activity->pevaluacion->seccion->grado_id;

        return DB::table('seccions')->insertGetId([
            'grado_id' => $gradeId,
            'name' => 'B',
            'description' => 'Sección B',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Mockea OpenRouterService para que siempre devuelva contenido predecible.
     */
    private function mockOpenRouter(string $content = 'Título Generado || Descripción generada para la lección.'): void
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

    // ═══════════════════════════════════════════════════════════════
    //  1. TESTS DE LISTADO (mode === 'list')
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function monta_en_modo_listado_por_defecto(): void
    {
        $this->createProfesorUser();
        $component = Livewire::test(LessonWizard::class);

        $component->assertSet('mode', 'list');
        $component->assertSet('currentStep', 1);
        $component->assertSet('viewMode', 'grid');
    }

    /** @test */
    public function listado_muestra_actividades_del_profesor(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        // Limpiar filtro de lapso que mount() puebla automáticamente
        $component->set('lapsoId', '');

        $component->assertSet('mode', 'list');
        $component->assertSee($activity->topic);
    }

    /** @test */
    public function listado_no_muestra_actividades_de_otro_profesor(): void
    {
        $data = $this->createProfesorUser();

        $otherProfesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-87654321',
            'ci_profesor' => '87654321',
            'name' => 'Otro Profesor',
            'lastname' => 'Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $otherActivity = $this->createActivity($otherProfesorId);

        $component = Livewire::test(LessonWizard::class);
        $component->set('lapsoId', '');
        $component->assertDontSee($otherActivity->topic);
    }

    /** @test */
    public function cambiar_viewMode_guarda_en_sesion(): void
    {
        $this->createProfesorUser();
        $component = Livewire::test(LessonWizard::class);

        $component->set('viewMode', 'table');
        $component->assertSet('viewMode', 'table');
        $this->assertEquals('table', session('lesson_wizard_view_mode'));
    }

    /** @test */
    public function search_filtra_actividades_por_topic(): void
    {
        $data = $this->createProfesorUser();
        $activity1 = $this->createActivity($data['profesor_id'], ['topic' => 'Matemáticas Básicas']);
        $activity2 = $this->createActivity($data['profesor_id'], ['topic' => 'Lengua y Literatura']);

        $component = Livewire::test(LessonWizard::class);
        $component->set('lapsoId', '');

        $component->assertSee('Matemáticas Básicas');
        $component->assertSee('Lengua y Literatura');

        $component->set('search', 'Matemáticas');
        $component->assertSee('Matemáticas Básicas');
        $component->assertDontSee('Lengua y Literatura');
    }

    /** @test */
    public function detail_modal_muestra_detalle_de_actividad(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('showDetails', $activity->id);

        $component->assertSet('showDetailModal', true);
        $this->assertNotNull($component->get('detailActivity'));
        $this->assertEquals($activity->id, $component->get('detailActivity')->id);
    }

    /** @test */
    public function closeDetails_cierra_modal(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('showDetails', $activity->id);
        $component->call('closeDetails');

        $component->assertSet('showDetailModal', false);
        $component->assertSet('detailActivity', null);
    }

    // ═══════════════════════════════════════════════════════════════
    //  2. TESTS DE INICIO DEL WIZARD Y NAVEGACIÓN
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function startWizard_cambia_a_modo_wizard(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->assertSet('mode', 'wizard');
        $component->assertSet('currentStep', 1);
        $component->assertSet('selectedActivityId', $activity->id);
        $component->assertSet('lessonTitle', $activity->topic);
        $component->assertSet('lessonDescription', $activity->description);
        $component->assertSet('saved', true);
    }

    /** @test */
    public function startWizard_carga_publicacion_existente(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        LmsActivityPublication::create([
            'activity_id' => $activity->id,
            'status' => 'PUBLISHED',
            'published_by' => $data['user']->id,
            'allow_downloads' => true,
        ]);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->assertSet('isPublished', true);
        $component->assertSet('pubStatus', 'PUBLISHED');
    }

    /** @test */
    public function goToStep_navega_entre_pasos(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->assertSet('currentStep', 1);

        $component->call('goToStep', 3);
        $component->assertSet('currentStep', 3);

        $component->call('goToStep', 5);
        $component->assertSet('currentStep', 5);

        $component->call('goToStep', 6);
        $component->assertSet('currentStep', 5);

        $component->call('goToStep', 0);
        $component->assertSet('currentStep', 1);
    }

    /** @test */
    public function goToStep_vuelve_saved_a_false(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->assertSet('saved', true);

        $component->call('goToStep', 2);
        $component->assertSet('saved', false);
    }

    /** @test */
    public function backToList_resetea_el_estado_del_wizard(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->set('lessonTitle', 'Título modificado');

        $component->call('backToList');

        $component->assertSet('mode', 'list');
        $component->assertSet('currentStep', 1);
        $component->assertSet('selectedActivityId', null);
        $component->assertSet('wizardSections', []);
        $component->assertSet('lessonTitle', '');
        $component->assertSet('saved', false);
    }

    // ═══════════════════════════════════════════════════════════════
    //  3. TESTS DE NAVEGACIÓN DE SLIDES
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function goToSlide_navega_entre_diapositivas(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Slide 1');
        $component->call('addWizardSection');
        $component->set('newSectionTitle', 'Slide 2');
        $component->call('addWizardSection');
        $component->set('newSectionTitle', 'Slide 3');
        $component->call('addWizardSection');

        $component->call('goToSlide', 1);
        $component->assertSet('currentSlideIndex', 1);

        $component->call('nextSlide');
        $component->assertSet('currentSlideIndex', 2);

        $component->call('prevSlide');
        $component->assertSet('currentSlideIndex', 1);

        $component->call('goToSlide', 10);
        $component->assertSet('currentSlideIndex', 2);
    }

    /** @test */
    public function toggleSidebar_commuta_y_persiste(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->assertSet('sidebarCompact', false);

        $component->call('toggleSidebar');
        $component->assertSet('sidebarCompact', true);
        $this->assertTrue(session('lms_sidebar_compact'));

        $component->call('toggleSidebar');
        $component->assertSet('sidebarCompact', false);
    }

    // ═══════════════════════════════════════════════════════════════
    //  4. TESTS DE CRUD DE SECCIONES
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function addWizardSection_agrega_seccion_con_titulo_personalizado(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Introducción');
        $component->call('addWizardSection');

        $sections = $component->get('wizardSections');
        $this->assertCount(1, $sections);
        $this->assertEquals('Introducción', $sections[0]['title']);
        $this->assertTrue($sections[0]['is_visible']);
        $this->assertStringStartsWith('temp_', $sections[0]['id']);
    }

    /** @test */
    public function addWizardSection_usa_titulo_default_si_vacio(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', '  ');
        $component->call('addWizardSection');

        $sections = $component->get('wizardSections');
        $this->assertCount(1, $sections);
        $this->assertEquals('Nueva diapositiva', $sections[0]['title']);
    }

    /** @test */
    public function removeWizardSection_elimina_seccion_por_indice(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Sección 1');
        $component->call('addWizardSection');
        $component->set('newSectionTitle', 'Sección 2');
        $component->call('addWizardSection');

        $component->call('removeWizardSection', 0);
        $sections = $component->get('wizardSections');
        $this->assertCount(1, $sections);
        $this->assertEquals('Sección 2', $sections[0]['title']);
    }

    /** @test */
    public function toggleWizardSectionVisibility_commuta_visibilidad(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Sección Test');
        $component->call('addWizardSection');

        $component->call('toggleWizardSectionVisibility', 0);
        $this->assertFalse($component->get('wizardSections')[0]['is_visible']);

        $component->call('toggleWizardSectionVisibility', 0);
        $this->assertTrue($component->get('wizardSections')[0]['is_visible']);
    }

    /** @test */
    public function moveSlide_reordena_secciones(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Primera');
        $component->call('addWizardSection');
        $component->set('newSectionTitle', 'Segunda');
        $component->call('addWizardSection');
        $component->set('newSectionTitle', 'Tercera');
        $component->call('addWizardSection');

        $component->call('moveSlide', 2, 0);
        $sections = $component->get('wizardSections');
        $this->assertEquals('Tercera', $sections[0]['title']);
        $this->assertEquals('Primera', $sections[1]['title']);
        $this->assertEquals('Segunda', $sections[2]['title']);
    }

    /** @test */
    public function publishedGuard_bloquea_operaciones_en_leccion_publicada(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        LmsActivityPublication::create([
            'activity_id' => $activity->id,
            'status' => 'PUBLISHED',
            'published_by' => $data['user']->id,
        ]);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->assertSet('isPublished', true);

        $component->set('newSectionTitle', 'Nueva');
        $component->call('addWizardSection');
        $this->assertCount(0, $component->get('wizardSections'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  5. TESTS DE CRUD DE CONTENIDO
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function addWizardContent_agrega_bloque_a_seccion(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Sección');
        $component->call('addWizardSection');

        $component->set('contentTitle', 'Mi Bloque');
        $component->set('contentBody', 'Contenido del bloque de prueba');
        $component->call('addWizardContent', 0);

        $sections = $component->get('wizardSections');
        $this->assertCount(1, $sections[0]['contents']);
        $this->assertEquals('Contenido del bloque de prueba', $sections[0]['contents'][0]['body']);
        $this->assertEquals('TEXT', $sections[0]['contents'][0]['type']);
    }

    /** @test */
    public function addWizardFirstBlock_crea_bloque_vacio(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Sección');
        $component->call('addWizardSection');

        $component->call('addWizardFirstBlock', 0);

        $sections = $component->get('wizardSections');
        $this->assertCount(1, $sections[0]['contents']);
        $this->assertEquals('', $sections[0]['contents'][0]['body']);
        $this->assertNull($sections[0]['contents'][0]['title']);
    }

    /** @test */
    public function removeWizardContent_elimina_bloque(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Sección');
        $component->call('addWizardSection');

        $component->set('contentBody', 'Bloque 1');
        $component->call('addWizardContent', 0);
        $component->set('contentBody', 'Bloque 2');
        $component->call('addWizardContent', 0);

        $component->call('removeWizardContent', 0, 0);
        $sections = $component->get('wizardSections');
        $this->assertCount(1, $sections[0]['contents']);
        $this->assertEquals('Bloque 2', $sections[0]['contents'][0]['body']);
    }

    // ═══════════════════════════════════════════════════════════════
    //  6. TESTS DE CRUD DE ENLACES Y EMBEDS
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function addWizardLink_agrega_enlace(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('linkTitle', 'Recurso Web');
        $component->set('linkUrl', 'https://ejemplo.com/recurso');
        $component->set('linkType', 'REFERENCE');
        $component->call('addWizardLink');

        $links = $component->get('wizardLinks');
        $this->assertCount(1, $links);
        $this->assertEquals('Recurso Web', $links[0]['title']);
        $this->assertEquals('https://ejemplo.com/recurso', $links[0]['url']);
        $this->assertEquals('REFERENCE', $links[0]['link_type']);
    }

    /** @test */
    public function removeWizardLink_elimina_enlace(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('linkTitle', 'Enlace 1');
        $component->set('linkUrl', 'https://ejemplo.com/1');
        $component->call('addWizardLink');

        $component->set('linkTitle', 'Enlace 2');
        $component->set('linkUrl', 'https://ejemplo.com/2');
        $component->call('addWizardLink');

        $component->call('removeWizardLink', 0);
        $links = $component->get('wizardLinks');
        $this->assertCount(1, $links);
        $this->assertEquals('https://ejemplo.com/2', $links[0]['url']);
    }

    /** @test */
    public function addWizardHtmlEmbed_agrega_embed(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('embedTitle', 'Diagrama Flujo');
        $component->set('embedHtml', '<p>Contenido embed</p>');
        $component->call('addWizardHtmlEmbed');

        $embeds = $component->get('wizardHtmlEmbeds');
        $this->assertCount(1, $embeds);
        $this->assertEquals('Diagrama Flujo', $embeds[0]['title']);
        $this->assertEquals('<p>Contenido embed</p>', $embeds[0]['html_content']);
    }

    /** @test */
    public function removeWizardHtmlEmbed_elimina_embed(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('embedTitle', 'Embed 1');
        $component->set('embedHtml', '<p>1</p>');
        $component->call('addWizardHtmlEmbed');

        $component->set('embedTitle', 'Embed 2');
        $component->set('embedHtml', '<p>2</p>');
        $component->call('addWizardHtmlEmbed');

        $component->call('removeWizardHtmlEmbed', 0);
        $embeds = $component->get('wizardHtmlEmbeds');
        $this->assertCount(1, $embeds);
        $this->assertEquals('Embed 2', $embeds[0]['title']);
    }

    // ═══════════════════════════════════════════════════════════════
    //  7. TESTS DE GENERACIÓN CON IA (MOCKEADA)
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function generateStep1Content_llena_titulo_y_descripcion(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('Título Generado || Descripción generada automáticamente.');

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->call('generateStep1Content');

        $component->assertSet('lessonTitle', 'Título Generado');
        $component->assertSet('lessonDescription', 'Descripción generada automáticamente.');
        $component->assertSet('showGenerationResult', true);
        $component->assertSet('generationType', 'step1');
    }

    /** @test */
    public function generateStep1Content_maneja_error_de_ia(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $this->mock(OpenRouterService::class, function ($mock) {
            $mock->shouldReceive('ask')
                ->andReturn([
                    'success' => false,
                    'content' => null,
                    'model' => null,
                    'usage' => null,
                    'error' => 'Error simulado',
                ]);
        });

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->call('generateStep1Content');

        $component->assertSet('generatingStep1', false);
    }

    /** @test */
    public function generateSectionContent_agrega_contenido_a_seccion(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('Contenido generado para la sección.');

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Mi Sección');
        $component->call('addWizardSection');

        $component->call('generateSectionContent', 0);

        $sections = $component->get('wizardSections');
        $this->assertCount(1, $sections[0]['contents']);
        $this->assertStringContainsString('Contenido generado', $sections[0]['contents'][0]['body']);
    }

    /** @test */
    public function generateSectionContent_salta_si_indice_invalido(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->call('generateSectionContent', 99);

        $component->assertSet('generatingSection', null);
    }

    /** @test */
    public function generateSlideText_agrega_bloque(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('## Título Generado'."\n\n".'Contenido markdown del slide.');

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Slide Test');
        $component->call('addWizardSection');

        $component->call('generateSlideText');

        $sections = $component->get('wizardSections');
        $this->assertNotEmpty($sections[0]['contents']);
    }

    /** @test */
    public function generateSlideImage_crea_bloque(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $this->mockOpenRouter('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect width="400" height="300" fill="#f8f9fa"/><text x="200" y="150" text-anchor="middle">Diagrama Educativo</text></svg>');

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Slide con Imagen');
        $component->call('addWizardSection');

        $component->call('generateSlideImage');

        $sections = $component->get('wizardSections');
        $this->assertNotEmpty($sections[0]['contents']);
    }

    /** @test */
    public function generateReviewQuestions_genera_preguntas(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $this->mockOpenRouter('## Preguntas de Repaso

1. **Pregunta 1** — Respuesta explicativa.
2. **Pregunta 2** — Respuesta explicativa.');

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->set('lessonTitle', 'Mi Lección');

        $component->call('generateReviewQuestions');

        $this->assertStringContainsString('Preguntas de Repaso', $component->get('reviewQuestions'));
    }

    /** @test */
    public function generateReviewQuestions_requiere_titulo(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('lessonTitle', '');
        $component->call('generateReviewQuestions');

        $component->assertSet('reviewQuestions', '');
    }

    // ═══════════════════════════════════════════════════════════════
    //  8. TESTS DE GENERACIÓN DE SECCIONES (PASO 2) CON IA
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function generateStep2Sections_agrega_secciones_desde_ia(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $this->mockOpenRouter(implode("\n", [
            '//INICIO',
            '',
            'Contenido de inicio para la lección educativa donde se presentan los conceptos fundamentales que los estudiantes van a aprender a lo largo de esta unidad didáctica del curso escolar correspondiente al programa de estudios vigente en el sistema educativo nacional venezolano.',
            '',
            '//DESARROLLO',
            '',
            '**Conceptos fundamentales del aprendizaje**',
            'En esta primera sección de la lección los estudiantes van a aprender los conceptos fundamentales que son necesarios para comprender el tema principal de esta unidad didáctica del curso escolar correspondiente al plan de estudios oficial vigente. Es importante que los alumnos presten atención a cada uno de los contenidos que se presentan a continuación porque estos sientan las bases para todo el aprendizaje futuro que se va a desarrollar en las secciones posteriores de la lección y en las unidades siguientes del programa educativo. Los docentes deben guiar este proceso educativo con ejemplos claros y actividades prácticas que faciliten la comprensión de los conceptos más importantes que se abordan en esta primera sección de la unidad didáctica del curso escolar correspondiente al año académico en curso.',
            '',
            '**Aplicaciones prácticas de los contenidos**',
            'Una vez que los estudiantes han comprendido los conceptos fundamentales de la lección es necesario explorar las aplicaciones prácticas que tienen estos contenidos en contextos reales y cotidianos del entorno escolar y social de los alumnos del curso. La capacidad de transferir el conocimiento teórico a situaciones concretas de la vida diaria es uno de los indicadores más importantes del aprendizaje profundo y significativo que se busca desarrollar en los estudiantes del sistema educativo. Por ello es necesario presentar una variedad de ejercicios y problemas contextualizados que permitan a los alumnos practicar y aplicar lo aprendido en escenarios auténticos relacionados con su vida diaria y su entorno inmediato familiar y social.',
            '',
            '**Ejercicios de refuerzo y práctica**',
            'Los ejercicios de refuerzo y práctica constituyen una herramienta pedagógica esencial para consolidar los aprendizajes adquiridos durante las fases anteriores del proceso educativo formal y sistemático. En esta sección se presentan una serie de actividades complementarias diseñadas específicamente para que los estudiantes practiquen de manera autónoma y refuercen su comprensión de los contenidos abordados en la lección. Cada ejercicio incluye instrucciones claras y precisas así como ejemplos resueltos que guían al alumno en su proceso de aprendizaje autodirigido y autónomo dentro del contexto del aula y del hogar. Estas actividades están diseñadas para promover el aprendizaje significativo y la autonomía del estudiante en su proceso de formación académica integral.',
            '',
            '**Evaluación formativa del aprendizaje**',
            'La evaluación formativa del aprendizaje es un proceso continuo y sistemático que permite tanto al docente como al estudiante identificar los avances y las dificultades que surgen durante el proceso de enseñanza y aprendizaje escolar. En esta sección se proponen diversas estrategias e instrumentos de evaluación que pueden aplicarse a lo largo de la unidad didáctica para monitorear el progreso de los alumnos y ajustar la enseñanza según sus necesidades específicas de aprendizaje. La retroalimentación oportuna y constructiva es un componente clave de este proceso evaluativo que favorece el aprendizaje profundo y significativo de los estudiantes del curso.',
            '',
            '**Actividades de cierre y síntesis**',
            'Las actividades de cierre y síntesis final tienen como objetivo principal integrar y consolidar todos los aprendizajes desarrollados a lo largo de la unidad didáctica completa del curso escolar. Estas actividades permiten a los estudiantes establecer conexiones significativas entre los diferentes conceptos abordados durante la lección y reflexionar sobre su propio proceso de aprendizaje personal y académico. La elaboración de mapas conceptuales resúmenes escritos y exposiciones orales son algunas de las estrategias pedagógicas que se pueden utilizar para facilitar esta síntesis final y garantizar que los aprendizajes sean duraderos y transferibles a otros contextos educativos y situaciones de la vida real de los estudiantes.',
            '',
            '//CIERRE',
            '',
            'Contenido final de cierre para concluir la lección educativa y reforzar los aprendizajes más importantes que los estudiantes han adquirido durante el desarrollo de toda la unidad didáctica del curso escolar. Este cierre incluye un resumen de los conceptos clave y sugerencias prácticas para continuar profundizando en el tema de manera autónoma y autodidacta fuera del aula de clases.',
        ]));

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->set('lessonTitle', 'Mi Lección');

        $component->call('generateStep2Sections');

        $sections = $component->get('wizardSections');
        $this->assertNotEmpty($sections);
    }

    // ═══════════════════════════════════════════════════════════════
    //  9. TESTS DE GUARDADO (saveStep2)
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function saveStep2_persiste_secciones_y_contenidos(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Introducción');
        $component->call('addWizardSection');
        $component->set('contentBody', 'Contenido de introducción');
        $component->call('addWizardContent', 0);

        $component->set('newSectionTitle', 'Desarrollo');
        $component->call('addWizardSection');
        $component->set('contentBody', 'Contenido de desarrollo');
        $component->call('addWizardContent', 1);

        $component->set('saveAnyway', true);
        $component->call('saveStep2');

        $this->assertEquals(2, LmsActivitySection::where('activity_id', $activity->id)->count());
        $this->assertEquals(2, LmsActivityContent::whereIn('section_id', LmsActivitySection::where('activity_id', $activity->id)->pluck('id'))->count());

        // Las secciones en memoria se reemplazan con IDs reales (los contenidos conservan temp_)
        $sections = $component->get('wizardSections');
        foreach ($sections as $section) {
            $this->assertStringStartsNotWith('temp_', (string) $section['id']);
        }
    }

    /** @test */
    public function saveStep2_persiste_titulo_y_descripcion(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('lessonTitle', 'Nuevo Título Lección');
        $component->set('lessonDescription', 'Nueva descripción de la lección');
        $component->set('saveAnyway', true);

        $component->call('saveStep2');

        $activity->refresh();
        $this->assertEquals('Nuevo Título Lección', $activity->topic);
        $this->assertEquals('Nueva descripción de la lección', $activity->description);
    }

    /** @test */
    public function saveStep2_muestra_confirmacion_si_no_hay_secciones(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->call('saveStep2');

        $component->assertSet('showUnsavedConfirm', true);
        $component->assertSet('pendingSaveAction', 'saveStep2');
    }

    /** @test */
    public function saveStep2_guarda_recursos_enlaces_embeds(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        // Agregar sección primero
        $component->set('newSectionTitle', 'Sección');
        $component->call('addWizardSection');
        $component->set('saveAnyway', true);
        $component->call('saveStep2');
        $sections = $component->get('wizardSections');
        $sectionId = $sections[0]['id'];

        // Agregar recursos, enlaces, embeds
        $component->set('linkSectionId', $sectionId);
        $component->set('linkTitle', 'Enlace Guardado');
        $component->set('linkUrl', 'https://test.com/guardado');
        $component->call('addWizardLink');

        $component->set('embedSectionId', $sectionId);
        $component->set('embedTitle', 'Embed Guardado');
        $component->set('embedHtml', '<p>Embed</p>');
        $component->call('addWizardHtmlEmbed');

        $component->call('saveStep2');

        $this->assertGreaterThanOrEqual(1, LmsActivityLink::where('activity_id', $activity->id)->count());
        $this->assertGreaterThanOrEqual(1, LmsHtmlEmbed::where('activity_id', $activity->id)->count());
    }

    // ═══════════════════════════════════════════════════════════════
    //  10. TESTS DE EXPORTACIÓN
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function showExport_abre_modal_con_datos_disponibles(): void
    {
        $data = $this->createProfesorUser();
        $sourceActivity = $this->createActivity($data['profesor_id'], ['topic' => 'Actividad Origen']);
        $targetActivity = $this->createActivity($data['profesor_id'], ['topic' => 'Actividad Destino']);

        // Crear segunda sección para que showExport no cierre por falta de secciones
        $this->createSecondSectionInGrade($targetActivity);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $sourceActivity->id);

        $component->call('showExport', $targetActivity->id);

        $component->assertSet('showExportModal', true);
        $component->assertSet('exportActivityId', $targetActivity->id);
    }

    /** @test */
    public function closeExportModal_cierra_modal(): void
    {
        $data = $this->createProfesorUser();
        $sourceActivity = $this->createActivity($data['profesor_id']);
        $targetActivity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $sourceActivity->id);
        $component->call('showExport', $targetActivity->id);

        $component->call('closeExportModal');
        $component->assertSet('showExportModal', false);
        $component->assertSet('exportActivityId', null);
    }

    /** @test */
    public function goToExportStep_navega_pasos_export(): void
    {
        $data = $this->createProfesorUser();
        $sourceActivity = $this->createActivity($data['profesor_id']);
        $targetActivity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $sourceActivity->id);
        $component->call('showExport', $targetActivity->id);

        $component->assertSet('exportWizardStep', 1);
        $component->call('goToExportStep', 3);
        $component->assertSet('exportWizardStep', 3);
    }

    // ═══════════════════════════════════════════════════════════════
    //  11. TESTS DE IMPORTACIÓN
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function showImport_abre_modal(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $sourceActivity = $this->createActivity($data['profesor_id']);

        // Crear segunda sección para que showImport no cierre por falta de secciones
        $this->createSecondSectionInGrade($sourceActivity);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->call('showImport', $sourceActivity->id);

        $component->assertSet('showImportModal', true);
        $component->assertSet('importActivityId', $sourceActivity->id);
    }

    /** @test */
    public function closeImportModal_cierra_modal(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $sourceActivity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->call('showImport', $sourceActivity->id);

        $component->call('closeImportModal');
        $component->assertSet('showImportModal', false);
        $component->assertSet('importActivityId', null);
    }

    /** @test */
    public function goToImportStep_navega_pasos_import(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);
        $sourceActivity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->call('showImport', $sourceActivity->id);

        $component->assertSet('importWizardStep', 1);
        $component->call('goToImportStep', 3);
        $component->assertSet('importWizardStep', 3);
    }

    // ═══════════════════════════════════════════════════════════════
    //  12. TESTS DE PUBLICACIÓN
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function confirmPublish_muestra_panel(): void
    {
        $data = $this->createProfesorUser(false, ['is_planner' => true]);
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        // Agregar secciones para que confirmPublish no tome atajo por falta de contenido
        $component->set('newSectionTitle', 'Sección test');
        $component->call('addWizardSection');

        $component->call('confirmPublish');
        $component->assertSet('showPublishConfirm', true);
    }

    /** @test */
    public function saveAndPublish_publica_leccion(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Sección para publicar');
        $component->call('addWizardSection');
        $component->set('contentBody', 'Contenido');
        $component->call('addWizardContent', 0);
        $component->set('saveAnyway', true);
        $component->call('saveStep2');

        $component->call('saveAndPublish');

        $activity->refresh();
        $publication = $activity->lmsPublication;
        $this->assertNotNull($publication);
        $this->assertEquals('PUBLISHED', $publication->status);
    }

    /** @test */
    public function resetWizardSections_limpia_todo(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('newSectionTitle', 'Sección 1');
        $component->call('addWizardSection');
        $component->set('newSectionTitle', 'Sección 2');
        $component->call('addWizardSection');
        $component->set('newSectionTitle', 'Sección 3');
        $component->call('addWizardSection');

        $component->call('resetWizardSections');

        $this->assertCount(0, $component->get('wizardSections'));
        $this->assertEquals(0, $component->get('currentSlideIndex'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  13. TESTS DE VISTA PREVIA DEL ESTUDIANTE
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function openListStudentPreview_carga_datos_de_actividad_guardada(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('openListStudentPreview', $activity->id);

        $component->assertSet('showListStudentPreview', true);
        $this->assertNotNull($component->get('listPreviewData'));
        $this->assertEquals($activity->topic, $component->get('listPreviewData')['title']);
    }

    /** @test */
    public function closeListStudentPreview_cierra_preview(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('openListStudentPreview', $activity->id);
        $component->call('closeListStudentPreview');

        $component->assertSet('showListStudentPreview', false);
        $component->assertSet('listPreviewData', null);
    }

    /** @test */
    public function openWizardStudentPreview_carga_datos_desde_estado_wizard(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->set('lessonTitle', 'Título Personalizado en Wizard');
        $component->call('openWizardStudentPreview');

        $component->assertSet('showListStudentPreview', true);
        $previewData = $component->get('listPreviewData');
        $this->assertEquals('Título Personalizado en Wizard', $previewData['title']);
    }

    // ═══════════════════════════════════════════════════════════════
    //  14. TESTS DE ELIMINACIÓN
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function deleteLesson_elimina_contenido_lms_completo(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $mediaId = DB::table('lms_media_library')->insertGetId([
            'uploaded_by' => $data['user']->id,
            'disk' => 'public',
            'path' => 'test/test.pdf',
            'original_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 1024,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $section = LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Sección a eliminar',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
        LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'TEXT',
            'title' => 'Contenido Test',
            'body' => 'Body',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
        LmsActivityResource::create([
            'activity_id' => $activity->id,
            'section_id' => $section->id,
            'media_id' => $mediaId,
            'uploaded_by' => $data['user']->id,
            'display_name' => 'Recurso Test',
            'is_visible' => true,
        ]);
        LmsActivityLink::create([
            'activity_id' => $activity->id,
            'added_by' => $data['user']->id,
            'title' => 'Link Test',
            'url' => 'https://test.com',
            'type' => 'REFERENCE',
            'is_visible' => true,
        ]);

        $component = Livewire::test(LessonWizard::class);
        $component->call('deleteLesson', $activity->id);

        $this->assertEquals(0, LmsActivitySection::where('activity_id', $activity->id)->count());
        $this->assertEquals(0, LmsActivityResource::where('activity_id', $activity->id)->count());
        $this->assertEquals(0, LmsActivityLink::where('activity_id', $activity->id)->count());
    }

    // ═══════════════════════════════════════════════════════════════
    //  15. TESTS DE RENDERIZADO DE CONTENIDO
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function renderContentBody_convierte_markdown_a_html(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $instance = $component->instance();
        $html = $instance->renderContentBody('## Título'."\n\n".'Párrafo de **texto** con _énfasis_.');

        $this->assertStringContainsString('<h2>Título</h2>', $html);
        $this->assertStringContainsString('<strong>texto</strong>', $html);
        $this->assertStringContainsString('<em>énfasis</em>', $html);
    }

    /** @test */
    public function renderContentBody_devuelve_vacio_si_body_null_o_vacio(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $html = $component->instance()->renderContentBody(null);
        $this->assertEquals('', $html);

        $html = $component->instance()->renderContentBody('');
        $this->assertEquals('', $html);
    }

    /** @test */
    public function renderContentBody_no_convierte_markdown_si_es_MATH(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $latex = 'La fórmula es \( E = mc^2 \) con \frac{1}{2}';
        $html = $component->instance()->renderContentBody($latex, 'MATH');

        // No debe convertir _ en <em> (LaTeX se preserva)
        $this->assertStringNotContainsString('<em>', $html);
    }

    /** @test */
    public function renderReviewQuestions_formatea_preguntas(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $html = $component->instance()->renderReviewQuestions('## Preguntas de Repaso

1. **Pregunta 1** — Respuesta.');

        $this->assertStringContainsString('review-questions', $html);
        $this->assertStringContainsString('Pregunta 1', $html);
    }

    // ═══════════════════════════════════════════════════════════════
    //  16. TESTS DE DETECCIÓN DE CAMBIOS (updating hook)
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function modificar_lessonTitle_marca_no_guardado(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->assertSet('saved', true);

        $component->set('lessonTitle', 'Nuevo título');
        $component->assertSet('saved', false);
    }

    /** @test */
    public function modificar_wizardSections_marca_no_guardado(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->assertSet('saved', true);

        $component->set('newSectionTitle', 'Nueva');
        $component->call('addWizardSection');
        $component->assertSet('saved', false);
    }

    /** @test */
    public function modificar_wizardLinks_marca_no_guardado(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);
        $component->assertSet('saved', true);

        $component->set('linkTitle', 'Link Test');
        $component->set('linkUrl', 'https://test.com');
        $component->call('addWizardLink');
        $component->assertSet('saved', false);
    }

    // ═══════════════════════════════════════════════════════════════
    //  17. TESTS DE RENDER (comprobación básica de que la vista carga)
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function render_en_modo_list_no_lanza_excepcion(): void
    {
        $data = $this->createProfesorUser();
        $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);

        // La vista debe contener el título del filtro y el layout
        $component->assertSee('Lapso');
    }

    /** @test */
    public function render_en_modo_wizard_no_lanza_excepcion(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $component = Livewire::test(LessonWizard::class);
        $component->call('startWizard', $activity->id);

        $component->assertSet('mode', 'wizard');
    }
}
