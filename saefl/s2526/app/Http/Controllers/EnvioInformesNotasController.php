<?php
namespace App\Http\Controllers;

use App\Jobs\EnviarInformeNotasJob;
use App\Models\app\Estudiant;
use Illuminate\Support\Facades\Log;

class EnvioInformesNotasController extends Controller
{
    public function sendBoletinesPorEmail($lapso_id = 1, $peducativo_id = 3, $sendWhatsappNotifications = false)
    {
        $estudiants     = Estudiant::getEstudiantsFormaly($peducativo_id); // Inicial = 1, Primaria = 2, Media General = 3
        $delayInSeconds = 15;                                              // Delay inicial de 0 segundos para el primer job
                                                                           // dd($estudiants->shuffle()->take(10));

        foreach ($estudiants as $estudiant) {

            $representant = $estudiant->representant;

            if ($representant) {

                $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);

                if ($exchange_ammount_expire_bill <= 0) { // Si no hay deuda

                    EnviarInformeNotasJob::dispatch($estudiant->id, $lapso_id, null, $sendWhatsappNotifications)
                        ->delay(now()->addSeconds($delayInSeconds));

                    $delayInSeconds += 90; // Incrementa el delay en 70 segundos para el próximo job

                } else {

                    Log::info("El estudiante {$estudiant->id} tiene una deuda de $exchange_ammount_expire_bill, no se enviará el boletín.");
                }
            } else {

                Log::info("El estudiante {$estudiant->id} no tiene un representante asociado, no se enviará el boletín.");
            }
        }

        Log::info("Se han encolado " . count($estudiants) . " boletines para el lapso $lapso_id, con intervalos de 60 segundos.");

        return 'Jobs encolados exitosamente con intervalos de 60 segundos';
    }
}
