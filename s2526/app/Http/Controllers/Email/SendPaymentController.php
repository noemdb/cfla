<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\ProcessNotifyReport;
use Illuminate\Http\Request;

use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Carbon\Carbon;
use Jenssegers\Date\Date;

class SendPaymentController extends Controller
{
    public function collectionSend($representant_id, array $inputs)
    {
        $representant = Representant::findOrFail($representant_id);
        $email = $representant->email;
        $time = Carbon::now(); //dd($time);
        $toDate = Date::now()->format('d F Y');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR

        $addressEmail = null;

        $subject = 'Reporte de Pago - Representante';
        $allEmails = array();
        if (validate_email($email)) { //dd($email);
            $dataEmail = [
                'subject' => $subject,
                // 'address' => $email,
                'address' => 'tester.saefl@gmail.com',
                'mailCCAddress' => env('MAIL_CC_ADDRESS', 'hello@example.com'),
                'representant' => $representant,
                'inputs' => $inputs,
                'toDate' => $toDate,
                'institucion' => $institucion,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
            ]; //dd($dataEmail);
            // ProcessNotifyCollect::dispatchSync($dataEmail); //dispatch
            ProcessNotifyReport::dispatch($dataEmail)->delay($time->addSeconds(30));
        }
        return $addressEmail;
    }
}
