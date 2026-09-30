<?php

namespace App\Events;

use App\Models\Visit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
     * Foto actual: sesiones vigentes (driver file) + usuarios
     * autenticados con actividad reciente (15 min, tabla visits).
     */
    public static function currentPayload(): array
    {
        $lifetime = (int) config('session.lifetime', 120);
        $threshold = now()->subMinutes($lifetime)->timestamp;
        $activeSessions = 0;
        $sessionPath = storage_path('framework/sessions');
        if (is_dir($sessionPath)) {
            foreach (glob($sessionPath.'/*') ?: [] as $file) {
                if (is_file($file) && filemtime($file) >= $threshold) {
                    $activeSessions++;
                }
            }
        }

        $authenticatedOnline = 0;
        try {
            $authenticatedOnline = Visit::whereNotNull('user_id')
                ->where('created_at', '>=', now()->subMinutes(15))
                ->distinct('user_id')
                ->count('user_id');
        } catch (\Throwable $e) {
            $authenticatedOnline = 0;
        }

        return [
            'active_sessions' => $activeSessions,
            'authenticated_online' => $authenticatedOnline,
            'timestamp' => now()->format('H:i:s'),
        ];
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
