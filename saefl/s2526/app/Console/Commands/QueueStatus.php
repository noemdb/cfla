<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class QueueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:stats {--list : Listar los nombres de los trabajos en las colas}';
    //php artisan queue:stats --list

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra estadísticas detalladas de las colas en Redis';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Consultando estado de colas en Redis...");

        $connection = config('queue.connections.redis.connection', 'default');
        $prefix     = config('database.redis.options.prefix', '');
        $redis      = Redis::connection($connection);

        // Obtener todas las llaves de colas
        $keys = $redis->keys('queues:*');

        $stats           = [];
        $processedQueues = [];
        $jobDetails      = [];

        foreach ($keys as $key) {
            $cleanKey = $prefix ? str_replace($prefix, '', $key) : $key;

            if (preg_match('/queues:([^:]+)$/', $cleanKey, $matches)) {
                $queueName = $matches[1];
                $type      = 'pending';
                $count     = $redis->llen($cleanKey);

                if ($this->option('list') && $count > 0) {
                    $jobs = $redis->lrange($cleanKey, 0, 9);
                    foreach ($jobs as $jobJson) {
                        $jobData                           = json_decode($jobJson, true);
                        $jobDetails[$queueName]['ready'][] = $jobData['displayName'] ?? 'Unknown';
                    }
                }
            } elseif (preg_match('/queues:([^:]+):delayed$/', $cleanKey, $matches)) {
                $queueName = $matches[1];
                $type      = 'delayed';
                $count     = $redis->zcard($cleanKey);

                if ($this->option('list') && $count > 0) {
                    $jobs = $redis->zrange($cleanKey, 0, 9);
                    foreach ($jobs as $jobJson) {
                        $jobData                             = json_decode($jobJson, true);
                        $jobDetails[$queueName]['delayed'][] = $jobData['displayName'] ?? 'Unknown';
                    }
                }
            } elseif (preg_match('/queues:([^:]+):reserved$/', $cleanKey, $matches)) {
                $queueName = $matches[1];
                $type      = 'reserved';
                $count     = $redis->zcard($cleanKey);

                if ($this->option('list') && $count > 0) {
                    $jobs = $redis->zrange($cleanKey, 0, 9);
                    foreach ($jobs as $jobJson) {
                        $jobData                              = json_decode($jobJson, true);
                        $jobDetails[$queueName]['reserved'][] = $jobData['displayName'] ?? 'Unknown';
                    }
                }
            } else {
                continue;
            }

            if (! isset($processedQueues[$queueName])) {
                $processedQueues[$queueName] = [
                    'queue'    => $queueName,
                    'pending'  => 0,
                    'delayed'  => 0,
                    'reserved' => 0,
                ];
            }

            $processedQueues[$queueName][$type] = $count;
        }

        $failedCount = DB::table('failed_jobs')->count();

        $rows = [];
        foreach ($processedQueues as $q) {
            $rows[] = [
                $q['queue'],
                $q['pending'],
                $q['reserved'],
                $q['delayed'],
                $failedCount,
            ];
        }

        if (empty($rows)) {
            $this->warn("No se encontraron colas activas en Redis.");
        } else {
            $this->table(
                ['Cola', 'Pendientes (Ready)', 'En Proceso (Reserved)', 'Programados (Delayed)', 'Fallidos (Total)'],
                $rows
            );

            if ($this->option('list')) {
                $this->newLine();
                $this->info("Detalle de trabajos (Top 10):");
                foreach ($jobDetails as $qName => $types) {
                    $this->line("<fg=cyan>Cola: {$qName}</>");
                    foreach ($types as $status => $names) {
                        $statusText = strtoupper($status);
                        $this->line("  [{$statusText}]");
                        foreach (array_unique($names) as $name) {
                            $countType = array_count_values($names)[$name];
                            $this->line("    - {$name} ({$countType} items)");
                        }
                    }
                }
            }
        }

        return 0;
    }
}
