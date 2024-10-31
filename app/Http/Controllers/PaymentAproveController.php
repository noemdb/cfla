<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJobPayment;
use App\Mail\SendEmailPayment;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Entity\Institucion;
use App\Models\app\Learner\Representant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class PaymentAproveController extends Controller
{
    public function SendPaymentRegistered($representant_id, array $inputs)
    {
        $representant = Representant::findOrFail($representant_id);
        $email = $representant->email;
        $time = Carbon::now();
        $toDate = Date::now()->format('d F Y');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR

        $subject = 'Reporte de Pago - Representante';
        $dataEmail = [
            'subject' => $subject,
            // 'address' => $email,
            'address' => 'noemdb@gmail.com',
            'mailCCAddress' => env('MAIL_CC_ADDRESS', 'hello@example.com'),
            'representant' => $representant,
            'inputs' => $inputs,
            'toDate' => $toDate,
            'institucion' => $institucion,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
        ]; 

        $template = new SendEmailPayment($dataEmail, $subject);
        SendEmailJobPayment::dispatch($dataEmail['address'], $template)->delay($time->addSeconds(30));

        return $dataEmail;

    }
}
