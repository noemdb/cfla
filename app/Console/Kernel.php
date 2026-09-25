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

        // Vigila el backlog de la cola binnacle (mejora propuesta #1): si el
        // worker cae, los eventos info/warning se acumulan en `jobs`. La alerta
        // entra en la propia bitácora y notifica a admin/dirección. Sin overlap
        // para que la revisión anterior termine antes de la siguiente.
        $schedule->command('binnacle:watch')->everyFiveMinutes()->withoutOverlapping();

        // Resumen diario por email (mejora propuesta #5): envía a admin/dirección
        // un resumen del día anterior (critical/alert, accesos, top actores).
        // Tras el archivado de las 03:00, antes del inicio de la jornada.
        $schedule->command('binnacle:report')->dailyAt('05:30')->withoutOverlapping();

        // Ancla externa del hash-chain (mejora #6, Spec §8.3): publica el hash
        // de la última entrada critical/alert a un log append-only fuera de la
        // BD. Entre el archivado (03:00) y el reporte (05:30).
        $schedule->command('binnacle:anchor')->dailyAt('04:00')->withoutOverlapping();

        // Gate de particionado (mejora #8, Spec §9): evalúa semanalmente si el
        // crecimiento proyectado de binnacle_entries justifica particionar por
        // fecha. Solo alerta; el procedimiento está documentado.
        $schedule->command('binnacle:check-growth')->weeklyOn(1, '06:15')->withoutOverlapping();

        // Recordatorios diarios de planificación (06:00): aprobaciones de
        // actividades cuya fecha inicial cae en 3 días (Jefe de Área,
        // Coordinación y Planificación), lecciones programadas por Jefe de Área
        // y áreas sin actividad reciente. Cada regla se agrupa como un resumen
        // por usuario. Sin overlap para no solapar con el arranque de jornada.
        $schedule->command('planning:daily-reminders')->dailyAt('06:00')->withoutOverlapping();

        // Recordatorio semanal a profesores sin actividades: todos los lunes
        // a las 06:00 se notifica a los profesores (is_profesor) activos que no
        // tienen al menos una actividad en sus pevaluaciones del lapso vigente.
        $schedule->command('professors:notify-missing-activities')->weeklyOn(1, '06:00')->withoutOverlapping();

        // Higiene de la idempotencia de notificaciones: las reclamaciones
        // caducadas de `notification_dedupe` ya no bloquean nada (la clave
        // incluye el bucket temporal) y solo ocupan espacio.
        $schedule->command('notifications:prune-dedupe')->weeklyOn(0, '04:30')->withoutOverlapping();

        // Retención: la tabla `notifications` solo crece. Se podan las LEÍDAS
        // más antiguas que notifications.retention_days (180 por defecto); las
        // no leídas nunca, salvo que se pase --purge-unread-days a mano.
        $schedule->command('notifications:prune')->weeklyOn(0, '05:00')->withoutOverlapping();
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
