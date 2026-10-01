<?php

namespace App\Console\Commands;

use App\Livewire\Admin\Logs\Services\LogParser;
use App\Models\User;
use App\Notifications\AdminLogErrorNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Vigila el log de Laravel y avisa al superadmin de los errores nuevos.
 *
 * Es la fuente de las ÚNICAS notificaciones que recibe el superadmin
 * (`userId=1`): `NotificationService` filtra todo lo demás aunque tenga otros
 * roles. Solo nivel ERROR (ver `--level`).
 *
 * Estado: guarda el offset (byte) por archivo en caché. La primera ejecución
 * arranca desde el final actual para no inundar con el histórico: los errores
 * viejos ya están visibles en `/admin/logs`. `--reset` vuelve a ese punto de
 * partida; `--since` reprocesa desde una fecha (para ponerse al día) y
 * `--from-start` desde el inicio (cuidado en logs grandes).
 *
 * Anti-spam por partida doble: el anti-spam por no leídas (`log_hash`,
 * ventana 24h) y la idempotencia a nivel de BD (misma huella): un reintento
 * del comando no duplica avisos.
 */
class NotifyAdminLogErrors extends Command
{
    protected $signature = 'admin:notify-log-errors
        {--file= : Archivo de log relativo a storage/logs (default: el del canal activo)}
        {--level=ERROR : Nivel a vigilar}
        {--limit=50 : Máximo de avisos por ejecución}
        {--max-bytes=5242880 : Máximo de bytes nuevos a procesar por pasada (5 MB)}
        {--dry-run : Mostrar lo que se avisaría sin notificar ni avanzar el offset}
        {--since= : Reprocesar desde esta fecha (YYYY-MM-DD HH:MM:SS), ignorando el offset}
        {--from-start : Procesar desde el inicio del archivo (peligroso en logs grandes)}
        {--reset : Olvidar el offset guardado y arrancar desde el final actual}';

    protected $description = 'Avisa al superadmin de los errores nuevos del log de Laravel';

