<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupVotingSessions extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'voting-sessions:cleanup';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Limpia las sesiones expiradas que no han votado y muestra estadísticas actualizadas';

    /**
     * Ejecutar el comando.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando limpieza de sesiones expiradas...');

        // Eliminar sesiones expiradas que no han votado
        $deleted = DB::delete("
            DELETE FROM voting_sessions
            WHERE expires_at < NOW()
            AND voted = false;
        ");

        $this->info("Sesiones eliminadas: $deleted");

        // Analizar tabla para actualizar estadísticas internas (equivalente a ANALYZE TABLE)
        DB::statement("ANALYZE TABLE voting_sessions");

        // Obtener estadísticas después de la limpieza
        $stats = DB::selectOne("
            SELECT
                COUNT(*) as total_sessions,
                COUNT(CASE WHEN voted = true THEN 1 END) as voted_sessions,
                COUNT(CASE WHEN expires_at < NOW() THEN 1 END) as expired_sessions,
                COUNT(CASE WHEN expires_at < NOW() AND voted = false THEN 1 END) as expired_unvoted
            FROM voting_sessions;
        ");

        // Mostrar estadísticas formateadas
        $this->info("\nEstadísticas actuales:");
        $this->table(
            ['Descripción', 'Cantidad'],
            [
                ['Total de sesiones', $stats->total_sessions],
                ['Sesiones votadas', $stats->voted_sessions],
                ['Sesiones expiradas', $stats->expired_sessions],
                ['Sesiones expiradas y no votadas', $stats->expired_unvoted],
            ]
        );

        $this->info("\nLimpieza completada exitosamente.");

        return Command::SUCCESS;
    }
}
