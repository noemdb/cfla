<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\BinnacleBacklogNotification;
use App\Services\Binnacle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Vigila el backlog de la cola dedicada de la bitácora (mejora propuesta #1).
 *
 * Si el worker cfla-binnacle-queue cae, los eventos info/warning se acumulan
 * en la tabla `jobs` sin persistirse. Este comando (programado, ver
 * Console\Kernel) cuenta los jobs pendientes y, si superan el umbral
 * config/binnacle.php#backlog_threshold, emite una entrada warning
 * event_type=queue_backlog en la propia bitácora y notifica a los
 * administradores/dirección.
 *
 * Uso:
 *   php8.2 artisan binnacle:watch                  # alerta (default)
 *   php8.2 artisan binnacle:watch --check          # solo exit code (health check)
 *   php8.2 artisan binnacle:watch --threshold=50
 *   php8.2 artisan binnacle:watch --quiet
 */
class BinnacleWatch extends Command
{
    protected $signature = 'binnacle:watch
        {--threshold= : Umbral de jobs pendientes (reemplaza config/binnacle.php)}
        {--check : Modo health check: exit 0 = ok, 1 = backlog, sin alertas}';

    protected $description = 'Detecta backlog en la cola binnacle y notifica si supera el umbral';

    public function handle(): int
    {
        $queue = config('binnacle.queue', 'binnacle');
        $threshold = (int) ($this->option('threshold') ?? config('binnacle.backlog_threshold', 100));
        $pending = DB::table('jobs')->where('queue', $queue)->count();

        $this->line("Cola {$queue}: {$pending} jobs pendientes (umbral {$threshold}).");

        if ($pending <= $threshold) {
            return self::SUCCESS;
        }

        // Modo --check: exit code para health checks externos (supervisor/nagios).
        if ($this->option('check')) {
            $this->error("Backlog: {$pending} jobs pendientes exceden el umbral {$threshold}.");

            return self::FAILURE;
        }

        $this->error("Backlog detectado: {$pending} jobs pendientes (umbral {$threshold}).");

        if (! $this->option('quiet')) {
            $this->notify($pending, $threshold);
        }

        return self::FAILURE;
    }

    private function notify(int $pending, int $threshold): void
    {
        // Entrada en la propia bitácora (auditable): quién vigila, queda constancia.
        Binnacle::log('queue_backlog', [
            'title' => 'Backlog en cola de bitácora',
            'description' => "{$pending} jobs pendientes superan el umbral de {$threshold}.",
            'category' => 'system',
            'severity' => 'warning',
            'metadata' => ['pending' => $pending, 'threshold' => $threshold],
        ]);

        $recipients = config('binnacle.alert_recipients', []);

        if (! empty($recipients)) {
            Notification::route('mail', $recipients)
                ->notify(new BinnacleBacklogNotification($pending, $threshold));
        }

        // Fallback: notificar a todos los usuarios con rol admin o dirección.
        $users = User::query()
            ->where(fn ($q) => $q->where('is_admin', true)->orWhere('is_director', true))
            ->where('is_active', 'enable')
            ->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new BinnacleBacklogNotification($pending, $threshold));
        }
    }
}
