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
        $schedule->command('lms:publish-scheduled')->everyFiveMinutes();
        $schedule->command('lms:cleanup-media')->weekly();
    }

    protected $commands = [
        \App\Console\Commands\CleanupVotingSessions::class,
        \App\Console\Commands\PublishScheduledLmsContent::class,
        \App\Console\Commands\CleanupLmsMedia::class,
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
