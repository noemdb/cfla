<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('voting-sessions:cleanup')->daily();
        $schedule->command('lms:cleanup-media')->weekly();

        // Mantiene legibles los diagramas SVG recién creados/editados.
        // Idempotente: solo persiste cuando hay cambios reales.
        $schedule->command('lms:normalize-svgs --dry-run')->hourly()->withoutOverlapping();

        // Bitácora de auditoría (Spec BINNACLE-001 §12): archiva las entradas
        // que superan la retención por categoría hacia binnacle_entries_archive.
        // De madrugada, sin solaparse con la ejecución anterior.
        $schedule->command('binnacle:archive')->dailyAt('03:00')->withoutOverlapping();
    }

    protected $commands = [
        \App\Console\Commands\CleanupVotingSessions::class,
        \App\Console\Commands\CleanupLmsMedia::class,
        \App\Console\Commands\NormalizeSvgContrast::class,
    ];

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
