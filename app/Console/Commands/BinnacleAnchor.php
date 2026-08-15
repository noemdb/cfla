<?php

namespace App\Console\Commands;

use App\Models\BinnacleEntry;
use App\Models\User;
use App\Notifications\BinnacleAnchorNotification;
use App\Services\Binnacle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

/**
 * Publica el hash de la última entrada critical/alert a un log ancla
 * append-only fuera de la BD y del control del DBA (Spec §8.3 / mejora #6).
 *
 * El hash-chain (ADR-003) protege contra manipulación sin acceso a MariaDB,
 * pero no contra un DBA que pueda recalcular la cadena. Este comando deja una
 * traza inmutable (archivo append-only, o email firmado con --notify) del hash
 * de la punta de la cadena; cualquier retroceso de la cadena por debajo de esa
 * ancla queda detectable con verifyAnchorIntegrity().
 *
 * Uso:
 *   php8.2 artisan binnacle:anchor                  # publica ancla en el archivo
 *   php8.2 artisan binnacle:anchor --path=/tmp/ancla.log
 *   php8.2 artisan binnacle:anchor --notify         # además envía email a admin/dirección
 *   php8.2 artisan binnacle:anchor --check          # verifica la última ancla (exit code)
 */
class BinnacleAnchor extends Command
{
    protected $signature = 'binnacle:anchor
        {--path= : Ruta del archivo ancla (reemplaza config/binnacle.php#anchor_path)}
        {--notify : Envía email de confirmación a admin/dirección}
        {--check : Modo verificación: exit 0 = ancla íntegra, 1 = rota/manipulada}';

    protected $description = 'Publica el hash de la última entrada critical/alert en un log ancla append-only';

    public function handle(): int
    {
        $path = $this->option('path') ?: config('binnacle.anchor_path');

        if ($this->option('check')) {
            return $this->check($path);
        }

        $last = BinnacleEntry::whereIn('event_severity', ['critical', 'alert'])
            ->orderByDesc('id')
            ->first();

        if (! $last || ! $last->entry_hash) {
            $this->warn('No hay entradas critical/alert con hash para anclar (¿worker caído?). No se escribió nada.');

            return self::SUCCESS;
        }

        $line = now()->toIso8601String().'|'.$last->id.'|'.$last->event_type.'|'.$last->entry_hash.PHP_EOL;

        $dir = dirname($path);
        if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
            $this->error("No se pudo crear el directorio del ancla: {$dir}");

            return self::FAILURE;
        }

        if (file_put_contents($path, $line, FILE_APPEND | LOCK_EX) === false) {
            $this->error("No se pudo escribir el ancla en {$path}.");

            return self::FAILURE;
        }

        $this->info("Ancla publicada (#{$last->id}): {$last->entry_hash}");
        $this->line("  Archivo: {$path}");
        $this->line('  Cadena  : tip '.$last->entry_hash.' (previous '.($last->previous_hash ?? 'genesis').')');

        // Meta-auditoría: el anclaje queda registrado en la propia bitácora.
        Binnacle::log('binnacle_anchor_sent', [
            'title' => 'Ancla externa del hash-chain publicada',
            'description' => "Ancla #{$last->id} ({$last->event_type}) publicada en {$path}.",
            'category' => 'system',
            'severity' => 'info',
            'metadata' => [
                'entry_id' => $last->id,
                'event_type' => $last->event_type,
                'entry_hash' => $last->entry_hash,
                'path' => $path,
            ],
        ]);

        if ($this->option('notify')) {
            $this->notify($last, $path);
        }

        return self::SUCCESS;
    }

    private function check(string $path): int
    {
        $anchor = Binnacle::verifyAnchorIntegrity($path);

        if (! $anchor['anchored']) {
            $this->warn("Ancla: {$anchor['reason']}.");

            return self::SUCCESS;
        }

        if (! $anchor['valid']) {
            $this->error("Ancla rota: {$anchor['reason']}.");

            return self::FAILURE;
        }

        $this->info("Ancla íntegra: {$anchor['reason']}.");
        $this->line('  Última ancla: '.json_encode($anchor['last_anchor']));

        return self::SUCCESS;
    }

    private function notify(BinnacleEntry $last, string $path): void
    {
        $recipients = config('binnacle.alert_recipients', []);

        if (! empty($recipients)) {
            Notification::route('mail', $recipients)
                ->notify(new BinnacleAnchorNotification($last, $path));
        }

        $users = User::query()
            ->where(fn ($q) => $q->where('is_admin', true)->orWhere('is_director', true))
            ->where('is_active', 'enable')
            ->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new BinnacleAnchorNotification($last, $path));
        }
    }
}
