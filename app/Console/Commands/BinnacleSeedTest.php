<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Genera datos de prueba en binnacle_entries para pruebas de carga (Spec §11).
 * Las filas se identifican con subject_identifier = 'seeded-benchmark' y se
 * pueden eliminar con --clean. Solo INSERT (los triggers no lo bloquean).
 */
class BinnacleSeedTest extends Command
{
    protected $signature = 'binnacle:seed-test
        {--count=50000 : Cantidad de filas a insertar}
        {--days=90 : Antigüedad máxima en días}
        {--clean : Elimina las filas sembradas previamente}';

    protected $description = 'Siembra datos de prueba en la bitácora para benchmarks (Spec §11)';

    public function handle(): int
    {
        if ($this->option('clean')) {
            DB::statement('SET @binnacle_archive_process = 1');
            $deleted = DB::table('binnacle_entries')->where('subject_identifier', 'seeded-benchmark')->delete();
            DB::statement('SET @binnacle_archive_process = NULL');
            $this->info("Filas de prueba eliminadas: {$deleted}.");

            return self::SUCCESS;
        }

        $count = (int) $this->option('count');
        $days = (int) $this->option('days');

        $categories = ['authentication', 'user_action', 'system', 'security', 'error'];
        // Sin critical/alert: esas severidades pertenecen a la hash-chain (ADR-003)
        // y filas con entry_hash NULL las marcarían como "rotas" en el dashboard.
        $severities = ['debug', 'info', 'info', 'info', 'warning'];
        $events = ['user_login', 'user_logout', 'model_created', 'model_updated', 'model_deleted',
            'exception_thrown', 'binnacle_accessed', 'user_login_failed', 'model_viewed'];

        $chunk = 1000;
        $total = 0;

        $this->line("Sembrando {$count} filas…");

        while ($total < $count) {
            $rows = [];

            for ($i = 0; $i < $chunk && $total + $i < $count; $i++) {
                $type = $events[array_rand($events)];
                $severity = $severities[array_rand($severities)];
                $createdAt = now()->subMinutes(random_int(0, $days * 1440));

                $rows[] = [
                    'uuid' => (string) Str::uuid(),
                    'event_type' => $type,
                    'event_category' => $categories[array_rand($categories)],
                    'event_severity' => $severity,
                    'title' => 'Evento de prueba '.($total + $i),
                    'description' => 'Fila de carga para benchmark (subject seeded-benchmark)',
                    'subject_type' => \App\Models\User::class,
                    'subject_id' => random_int(1, 3431),
                    'subject_identifier' => 'seeded-benchmark',
                    'object_type' => null,
                    'object_id' => null,
                    'object_identifier' => null,
                    'ip_address' => long2ip(random_int(0, 4294967295)),
                    'user_agent' => null,
                    'request_method' => 'GET',
                    'request_url' => '/admin/binnacle',
                    'request_id' => (string) Str::uuid(),
                    'session_id' => Str::random(40),
                    'old_values' => null,
                    'new_values' => null,
                    'changed_fields' => null,
                    'metadata' => json_encode(['seeded' => true]),
                    'entry_hash' => null,
                    'previous_hash' => null,
                    'created_at' => $createdAt,
                    'created_by' => random_int(1, 3431),
                ];
            }

            DB::table('binnacle_entries')->insert($rows);
            $total += count($rows);
            $this->output->write("\r  Insertadas: {$total}");
        }

        $this->newLine();
        $this->info("Datos de prueba sembrados: {$total} filas. Límpielos con --clean.");

        return self::SUCCESS;
    }
}
