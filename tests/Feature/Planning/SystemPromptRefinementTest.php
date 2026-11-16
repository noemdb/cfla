<?php

namespace Tests\Feature\Planning;

use App\Models\User;
use App\Services\OpenRouterService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Tests\TestCase;

/**
 * Composición y refinamiento con IA (OpenRouter) del system prompt de
 * consolidado de /app/planning/activities.
 */
class SystemPromptRefinementTest extends TestCase
{
    use DatabaseTransactions;

    private const REFINEMENT = "# REFINAMIENTO PARA ESTA EJECUCIÓN\n\n"
        ."## Énfasis temático\nPrioriza la alimentación local y la cultura venezolana.\n\n"
        ."## Vinculación curricular\nConecta Ciencias Naturales con Cultura e Identidad.\n\n"
        ."## Evidencia y trazabilidad\nCita los act_id de cada hallazgo.\n\n"
        ."## Riesgos y sesgos a vigilar\nEvita generalizaciones por sección.\n\n"
        ."## Criterios de aceptación adicionales\nToda afirmación debe citar una actividad fuente.";

    public function test_compose_appends_parameters_and_optional_blocks(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);

        $base = (string) file_get_contents(base_path('blueprint/activities/alimentacion/systemPrompt.md'));

        $response = $this->actingAs($planner)->postJson(
            route('app.planning.activities.system-prompt.compose'),
            [
                'eje' => 'Alimentación, cultura e identidad',
                'areas' => 'Ciencias Naturales · Cultura e Identidad',
                'nivel' => 'EDUCACIÓN PRIMARIA',
                'periodo' => '1ER MOMENTO · 2026',
                'titulo' => 'Consolidado de prueba',
            ]
        );

        $response->assertOk();
        $prompt = (string) $response->json('prompt');

        self::assertStringStartsWith(rtrim($base), $prompt);
        self::assertStringContainsString('# PARÁMETROS DE ESTA EJECUCIÓN', $prompt);
        self::assertStringContainsString('(`ejeTematico`): Alimentación, cultura e identidad', $prompt);
        self::assertStringContainsString('(`areasArticuladas`): Ciencias Naturales · Cultura e Identidad', $prompt);
        self::assertStringContainsString('(`nivelEducativo`): EDUCACIÓN PRIMARIA', $prompt);
        self::assertStringContainsString('(`periodo`): 1ER MOMENTO · 2026', $prompt);
        self::assertStringContainsString('(`reportTitle`): Consolidado de prueba', $prompt);
        self::assertStringNotContainsString('# INSTRUCCIONES ADICIONALES', $prompt);
        self::assertStringNotContainsString('# REFINAMIENTO PARA ESTA EJECUCIÓN', $prompt);
    }

    public function test_compose_inserts_refinement_before_instructions(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);

        $response = $this->actingAs($planner)->postJson(
            route('app.planning.activities.system-prompt.compose'),
            [
                'eje' => 'Robótica educativa',
                'instrucciones' => "Enfatiza la progresión.\nCompara secciones A/B.",
                'refinamiento' => self::REFINEMENT,
            ]
        );

        $response->assertOk();
        $prompt = (string) $response->json('prompt');

        $refinementPos = mb_strpos($prompt, '# REFINAMIENTO PARA ESTA EJECUCIÓN');
        $instructionsPos = mb_strpos($prompt, '# INSTRUCCIONES ADICIONALES');

        self::assertNotFalse($refinementPos);
        self::assertNotFalse($instructionsPos);
        self::assertLessThan($instructionsPos, $refinementPos);
        self::assertStringContainsString("Enfatiza la progresión.\nCompara secciones A/B.", $prompt);
    }

    public function test_refine_returns_ai_refinement_via_openrouter(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);

        $mock = Mockery::mock(OpenRouterService::class);
        $mock->shouldReceive('ask')
            ->once()
            ->withArgs(function (string $system, string $user, array $overrides): bool {
                return str_contains($system, 'ingeniero de prompts')
                    && str_contains($user, 'Robótica educativa')
                    && str_contains($user, 'REPORT_DATA')
                    && ! empty($overrides['model']);
            })
            ->andReturn([
                'success' => true,
                'content' => self::REFINEMENT,
                'model' => 'test/model',
                'usage' => [],
                'finish_reason' => 'stop',
                'error' => null,
            ]);

        $this->app->instance(OpenRouterService::class, $mock);

        $response = $this->actingAs($planner)->postJson(
            route('app.planning.activities.system-prompt.refine'),
            ['eje' => 'Robótica educativa']
        );

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'prompt' => self::REFINEMENT,
            'model' => 'test/model',
            'error' => null,
        ]);
    }

    public function test_refine_falls_back_when_first_model_returns_truncated_content(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);

        $mock = Mockery::mock(OpenRouterService::class);
        $mock->shouldReceive('ask')
            ->once()
            ->andReturn([
                'success' => true,
                'content' => self::REFINEMENT,
                'model' => 'model/truncated',
                'usage' => [],
                'finish_reason' => 'length',
                'error' => null,
            ]);
        $mock->shouldReceive('ask')
            ->once()
            ->andReturn([
                'success' => true,
                'content' => self::REFINEMENT,
                'model' => 'model/ok',
                'usage' => [],
                'finish_reason' => 'stop',
                'error' => null,
            ]);

        $this->app->instance(OpenRouterService::class, $mock);

        $this->actingAs($planner)->postJson(
            route('app.planning.activities.system-prompt.refine'),
            ['eje' => 'Identidad cultural']
        )->assertOk()->assertJson([
            'success' => true,
            'model' => 'model/ok',
        ]);
    }

    public function test_refine_returns_502_when_all_models_fail(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);

        $mock = Mockery::mock(OpenRouterService::class);
        $mock->shouldReceive('ask')->andReturn([
            'success' => false,
            'content' => null,
            'model' => null,
            'usage' => null,
            'finish_reason' => null,
            'error' => 'HTTP 429',
        ]);

        $this->app->instance(OpenRouterService::class, $mock);

        $response = $this->actingAs($planner)->postJson(
            route('app.planning.activities.system-prompt.refine'),
            ['eje' => 'Salud integral']
        );

        $response->assertStatus(502);
        $response->assertJsonPath('success', false);
        self::assertStringContainsString('No se pudo mejorar el prompt con IA', (string) $response->json('error'));
    }

    public function test_refine_requires_eje_transversal(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);

        $this->actingAs($planner)
            ->postJson(route('app.planning.activities.system-prompt.refine'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('eje');
    }

    public function test_prompt_endpoints_are_forbidden_for_non_planners(): void
    {
        $user = User::factory()->create([
            'is_planner' => false,
            'is_admin' => false,
            'is_diagnostic' => false,
        ]);

        $this->actingAs($user)
            ->postJson(route('app.planning.activities.system-prompt.compose'), ['eje' => 'X'])
            ->assertForbidden();

        $this->actingAs($user)
            ->postJson(route('app.planning.activities.system-prompt.refine'), ['eje' => 'X'])
            ->assertForbidden();
    }
}
