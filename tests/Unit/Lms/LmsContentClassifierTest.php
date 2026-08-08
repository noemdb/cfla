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

    /* ─────────────────────────────────────────────────────────────────────
     * Clasificación fina (Spec "Campo content_type en lms_activity_sections")
     * ─────────────────────────────────────────────────────────────────── */

    public function test_classify_content_detects_mermaid_before_other_types(): void
    {
        // Precedencia: mermaid gana aunque el type sea TEXT.
        $this->assertSame('mermaid', $this->classifier->classifyContent('TEXT', '<div class="mermaid">flowchart LR
A-->B</div>'));
    }

    public function test_classify_content_detects_svg_in_image_and_html(): void
    {
        $svg = '<figure><svg viewBox="0 0 10 10"><rect width="10" height="10"/></svg></figure>';
        $this->assertSame('svg', $this->classifier->classifyContent('IMAGE', $svg));
        // SVG dentro de HTML también → svg (como ya decide la vista de impresión).
        $this->assertSame('svg', $this->classifier->classifyContent('HTML', $svg));
    }

    public function test_classify_content_distinguishes_raster_image_by_mime(): void
    {
        $this->assertSame('image', $this->classifier->classifyContent('IMAGE', '<p>foto</p>', 'image/png'));
        $this->assertSame('image', $this->classifier->classifyContent('IMAGE', '<p>foto</p>', 'image/jpeg'));
        // Sin mime → se asume svg (ilustración "Generar Imagen").
        $this->assertSame('svg', $this->classifier->classifyContent('IMAGE', '<p>ilustración</p>'));
    }

    public function test_classify_content_detects_math(): void
    {
        $this->assertSame('math', $this->classifier->classifyContent('TEXT', 'La fórmula $E=mc^2$ es clave.'));
        $this->assertSame('math', $this->classifier->classifyContent('TEXT', '$$\\int_0^1 x^2 dx$$'));
        $this->assertSame('math', $this->classifier->classifyContent('TEXT', 'Usa \\(x+1\\) aquí.'));
        $this->assertSame('text', $this->classifier->classifyContent('TEXT', 'Sin matemáticas en este párrafo.'));
    }

    public function test_classify_content_maps_video_audio_and_html(): void
    {
        $this->assertSame('video', $this->classifier->classifyContent('VIDEO', ''));
        $this->assertSame('audio', $this->classifier->classifyContent('AUDIO', ''));
        $this->assertSame('html', $this->classifier->classifyContent('HTML', '<iframe src="https://x"></iframe>'));
    }

    public function test_classify_content_detects_markdown_structure(): void
    {
        $body = '<ul><li>Primero</li><li>Segundo</li></ul>';
        $this->assertSame('markdown', $this->classifier->classifyContent('TEXT', $body));

        $prose = '<p>Un párrafo de prosa sin estructura adicional.</p>';
        $this->assertSame('text', $this->classifier->classifyContent('TEXT', $prose));
    }

    public function test_classify_section_aggregates_types(): void
    {
        $contents = collect([
            (object) ['type' => 'TEXT', 'body' => '<p>prosa</p>', 'media' => null],
            (object) ['type' => 'TEXT', 'body' => '<p>más prosa</p>', 'media' => null],
        ]);
        $this->assertSame('text', $this->classifier->classifySection($contents));
    }

    public function test_classify_section_returns_mixed_for_multiple_types(): void
    {
        $contents = collect([
            (object) ['type' => 'TEXT', 'body' => '<p>prosa</p>', 'media' => null],
            (object) ['type' => 'IMAGE', 'body' => '<svg viewBox="0 0 1 1"><rect width="1" height="1"/></svg>', 'media' => null],
        ]);
        $this->assertSame('mixed', $this->classifier->classifySection($contents));
    }

    public function test_classify_section_returns_none_when_empty(): void
    {
        $this->assertSame('none', $this->classifier->classifySection([]));
        $this->assertSame('none', $this->classifier->classifySection(collect()));
    }
}
