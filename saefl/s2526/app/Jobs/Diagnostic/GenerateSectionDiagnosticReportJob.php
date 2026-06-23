<?php

namespace App\Jobs\Diagnostic;

use App\Services\Diagnostic\Section\SectionDiagnosticAggregatorService;
use App\Services\Diagnostic\Section\SectionReportBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateSectionDiagnosticReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $sectionId;
    protected string $diagnosticId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $sectionId, string $diagnosticId)
    {
        $this->sectionId = $sectionId;
        $this->diagnosticId = $diagnosticId;
    }

    /**
     * Execute the job.
     */
    public function handle(
        SectionDiagnosticAggregatorService $aggregator,
        SectionReportBuilder $builder
    ): void {
        try {
            Log::info("Starting section report generation", [
                'section_id' => $this->sectionId,
                'diagnostic_id' => $this->diagnosticId
            ]);

            // 1. Aggregate data from individual reports
            $data = $aggregator->aggregate($this->sectionId, $this->diagnosticId);

            if (empty($data)) {
                Log::warning("No data found to aggregate for section report", [
                    'section_id' => $this->sectionId,
                    'diagnostic_id' => $this->diagnosticId
                ]);
                return;
            }

            // 2. Build and persist the section report
            $report = $builder->build($data);

            Log::info("Section report generated successfully", [
                'report_id' => $report->id,
                'section_id' => $this->sectionId
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to generate section report", [
                'section_id' => $this->sectionId,
                'diagnostic_id' => $this->diagnosticId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
