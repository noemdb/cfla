<?php

namespace Tests\Unit\Services\Planning;

use App\Services\Planning\DiagnosticMarkdownBuilder;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class DiagnosticMarkdownBuilderTest extends TestCase
{
    public function test_it_builds_a_self_contained_snapshot_without_exposing_the_access_token(): void
    {
        $report = [
            'generated_at' => CarbonImmutable::parse('2026-11-10T09:40:00-04:00'),
            'source_url' => '/app/planning/diagnostico?selectedId=7',
            'selected' => [
                'id' => 7,
                'name' => 'Diagnóstico | Primer Momento',
                'description' => 'Evaluación integral',
                'active' => true,
                'token' => 'must-not-appear',
                'lapso' => '1.er Momento',
                'pestudio' => 'Primaria',
                'referent' => 'Referente A',
            ],
            'catalog' => [],
            'filters' => [],
            'kpis' => [],
            'summary' => [],
            'pensums' => [],
            'grades' => [],
            'knowledge_areas' => [],
            'question_bank' => [
                'filters' => [],
                'summary' => [],
                'selected_pensum' => 'Pensum de muestra',
                'questions' => [[
                    'group' => 'Grupo A',
                    'question' => "Texto completo | con salto\nde línea",
                    'type' => 'multiple',
                    'difficulty' => 'medium',
                    'weighing' => 1.5,
                    'order' => 1,
                    'active' => true,
                    'competency' => 'Competencia',
                    'indicator' => 'I1 — Indicador',
                    'loads' => '1/A · Docente · Lapso',
                    'options' => [
                        ['text' => 'Opción incorrecta', 'correct' => false],
                        ['text' => 'Opción correcta', 'correct' => true],
                    ],
                ]],
            ],
            'activation' => ['search' => '', 'pensums' => []],
            'alerts' => [],
        ];

        $markdown = (new DiagnosticMarkdownBuilder())->build($report);

        self::assertStringContainsString('language: "es-VE"', $markdown);
        self::assertStringContainsString('generated_at: "2026-11-10T09:40:00-04:00"', $markdown);
        self::assertStringContainsString('## 1. Cómo leer este documento', $markdown);
        self::assertStringContainsString('## 12. Banco de preguntas', $markdown);
        self::assertStringContainsString('Diagnóstico \\| Primer Momento', $markdown);
        self::assertStringContainsString('Texto completo \\| con salto de línea', $markdown);
        self::assertStringContainsString('| medium | 1,50 | 1 | Sí |', $markdown);
        self::assertStringContainsString('Opción correcta [correcta]', $markdown);
        self::assertStringNotContainsString('must-not-appear', $markdown);
        $this->assertMarkdownTablesHaveConsistentColumns($markdown);
    }

    public function test_it_exports_a_catalog_when_no_instrument_is_selected(): void
    {
        $markdown = (new DiagnosticMarkdownBuilder())->build([
            'generated_at' => CarbonImmutable::parse('2026-11-10T09:40:00-04:00'),
            'source_url' => '/app/planning/diagnostico',
            'selected' => null,
            'catalog' => [[
                'name' => 'Diagnóstico de muestra',
                'active' => true,
                'lapso' => '1.er Momento',
                'pestudio' => 'Primaria',
                'referent' => 'R1 — Referente',
                'questions' => 4,
                'sessions' => 2,
                'completed_sessions' => 1,
                'answers' => 3,
            ]],
            'filters' => [],
        ]);

        self::assertStringContainsString('Catálogo completo', $markdown);
        self::assertStringContainsString('para exportar el detalle de un instrumento, selecciónalo', $markdown);
        self::assertStringContainsString('Diagnóstico de muestra', $markdown);
        self::assertStringContainsString('## 6. Ficha del instrumento', $markdown);
        $this->assertMarkdownTablesHaveConsistentColumns($markdown);
    }

    private function assertMarkdownTablesHaveConsistentColumns(string $markdown): void
    {
        $tableColumns = null;
        $tableCount = 0;

        foreach (explode("\n", $markdown) as $line) {
            if (! str_starts_with($line, '|') || ! str_ends_with($line, '|')) {
                $tableColumns = null;
                continue;
            }

            $cells = preg_split('/(?<!\\\\)\|/', $line, -1, PREG_SPLIT_NO_EMPTY);
            $columnCount = count($cells);
            if ($tableColumns === null) {
                $tableColumns = $columnCount;
                $tableCount++;
                continue;
            }

            self::assertSame($tableColumns, $columnCount, 'Markdown table row has an inconsistent number of columns.');
        }

        self::assertGreaterThan(0, $tableCount, 'Expected at least one Markdown table.');
    }
}
