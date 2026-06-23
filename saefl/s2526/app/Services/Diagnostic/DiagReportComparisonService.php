<?php

namespace App\Services\Diagnostic;

use App\Models\app\Instrument\DiagReport;
use App\Models\app\Estudiant;
use Illuminate\Support\Collection;

class DiagReportComparisonService
{
    /**
     * Compare reports for a student across different lapsos.
     *
     * @param int $estudiantId
     * @param int $diagMainId
     * @param array $lapsoIds
     * @return array
     */
    public function compareAcrossLapsos(int $estudiantId, int $diagMainId, array $lapsoIds): array
    {
        $reports = DiagReport::with(['results', 'pensumResults', 'indicatorResults'])
            ->where('estudiant_id', $estudiantId)
            ->where('diag_main_id', $diagMainId)
            ->whereIn('lapso_id', $lapsoIds)
            ->orderBy('lapso_id')
            ->get();

        if ($reports->count() < 2) {
            return [
                'success' => false,
                'message' => 'Need at least 2 reports to compare',
            ];
        }

        return [
            'success' => true,
            'reports' => $reports,
            'comparison' => $this->calculateComparison($reports),
        ];
    }

    /**
     * Calculate comparison metrics between reports.
     */
    protected function calculateComparison(Collection $reports): array
    {
        $comparison = [
            'global_precision' => [],
            'pensum_precision' => [],
            'indicator_gaps' => [],
            'improvement_summary' => null,
        ];

        // Global precision comparison
        foreach ($reports as $report) {
            if ($report->results) {
                $comparison['global_precision'][] = [
                    'lapso_id' => $report->lapso_id,
                    'precision' => $report->results->precision,
                    'total_questions' => $report->results->total_answered_questions,
                ];
            }
        }

        // Calculate improvement
        if (count($comparison['global_precision']) >= 2) {
            $first = $comparison['global_precision'][0]['precision'];
            $last = end($comparison['global_precision'])['precision'];
            $improvement = $last - $first;

            $comparison['improvement_summary'] = [
                'initial_precision' => $first,
                'final_precision' => $last,
                'improvement' => $improvement,
                'improvement_percentage' => $first > 0 ? ($improvement / $first) * 100 : 0,
                'trend' => $improvement > 0 ? 'improving' : ($improvement < 0 ? 'declining' : 'stable'),
            ];
        }

        return $comparison;
    }

    /**
     * Get improvement recommendations based on comparison.
     */
    public function getImprovementRecommendations(int $estudiantId, int $diagMainId, array $lapsoIds): array
    {
        $comparison = $this->compareAcrossLapsos($estudiantId, $diagMainId, $lapsoIds);

        if (!$comparison['success']) {
            return [];
        }

        $recommendations = [];
        $summary = $comparison['comparison']['improvement_summary'];

        if ($summary && $summary['trend'] === 'declining') {
            $recommendations[] = [
                'priority' => 'high',
                'type' => 'transversal',
                'recommendation' => 'Se observa una tendencia descendente en el rendimiento general. Se recomienda intervención inmediata.',
            ];
        } elseif ($summary && $summary['trend'] === 'improving') {
            $recommendations[] = [
                'priority' => 'low',
                'type' => 'transversal',
                'recommendation' => 'Se observa mejora continua. Mantener las estrategias actuales.',
            ];
        }

        return $recommendations;
    }
}
