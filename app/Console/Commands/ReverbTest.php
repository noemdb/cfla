<?php

namespace App\Console\Commands;

use App\Events\ReverbTestEvent;
use App\Models\User;
use App\Notifications\ReverbTestNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Diagnóstico de Laravel Reverb.
 *
 * 1. Reporta la configuración efectiva (conexión, host/puerto públicos e internos).
 * 2. Abre un WebSocket (cliente mínimo) al server interno y valida el handshake 101.
 * 3. Se suscribe al canal público `reverb.test`, emite un evento desde el backend
 *    y espera recibir el frame para confirmar la entrega end-to-end.
 * 4. (Opcional, --proxy) repite el handshake a través del proxy público
 *    (REVERB_HOST:REVERB_PORT) para validar el túnel Apache/nginx.
 *
 * Requiere que Reverb esté levantado (Supervisor) y que el `.env` tenga
 * REVERB_* coherentes. Usar con /usr/bin/php8.2.
 *
 * ── Casos de uso ─────────────────────────────────────────────────────────────
 * 1) Smoke test rápido tras desplegar o reiniciar.
 *    `php8.2 artisan reverb:test`
 *    Confirma en un comando que el server arrancó, que el backend puede alcanzarlo
 *    y que un evento realmente llega al cliente. Ideal para CI / post-deploy.
 *
 * 2) Validar el túnel del proxy reverso (Apache/nginx) — el mismo escenario de
 *    producción. `php8.2 artisan reverb:test --proxy`
 *    Verifica el handshake por el host/puerto PÚBLICOS (REVERB_HOST:REVERB_PORT),
 *    es decir, que /app se enruta bien y que Reverb no devuelve 404 por falta de
 *    prefijo. Es la prueba que más se acerca a lo que ve el navegador.
 *
 * 3) Diagnóstico de un dominio que no resuelve desde la shell.
 *    `php8.2 artisan reverb:test --proxy --proxy-host=127.0.0.1`
 *    Útil en local (p. ej. cfla.local sin entrada en /etc/hosts) o detrás de un
 *    NAT: apunta el socket a una IP pero conserva el `Host:` del vhost para que
 *    Apache enrute al sitio correcto.
 *
 * 4) Reproducir un evento a mano desde el backend.
 *    `event(new App\Events\ReverbTestEvent('mensaje'))` (tinker o un evento real)
 *    Mientras se observa la consola del navegador suscrita a `reverb.test`, sirve
 *    para confirmar que la entrega llega al cliente sin tocar el dominio.
 *
 * 5) Verificación con un canal de negocio real.
 *    Reemplazar el canal por el de una competencia/scoreboard y disparar el evento
 *    real (p. ej. `ScoreboardUpdated`) para validar que el WebSocket alimenta la
 *    vista en vivo que usa `data-reverb="enabled"`.
 *
 * 6) Probar la campana de notificaciones del navbar.
 *    `php8.2 artisan reverb:test --notify=<username>`
 *    Envía una notificación de base de datos a un usuario (por username o email)
 *    vía NotificationService::notifyUsers(): persiste la fila en `notifications`
 *    y emite el broadcast `NotificationReceived`. La campana
 *    (`app.notifications.notification-bell`) la muestra al instante (Reverb) o en
 *    ≤30s vía `wire:poll.30s`. Útil para validar el flujo completo de notificación
 *    → WebSocket → UI sin tocar el dominio.
 *
 * ──────────────────────────────────────────────────────────────────────────────
 */
class ReverbTest extends Command
{
    protected $signature = 'reverb:test
        {--channel=reverb.test : Canal público de prueba}
        {--proxy : Probar también el handshake a través del proxy público}
        {--proxy-host= : IP/host a conectar para el túnel (si el dominio público no resuelve desde CLI)}
        {--proxy-port= : Puerto del proxy a conectar (default: REVERB_PORT)}
        {--notify= : Username a notificar — envía una notificación a la campana del navbar}
        {--timeout=5 : Segundos a esperar el evento tras emitirlo}';

    protected $description = 'Verifica el funcionamiento de Laravel Reverb (config, handshake y entrega end-to-end)';

