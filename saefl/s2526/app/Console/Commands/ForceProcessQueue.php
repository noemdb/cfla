<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ForceProcessQueue extends Command
{
    protected $signature   = 'queue:force {queue=default} {--limit=50}';
    protected $description = 'Mueve jobs delayed a ready en lotes para evitar picos de memoria';

    public function handle()
    {
        $queue      = $this->argument('queue');
        $delayedKey = "queues:{$queue}:delayed";
        $readyKey   = "queues:{$queue}";
        $limit      = (int) $this->option('limit');

        if ($limit <= 0) {
            $this->warn("⚠️  Usa --limit=50 o mayor para control de memoria.");
            return self::FAILURE;
        }

        // ✅ Consulta SOLO los necesarios directamente en Redis
        $jobs  = Redis::zrange($delayedKey, 0, $limit - 1);
        $count = count($jobs);

        if ($count === 0) {
            $this->info("✅ No hay jobs delayed en '{$queue}'.");
            return self::SUCCESS;
        }

        $this->info("📦 Moviendo {$count} jobs de delayed → ready...");
        $bar = $this->output->createProgressBar($count);

        foreach ($jobs as $job) {
            Redis::rpush($readyKey, $job);
            Redis::zrem($delayedKey, $job);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        $this->info("🚀 Iniciando worker seguro...");
        $this->call('queue:work', [
            'connection'        => 'redis',
            '--queue'           => $queue,
            '--tries'           => 3,
            '--timeout'         => 300,
            '--stop-when-empty' => true,
        ]);

        return self::SUCCESS;
    }
}
