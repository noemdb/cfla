<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsHtmlEmbed;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Lote R — Recursos Compartidos (/app/estudiante/recursos).
 * Mejoras UI alineadas con blueprint/namethatui/namethatui_analysis.md:
 *   R1 skeleton de carga · R2 empty state ilustrado · R3 focus visible + aria-labels
 *   R4 pill de materia (D2) + chip de tipo de archivo · R5 modal accesible · R6 search icon.
 *
 * @see blueprint/estudiant/recursos-ui.md
 */
class StudentResourceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_resources_page_renders_subject_pill_and_type_chip(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Matemática', $s);
        $this->createResource($activity->id, 'Guía de fracciones.pdf', 'application/pdf');

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->assertSee('Guía de fracciones.pdf')
            // R4 · chip de tipo de archivo (PDF → rose) — tag no interactivo
            ->assertSee('bg-rose-100 text-rose-700')
            ->assertSee('PDF')
            // R4 · pill de materia con color D2 (Matemática → sky)
            ->assertSee('text-sky-600 dark:text-sky-300')
            // R3 · acciones de tarjeta con foco visible
            ->assertSee('focus-visible:ring-2 ring-emerald-500/50', false);
    }

    public function test_resources_cards_have_larger_scale_and_solid_download(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Matemática', $s);
        $this->createResource($activity->id, 'Guía de fracciones.pdf', 'application/pdf');

        $student = $this->createStudentInSeccion($seccionId);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->html();

        // R7 · tarjeta más grande: padding p-5, título text-base, icono w-12
        $this->assertStringContainsString('rounded-xl p-5 space-y-4', $html);
        $this->assertStringContainsString('text-base font-semibold', $html);
        $this->assertStringContainsString('w-12 h-12', $html);
        // R7 · botón Descargar sólido (afordancia primaria)
        $this->assertStringContainsString('bg-emerald-600', $html);
        // Descargar abre en pestaña nueva (target=_blank + noopener)
        $this->assertStringContainsString('target="_blank" rel="noopener noreferrer"', $html);
        // R7 · cuerpo de la tarjeta: descripción visible (anatomía Card)
        $this->assertStringContainsString('Recurso de prueba', $html);
        $this->assertStringContainsString('line-clamp-2', $html);
        // R7 · contenedor más ancho
        $this->assertStringContainsString('max-w-4xl', $html);
    }

    public function test_resources_search_empty_state_shows_mascot_and_ctas(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Matemática', $s);
        $this->createResource($activity->id, 'Guía de fracciones.pdf', 'application/pdf');

        // Fecha fija de nacimiento: 6 años → franja infantil (mascota visible,
        // C4). Sin ella la fábrica genera una edad aleatoria y el test sería
        // intermitente (la mascota se oculta para 13–15 años).
        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(6)->subMonths(1)->toDateString(),
        ]);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->set('search', 'xyz-no-existe')
            ->assertSee('No encontramos recursos para')
            ->assertSee('xyz-no-existe')
            // R2 · micro-copia
            ->assertSee('Prueba con otra búsqueda o cambia el lapso.')
            // R2 · mascota idle (franja ≤12: sin date_birth → age null → visible)
            ->assertSee('animate-mascot-float')
            // R2 · CTAs: Vuelve a intentarlo (solo con búsqueda) + Ver todos
            ->assertSee('Vuelve a intentarlo')
            ->assertSee('Ver todos')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSee('Guía de fracciones.pdf');
    }

    public function test_resources_empty_state_without_filters_shows_base_message(): void
    {
        $seccionId = $this->createEvaluacionChain()[0];

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->assertSee('Aún no hay recursos compartidos.')
            ->assertSee('Ver todos')
            // Sin búsqueda activa el CTA "Vuelve a intentarlo" no aparece
            ->assertDontSee('Vuelve a intentarlo');
    }

    public function test_resources_loading_skeleton_is_scoped_to_filters(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Matemática', $s);
        $this->createResource($activity->id, 'Guía de fracciones.pdf', 'application/pdf');

        $student = $this->createStudentInSeccion($seccionId);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->html();

        // R1 · skeleton con target scoped (buscar / lapso / paginar), aria-hidden
        $this->assertStringContainsString('wire:loading.delay.shorter', $html);
        $this->assertStringContainsString('wire:target="search, lapsoId, typeFilter, asignaturaId, gotoPage"', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
        // La grilla real se oculta mientras carga con el mismo target
        $this->assertStringContainsString('wire:loading.remove', $html);
    }

    public function test_resources_filters_have_aria_labels_and_focus_rings(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Matemática', $s);
        $this->createResource($activity->id, 'Guía de fracciones.pdf', 'application/pdf');

        $student = $this->createStudentInSeccion($seccionId);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->html();

        // R3/R6 · aria-labels descriptivos en los filtros
        $this->assertStringContainsString('aria-label="Buscar recurso o actividad"', $html);
        $this->assertStringContainsString('aria-label="Filtrar por lapso"', $html);
        // R6 · icono de búsqueda dentro del campo
        $this->assertStringContainsString('pl-9', $html);
        // R3 · receta E1 de foco visible en los controles
        $this->assertStringContainsString('focus-visible:ring-2 ring-emerald-500/50', $html);
        $this->assertStringContainsString('dark:focus-visible:ring-offset-gray-900', $html);
    }

    public function test_preview_modal_is_an_accessible_dialog(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Matemática', $s);
        $resource = $this->createResource($activity->id, 'Guía de fracciones.pdf', 'application/pdf');

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->call('preview', $resource->id)
            ->assertSet('showPreviewModal', true)
            ->assertSet('previewResource.id', $resource->id)
            // R5 · patrón <dialog>: role, aria-modal, aria-labelledby
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false)
            ->assertSee('aria-labelledby="preview-title"', false)
            ->assertSee('id="preview-title"', false)
            // R5 · cierre con Escape + retorno de foco al trigger
            ->assertSee('@keydown.escape.window', false)
            ->assertSee('data-preview-close', false)
            ->assertSee('data-preview-trigger-'.$resource->id, false)
            // R5 · scrim ::backdrop (clic fuera cierra)
            ->assertSee('bg-black/60', false)
            // Descargar abre en pestaña nueva (target=_blank + noopener)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->call('closePreview')
            ->assertSet('showPreviewModal', false)
            ->assertSet('previewResource', null);
    }

    public function test_embed_preview_opens_special_modal_with_html_content(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Biología', $s);

        $teacher = User::factory()->create(['is_profesor' => true]);
        $embed = LmsHtmlEmbed::create([
            'activity_id' => $activity->id,
            'added_by' => $teacher->id,
            'title' => 'Video embebido',
            'html_content' => '<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video de prueba" width="560" height="315"></iframe>',
            'render_condition' => 'ALWAYS',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            // La tarjeta embed pasa el tipo explícito → modal especial, no el genérico
            ->assertSee("preview({$embed->id}, 'embed')", false)
            ->call('preview', $embed->id, 'embed')
            ->assertSet('showEmbedPreviewModal', true)
            ->assertSet('embedPreview.id', $embed->id)
            ->assertSet('showPreviewModal', false)
            // R5 · patrón <dialog>: role, aria-modal, aria-labelledby + Escape + retorno de foco
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false)
            ->assertSee('aria-labelledby="embed-preview-title"', false)
            ->assertSee('id="embed-preview-title"', false)
            ->assertSee('@keydown.escape.window', false)
            ->assertSee('data-embed-preview-close', false)
            ->assertSee('data-preview-trigger-'.$embed->id, false)
            // El HTML embebido se renderiza vía math-text (DOMPurify + KaTeX):
            // el contenido viaja en data-math-content (escapado) y se inyecta
            // en el DOM por JS en el cliente — mismo pipeline que la lección.
            ->assertSee('x-data="mathContent()"', false)
            ->assertSee('data-math-content="', false)
            ->assertSee('&lt;iframe src=&quot;https://www.youtube.com/embed/dQw4w9WgXcQ&quot;', false)
            ->call('closeEmbedPreview')
            ->assertSet('showEmbedPreviewModal', false)
            ->assertSet('embedPreview', null);
    }

    public function test_embed_preview_renders_mermaid_diagram_with_wrapper(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Biología', $s);

        $teacher = User::factory()->create(['is_profesor' => true]);
        $embed = LmsHtmlEmbed::create([
            'activity_id' => $activity->id,
            'added_by' => $teacher->id,
            'title' => 'Diagrama de identidad',
            'html_content' => "graph TD\n    A[\"Construyendo<br/>Nuestra Identidad\"] --> B[\"Yo Individual\"]\n    B --> C[\"Nosotros Colectivo\"]",
            'render_condition' => 'ALWAYS',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->call('preview', $embed->id, 'embed')
            ->assertSet('showEmbedPreviewModal', true)
            // is_mermaid detectado por ensureMermaidWrapper (keyword inicial)
            ->assertSet('embedPreview.is_mermaid', true)
            // wrapper mermaidEmbed en el body del modal (mismo pipeline que la lección)
            ->assertSee('x-data="mermaidEmbed()"', false)
            // código extraído con <br/> de labels conservado (A1)
            ->assertSee('data-mermaid-code="graph TD', false)
            ->assertSee('Construyendo&lt;br/&gt;Nuestra Identidad', false)
            ->call('closeEmbedPreview')
            ->assertSet('showEmbedPreviewModal', false);
    }

    public function test_embed_preview_renders_markdown_and_latex_via_math_text(): void
    {
        [$seccionId, $pevaluacionId, $s] = $this->createEvaluacionChain();

        $activity = $this->createActivityWithSubject($pevaluacionId, 'Matemática', $s);

        $teacher = User::factory()->create(['is_profesor' => true]);
        // Markdown puro → conversión server-side (Str::markdown)
        $mdEmbed = LmsHtmlEmbed::create([
            'activity_id' => $activity->id,
            'added_by' => $teacher->id,
            'title' => 'Guía',
            'html_content' => "## Fórmula de Einstein\n\nLa energía es **cinética** o **potencial**.",
            'render_condition' => 'ALWAYS',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
        // LaTeX puro → se preserva crudo para el auto-render de KaTeX
        // (Str::markdown se comería los delimitadores \( \))
        $mathEmbed = LmsHtmlEmbed::create([
            'activity_id' => $activity->id,
            'added_by' => $teacher->id,
            'title' => 'Fórmula',
            'html_content' => 'La energía es \\(E = mc^2\\).',
            'render_condition' => 'ALWAYS',
            'sort_order' => 2,
            'is_visible' => true,
        ]);

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            // ── Markdown: no es mermaid → pipeline math-text + Str::markdown ──
            ->call('preview', $mdEmbed->id, 'embed')
            ->assertSet('showEmbedPreviewModal', true)
            ->assertSet('embedPreview.is_mermaid', false)
            ->assertSee('x-data="mathContent()"', false)
            // markdown convertido server-side y escapado en data-math-content
            ->assertSee('data-math-content="', false)
            ->assertSee('&lt;h2&gt;Fórmula de Einstein&lt;/h2&gt;', false)
            ->call('closeEmbedPreview')
            // ── LaTeX: delimitadores \(...\) preservados para KaTeX ──
            ->call('preview', $mathEmbed->id, 'embed')
            ->assertSet('showEmbedPreviewModal', true)
            ->assertSet('embedPreview.is_mermaid', false)
            ->assertSee('x-data="mathContent()"', false)
            ->assertSee('La energía es \\(E = mc^2\\).', false)
            ->call('closeEmbedPreview')
            ->assertSet('showEmbedPreviewModal', false);
    }

    public function test_preview_denied_for_resource_outside_student_section(): void
    {
        [, $pevaluacionB, $sB] = $this->createEvaluacionChain();
        [$seccionC] = $this->createEvaluacionChain();

        $activityB = $this->createActivityWithSubject($pevaluacionB, 'Otra Materia', $sB);
        $resourceB = $this->createResource($activityB->id, 'Recurso ajeno.pdf', 'application/pdf');

        // El estudiante pertenece a la sección C; el recurso vive en la B.
        $studentC = $this->createStudentInSeccion($seccionC);

        Livewire::actingAs($studentC)
            ->test(\App\Livewire\Student\Lms\ResourceList::class)
            ->call('preview', $resourceB->id)
            ->assertSet('showPreviewModal', false)
            ->assertSet('previewResource', null)
            // El recurso ajeno tampoco aparece en el listado (scopeResources)
            ->assertDontSee('Recurso ajeno.pdf');
    }

    // ─── Helpers (misma cadena FK que StudentHomeTest) ─────────────────────

    /**
     * La cadena FK se parametriza con un contador: algunos campos (RIF de la
     * institución, códigos de lapso/pestudio/grado/asignatura) son únicos, y
     * varios tests crean DOS cadenas (secciones distintas) en el mismo test.
     */
    private static int $chainCounter = 0;

    private function createStudentInSeccion(int $seccionId, array $overrides = []): User
    {
        $user = User::factory()->create(['is_student' => true]);

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan',
            'description' => 'Test plan description',
            'observations' => 'Test observations',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estudiant = \App\Models\app\Learner\Estudiant::factory()->create(array_merge([
            'user_id' => $user->id,
            'planpago_id' => $planpagoId,
        ], $overrides));

        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionId,
        ]);

        return $user;
    }

    private function createEvaluacionChain(): array
    {
        self::$chainCounter++;
        $s = self::$chainCounter;
        $code = fn (string $base) => "{$base}-{$s}";

        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => $code('LAP-TEST'),
            'code_sm' => 'LT',
            'name' => 'Test Lapso '.$s,
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA',
            'name' => 'Test Scale '.$s,
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Institution '.$s,
            'legalname' => 'Test Institution Legal '.$s,
            'rif_institution' => 'J-'.str_pad((string) $s, 8, '0', STR_PAD_LEFT).'-9',
            'email_institution' => 'test'.$s.'@institution.test',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año Escolar '.$s,
            'description' => 'Test',
            'finicial' => now(),
            'ffinal' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId,
            'name' => 'Test PE '.$s,
            'description' => 'Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => $code('PEST-TEST'),
            'name' => 'Test Plan de Estudio '.$s,
            'scale' => $escalaId,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId,
            'name' => 'Test Grado '.$s,
            'code' => $code('GR-TEST'),
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId,
            'name' => 'A'.$s,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => $code('ASIG-TEST'),
            'name' => 'Test Asignatura '.$s,
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

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-'.str_pad((string) $s, 8, '0', STR_PAD_LEFT),
            'ci_profesor' => str_pad((string) $s, 8, '0', STR_PAD_LEFT),
            'name' => 'Profesor Test '.$s,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'objetivo' => 'Test objetivo '.$s,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$seccionId, $pevaluacionId, $s];
    }

    private function createActivityWithSubject(int $pevaluacionId, string $subject, int $chainSuffix = 0): Activity
    {
        // Re-apunta la asignatura del pensum para controlar el color D2.
        DB::table('asignaturas')
            ->where('code', $chainSuffix ? "ASIG-TEST-{$chainSuffix}" : 'ASIG-TEST')
            ->update(['name' => $subject]);

        $activity = Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now()->subDays(2),
            'ffinal' => now()->addDays(7),
            'topic' => 'Tema de la actividad',
            'status' => true,
        ]);

        // Publicación PUBLISHED para que los recursos sean visibles para el estudiante
        $publisher = \App\Models\User::factory()->create(['is_profesor' => true]);
        DB::table('lms_activity_publications')->insert([
            'activity_id' => $activity->id,
            'published_by' => $publisher->id,
            'status' => 'PUBLISHED',
            'publish_at' => now()->subDay(),
            'published_at' => now()->subDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $activity;
    }

    private function createResource(int $activityId, string $displayName, string $mimeType): LmsActivityResource
    {
        $teacher = User::factory()->create(['is_profesor' => true]);

        $mediaId = DB::table('lms_media_library')->insertGetId([
            'uploaded_by' => $teacher->id,
            'disk' => 'public',
            'path' => 'lms/test-'.$displayName,
            'original_name' => $displayName,
            'mime_type' => $mimeType,
            'size_bytes' => 1024,
            'provider' => 'LOCAL',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return LmsActivityResource::create([
            'activity_id' => $activityId,
            'media_id' => $mediaId,
            'uploaded_by' => $teacher->id,
            'display_name' => $displayName,
            'description' => 'Recurso de prueba',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
    }
}