    public function handle(): int
    {
        $failures = 0;

        $failures += $this->stepConfig() ? 0 : 1;
        $failures += $this->stepHandshakeInternal() ? 0 : 1;

        if ($this->option('proxy')) {
            $failures += $this->stepHandshakeProxy() ? 0 : 1;
        }

        $failures += $this->stepEndToEnd() ? 0 : 1;

        if ($this->option('notify')) {
            $failures += $this->stepNotify() ? 0 : 1;
        }

        $this->newLine();
        if ($failures === 0) {
            $this->info('✅ Reverb operativo en todos los pasos verificados.');

            return self::SUCCESS;
        }

        $this->error("❌ {$failures} paso(s) con fallo. Revisar §10 de context/reverb/PRODUCCION.md.");

        return self::FAILURE;
    }

    private function stepConfig(): bool
    {
        $this->newLine();
        $this->info('1) Configuración');

        $default = config('broadcasting.default');
        $conn = config('broadcasting.connections.'.$default);

        $publicHost = config('reverb.apps.apps.0.options.host');
        $publicPort = config('reverb.apps.apps.0.options.port');
        $publicScheme = config('reverb.apps.apps.0.options.scheme');
        $internalHost = config('reverb.servers.reverb.host');
        $internalPort = config('reverb.servers.reverb.port');

        $rows = [
            ['broadcasting.default', var_export($default, true)],
            ['driver', $conn['driver'] ?? '?'],
            ['APP_ID', config('reverb.apps.apps.0.app_id')],
            ['APP_KEY', config('reverb.apps.apps.0.key')],
            ['host público', $publicHost],
            ['puerto público', $publicPort],
            ['scheme público', $publicScheme],
            ['host interno', $internalHost],
            ['puerto interno', $internalPort],
        ];
        $this->table(['Clave', 'Valor'], $rows);

        $ok = ($default === 'reverb') && $internalHost && $internalPort;
        if (!$ok) {
            $this->warn('La conexión por defecto no es "reverb" o faltan host/puerto internos.');

            return false;
        }

        return true;
    }

    private function stepHandshakeInternal(): bool
    {
        return $this->handshake(
            '2) Handshake WebSocket (server interno)',
            config('reverb.servers.reverb.host'),
            (int) config('reverb.servers.reverb.port'),
            'http',
            config('reverb.servers.reverb.host')
        );
    }

    private function stepHandshakeProxy(): bool
    {
        $publicHost = (string) config('reverb.apps.apps.0.options.host');
        $publicPort = (int) config('reverb.apps.apps.0.options.port');
        // Destino del túnel: por defecto el host público; si no resuelve desde
        // CLI, se puede fijar con --proxy-host (p. ej. 127.0.0.1) manteniendo
        // el Host: <dominio> para que Apache enrute por el vhost correcto.
        $targetHost = $this->option('proxy-host') ?: $publicHost;
        $targetPort = $this->option('proxy-port') !== null
            ? (int) $this->option('proxy-port')
            : $publicPort;

        // Auto-fallback: si el host público no resuelve y no se indicó un destino
        // explícito, reintenta contra 127.0.0.1 conservando el Host: del vhost.
        // Así `--proxy` a secas funciona en local (cfla.local sin /etc/hosts).
        if (!$this->option('proxy-host') && !$this->resolves($publicHost)) {
            $this->warn("  {$publicHost} no resuelve desde CLI; usando fallback a 127.0.0.1 (Host: {$publicHost}).");
            $targetHost = '127.0.0.1';
        }

        return $this->handshake(
            '3) Handshake WebSocket (a través del proxy público)',
            $targetHost,
            $targetPort,
            (string) config('reverb.apps.apps.0.options.scheme'),
            $publicHost
        );
    }

