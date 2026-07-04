<?php

namespace App\Services\Diagnostic;

use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagReportIndicatorResult;
use App\Models\app\Instrument\DiagIndicator;

class DiagContrastService
{
    /**
     * Calculate and store the contrast (gap) for a given report.
     *
     * @param DiagReport $report
     * @return void
     */
    public function calculateContrast(DiagReport $report)
    {
        // 1. Retrieve all relevant indicators for the report's Referent/Pensum
        // This is a simplified fetch; real logic would filter by applied instrument questions.
        $indicators = $this->getRelevantIndicators($report);

        foreach ($indicators as $indicator) {
            // 2. Determine Observed Level (from Aggregation Service or direct calculation)
            // Placeholder: Assume we have a method to get observed level
            $observedLevel = $this->calculateObservedLevel($report, $indicator);

            // 3. Get Expected Level
            $expectedLevel = $indicator->expected_level; // Assuming stored as string or mapped int

            // 4. Calculate Gap
            $gapValue = $this->calculateGapValue($expectedLevel, $observedLevel);
            $gapLabel = $this->getGapLabel($gapValue);

            // 5. Store Result
            DiagReportIndicatorResult::updateOrCreate(
                [
                    'report_id' => $report->id,
                    'indicator_id' => $indicator->id,
                    'pensum_id' => $indicator->competency->pensum_id ?? 0, // Fallback
                ],
                [
                    'expected_level' => $expectedLevel,
                    'observed_level' => $observedLevel,
                    'gap_value' => $gapValue,
                    'gap_label' => $gapLabel,
                ]
            );
        }
    }

    protected function getRelevantIndicators(DiagReport $report)
    {
        // Placeholder
        return [];
    }

    protected function calculateObservedLevel(DiagReport $report, DiagIndicator $indicator)
    {
        // Placeholder: Logic to fetch answers linked to this indicator and aggregate
        return 'Satisfactory';
    }

    protected function calculateGapValue($expected, $observed)
    {
        // Map levels to integers if they are strings
        $levels = [
            'Insufficient' => 1,
            'Developing' => 2,
            'Satisfactory' => 3,
            'Outstanding' => 4
        ];

        $expVal = $levels[$expected] ?? 0;
        $obsVal = $levels[$observed] ?? 0;

        return $expVal - $obsVal;
    }

    protected function getGapLabel($gapValue)
    {
        if ($gapValue <= 0) return 'none'; // Met or exceeded
        if ($gapValue == 1) return 'low';
        if ($gapValue == 2) return 'medium';
        return 'high';
    }
}
