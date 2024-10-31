<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\Payment\ProcessNotifyPayment;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Entity\Institucion;
use App\Models\app\Learner\Representant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use App\Jobs\Email\ProcessNotifyCollect;

class SendPaymentController extends Controller
{
    public function collectionSend($representant_id, array $inputs)
    {
        $representant = Representant::findOrFail($representant_id);
        $email = $representant->email;
        $time = Carbon::now();
        $toDate = Date::now()->format('d F Y');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR

        $addressEmail = null;

        $subject = 'Reporte de Pago - Representante';
        $allEmails = array();
        $dataEmail = [
            'subject' => $subject,
            'address' => $email,
            // 'address' => 'tester.saefl@gmail.com',
            'mailCCAddress' => env('MAIL_CC_ADDRESS', 'hello@example.com'),
            'representant' => $representant,
            'inputs' => $inputs,
            'toDate' => $toDate,
            'institucion' => $institucion,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
        ]; 
        ProcessNotifyPayment::dispatch($dataEmail)->delay($time->addSeconds(30));
        return $addressEmail;
    }
}
