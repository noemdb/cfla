<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar índices
        DB::statement('CREATE INDEX idx_voting_sessions_poll_device ON voting_sessions (poll_id, fingerprint, ip, voted)');
        DB::statement('CREATE INDEX idx_voting_sessions_uuid ON voting_sessions (uuid)');
        DB::statement('CREATE INDEX idx_voting_sessions_expires ON voting_sessions (expires_at)');
        DB::statement('CREATE INDEX idx_voting_sessions_unique_check ON voting_sessions (poll_id, fingerprint, voted)');
        DB::statement('CREATE INDEX idx_voting_sessions_ip_check ON voting_sessions (poll_id, ip, voted)');

        // Verificaciones de integridad (solo para desarrollo)
        if (app()->environment('local', 'development')) {
            $this->runDataChecks();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar índices
        DB::statement('DROP INDEX idx_voting_sessions_poll_device ON voting_sessions');
        DB::statement('DROP INDEX idx_voting_sessions_uuid ON voting_sessions');
        DB::statement('DROP INDEX idx_voting_sessions_expires ON voting_sessions');
        DB::statement('DROP INDEX idx_voting_sessions_unique_check ON voting_sessions');
        DB::statement('DROP INDEX idx_voting_sessions_ip_check ON voting_sessions');
    }

    /**
     * Ejecutar verificaciones de datos
     */
    protected function runDataChecks(): void
    {
        // Verificar duplicados por fingerprint
        $fingerprintDuplicates = DB::select('
            SELECT poll_id, fingerprint, COUNT(*) as duplicates
            FROM voting_sessions
            WHERE fingerprint IS NOT NULL
            AND voted = true
            GROUP BY poll_id, fingerprint
            HAVING COUNT(*) > 1
        ');

        if (!empty($fingerprintDuplicates)) {
            logger()->warning('Se encontraron duplicados por fingerprint:', $fingerprintDuplicates);
        }

        // Verificar duplicados por IP
        $ipDuplicates = DB::select('
            SELECT poll_id, ip, COUNT(*) as duplicates
            FROM voting_sessions
            WHERE ip IS NOT NULL
            AND voted = true
            GROUP BY poll_id, ip
            HAVING COUNT(*) > 1
        ');

        if (!empty($ipDuplicates)) {
            logger()->warning('Se encontraron duplicados por IP:', $ipDuplicates);
        }

        // Mostrar estadísticas
        $stats = DB::select('
            SELECT
                vs.poll_id,
                vp.title as poll_title,
                COUNT(*) as total_sessions,
                SUM(CASE WHEN vs.voted = true THEN 1 ELSE 0 END) as voted_sessions,
                COUNT(DISTINCT vs.fingerprint) as unique_fingerprints,
                COUNT(DISTINCT vs.ip) as unique_ips
            FROM voting_sessions vs
            JOIN voting_polls vp ON vs.poll_id = vp.id
            GROUP BY vs.poll_id, vp.title
            ORDER BY vs.poll_id
        ');

        logger()->info('Estadísticas de sesiones de votación:', $stats);
    }
};
