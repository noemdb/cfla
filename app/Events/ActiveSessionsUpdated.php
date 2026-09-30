<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Conteo de sesiones activas para el chart del dashboard /admin.
 *
 * Se emite por WebSocket (Reverb) en el canal privado `admin.sessions`
 * como `sessions.updated`. Emisores: comando `admin:broadcast-sessions`
 * (scheduler, cada minuto) y la visita a /admin (valor inmediato).
 */
class ActiveSessionsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(?array $payload = null)
    {
        $this->payload = $payload ?? self::currentPayload();
    }

    /**
     * Foto actual: sesiones vigentes según el driver configurado
     * (`database` → tabla sessions por last_activity, `file` → archivos
     * vigentes en storage) + usuarios autenticados con actividad
     * reciente (15 min, heartbeat `users.last_seen_at`).
     */
    public static function currentPayload(): array
    {
        $lifetime = (int) config('session.lifetime', 120);

        return [
            'active_sessions' => self::countActiveSessions($lifetime),
            'authenticated_online' => self::countOnlineUsers(),
            'timestamp' => now()->format('H:i:s'),
        ];
    }

    /**
     * Usuarios autenticados con heartbeat reciente (middleware
     * UpdateLastSeen, ventana 15 min). Independiente del SESSION_DRIVER.
     */
    public static function countOnlineUsers(int $minutes = 15): int
    {
        try {
            return (int) User::where('last_seen_at', '>=', now()->subMinutes($minutes))->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Cuenta sesiones vigentes según SESSION_DRIVER. Los drivers no
     * enumerables (redis, memcached, cookie, array…) caen al heartbeat
     * de `users.last_seen_at` en vez de devolver 0.
     */
    public static function countActiveSessions(?int $lifetime = null): int
    {
        $lifetime ??= (int) config('session.lifetime', 120);
        $driver = (string) config('session.driver', 'file');

        if ($driver === 'database') {
            try {
                return (int) DB::table(config('session.table', 'sessions'))
                    ->where('last_activity', '>=', now()->subMinutes($lifetime)->timestamp)
                    ->count();
            } catch (\Throwable $e) {
                return 0;
            }
        }

        if ($driver === 'file') {
            $threshold = now()->subMinutes($lifetime)->timestamp;
            $count = 0;
            $sessionPath = storage_path('framework/sessions');
            if (is_dir($sessionPath)) {
                foreach (glob($sessionPath.'/*') ?: [] as $file) {
                    if (is_file($file) && filemtime($file) >= $threshold) {
                        $count++;
                    }
                }
            }

            return $count;
        }

        return self::countOnlineUsers();
    }

    /**
     * Canal privado: solo admin o personal de diagnóstico
     * (autorizado en routes/channels.php).
     */
    public function broadcastOn()
    {
        return new PrivateChannel('admin.sessions');
    }

    public function broadcastAs(): string
    {
        return 'sessions.updated';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
