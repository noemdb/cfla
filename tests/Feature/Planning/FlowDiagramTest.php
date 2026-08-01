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

    public function test_flow_hub_opens_diagram_in_new_tab(): void
    {
        $planner = $this->makePlanner();

        $response = $this->actingAs($planner)->get(route('app.planning.flow.index'));

        $response->assertOk();
        // El hub abre el diagrama en una pestaña nueva (_blank) en lugar de un modal.
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener"', false);
        $response->assertSee(route('app.planning.diagram.flow.show', 'activity-lesson'), false);
        $response->assertDontSee('flow-diagram-modal');
        $response->assertDontSee('<iframe', false);
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
