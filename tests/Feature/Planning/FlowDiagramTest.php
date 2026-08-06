<?php

namespace Tests\Feature\Planning;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FlowDiagramTest extends TestCase
{
    use DatabaseTransactions;

    private function makePlanner(): User
    {
        return User::factory()->create(['is_planner' => true]);
    }

    // ─── Hub /app/planning/flow ─────────────────────────────────

    public function test_planning_dashboard_links_to_flow_hub(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.index'));

        $response->assertOk();
        $response->assertSee(route('app.planning.flow.index'), false);
    }

    public function test_flow_hub_requires_auth(): void
    {
        $this->get(route('app.planning.flow.index'))->assertRedirect(route('login'));
    }

    public function test_flow_hub_requires_planner(): void
    {
        $planner = User::factory()->create(['is_planner' => false]);

        $this->actingAs($planner)->get(route('app.planning.flow.index'))->assertForbidden();
    }

    public function test_flow_hub_lists_activity_lesson_diagram(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('Flujo de Actividad y Lección (LMS)');
        $response->assertSee(route('app.planning.diagram.flow.show', 'activity-lesson'), false);
    }

    public function test_flow_hub_lists_consejo_directivo_diagram(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('Informe al Consejo Directivo · CFLA 2026');
        $response->assertSee(route('app.planning.diagram.flow.show', 'consejo-directivo'), false);
    }

    public function test_flow_hub_lists_activity_lesson_planning_diagram(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('Planificación en el Flujo Actividad / Lección');
        $response->assertSee(route('app.planning.diagram.flow.show', 'activity-lesson-planning'), false);
    }

    public function test_flow_hub_opens_diagram_in_new_tab(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        // El hub abre el diagrama en una pestaña nueva (_blank) en lugar de un modal.
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener"', false);
        $response->assertSee(route('app.planning.diagram.flow.show', 'activity-lesson'), false);
    }

    public function test_flow_hub_displays_visual_thumbnail_preview(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        // Cada tarjeta incorpora una vista previa visual (mini-diagrama conceptual).
        $response->assertSee('h-36', false);
        $response->assertSee('border-b border-white/5', false);
    }

    public function test_flow_hub_orders_activity_lesson_before_consejo_directivo(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $html = $response->getContent();

        $activityPos = strpos($html, 'Flujo de Actividad y Lección (LMS)');
        $consejoPos  = strpos($html, 'Informe al Consejo Directivo');

        $this->assertNotFalse($activityPos, 'El diagrama Activity-Lesson debería estar presente');
        $this->assertNotFalse($consejoPos,  'El diagrama Consejo Directivo debería estar presente');
        $this->assertLessThan($consejoPos, $activityPos);
    }

    public function test_flow_hub_sets_descriptive_accessibility_labels(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('Abrir el diagrama del flujo de actividad y lección LMS', false);
        $response->assertSee('Abrir el diagrama de casos de uso de Planning en el flujo Actividad-Lección', false);
        $response->assertSee('Abrir el informe al Consejo Directivo CFLA 2026', false);
    }

    public function test_flow_hub_displays_tag_chips_per_diagram(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('LMS', false);
        $response->assertSee('Publicación', false);
        $response->assertSee('Planning', false);
        $response->assertSee('Consejo Directivo', false);
    }

    public function test_flow_hub_includes_search_and_category_filter(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('Buscar diagrama por título, descripción o etiqueta', false);
        $response->assertSee('Todas las categorías', false);
    }

    public function test_flow_hub_includes_sort_selector(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('Orden: por relevancia', false);
        $response->assertSee('Orden: más recientes', false);
        $response->assertSee('Orden: por categoría', false);
    }

    public function test_flow_hub_note_responsive_has_break_all(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('break-all', false);
    }

    public function test_flow_hub_displays_status_badge(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('actualizado', false);
        $response->assertSee('nuevo', false);
    }

    public function test_flow_hub_displays_meta_metadata_chips(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        // Categoría
        $response->assertSee('LMS', false);
        $response->assertSee('Informe', false);
        // Duración
        $response->assertSee('5 min', false);
        $response->assertSee('10 min', false);
        // Audiencia
        $response->assertSee('Docentes · Coordinación', false);
        $response->assertSee('Consejo Directivo', false);
    }

    public function test_flow_hub_includes_preview_modal_with_iframe(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        $response->assertSee('Vista previa', false);
        $response->assertSee('<iframe', false);
        $response->assertSee('openPreview', false);
        $response->assertSee('closePreview', false);
    }

    // ─── Diagramas /app/planning/diagram/flow/{slug} ────────────

    public function test_activity_lesson_diagram_is_served(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)
            ->get(route('app.planning.diagram.flow.show', 'activity-lesson'));

        $response->assertOk();

        // response()->file() devuelve un BinaryFileResponse: se verifica el archivo servido.
        $file = $response->baseResponse->getFile();
        $this->assertNotNull($file);
        $this->assertSame('flujoActivityLesson.html', $file->getFilename());
    }

    public function test_consejo_directivo_diagram_is_served(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)
            ->get(route('app.planning.diagram.flow.show', 'consejo-directivo'));

        $response->assertOk();

        $file = $response->baseResponse->getFile();
        $this->assertNotNull($file);
        $this->assertSame('flujoConsejoDirectivo.html', $file->getFilename());
    }

    public function test_activity_lesson_planning_diagram_is_served(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)
            ->get(route('app.planning.diagram.flow.show', 'activity-lesson-planning'));

        $response->assertOk();

        $file = $response->baseResponse->getFile();
        $this->assertNotNull($file);
        $this->assertSame('flujoActivityLessonPlanning.html', $file->getFilename());
    }

    public function test_unknown_diagram_returns_404(): void
    {
        $planner = $this->makePlanner();

        $this->actingAs($planner)
            ->get(route('app.planning.diagram.flow.show', 'no-existe'))
            ->assertNotFound();
    }

    public function test_diagram_requires_auth(): void
    {
        $this->get(route('app.planning.diagram.flow.show', 'activity-lesson'))
            ->assertRedirect(route('login'));
    }
}
