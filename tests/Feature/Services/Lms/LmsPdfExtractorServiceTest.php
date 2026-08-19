<?php

namespace Tests\Feature\Services\Lms;

use App\Services\Lms\LmsPdfExtractorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Verifica el encadenado API de Datalab → pdftotext del extractor de PDFs del módulo LMS.
 *
 * @group lms
 * @group lms-pdf
 */
class LmsPdfExtractorServiceTest extends TestCase
{
    private function makePdf(string $content): string
    {
        $pdf = Pdf::loadHTML($content);
        $path = tempnam(sys_get_temp_dir(), 'lms-pdf-').'.pdf';
        file_put_contents($path, $pdf->output());

        return $path;
    }

    /** @test */
    public function extract_cae_a_pdftotext_cuando_no_hay_api_key(): void
    {
        config(['lms.datalab.api_key' => null]);

        $pdfPath = $this->makePdf('<html><body><h1>Las fracciones</h1><p>Una fracción representa partes de un todo.</p></body></html>');

        try {
            $text = app(LmsPdfExtractorService::class)->extract($pdfPath);

            $this->assertStringContainsString('fracciones', mb_strtolower($text));
        } finally {
            @unlink($pdfPath);
        }
    }

    /** @test */
    public function extract_usa_la_api_cuando_devuelve_markdown(): void
    {
        Http::fake([
            '*/api/v1/convert' => Http::response([
                'success' => true,
                'request_id' => 'abc123',
                'request_check_url' => 'https://www.datalab.to/api/v1/convert/abc123',
            ]),
            '*/api/v1/convert/abc123' => Http::response([
                'status' => 'complete',
                'success' => true,
                'markdown' => "## **Genes y ambiente**\n\nLa expresión génica se regula por factores ambientales.",
            ]),
        ]);

        config([
            'lms.datalab.api_key' => 'test-key',
            'lms.datalab.poll_interval' => 1,
        ]);

        $pdfPath = $this->makePdf('<html><body><p>Texto de relleno del PDF.</p></body></html>');

        try {
            $text = app(LmsPdfExtractorService::class)->extract($pdfPath);

            // marker devuelve markdown con encabezados ## (pdftotext no).
            $this->assertStringContainsString('##', $text);
            $this->assertStringContainsString('Genes y ambiente', $text);
        } finally {
            @unlink($pdfPath);
        }
    }

    /** @test */
    public function extract_cae_a_pdftotext_cuando_la_api_reporta_fallo(): void
    {
        Http::fake([
            '*/api/v1/convert' => Http::response([
                'success' => true,
                'request_id' => 'abc123',
                'request_check_url' => 'https://www.datalab.to/api/v1/convert/abc123',
            ]),
            '*/api/v1/convert/abc123' => Http::response([
                'status' => 'failed',
                'success' => false,
                'error' => 'Page rate limit exceeded.',
            ]),
        ]);

        config([
            'lms.datalab.api_key' => 'test-key',
            'lms.datalab.poll_interval' => 1,
        ]);

        $pdfPath = $this->makePdf('<html><body><p>Contenido textual de prueba.</p></body></html>');

        try {
            $text = app(LmsPdfExtractorService::class)->extract($pdfPath);

            $this->assertStringContainsString('textual', mb_strtolower($text));
        } finally {
            @unlink($pdfPath);
        }
    }

    /** @test */
    public function datalab_to_markdown_reintenta_ante_429(): void
    {
        Http::fake([
            '*/api/v1/convert' => Http::sequence()
                ->push(['detail' => 'Rate limit exceeded'], 429)
                ->push([
                    'success' => true,
                    'request_id' => 'abc123',
                    'request_check_url' => 'https://www.datalab.to/api/v1/convert/abc123',
                ]),
            '*/api/v1/convert/abc123' => Http::response([
                'status' => 'complete',
                'success' => true,
                'markdown' => '## **Resultado final**',
            ]),
        ]);

        config([
            'lms.datalab.api_key' => 'test-key',
            'lms.datalab.poll_interval' => 1,
            'lms.datalab.max_attempts' => 3,
        ]);

        $pdfPath = $this->makePdf('<html><body><p>Texto de relleno.</p></body></html>');

        try {
            $markdown = app(LmsPdfExtractorService::class)->datalabToMarkdown($pdfPath);

            $this->assertSame('## **Resultado final**', $markdown);
            Http::assertSentCount(3); // 2 submit (1 fallido) + 1 poll
        } finally {
            @unlink($pdfPath);
        }
    }
}
