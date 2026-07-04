<?php

namespace App\Console\Commands\Diagnostic;

use Illuminate\Console\Command;
use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagResult;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagReportPensum;
use Illuminate\Support\Facades\DB;

class SyncPrecisionCommand extends Command
{
    /**
     * The name and signature of the console command.
     * php artisan diagnostic:sync-precision
     *
     * @var string
     */
    protected $signature = 'diagnostic:sync-precision {--report= : Specific report ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize precision and total answered questions in diag_results table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reportId = $this->option('report');

        $query = DiagReport::query();

        if ($reportId) {
            $query->where('id', $reportId);
        }

        $reports = $query->get();
        $total = $reports->count();

        if ($total === 0) {
            $this->info('No reports found to sync.');
            return 0;
        }

        $this->info("Starting sync for {$total} reports...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($reports as $report) {
            try {
                // 1. Sync GLOBAL precision (DiagResult)
                $globalStats = DiagAnswer::getStudentPrecisionStats($report->estudiant_id);
                DiagResult::updateOrCreate(
                    ['report_id' => $report->id],
                    [
                        'total_answered_questions' => $globalStats['total_answered'],
                        'precision' => $globalStats['precision'],
                    ]
                );

                // 2. Sync PER-AREA precision (DiagReportPensum)
                // We get the pensums associated with the student's answers for this diagnostic session
                $pensumIds = DiagAnswer::where('estudiant_id', $report->estudiant_id)
                    ->whereHas('question', function ($q) use ($report) {
                        $q->where('activo', 1);
                    })
                    ->get()
                    ->pluck('question.pensum_id')
                    ->unique()
                    ->filter();

                foreach ($pensumIds as $pensumId) {
                    $areaStats = DiagAnswer::getStudentPrecisionStats($report->estudiant_id, $pensumId);

                    DiagReportPensum::updateOrCreate(
                        [
                            'report_id' => $report->id,
                            'pensum_id' => $pensumId
                        ],
                        [
                            'total_answered_questions' => $areaStats['total_answered'],
                            'precision' => $areaStats['precision'],
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->error("\nError syncing report {$report->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nSync completed successfully.");

        return 0;
    }
}
