<?php

namespace Tests\Unit\Services\Planning;

use App\Services\Planning\ActivityMarkdownBuilder;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class ActivityMarkdownBuilderTest extends TestCase
{
    public function test_it_builds_a_complete_snapshot_for_llm_analysis(): void
    {
        $markdown = (new ActivityMarkdownBuilder)->build($this->report());

        self::assertStringContainsString('document_type: "dataset_snapshot_for_llm"', $markdown);
        self::assertStringContainsString('generated_at: "2026-11-13T10:30:00-04:00"', $markdown);
        self::assertStringContainsString('total_plans: 1', $markdown);
        self::assertStringContainsString('## 1. Cómo usar este documento', $markdown);
        self::assertStringContainsString('## 2. Glosario de campos', $markdown);
        self::assertStringContainsString('## 3. Filtros aplicados y alcance', $markdown);
        self::assertStringContainsString('## 4. Resumen ejecutivo', $markdown);
        self::assertStringContainsString('### 6.1 Plan #101', $markdown);
        self::assertStringContainsString('#### 6.1.1 Actividad #9001', $markdown);
        self::assertStringContainsString('**Sección INICIO:**', $markdown);
        self::assertStringContainsString('**Sección DESARROLLO:**', $markdown);
        self::assertStringContainsString('**Sección CIERRE:**', $markdown);
        self::assertStringContainsString('**Indicadores de logro**', $markdown);
        self::assertStringContainsString('| 1 | Indicador \\| clave | 40,00 | Cuantitativo |', $markdown);
        self::assertStringContainsString('**Imagen complementaria:** `supplements/funcion-lineal.jpg`', $markdown);
        self::assertStringContainsString('```markdown', $markdown);
        self::assertStringContainsString('## 7. Actividades en revisión', $markdown);
        self::assertStringContainsString('| 101 | Matemática (MAT4) | 4to Año A |', $markdown);
        self::assertStringContainsString('## 8. Observaciones de coordinación', $markdown);
        self::assertStringContainsString('> Falta profundizar en los referentes.', $markdown);
        self::assertStringContainsString('## 9. Señales de calidad y completitud', $markdown);
        self::assertStringContainsString('sin aprendizaje', $markdown);
        self::assertStringContainsString('## 10. Matriz plana de actividades', $markdown);
        self::assertStringContainsString('## 11. Guía de análisis y productos esperados', $markdown);
        self::assertStringContainsString('## 12. Trazabilidad técnica', $markdown);
        self::assertStringContainsString('| Actividades aprobadas | 1 | 50,0 % |', $markdown);
        self::assertStringContainsString('| Actividades por debajo del AVR | 1 | 50,0 % |', $markdown);

        $this->assertMarkdownTablesHaveConsistentColumns($markdown);
    }

    public function test_it_handles_an_empty_universe_without_breaking_the_document(): void
    {
        $markdown = (new ActivityMarkdownBuilder)->build([
            'generated_at' => CarbonImmutable::parse('2026-11-13T10:30:00-04:00'),
            'source_url' => '/app/planning/activities',
            'filters' => [
                'pestudio' => 'Todos',
                'grado' => 'Todos',
                'seccion' => 'Todos',
                'lapso' => 'Todos',
                'profesor' => 'Todos',
                'status_activities' => 'Todas',
                'filter_status' => 'Todos',
                'filter_observations' => false,
                'raw' => [],
            ],
            'plans' => [],
        ]);

        self::assertStringContainsString('Sin planes de evaluación en el universo filtrado', $markdown);
        self::assertStringContainsString('No hay planes de evaluación en el universo filtrado.', $markdown);
        self::assertStringContainsString('No hay actividades en el universo filtrado.', $markdown);
        self::assertStringContainsString('## 12. Trazabilidad técnica', $markdown);

        $this->assertMarkdownTablesHaveConsistentColumns($markdown);
    }

    /**
     * @return array<string, mixed>
     */
    private function report(): array
    {
        return [
            'generated_at' => CarbonImmutable::parse('2026-11-13T10:30:00-04:00'),
            'source_url' => '/app/planning/activities',
            'filters' => [
                'search' => null,
                'pestudio' => 'Educación Media General',
                'grado' => '4to Año',
                'seccion' => 'A',
                'lapso' => '1.er Momento',
                'profesor' => 'Pérez Ana',
                'status_activities' => 'Con actividades',
                'filter_status' => 'En revisión',
                'filter_observations' => true,
                'raw' => ['pestudio_id' => 3, 'lapso_id' => 8],
            ],
            'plans' => [[
                'id' => 101,
                'pestudio' => [
                    'name' => 'Educación Media General',
                    'code' => 'EMG',
                    'activities_avr' => 100,
                ],
                'grado' => ['name' => '4to Año', 'code' => '4A'],
                'seccion' => ['name' => 'A', 'amount_student' => 32],
                'asignatura' => [
                    'name' => 'Matemática',
                    'code' => 'MAT4',
                    'hour_t_week' => 4,
                    'hour_p_week' => 2,
                    'unid_credit' => 5,
                ],
                'lapso' => [
                    'name' => '1.er Momento',
                    'code' => 'L1',
                    'finicial' => CarbonImmutable::parse('2026-09-15'),
                    'ffinal' => CarbonImmutable::parse('2026-12-15'),
                ],
                'profesor' => ['name' => 'Ana', 'lastname' => 'Pérez'],
                'grupo' => null,
                'objetivo' => 'Consolidar el pensamiento algebraico.',
                'description' => 'Plan de evaluación del primer momento.',
                'observations' => 'Falta profundizar en los referentes.',
                'category' => 'regular',
                'status_official' => true,
                'status_baremo' => 'true',
                'nota_type' => 'PROMEDIADA',
                'created_at' => CarbonImmutable::parse('2026-09-10T08:00:00-04:00'),
                'updated_at' => CarbonImmutable::parse('2026-09-12T09:30:00-04:00'),
                'activities' => [
                    [
                        'id' => 9001,
                        'finicial' => CarbonImmutable::parse('2026-09-20'),
                        'ffinal' => CarbonImmutable::parse('2026-09-27'),
                        'topic' => 'Función lineal',
                        'thematic' => 'Pendiente, dominio y rango.',
                        'references' => 'Stewart, Cálculo. Ministerio de Educación.',
                        'teaching' => 'INICIO: Preguntas previas. DESARROLLO: Modelado. CIERRE: Metacognición.',
                        'teaching_sections' => [
                            'sections' => [
                                'INICIO' => 'Preguntas previas.',
                                'DESARROLLO' => 'Modelado paso a paso.',
                                'CIERRE' => 'Metacognición.',
                            ],
                            'missing' => [],
                        ],
                        'learning' => 'Identifica pendiente y dominio.',
                        'description' => 'Prueba escrita con ejercicios.',
                        'observations' => 'ODS 4: educación de calidad.',
                        'comments' => 'Ajustar el cierre.',
                        'status' => false,
                        'word_count' => 120,
                        'activities_avr' => 100,
                        'created_at' => CarbonImmutable::parse('2026-09-11T10:00:00-04:00'),
                        'updated_at' => CarbonImmutable::parse('2026-09-12T11:00:00-04:00'),
                        'achievements' => [
                            ['name' => 'Indicador | clave', 'weighting' => 40, 'quantitative' => true],
                        ],
                        'supplement' => [
                            'text' => "# Material de apoyo\n\nContenido complementario.",
                            'image_url' => 'supplements/funcion-lineal.jpg',
                        ],
                        'lms' => [
                            'status' => 'PUBLISHED',
                            'published_at' => CarbonImmutable::parse('2026-09-18T12:00:00-04:00'),
                            'publish_at' => CarbonImmutable::parse('2026-09-18T12:00:00-04:00'),
                            'unpublish_at' => null,
                            'sections' => 2,
                            'resources' => 1,
                            'links' => 0,
                            'embeds' => 0,
                        ],
                    ],
                    [
                        'id' => 9002,
                        'finicial' => CarbonImmutable::parse('2026-10-04'),
                        'ffinal' => CarbonImmutable::parse('2026-10-11'),
                        'topic' => 'Sistema de ecuaciones',
                        'thematic' => 'Métodos de resolución.',
                        'references' => 'Guía institucional.',
                        'teaching' => 'Resolución guiada de sistemas de ecuaciones.',
                        'teaching_sections' => [
                            'sections' => [],
                            'missing' => ['INICIO', 'DESARROLLO', 'CIERRE'],
                        ],
                        'learning' => '',
                        'description' => 'Taller grupal.',
                        'observations' => '',
                        'comments' => null,
                        'status' => true,
                        'word_count' => 80,
                        'activities_avr' => 100,
                        'created_at' => CarbonImmutable::parse('2026-09-11T10:05:00-04:00'),
                        'updated_at' => CarbonImmutable::parse('2026-09-12T11:05:00-04:00'),
                        'achievements' => [],
                        'supplement' => null,
                        'lms' => null,
                    ],
                ],
            ]],
        ];
    }

    private function assertMarkdownTablesHaveConsistentColumns(string $markdown): void
    {
        $tableColumns = null;

        foreach (explode("\n", $markdown) as $line) {
            if (! str_starts_with($line, '|') || ! str_ends_with($line, '|')) {
                $tableColumns = null;

                continue;
            }

            $cells = preg_split('/(?<!\\\\)\|/', $line, -1, PREG_SPLIT_NO_EMPTY);
            $columnCount = count($cells);
            if ($tableColumns === null) {
                $tableColumns = $columnCount;

                continue;
            }

            self::assertSame($tableColumns, $columnCount, 'Markdown table row has an inconsistent number of columns.');
        }
    }
}
