<?php

namespace App\Console\Commands;

use App\Events\ActiveSessionsUpdated;
use Illuminate\Console\Command;

/**
 * Emite por WebSocket (Reverb, canal privado `admin.sessions`) el conteo
 * actual de sesiones activas para el chart del dashboard /admin.
 *
 * Agendado cada minuto en App\Console\Kernel. Si Reverb está caído el
 * comando falla sin romper nada: el dashboard muestra el último valor
 * renderizado en servidor.
 */
class BroadcastActiveSessions extends Command
{
    protected $signature = 'admin:broadcast-sessions';

    protected $description = 'Emite el conteo de sesiones activas al canal privado admin.sessions (dashboard /admin)';

    public function handle(): int
    {
        $payload = ActiveSessionsUpdated::currentPayload();

        try {
            ActiveSessionsUpdated::dispatch($payload);
        } catch (\Throwable $e) {
            $this->warn('No se pudo emitir (¿Reverb caído?): '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Sesiones activas: {$payload['active_sessions']} · autenticados (15 min): {$payload['authenticated_online']}");

        return self::SUCCESS;
    }
}
