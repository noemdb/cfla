<?php

namespace App\Http\Controllers\Admin\Email;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Jobs\Email\Admin\ProcessNotifyRecargosMorosidad; // Lo crearemos

class SetRecargosMorosidadController extends Controller
{
    public function messegesSend(int $cantidadNuevos, string $logOutput)
    {
        $subject = '🔔 Nuevos Recargos por Morosidad Generados';
        $time = Carbon::now();
        $toDate = $time->format('d F Y');
        $email = env('MAIL_ADDRESS_ADMON');

        if (validate_email($email)) {
            $mailCCAddress = env('MAIL_CC_ADDRESS_ADMON', $email);
            $dataEmail = [
                'mailCCAddress' => $mailCCAddress,
                'subject' => $subject,
                'address' => $email,
                'cantidad_nuevos' => $cantidadNuevos,
                'log_output' => $logOutput,
                'toDate' => $toDate,
                'view' => 'email.admin.recargos_morosidad', // Crearemos esta vista
            ];

            ProcessNotifyRecargosMorosidad::dispatch($dataEmail)->delay($time);
        }
    }
}