<?php

namespace Tests\Unit\Lms;

use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Services\Lms\LmsAiOrchestrationService;
use App\Services\Lms\LmsSvgAiRepairService;
use App\Services\Lms\LmsSvgRepairService;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitarios de LmsSvgAiRepairService — lógica de daño, extracción de SVG
 * y el pipeline de reparación (IA con fallback → determinista).
 *
 * Nota: NO ejecuta llamadas reales de IA; el LmsAiOrchestrationService se
 * mockea para controlar el comportamiento (éxito, fallo, respuesta inválida).
 * Los tests de integración con IA real están cubiertos por el test E2E
 * del comando lms:repair-svgs (se ejecuta manualmente contra la DB de dev).
 */
class LmsSvgAiRepairServiceTest extends TestCase
{
    private LmsSvgRepairService $repairService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repairService = new LmsSvgRepairService();
    }

    // ─── damageReport ────────────────────────────────────────────────────

    public function test_damage_report_detects_broken_tag(): void
    {
        $svg = '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="560" y="210" width="380" height="170" rx="8"</svg>';
        $svc = $this->makeService('<fake>');

        $report = $svc->damageReport($svg);
        $this->assertContains('tag_roto', $report['issues']);
    }

    public function test_damage_report_detects_unclosed_svg(): void
    {
        $svg = '<svg viewBox="0 0 1000 100" xmlns="http://www.w3.org/2000/svg"><rect/></svg>';
        // Perfecto → no issues.
        $svc = $this->makeService('<svg>ok</svg>');
        $this->assertEmpty($svc->damageReport($svg)['issues']);

        // Sin cierre.
        $broken = '<svg viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg"><rect/></foo>';
        $report = $svc->damageReport($broken);
        $this->assertContains('svg_sin_cierre', $report['issues']);
    }

    public function test_damage_report_detects_large_empty_bottom(): void
    {
        // Canvas 1000x950, contenido hasta y=380 → 60% vacío → canvas_desproporcionado.
        $svg = '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="0" y="0" width="1000" height="950" fill="#f8f9fa"/>'
            . '<rect x="60" y="210" width="380" height="170" fill="#e3f2fd"/>'
            . '</svg>';
        $svc = $this->makeService('<svg>ok</svg>');
        $report = $svc->damageReport($svg);
        $this->assertNotEmpty(array_filter($report['issues'], fn ($i) => str_starts_with($i, 'canvas_desproporcionado')));
        $this->assertGreaterThanOrEqual(50, $report['empty_bottom_pct']);
    }

    public function test_damage_report_returns_no_issues_for_healthy_svg(): void
    {
        // Contenido que llena ~90% del canvas → sin daños.
        $svg = '<svg viewBox="0 0 800 500" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="0" y="0" width="800" height="500" fill="#f8f9fa"/>'
            . '<rect x="50" y="50" width="300" height="400" fill="#e3f2fd"/>'
            . '</svg>';
        $svc = $this->makeService('<svg>ok</svg>');
        $this->assertEmpty($svc->damageReport($svg)['issues']);
    }

    // ─── repairSvg — pipeline IA → fallback ──────────────────────────────

    public function test_repair_svg_returns_unchanged_when_no_damage(): void
    {
        $svg = '<svg viewBox="0 0 800 500" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="0" y="0" width="800" height="500" fill="#f8f9fa"/>'
            . '<rect x="50" y="50" width="300" height="400" fill="#e3f2fd"/>'
            . '</svg>';
        $svc = $this->makeService($svg);
        $result = $svc->repairSvg($svg);

        $this->assertSame('unchanged', $result->strategy);
        $this->assertSame($svg, $result->svg);
    }

    public function test_repair_svg_uses_ai_when_orchestrator_succeeds(): void
    {
        $broken = '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="60" y="210" width="380" height="170" fill="#e3f2fd"/>'
            . '<rect x="560" y="210" width="380" height="170" rx="8"</svg>';
        $repaired = '<svg viewBox="0 0 1000 420" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="60" y="210" width="380" height="170" fill="#e3f2fd"/>'
            . '<rect x="560" y="210" width="380" height="170" rx="8" fill="#fce4ec"/>'
            . '</svg>';

        $svc = $this->makeService($repaired, ['success' => true, 'content' => $repaired, 'model' => 'claude-test', 'usage' => []]);
        $result = $svc->repairSvg($broken);

        $this->assertSame('ai', $result->strategy);
        $this->assertSame('claude-test', $result->model);
        $this->assertStringContainsString('viewBox="0 0 1000 420"', $result->svg);
    }

    public function test_repair_svg_falls_back_to_deterministic_when_ai_fails(): void
    {
        $broken = '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="560" y="210" width="380" height="170" rx="8"</svg>';

        $svc = $this->makeService($broken, ['success' => false, 'error' => 'rate limit']);
        $result = $svc->repairSvg($broken);

        $this->assertSame('deterministic', $result->strategy);
        // El tag roto se eliminó y el svg se re-cerró.
        $this->assertStringNotContainsString('<rect x="560"', $result->svg);
        $this->assertSame(1, substr_count($result->svg, '</svg>'));
    }

    public function test_repair_svg_returns_error_when_ai_fails_and_deterministic_cannot_fix(): void
    {
        // Daño = solo canvas desproporcionado (sin tag roto): el fallback
        // determinista (repair) no lo corrige → strategy 'error'.
        $broken = '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="0" y="0" width="1000" height="950" fill="#f8f9fa"/>'
            . '<rect x="60" y="210" width="380" height="170" fill="#e3f2fd"/>'
            . '</svg>';

        $svc = $this->makeService($broken, ['success' => false, 'error' => 'all models failed']);
        $result = $svc->repairSvg($broken);

        $this->assertSame('error', $result->strategy);
        $this->assertStringContainsString('all models failed', $result->error);
    }

    // ─── repairContent (sin DB — mockeamos el modelo) ────────────────────

    public function test_repair_content_returns_no_svg_when_body_lacks_svg(): void
    {
        $content = $this->mockContent('sin svg aquí', id: 999);
        $svc = $this->makeService('<svg>ok</svg>');
        $result = $svc->repairContent($content);

        $this->assertSame('no-svg', $result->strategy);
        $this->assertSame(999, $result->contentId);
    }

    public function test_repair_content_persists_when_enabled(): void
    {
        $body = '<figure class="my-6"><figcaption>G</figcaption><div class="f">'
            . '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="0" y="0" width="1000" height="950" fill="#f8f9fa"/>'
            . '<rect x="560" y="210" width="380" height="170" rx="8"</svg>'
            . '</div></figure>';
        $repairedSvg = '<svg viewBox="0 0 1000 420" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="0" y="0" width="1000" height="420" fill="#f8f9fa"/>'
            . '<rect x="560" y="210" width="380" height="170" rx="8" fill="#fce4ec"/>'
            . '</svg>';
        $newBody = '<figure class="my-6"><figcaption>G</figcaption><div class="f">'
            . $repairedSvg
            . '</div></figure>';

        $content = $this->mockContent($body, id: 100, expectsUpdate: $newBody);
        $svc = $this->makeService($repairedSvg, ['success' => true, 'content' => $repairedSvg, 'model' => 'test', 'usage' => []]);

        $result = $svc->repairContent($content, ['persist' => true]);
        $this->assertSame('ai', $result->strategy);
        $this->assertSame(100, $result->contentId);
        $this->assertArrayHasKey('persisted', $result->changes);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    /**
     * Crea un mock de LmsActivityContent que simula body, id, section y update().
     * El contenido NO toca la BD; todos los campos son inline.
     */
    private function mockContent(
        string $body,
        int $id = 42,
        ?string $expectsUpdate = null,
    ): LmsActivityContent {
        $mock = $this->createMock(LmsActivityContent::class);
        // createMock reemplaza TODOS los métodos (incluido __get/__isset), así
        // que los atributos del modelo se exponen vía el mapa de __get. __isset
        // debe devolver true: el servicio usa '$content->body ?? '''.
        $mock->method('__get')->willReturnMap([
            ['body', $body],
            ['id', $id],
            ['title', 'Diagrama de prueba'],
            ['section', null],
        ]);
        $mock->method('__isset')->willReturn(true);
        $mock->expects($expectsUpdate !== null ? $this->once() : $this->never())
            ->method('update')
            ->with(['body' => $expectsUpdate]);
        return $mock;
    }

    /**
     * Crea el servicio con un LmsAiOrchestrationService mockeado.
     */
    private function makeService(string $repairedSvg, array $orchestratorResult = ['success' => false, 'error' => 'no config']): LmsSvgAiRepairService
    {
        $orchestrator = $this->createMock(LmsAiOrchestrationService::class);
        $orchestrator->expects($this->any())
            ->method('askWithCompaction')
            ->willReturn($orchestratorResult);

        return new LmsSvgAiRepairService($orchestrator, $this->repairService);
    }
}
