<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Carbon;

class RetryFailedJobs extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retry:failed-jobs {--delay-to=6}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reintenta todos los jobs fallidos y los programa para una hora específica del día.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Hora objetivo para ejecutar los jobs (ej. 6 AM)
        $targetHour = (int)$this->option('delay-to');
        $now = Carbon::now();
        $targetTime = Carbon::createFromTime($targetHour, 0, 0);

        if ($now->greaterThan($targetTime)) {
            // Ya pasó la hora objetivo, programar para mañana
            $targetTime->addDay();
        }

        $secondsToDelay = $now->diffInSeconds($targetTime);

        $this->info("Calculando retraso: " . $secondsToDelay . " segundos hasta las {$targetHour}:00");

        // Obtener todos los jobs fallidos con un mensaje específico
        $failedJobs = DB::table('failed_jobs')
            ->where('exception', 'LIKE', '%Swift_TransportException: Expected response code 250 but got code "451"%')
            ->get();

        if ($failedJobs->isEmpty()) {
            $this->info("No hay jobs fallidos para reintentar.");
            return 0;
        }

        foreach ($failedJobs as $job) {
            try {
                // Deserializar el payload
                $payload = json_decode($job->payload, true);
                $command = unserialize($payload['data']['command']);

                // Programar el job para la hora objetivo
                dispatch((clone $command)->onQueue($job->queue))->delay($secondsToDelay);

                Log::info("Job ID: {$job->id} reprogramado para las {$targetTime}");

                // Eliminar de failed_jobs
                DB::table('failed_jobs')->where('id', $job->id)->delete();

            } catch (\Exception $e) {
                Log::error("Error al reintentar job fallido (ID: {$job->id}): " . $e->getMessage());
                continue;
            }
        }

        $this->info("Todos los jobs han sido reprogramados.");
        return 0;
    }
}