<?php

namespace App\Services\Diagnostic\Section;

use App\Models\app\Instrument\SectionDiagnosticReport;
use App\Models\app\Instrument\SectionGlobalResult;
use App\Models\app\Instrument\SectionAreaResult;
use App\Models\app\Instrument\SectionAreaInsight;
use App\Models\app\Instrument\SectionProfile;
use App\Models\app\Instrument\SectionContrast;
use App\Models\app\Instrument\SectionRecommendation;
use Illuminate\Support\Facades\DB;

class SectionReportBuilder
{
    /**
     * Construye y persiste el informe completo
     */
    public function build(array $data): SectionDiagnosticReport
    {
        return DB::transaction(function () use ($data) {
            // 1. Crear el reporte principal
            $report = SectionDiagnosticReport::updateOrCreate(
                [
                    'section_id' => $data['section_id'],
                    'diagnostic_id' => $data['diagnostic_id']
                ],
                [
                    'students_count' => $data['students_count'],
                    'global_precision_avg' => $data['global_precision_avg'],
                    'generated_at' => now(),
                    'status' => 'generated',
                    'source_prompt_version' => '1.0'
                ]
            );

            // 2. Resultado Global
            $this->buildGlobalResult($report, $data);

            // 3. Resultados por Área e Insights
            $this->buildAreaResults($report, $data['area_results']);

            // 4. Perfil Pedagógico
            $this->buildProfile($report, $data);

            // 5. Contrastes y Brechas
            $this->buildContrast($report, $data);

            // 6. Recomendaciones
            $this->buildRecommendations($report, $data['recommendations'] ?? []);

            return $report;
        });
    }

    protected function buildGlobalResult(SectionDiagnosticReport $report, array $data): void
    {
        SectionGlobalResult::updateOrCreate(
            ['section_diagnostic_report_id' => $report->id],
            [
                'global_summary' => $data['global_summary'] ?? "Informe consolidado para {$data['students_count']} estudiantes.",
                'precision_distribution' => $data['distribution'],
                'open_ended_response_level_distribution' => $data['distribution'],
                'total_questions_avg' => 0,
                'confidence_level' => 'HIGH'
            ]
        );
    }

    protected function buildAreaResults(SectionDiagnosticReport $report, array $areaResults): void
    {
        foreach ($areaResults as $areaData) {
            $area = SectionAreaResult::updateOrCreate(
                [
                    'section_diagnostic_report_id' => $report->id,
                    'subject_id' => $areaData['subject_id']
                ],
                [
                    'area_name' => $areaData['area_name'],
                    'level_distribution' => $areaData['level_distribution'],
                    'precision_avg' => $areaData['precision_avg'],
                    'dominant_errors' => $areaData['dominant_errors'],
                    'observation' => $areaData['observation']
                ]
            );

            // Guardar debilidades recurrentes como insights
            if (!empty($areaData['weaknesses'])) {
                foreach ($areaData['weaknesses'] as $weakness) {
                    SectionAreaInsight::updateOrCreate(
                        [
                            'section_area_result_id' => $area->id,
                            'type' => 'weakness',
                            'description' => $weakness
                        ],
                        ['frequency' => 0] // Podríamos calcular la frecuencia real si fuera necesario
                    );
                }
            }

            // Guardar fortalezas recurrentes como insights
            if (!empty($areaData['strengths'])) {
                foreach ($areaData['strengths'] as $strength) {
                    SectionAreaInsight::updateOrCreate(
                        [
                            'section_area_result_id' => $area->id,
                            'type' => 'strength',
                            'description' => $strength
                        ],
                        ['frequency' => 0]
                    );
                }
            }
        }
    }

    protected function buildProfile(SectionDiagnosticReport $report, array $data): void
    {
        SectionProfile::updateOrCreate(
            ['section_diagnostic_report_id' => $report->id],
            [
                'strengths' => implode(', ', $data['insights']['strengths'] ?? []),
                'needs' => implode(', ', $data['insights']['weaknesses'] ?? []),
                'attitudinal_factors' => 'Analizado en base a la completitud y calidad de respuestas del grupo.',
                'cognitive_summary' => 'Perfil cognitivo consolidado del grupo.',
                'potential_barriers' => 'Barreras detectadas a nivel grupal.',
                'dominant_processing_style' => $data['styles']['processing_style'] ?? 'empirista-inductivo',
                'dominant_learning_style' => $data['styles']['learning_style'] ?? 'visual'
            ]
        );
    }

    protected function buildContrast(SectionDiagnosticReport $report, array $data): void
    {
        SectionContrast::updateOrCreate(
            ['section_diagnostic_report_id' => $report->id],
            [
                'gaps' => $data['contrast']['gaps_summary'] ?? 'Análisis de brechas detectadas.',
                'critical_subjects' => $data['contrast']['critical_subjects'] ?? []
            ]
        );
    }

    protected function buildRecommendations(SectionDiagnosticReport $report, array $recommendations): void
    {
        // Limpiar recomendaciones existentes para evitar duplicados en actualizaciones
        SectionRecommendation::where('section_diagnostic_report_id', $report->id)->delete();

        foreach ($recommendations as $rec) {
            SectionRecommendation::create([
                'section_diagnostic_report_id' => $report->id,
                'type' => $rec['type'],
                'priority' => $rec['priority'] ?? 'MEDIUM',
                'recommendation' => $rec['recommendation']
            ]);
        }
    }
}