    public function handle(): int
    {
        $parser = app(LogParser::class);
        $path = $this->resolvePath($parser);

        if (! is_file($path)) {
            $this->warn("No existe el archivo de log: {$path}");

            return self::FAILURE;
        }

        $admin = User::find((int) config('notifications.superadmin_id', 1));

        if (! $admin || ($admin->is_active ?? 'enable') !== 'enable') {
            $this->warn('Superadmin no encontrado o inactivo: no hay a quién avisar.');

            return self::FAILURE;
        }

        $level = strtoupper((string) $this->option('level'));
        $limit = max(1, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');
        $stateKey = 'admin_log_errors.offset.'.md5($path);
        $size = (int) @filesize($path);

        if ($this->option('reset')) {
            Cache::forget($stateKey);
            $this->line('  Offset olvidado.');
        }

        $state = Cache::get($stateKey);
        $offset = $this->startingOffset($state, $size);

        if ($this->option('since')) {
            $offset = 0;
        }

        $chunk = $this->readNewBytes($path, $offset, (int) $this->option('max-bytes'));

        if ($chunk === '') {
            $this->line('  Sin bytes nuevos desde el offset '.number_format($offset).'. Nada que avisar.');
            $this->rememberOffset($stateKey, $path, $size, $dryRun);

            return self::SUCCESS;
        }

        $entries = $parser->parseContent($chunk, $level);

        if ($this->option('since')) {
            $since = (string) $this->option('since');
            $entries = array_values(array_filter($entries, fn ($e) => ($e['date'] ?? '') >= $since));
        }

        $entries = array_slice($entries, 0, $limit);

        $this->line(sprintf(
            '  %s: %d entrada(s) de nivel %s en %d bytes nuevos%s',
            basename($path),
            count($entries),
            $level,
            strlen($chunk),
            $dryRun ? ' [dry-run]' : ''
        ));

        $sent = 0;

        foreach ($entries as $entry) {
            $sent += $this->notify($admin, $path, $entry, $dryRun);
        }

        $this->rememberOffset($stateKey, $path, $size, $dryRun);

        $this->info("  Avisos emitidos: {$sent} de ".count($entries).'.');

        return self::SUCCESS;
    }

    /**
     * Offset de partida: el guardado, salvo que el archivo haya rotado
     * (encogido) o la primera ejecución pida empezar desde el final.
     */
    private function startingOffset($state, int $size): int
    {
        if ($this->option('from-start') || $this->option('reset')) {
            return 0;
        }

        if (! is_array($state) || ! isset($state['offset'])) {
            // Primera ejecución: arrancar desde el final para no inundar con
            // el histórico (ya visible en /admin/logs).
            $this->line('  Primera ejecución: offset inicial al final del archivo (sin avisos históricos).');

            return $size;
        }

        $offset = (int) $state['offset'];

        // El archivo se truncó o rotó: empezar desde el inicio del nuevo.
        if ($offset > $size) {
            return 0;
        }

        return max(0, $offset);
    }

    private function rememberOffset(string $stateKey, string $path, int $size, bool $dryRun): void
    {
        if ($dryRun) {
            return;
        }

        $realSize = (int) @filesize($path);
        Cache::forever($stateKey, ['offset' => $realSize > 0 ? $realSize : $size, 'at' => now()->toDateTimeString()]);
    }

    private function readNewBytes(string $path, int $offset, int $maxBytes): string
    {
        $handle = @fopen($path, 'rb');

        if (! $handle) {
            return '';
        }

        fseek($handle, $offset);
        $chunk = (string) stream_get_contents($handle, $maxBytes);
        fclose($handle);

        return $chunk;
    }

    /**
     * Una notificación por entrada de error. La huella de idempotencia es el
     * hash estable del parser: un reintento del comando no duplica el aviso.
     */
    private function notify(User $admin, string $path, array $entry, bool $dryRun): int
    {
        $message = trim((string) ($entry['message'] ?? ''));

        if ($message === '') {
            return 0;
        }

        $context = trim((string) ($entry['context'] ?? ''));

        $this->line('  ['.($entry['date'] ?? '?').'] '.mb_substr($message, 0, 120));

        if ($dryRun) {
            return 0;
        }

        $service = app(NotificationService::class);

        $subject = ['log_hash' => (string) ($entry['hash'] ?? '')];

        $users = $service->filterByDedupe([$admin], AdminLogErrorNotification::TYPE, $subject);

        if ($users->isEmpty()) {
            return 0;
        }

        return $service->notifyUsers(
            $users,
            new AdminLogErrorNotification(
                type: AdminLogErrorNotification::TYPE,
                message: '['.$entry['level'].'] '.basename($path).' — '.mb_substr($message, 0, 200),
                url: route('admin.logs'),
                logFile: basename($path),
                logDate: (string) ($entry['date'] ?? ''),
                logEnv: $entry['env'] !== '' ? (string) $entry['env'] : null,
                logContext: $context !== '' ? mb_substr($context, 0, 500) : null,
                logHash: (string) ($entry['hash'] ?? ''),
                action: 'registró un error',
            ),
            $subject
        );
    }

    /**
     * Archivo a vigilar: el del canal de log activo (`single` → laravel.log,
     * `daily` → laravel-YYYY-MM-DD.log) o el indicado con `--file`.
     */
    private function resolvePath(LogParser $parser): string
    {
        if ($this->option('file')) {
            return $parser->resolvePath('', (string) $this->option('file'));
        }

        $default = (string) config('logging.default', 'single');

        if ($default === 'daily' || str_starts_with($default, 'daily')) {
            $file = 'laravel-'.now()->toDateString().'.log';
        } else {
            $channel = config('logging.channels.'.$default.'.path')
                ?? storage_path('logs/laravel.log');
            $file = basename((string) $channel);
        }

        return $parser->resolvePath('', $file);
    }
}
