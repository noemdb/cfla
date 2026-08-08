<?php

namespace Tests\Unit\Lms;

use App\Services\Lms\LmsContentClassifier;
use PHPUnit\Framework\TestCase;

/**
 * LmsContentClassifier — P4: clasificador único de contenido LMS.
 * Centraliza la detección mermaid/IMAGE que estaba duplicada en 4 vistas.
 */
class LmsContentClassifierTest extends TestCase
{
    private LmsContentClassifier $classifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classifier = new LmsContentClassifier();
    }

    public function test_is_image_body_detects_type_image(): void
    {
        $this->assertTrue($this->classifier->isImageBody('IMAGE', '<p>sin svg</p>'));
    }

    public function test_is_image_body_detects_svg_in_any_type(): void
    {
        // Un body con <svg> se considera ilustración aunque el type sea TEXT/HTML.
        $this->assertTrue($this->classifier->isImageBody('TEXT', '<figure><svg viewBox="0 0 10 10"></svg></figure>'));
        $this->assertFalse($this->classifier->isImageBody('TEXT', '<p>texto normal</p>'));
    }

    public function test_is_mermaid_body_detects_css_class(): void
    {
        $body = '<div class="mermaid">flowchart LR
A-->B</div>';
        $this->assertTrue($this->classifier->isMermaidBody($body));
    }

    public function test_is_mermaid_body_detects_keyword_dialects(): void
    {
        foreach (['flowchart LR', 'graph TD', 'mindmap', 'sequenceDiagram', 'classDiagram', 'gantt', 'pie', 'stateDiagram', 'erDiagram', 'journey', 'gitgraph', 'timeline'] as $dialect) {
            $this->assertTrue($this->classifier->isMermaidBody($dialect . "\nA-->B"), "debe detectar: {$dialect}");
        }
    }

    public function test_is_mermaid_body_rejects_plain_text(): void
    {
        $this->assertFalse($this->classifier->isMermaidBody('<p>El poder no se limita a actos explícitos.</p>'));
        $this->assertFalse($this->classifier->isMermaidBody('Un texto que menciona flowchart pero no es un diagrama.'));
    }

    public function test_extract_mermaid_code_from_wrapped_div(): void
    {
        $body = '<div class="mermaid">flowchart LR
A[Inicio] --> B[Fin]</div>';
        $this->assertSame(
            "flowchart LR\nA[Inicio] --> B[Fin]",
            $this->classifier->extractMermaidCode($body)
        );
    }

    public function test_extract_mermaid_code_falls_back_to_whole_body(): void
    {
        // Diagrama "desnudo" (sin wrapper div): se usa el body completo.
        $body = "graph TD\nA-->B";
        $this->assertSame($body, $this->classifier->extractMermaidCode($body));
    }

    public function test_extract_mermaid_code_preserves_br_for_multiline_labels(): void
    {
        // A1: los labels multi-línea usan <br/> — strip_tags puro los eliminaría
        // y concatenaría el texto en una sola línea larga.
        $body = '<div class="mermaid">flowchart LR
A["Línea uno<br/>Línea dos"] --> B</div>';
        $code = $this->classifier->extractMermaidCode($body);

        $this->assertStringContainsString('<br/>', $code);
        $this->assertStringContainsString('Línea uno', $code);
        $this->assertStringContainsString('Línea dos', $code);
    }
}
