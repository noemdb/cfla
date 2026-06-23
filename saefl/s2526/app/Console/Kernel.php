<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\AssignStudentTokens::class,
        Commands\SendPulseSyncEmails::class,
        Commands\ImportSendPulseEmailHistory::class,
        Commands\RetryFailedJobs::class,
        Commands\BackupAndTruncateTable::class,
        Commands\MigrarNotasPeriodo2425Command::class,
        Commands\AnalyzeTeachingWords::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        require __DIR__ . '/include/set_exchange_rate_goutte.php';

        require __DIR__ . '/include/coll_political_calendar.php';
        //require(__DIR__ . '/include/congratulations.php');

        // Respaldos locales a la base de datos
        require __DIR__ . '/include/backup.php';

        // require (__DIR__ . '/include/set_exchange_rate.php');
        // require (__DIR__ . '/include/set_exchange_rate_goutte.php');
        // require(__DIR__ . '/include/catchments.php');

        // Envio de informe de notas por email
        //require __DIR__ . '/include/boletins.php';

        //eliminación de notas duplicadas
        require __DIR__ . '/include/pevaluacions.php';

        //$schedule->command('facebook:refresh-token')->daily();

        //Limpieza Automática app/temp/*
        $schedule->command('clean:tempfiles')->everyFiveMinutes();

        // Actualizar estados de emails cada 5 minutos (resend)
        // $schedule->command('resend:update-status')
        // ->everyFiveMinutes()
        // ->withoutOverlapping();

        // Actualizar estados de emails cada 5 minutos (sendpulse)
        // $schedule->command('sendpulse:update-status')
        // ->everyFiveMinutes()
        // ->withoutOverlapping();

        // $schedule->command('sendpulse:sync-emails --days=1')->hourly();

        // Ejecutar a las 1:00 AM todos los días
        // $schedule->command('retry:failed-jobs --delay-to=6')
        //      ->dailyAt('01:00')
        //      ->timezone('America/Caracas'); // Ajusta según tu zona horaria

        //automatizar la generacion de recargos
        //$schedule->job(new VerificarYGenerarRecargosMorosidad())->dailyAt('01:00');

        // Verificar token de Facebook todos los días a las 22:00
        /*
        $schedule->command('facebook:check-expiration')
            ->dailyAt('22:00')
            ->timezone('America/Caracas') // Ajusta según tu zona horaria
            ->onSuccess(function () {
                Log::info('Verificación de token Facebook completada exitosamente');
            })
            ->onFailure(function () {
                Log::error('Falló la verificación automática del token Facebook');
            });
        */

        // También puedes ejecutarlo cada hora para mayor seguridad
        // $schedule->command('facebook:check-expiration')
        //     ->hourly()
        //     ->unlessBetween('22:00', '06:00'); // No ejecutar en la noche

        // ── Envío masivo de cartas de matrícula 2025-2026 ─────────────
        // EJECUCIÓN ÚNICA: 2026-05-12 a las 09:00 (America/Caracas)
        // Una vez enviadas, estos bloques pueden comentarse o eliminarse.
        $schedule->command('interview:test-email 0 - --type=send-accepted --no-interaction')
            ->dailyAt('09:00')
            ->timezone('America/Caracas')
            ->when(fn () => now()->timezone('America/Caracas')->format('Y-m-d') === '2026-05-12')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/interview-send-accepted.log'));

        $schedule->command('interview:test-email 0 - --type=send-standby --no-interaction')
            ->dailyAt('09:00')
            ->timezone('America/Caracas')
            ->when(fn () => now()->timezone('America/Caracas')->format('Y-m-d') === '2026-05-12')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/interview-send-standby.log'));
        // ─────────────────────────────────────────────────────────────

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
