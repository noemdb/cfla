<?php

namespace Tests\Feature\Diagnostic;

use Tests\TestCase;

/**
 * Regresión de la notación matemática en <x-diag.math-cell>.
 *
 * Contexto: \ce{} (y \pu{}) pertenecen a la extensión mhchem, opcional en
 * KaTeX. Sin cargarla el núcleo responde "Undefined control sequence: \ce".
 * Además auto-render sólo procesa texto DENTRO de delimitadores, así que una
 * fórmula química escrita "a pelo" nunca llegaba a KaTeX.
 *
 * Estos tests cubren el lado servidor (envoltura de delimitadores + neutralización
 * de la CVE de KaTeX). El lado cliente (carga de mhchem) se verificó aparte con
 * un bundle real de esbuild + jsdom.
 */
class DiagnosticMathChemistryTest extends TestCase
{
    private function renderCell(string $content): string
    {
        $html = \Illuminate\Support\Facades\Blade::render(
            '<x-diag.math-cell :content="$c" uid="t" />',
            ['c' => $content]
        );

        return $html;
    }

    /** Texto realmente visible en la celda (fuera del x-init de Alpine). */
    private function cellText(string $html): string
    {
        $doc = new \DOMDocument;
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?><body>'.$html.'</body>');
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $node = null;
        foreach ($doc->getElementsByTagName('div') as $div) {
            if ($div->hasAttribute('wire:ignore')) {
                $node = $div;
                break;
            }
        }
        if ($node === null) {
            return '';
        }

        return trim($node->textContent);
    }

    public function test_quimica_sin_delimitadores_se_envuelve_en_bloque_display(): void
    {
        $content = "\\ce{N2 + 3H2 <=> 2NH3}\n\n\\ce{CaCO3 ->[\\Delta] CaO + CO2 ^}\n\n\\ce{Ba^2+ + SO4^2- -> BaSO4 v} ";
        $text = $this->cellText($this->renderCell($content));

        $this->assertStringContainsString('$$\ce{N2 + 3H2 <=> 2NH3}$$', $text);
        $this->assertStringContainsString('$$\ce{CaCO3 ->[\Delta] CaO + CO2 ^}$$', $text);
        $this->assertStringContainsString('$$\ce{Ba^2+ + SO4^2- -> BaSO4 v}$$', $text);
    }

    public function test_quimica_dentro_de_una_frase_se_envuelve_inline(): void
    {
        $text = $this->cellText($this->renderCell('La reaccion es \ce{H2 + O2 -> H2O} hoy.'));

        $this->assertStringContainsString('\(\ce{H2 + O2 -> H2O}\)', $text);
    }

    public function test_matematica_con_delimitadores_propios_no_se_toca(): void
    {
        $content = '$$f(x) = \frac{x^2 - 1}{x + 1}$$';
        $text = $this->cellText($this->renderCell($content));

        $this->assertSame($content, $text);
    }

    public function test_ce_ya_delimitado_no_se_doble_envuelve(): void
    {
        $content = '$$\ce{H2O}$$';
        $text = $this->cellText($this->renderCell($content));

        $this->assertSame($content, $text);
        $this->assertStringNotContainsString('$$$$', $text);
    }

    public function test_ce_con_llaves_anidadas_se_envuelve_una_sola_vez(): void
    {
        $text = $this->cellText($this->renderCell('\ce{\frac{a}{b}}'));

        $this->assertStringContainsString('$$\ce{\frac{a}{b}}$$', $text);
        // 2 = un par delimitador (apertura + cierre). 4 significaría doble envoltura.
        $this->assertSame(2, substr_count($text, '$$'), 'no debe duplicar delimitadores');
    }

    public function test_texto_plano_queda_intacto(): void
    {
        $text = $this->cellText($this->renderCell('Sin matematicas aqui'));

        $this->assertSame('Sin matematicas aqui', $text);
    }

    public function test_macros_html_de_katex_se_neutralizan_aunque_haya_ce(): void
    {
        $text = $this->cellText($this->renderCell('\htmlData{x}{onclick=alert(1)} \ce{H2O}'));

        $this->assertStringNotContainsString('htmlData', $text);
        $this->assertStringNotContainsString('onclick', $text);
        $this->assertStringContainsString('$$\ce{H2O}$$', $text);
    }

    public function test_pu_operadores_de_unidad_tambien_se_envuelven(): void
    {
        $text = $this->cellText($this->renderCell('\pu{123 kJ/mol}'));

        $this->assertStringContainsString('$$\pu{123 kJ/mol}$$', $text);
    }

    public function test_la_celda_conserva_wire_ignore_para_morphs(): void
    {
        $html = $this->renderCell('$$\ce{H2O}$$');

        $this->assertStringContainsString('wire:ignore', $html);
    }
}
