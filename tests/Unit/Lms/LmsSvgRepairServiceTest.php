<?php

namespace Tests\Unit\Lms;

use App\Services\Lms\LmsSvgRepairService;
use PHPUnit\Framework\TestCase;

/**
 * LmsSvgRepairService — P1/P2: reparación defensiva y validación de SVGs
 * truncados por el generador (LLM).
 *
 * Bug real (contenido 2232, actividad 85): el SVG terminaba en
 * '<rect x="560" ... rx="8"</svg>' — un tag de apertura sin su '>' que se
 * comía el '</svg>'. El navegador pintaba un rectángulo NEGRO (fill por
 * defecto del SVG) que tapaba la mitad del diagrama.
 */
class LmsSvgRepairServiceTest extends TestCase
{
    private LmsSvgRepairService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LmsSvgRepairService;
    }

    private function figure(string $svg): string
    {
        return "<figure class=\"my-6\">\n  <figcaption>Diagrama</figcaption>\n"
            ."  <div class=\"flex justify-center rounded-xl p-2\">\n"
            .$svg
            ."\n  </div>\n</figure>";
    }

    public function test_repair_removes_broken_tag_and_recloses_svg(): void
    {
        // Body real truncado (contenido 2232): el <rect> no tiene su '>'.
        $broken = $this->figure(
            '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <rect x=\"60\" y=\"210\" width=\"380\" height=\"170\" fill=\"#e3f2fd\"/>"
            ."\n  <rect x=\"560\" y=\"210\" width=\"380\" height=\"170\" rx=\"8\"</svg>"
        );

        $repaired = $this->service->repair($broken);

        // El tag roto desaparece por completo (incluido el '</svg>' que tragó).
        $this->assertStringNotContainsString('<rect x="560"', $repaired);
        // El svg vuelve a estar cerrado exactamente una vez.
        $this->assertSame(1, substr_count($repaired, '<svg'));
        $this->assertSame(1, substr_count($repaired, '</svg>'));
        // El wrapper figure se conserva.
        $this->assertStringContainsString('</figure>', $repaired);
    }

    public function test_repair_leaves_well_formed_bodies_untouched(): void
    {
        $good = $this->figure(
            '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <rect x=\"0\" y=\"0\" width=\"100\" height=\"100\" fill=\"#f8f9fa\"/>"
            ."\n  <text x=\"50\" y=\"50\">Hola</text>"
            ."\n</svg>"
        );

        $this->assertSame($good, $this->service->repair($good));
    }

    public function test_repair_ignores_bodies_without_svg(): void
    {
        $plain = '<p>Texto normal sin diagramas.</p>';
        $this->assertSame($plain, $this->service->repair($plain));
    }

    public function test_repair_handles_broken_text_tag(): void
    {
        // Variante con <text> roto (contenido 1928).
        $broken = $this->figure(
            '<svg viewBox="0 0 1200 950" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <rect width=\"1200\" height=\"950\" fill=\"#f8f9fa\"/>"
            ."\n  <text x=\"230\" y=\"695\" font-size=\"13\" fill=\"#333333\"</svg>"
        );

        $repaired = $this->service->repair($broken);

        $this->assertStringNotContainsString('<text x="230"', $repaired);
        $this->assertSame(1, substr_count($repaired, '</svg>'));
        $this->assertStringContainsString('</figure>', $repaired);
    }

    public function test_is_well_formed_rejects_truncated_svg(): void
    {
        // El mismo bug del contenido 2232: pasa la extracción por regex del
        // generador (existe '<svg ...>' y '</svg>') pero está malformado.
        $truncated = '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <rect x=\"560\" y=\"210\" width=\"380\" height=\"170\" rx=\"8\"</svg>";

        $this->assertFalse($this->service->isWellFormed($truncated));
    }

    public function test_is_well_formed_accepts_complete_svg(): void
    {
        $good = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <rect x=\"0\" y=\"0\" width=\"100\" height=\"100\" fill=\"#f8f9fa\"/>"
            ."\n  <text x=\"50\" y=\"50\">Hola</text>"
            ."\n</svg>";

        $this->assertTrue($this->service->isWellFormed($good));
    }

    public function test_is_well_formed_accepts_self_closed_shapes_and_tspans(): void
    {
        // SVG real bien formado con rects self-closed, marker, path y tspans
        // (estilo del generador): no debe dar falso positivo.
        $good = '<svg viewBox="0 0 1000 780" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <defs>\n    <marker id=\"arrow\" markerWidth=\"10\" markerHeight=\"10\" refX=\"8\" refY=\"3\" orient=\"auto\" markerUnits=\"strokeWidth\">\n      <path d=\"M0,0 L8,3 L0,6 Z\" fill=\"#777777\"/>\n    </marker>\n  </defs>"
            ."\n  <rect x=\"55\" y=\"135\" width=\"200\" height=\"100\" rx=\"8\" fill=\"#e3f2fd\" stroke=\"#bbbbbb\"/>"
            ."\n  <line x1=\"257\" y1=\"185\" x2=\"283\" y2=\"185\" stroke=\"#777777\" stroke-width=\"2\" marker-end=\"url(#arrow)\"/>"
            ."\n  <text x=\"155\" y=\"170\" font-family=\"Arial\" font-size=\"14\" fill=\"#1a1a1a\" text-anchor=\"middle\">"
            ."\n    <tspan x=\"155\" dy=\"0\">Ocurre una situación</tspan>"
            ."\n    <tspan x=\"155\" dy=\"18\">de acoso</tspan>"
            ."\n  </text>"
            ."\n</svg>";

        $this->assertTrue($this->service->isWellFormed($good));
    }

    public function test_is_well_formed_rejects_missing_svg_close(): void
    {
        $this->assertFalse($this->service->isWellFormed('<svg viewBox="0 0 10 10"><rect width="10" height="10"/>'));
        $this->assertFalse($this->service->isWellFormed('<rect width="10" height="10"/></svg>'));
    }

    public function test_crop_to_content_reduces_tall_canvas(): void
    {
        // Caso real 2232: canvas 1000x950, contenido hasta y=380 (60% vacío).
        $svg = '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            .'<rect x="0" y="0" width="1000" height="950" fill="#f8f9fa"/>'
            .'<rect x="60" y="210" width="380" height="170" fill="#e3f2fd"/>'
            .'<rect x="560" y="210" width="380" height="170" fill="#fce4ec"/>'
            .'</svg>';

        $cropped = $this->service->cropToContent($svg);

        // 380 (bottom) + margen 40 = 420. El ancho no cambia.
        $this->assertStringContainsString('viewBox="0 0 1000 420"', $cropped);
    }

    public function test_crop_to_content_keeps_text_bottom_into_account(): void
    {
        // Contenido de texto bajo las cajas: y=360 + font-size 14 → 374.
        $svg = '<svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">'
            .'<rect x="0" y="0" width="1000" height="950" fill="#f8f9fa"/>'
            .'<text x="500" y="360" font-size="14" fill="#333">Texto final</text>'
            .'</svg>';

        $cropped = $this->service->cropToContent($svg);

        // 360 + 14 + margen 40 = 414.
        $this->assertStringContainsString('viewBox="0 0 1000 414"', $cropped);
    }

    public function test_crop_to_content_leaves_filled_canvas_untouched(): void
    {
        // Contenido que ocupa > 80% del canvas → sin recorte (conservador).
        $svg = '<svg viewBox="0 0 1000 780" xmlns="http://www.w3.org/2000/svg">'
            .'<rect x="0" y="0" width="1000" height="780" fill="#f8f9fa"/>'
            .'<rect x="50" y="50" width="900" height="650" fill="#e3f2fd"/>'
            .'</svg>';

        $this->assertSame($svg, $this->service->cropToContent($svg));
    }

    public function test_crop_to_content_ignores_missing_viewbox(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"/></svg>';
        $this->assertSame($svg, $this->service->cropToContent($svg));
    }

    public function test_crop_to_content_ignores_background_rect(): void
    {
        // El rect de fondo (≥ 90% del canvas) no debe contar como contenido.
        $svg = '<svg viewBox="0 0 1000 900" xmlns="http://www.w3.org/2000/svg">'
            .'<rect x="0" y="0" width="1000" height="900" fill="#f8f9fa"/>'
            .'<rect x="100" y="100" width="200" height="100" fill="#e3f2fd"/>'
            .'</svg>';

        $cropped = $this->service->cropToContent($svg);

        // 200 (bottom) + 40 = 240 — el fondo 900 no infla el recorte.
        $this->assertStringContainsString('viewBox="0 0 1000 240"', $cropped);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Contrast:Normalización de contraste de textos SVG (fill en <text>/<tspan>)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_normalize_contrast_darkens_light_gray_text_fills(): void
    {
        // Caso real del contenido 2232 (Diagrama: Dinámicas de poder…):
        // subtítulos y etiquetas en #555/#888 quedaban poco legibles sobre
        // los fondos pastel a 12–14px.
        $svg = '<svg viewBox="0 0 1000 420" xmlns="http://www.w3.org/2000/svg">'
            .'<rect x="0" y="0" width="1000" height="950" fill="#f8f9fa"/>'
            .'<text x="500" y="65" font-size="14" font-style="italic" fill="#555555">El poder no solo se ejerce…</text>'
            .'<text x="250" y="262" font-size="13" font-style="italic" fill="#888888">(signo visible)</text>'
            .'<text x="250" y="292" font-size="14" fill="#333333">• Reglas y normas claras</text>'
            .'<text x="500" y="128" font-size="17" font-weight="bold" fill="#1a1a1a">PODER</text>'
            .'</svg>';

        $out = $this->service->normalizeContrast($svg);

        $this->assertStringContainsString('fill="#333333"', $out); // #555 → #333
        $this->assertStringContainsString('>PODER</text>', $out);
        $this->assertStringNotContainsString('fill="#555555"', $out);
        $this->assertStringNotContainsString('fill="#888888"', $out);
        // Los textos ya oscuros (#1a1a1a) no se tocan.
        $this->assertStringContainsString('fill="#1a1a1a"', $out);
        // El rect de fondo NO se altera (diseño intacto).
        $this->assertStringContainsString('fill="#f8f9fa"', $out);
    }

    public function test_normalize_contrast_maps_all_light_grays(): void
    {
        $svg = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            .'<text fill="#eeeeee">a</text>'
            .'<text fill="#dddddd">b</text>'
            .'<text fill="#cccccc">c</text>'
            .'<text fill="#bbbbbb">d</text>'
            .'<text fill="#999999">e</text>'
            .'<text fill="#666666">f</text>'
            .'<text fill="#777777">g</text>'
            .'</svg>';

        $out = $this->service->normalizeContrast($svg);

        // Mapeo esperado (gris claro → gris oscuro ≥ AA sobre blanco).
        $this->assertStringContainsString('<text fill="#666666">a</text>', $out);  // #eeeeee
        $this->assertStringContainsString('<text fill="#555555">b</text>', $out);  // #dddddd
        $this->assertStringContainsString('<text fill="#4d4d4d">c</text>', $out);  // #cccccc
        $this->assertStringContainsString('<text fill="#444444">d</text>', $out);  // #bbbbbb
        $this->assertStringContainsString('<text fill="#3d3d3d">e</text>', $out);  // #999999
        $this->assertStringContainsString('<text fill="#333333">f</text>', $out);  // #666666
        $this->assertStringContainsString('<text fill="#333333">g</text>', $out);  // #777777
    }

    public function test_normalize_contrast_darkens_tspan_fills(): void
    {
        $svg = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            .'<text x="10" y="10" fill="#1a1a1a">'
            .'  <tspan x="10" dy="0" fill="#999999">Subtítulo tenue</tspan>'
            .'</text>'
            .'</svg>';

        $out = $this->service->normalizeContrast($svg);

        $this->assertStringContainsString('<tspan x="10" dy="0" fill="#3d3d3d">', $out);
    }

    public function test_normalize_contrast_preserves_colored_text_and_no_svg(): void
    {
        // Textos con matiz (color puro) no deben tocarse.
        $svg = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            .'<text fill="#1976d2">Azul</text>'
            .'<text fill="#c2185b">Rosa</text>'
            .'<text fill="#ffffff">Blanco</text>'
            .'</svg>';

        $this->assertSame($svg, $this->service->normalizeContrast($svg));

        // Sin SVG: no hace nada.
        $this->assertSame('<p>Texto normal</p>', $this->service->normalizeContrast('<p>Texto normal</p>'));
    }

    public function test_repair_applies_contrast_normalization(): void
    {
        // El punto de entrada público (repair) también oscurece textos claros.
        $body = $this->figure(
            '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <rect x=\"0\" y=\"0\" width=\"100\" height=\"100\" fill=\"#f8f9fa\"/>"
            ."\n  <text x=\"50\" y=\"50\" fill=\"#666666\">En negro</text>"
            ."\n</svg>"
        );

        $out = $this->service->repair($body);

        $this->assertStringNotContainsString('fill="#666666"', $out);
        $this->assertStringContainsString('fill="#333333"', $out);
        // figure intact
        $this->assertStringContainsString('</figure>', $out);
    }

    public function test_normalize_contrast_darkens_light_gray_strokes_and_markers(): void
    {
        // Mejora 6: flechas y bordes tenues (#bbbbbb/#cccccc) deben oscurecerse.
        $svg = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            .'<defs>'
            .'<marker id="arrow" markerWidth="8" markerHeight="8" refX="8" refY="4" orient="auto">'
            .'<path d="M0,0 L0,8 L8,4 z" fill="#cccccc"/>'
            .'</marker>'
            .'</defs>'
            .'<line x1="10" y1="90" x2="90" y2="10" stroke="#bbbbbb" stroke-width="2" marker-end="url(#arrow)"/>'
            .'<rect x="20" y="20" width="40" height="40" fill="#e3f2fd" stroke="#cccccc"/>'
            .'</svg>';

        $out = $this->service->normalizeContrast($svg);

        // marker path #cccccc → #4d4d4d ; line stroke #bbbbbb → #444444 ;
        // rect stroke #cccccc → #4d4d4d. El rect fill pastel no cambia.
        $this->assertStringContainsString('<path d="M0,0 L0,8 L8,4 z" fill="#4d4d4d"/>', $out);
        $this->assertStringContainsString('stroke="#444444" stroke-width="2"', $out);
        $this->assertStringContainsString('fill="#e3f2fd" stroke="#4d4d4d"', $out);
        $this->assertStringNotContainsString('stroke="#bbbbbb"', $out);
        $this->assertStringNotContainsString('fill="#cccccc"', $out);
    }

    public function test_normalize_contrast_keeps_pastel_fills_and_equal_rgb(): void
    {
        // Fondos con matiz (pasteles) y gris oscuro nunca se alteran.
        $svg = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            .'<rect x="0" y="0" width="100" height="100" fill="#f8f9fa"/>'   // casi blanco neutro
            .'<rect x="10" y="10" width="30" height="30" fill="#fff3e0"/>'  // pastel naranja
            .'<rect x="50" y="10" width="30" height="30" fill="#1a1a1a"/>'  // oscuro
            .'</svg>';

        $out = $this->service->normalizeContrast($svg);

        $this->assertStringContainsString('fill="#f8f9fa"', $out);
        $this->assertStringContainsString('fill="#fff3e0"', $out);
        $this->assertStringContainsString('fill="#1a1a1a"', $out);
    }

    public function test_ensure_accessibility_adds_role_and_label_from_figcaption(): void
    {
        // Mejora 3: un <figure><figcaption>…</figcaption></figure> sin rol en el
        // <svg> propaga la etiqueta del diagrama al canvas.
        $body = $this->figure(
            '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <text x=\"50\" y=\"50\">PODER</text>"
            ."\n</svg>"
        );

        $out = $this->service->ensureAccessibility($body);

        $this->assertMatchesRegularExpression('/<svg[^>]*role="img"[^>]*aria-label="[^"]*Diagrama[^\"]*"[^>]*aria-hidden="true"/', $out);
        // No duplica los textos internos para lectores de pantalla.
        $this->assertSame(1, substr_count($out, 'role="img"'));
    }

    public function test_ensure_accessibility_respects_existing_role(): void
    {
        // Si el SVG ya trae role propio con aria-label, no se toca.
        $svg = '<figure><figcaption>D</figcaption><div><svg viewBox="0 0 10 10" role="img" aria-label="Ya descrito"></svg></div></figure>';

        $out = $this->service->ensureAccessibility($svg);

        $this->assertStringContainsString('aria-label="Ya descrito"', $out);
        $this->assertStringNotContainsString('aria-hidden="true"', $out);
    }

    public function test_ensure_accessibility_ignores_bodies_without_label(): void
    {
        $plain = '<p>Sin diagramas.</p>';
        $this->assertSame($plain, $this->service->ensureAccessibility($plain));

        $noLabel = '<figure><div><svg viewBox="0 0 10 10"></svg></div></figure>';
        $this->assertSame($noLabel, $this->service->ensureAccessibility($noLabel));
    }

    public function test_render_image_chains_repair_contrast_and_accessibility(): void
    {
        // Mejora 4: pipeline único de render (igual en pantalla y print).
        $body = $this->figure(
            '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">'
            ."\n  <rect x=\"0\" y=\"0\" width=\"100\" height=\"100\" fill=\"#f8f9fa\"/>"
            ."\n  <text x=\"50\" y=\"50\" fill=\"#666666\">Bajo a tipo oscuro</text>"
            ."\n</svg>"
        );

        $out = $this->service->renderImage($body);

        // Contraste aplicado.
        $this->assertStringContainsString('fill="#333333"', $out);
        $this->assertStringNotContainsString('fill="#666666"', $out);
        // Accesibilidad aplicada (rol + etiqueta del figcaption).
        $this->assertStringContainsString('role="img"', $out);
        $this->assertStringContainsString('aria-hidden="true"', $out);
        // Estructura intacta.
        $this->assertStringContainsString('</figure>', $out);
    }
}
