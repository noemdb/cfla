<?php

namespace App\Console\Commands\Diagnostic;

use App\Jobs\Diagnostic\GenerateSectionDiagnosticReportJob;
use App\Services\Diagnostic\Section\SectionDiagnosticAggregatorService;
use App\Services\Diagnostic\Section\SectionReportBuilder;
use Illuminate\Console\Command;

class GenerateSectionDiagnosticReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnostic:generate-section-report 
                            {section_id : ID of the section} 
                            {diagnostic_id : ID of the diagnostic cycle (e.g. LAP-2026-1)} 
                            {--queue : Whether to run the job in the background}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a pedagogical synthesis report for a specific section.';

    /**
     * Execute the console command.
     */
    public function handle(
        SectionDiagnosticAggregatorService $aggregator,
        SectionReportBuilder $builder
    ): int {
        $sectionId = $this->argument('section_id');
        $diagnosticId = $this->argument('diagnostic_id');

        if ($this->option('queue')) {
            GenerateSectionDiagnosticReportJob::dispatch($sectionId, $diagnosticId);
            $this->info("Job dispatched to queue for section ID {$sectionId}.");
            return self::SUCCESS;
        }

        $this->info("Generating section report for section ID {$sectionId}...");

        $data = $aggregator->aggregate($sectionId, $diagnosticId);

        if (empty($data)) {
            $this->error("No individual reports found for section {$sectionId} and diagnostic {$diagnosticId}.");
            return self::FAILURE;
        }

        $report = $builder->build($data);

        $this->info("Report successfully generated! ID: {$report->id}");
        $this->table(
            ['Field', 'Value'],
            [
                ['Report ID', $report->id],
                ['Section ID', $report->section_id],
                ['Students Count', $report->students_count],
                ['Avg Precision', $report->global_precision_avg . '%'],
                ['Status', $report->status],
            ]
        );

        return self::SUCCESS;
    }
}
