<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\app\Planpago\Cuentaxpagar;
use Carbon\Carbon;
use App\Http\Controllers\Admin\Email\SetRecargosMorosidadController; // Lo crearemos

class VerificarYGenerarRecargosMorosidad implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $hoy = Carbon::now()->format('Y-m-d');

        $existeCuotaHoy = Cuentaxpagar::withoutGlobalScopes()
            ->where('enable_late_payment', true)
            ->whereDate('date_late_payment', $hoy)
            ->exists();

        if (!$existeCuotaHoy) {
            Log::info("Job Recargos: No hay cuotas con date_late_payment = {$hoy}. Comando no ejecutado.");
            return;
        }

        Log::info("Job Recargos: Ejecutando comando recargos:generar...");
        Artisan::call('recargos:generar');
        $output = Artisan::output();

        // ✅ Analizar salida para detectar recargos NUEVOS
        $nuevos = 0;
        if (preg_match('/Recargos NUEVOS:\s*(\d+)/', $output, $matches)) {
            $nuevos = (int) $matches[1];
        }

        Log::info("Job Recargos: Recargos NUEVOS detectados: {$nuevos}");

        if ($nuevos > 0) {
            // Disparar notificación
            $controller = new SetRecargosMorosidadController();
            $controller->messegesSend($nuevos, $output);
        }
    }
}