    /** Verifica si un hostname resuelve (o es una IP literal). */
    private function resolves(string $host): bool
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return true;
        }
        $records = @dns_get_record($host, DNS_A | DNS_AAAA);

        return !empty($records);
    }

    private function handshake(string $title, string $host, int $port, string $scheme, ?string $hostHeader = null): bool
    {
        $this->newLine();
        $this->info($title);

        $key = config('reverb.apps.apps.0.key');
        if (!$key) {
            $this->warn('REVERB_APP_KEY vacío.');

            return false;
        }

        $socket = $this->connect($host, $port, $scheme, $key, $hostHeader ?? $host, $httpResponse);
        if (!$socket) {
            return false;
        }

        $expected = $this->expectedAccept($this->lastSecKey);
        if (!str_contains($httpResponse, '101 Switching Protocols')) {
            $this->warn("Sin 101 Switching Protocols. Respuesta:\n{$httpResponse}");
            fclose($socket);

            return false;
        }

        if (!str_contains($httpResponse, 'Sec-WebSocket-Accept: '.$expected)) {
            $this->warn('Sec-WebSocket-Accept no coincide (handshake no válido).');
            fclose($socket);

            return false;
        }

        $this->info("  101 Switching Protocols OK — server responde en {$scheme}://{$host}:{$port}");
        fclose($socket);

        return true;
    }

    private function stepEndToEnd(): bool
    {
        $this->newLine();
        $this->info('4) Entrega end-to-end (suscribirse → emitir → recibir)');

        $host = config('reverb.servers.reverb.host');
        $port = (int) config('reverb.servers.reverb.port');
        $key = config('reverb.apps.apps.0.key');
        $channel = $this->option('channel');
        $timeout = max(1, (int) $this->option('timeout'));

        if (!$key) {
            $this->warn('REVERB_APP_KEY vacío.');

            return false;
        }

        $socket = $this->connect($host, $port, 'http', $key, $host, $httpResponse);
        if (!$socket) {
            return false;
        }
        if (!str_contains($httpResponse, '101 Switching Protocols')) {
            $this->warn("Handshake falló: {$httpResponse}");
            fclose($socket);

            return false;
        }

        $this->sendFrame($socket, '{"event":"pusher:subscribe","data":{"channel":"'.$channel.'"}}');

        $event = new ReverbTestEvent('reverb:test ping');
        $this->line('  Emitiendo evento en canal "'.$channel.'" …');
        try {
            event($event);
            $this->line('  event() emitido sin error.');
        } catch (\Throwable $e) {
            $this->warn('  event() lanzó: '.$e->getMessage());
        }

        $deadline = microtime(true) + $timeout;
        $received = '';
        while (microtime(true) < $deadline) {
            if ($frame = $this->readFrame($socket, $deadline - microtime(true))) {
                $this->line('  [frame] '.$frame);
                // El mensaje real lleva "event":"<canal>"; el ACK de suscripción
                // tiene event=pusher_internal:subscription_succeeded (no confundir).
                if (str_contains($frame, '"event":"'.$channel.'"')) {
                    $received = $frame;
                    break;
                }
            }
        }

        fclose($socket);

        if ($received === '') {
            $this->warn("No se recibió el evento en {$timeout}s. Verificar que Reverb esté activo y el canal sea público.");

            return false;
        }

        $this->info('  Evento recibido: '.$received);

        return true;
    }

    /**
     * Paso opcional (--notify): envía una notificación de base de datos a un
     * usuario para que aparezca en la campana del navbar (`notification-bell`).
     *
     * Usa NotificationService::notifyUsers() — persiste la fila en `notifications`
     * y emite el broadcast `NotificationReceived` por destinatario — de modo que
     * el dropdown se actualiza en tiempo real (o vía wire:poll.30s si Reverb cae).
     */
    private function stepNotify(): bool
    {
        $this->newLine();
        $this->info('5) Notificación a la campana del navbar');

        $username = (string) $this->option('notify');
        $user = User::query()->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$user) {
            $this->warn("Usuario '{$username}' no encontrado (se buscó por username y email).");

            return false;
        }

        $message = 'Reverb: notificación de prueba para '.$user->username.' ('.now()->toDateTimeString().')';
        $this->line('  Destinatario: #'.$user->id.' '.$user->username.' <'.$user->email.'>');

        try {
            app(NotificationService::class)->notifyUsers([$user], new ReverbTestNotification($message));
        } catch (\Throwable $e) {
            $this->warn('  NotificationService lanzó: '.$e->getMessage());

            return false;
        }

        $this->info('  Notificación enviada. Debería aparecer en la campana del navbar de "'.$user->username.'".');
        $this->line('  Si Reverb está activo, el broadcast la muestra al instante; si no, wire:poll.30s la trae en ≤30s.');

        return true;
    }

    /**
     * Conecta y ejecuta el handshake WebSocket (GET /app/{key}). Devuelve el socket
     * o false, y llena $response con el status/headers recibidos.
     *
     * @param  string  $hostHeader  Valor del header Host (vhost que enruta Apache)
     * @return resource|false
     */
    private function connect(string $host, int $port, string $scheme, string $key, string $hostHeader, ?string &$response)
    {
        $path = '/app/'.rawurlencode($key).'?protocol=7&client=js&version=8.5.0&flash=false';
        $transport = ($scheme === 'https' ? 'ssl' : 'tcp').'://'.$host.':'.$port;

        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client($transport, $errno, $errstr, 5);
        if (!$socket) {
            $this->warn("No se pudo conectar a {$transport}: {$errstr} (errno {$errno}).");

            return false;
        }
        stream_set_timeout($socket, 5);

        $this->lastSecKey = base64_encode(random_bytes(16));
        $headers = [
            "GET {$path} HTTP/1.1",
            'Host: '.$hostHeader.':'.$port,
            'Upgrade: websocket',
            'Connection: Upgrade',
            'Sec-WebSocket-Key: '.$this->lastSecKey,
            'Sec-WebSocket-Version: 13',
            'Sec-WebSocket-Protocol: ',
            '',
            '',
        ];

        fwrite($socket, implode("\r\n", $headers));

        $response = '';
        while (!feof($socket) && !str_contains($response, "\r\n\r\n")) {
            $response .= fread($socket, 1024);
        }

        return $socket;
    }

    private function expectedAccept(string $key): string
    {
        return base64_encode(sha1($key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
    }

    /** Envía un frame de texto enmascarado (cliente → servidor). */
    private function sendFrame($socket, string $payload): void
    {
        $mask = random_bytes(4);
        $len = strlen($payload);
        $header = "\x81";
        if ($len <= 125) {
            $header .= chr(0x80 | $len);
        } elseif ($len <= 0xFFFF) {
            $header .= chr(0x80 | 126).pack('n', $len);
        } else {
            $header .= chr(0x80 | 127).pack('J', $len);
        }
        $masked = $payload ^ str_pad('', $len, $mask);
        fwrite($socket, $header.$mask.$masked);
    }

    /**
     * Lee y desempaqueta un frame del servidor (sin máscara), devolviendo el
     * payload de texto (o null en tiempo de espera). Responde pong a los pings.
     */
    private function readFrame($socket, float $timeout): ?string
    {
        stream_set_timeout($socket, max(0, (int) $timeout) ?: 1);

        $head = $this->readBytes($socket, 2);
        if ($head === null) {
            return null;
        }
        $b1 = ord($head[0]);
        $b2 = ord($head[1]);
        $opcode = $b1 & 0x0F;
        $masked = ($b2 & 0x80) !== 0;
        $len = $b2 & 0x7F;

        if ($len === 126) {
            $ext = $this->readBytes($socket, 2);
            if ($ext === null) {
                return null;
            }
            $len = unpack('n', $ext)[1];
        } elseif ($len === 127) {
            $ext = $this->readBytes($socket, 8);
            if ($ext === null) {
                return null;
            }
            $len = unpack('J', $ext)[1];
        }

        $mask = $masked ? $this->readBytes($socket, 4) : null;
        $payload = $len > 0 ? $this->readBytes($socket, $len) : '';

        if ($opcode === 0x9) { // ping → pong
            $this->sendFrame($socket, $payload);
        }
        if ($opcode !== 0x1 && $opcode !== 0x0) {
            return null;
        }

        if ($mask) {
            $payload = $payload ^ str_pad('', strlen($payload), $mask);
        }

        return $payload;
    }

    /** @return string|null */
    private function readBytes($socket, int $count)
    {
        $data = '';
        while (strlen($data) < $count) {
            $chunk = fread($socket, $count - strlen($data));
            if ($chunk === '' || $chunk === false) {
                $meta = stream_get_meta_data($socket);
                if (!empty($meta['timed_out']) || feof($socket)) {
                    return null;
                }
                continue;
            }
            $data .= $chunk;
        }

        return $data;
    }

    /** @var string */
    private $lastSecKey = '';
}
