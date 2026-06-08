<?php

namespace App\Http\Controllers\Administracion\Email\RegistroPago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Jobs\Email\RegistroPago\ProcessNotifyTicketSend;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Http\Controllers\Administracion\PDF\RepresentantController;

class sendTicketPaymentController extends Controller
{
    public function sendMail($pago_combinado_id)
    {
        $registro_pago_combinado = RegistroPagoCombinado::findOrFail($pago_combinado_id); //dd($registro_pago_combinado);
        $representant = $registro_pago_combinado->representant;
        $time = Carbon::now();
        $allEmails = array();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//DIRECTOR
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        $email = $representant->email;
        if (validate_email($email)) {
            
            $allEmails[]=$email;
            $address = (env('MAIL_MODE_TESTER', false)) ? env('MAIL_ADDRESS_TESTER', null) : $email ;
            $mailCCAddress = (env('MAIL_MODE_TESTER', false)) ? env('MAIL_CC_ADDRESS_TESTER', null) : env('MAIL_CC_ADDRESS', null) ;

            $representantPDF = New RepresentantController;
            $reciboPDF = $representantPDF->reciboMail($registro_pago_combinado->id); //dd($reciboPDF);

            $data = '%PDF-1.2 6 0 obj << /S /GoTo /D (chapter.1) >>';
            
            $dataEmail = [
                'view' => 'email.registropago.ticketPayment.messege',
                'subject' => 'Recibo de Pago N. '.$registro_pago_combinado->id,
                // 'attach' => $reciboPDF,
                'attach' => base64_encode(base64_decode($reciboPDF->output())),
                // 'attach' => $data,

                'address' => $address,
                'mailCCAddress' => $mailCCAddress,

                'registro_pago_combinado' => $registro_pago_combinado,
                'representant' => $representant,
                'institucion' => $institucion,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
                'toDate' => $toDate,
                'lastDate' => $lastDate,
            ]; //dd($dataEmail);
            ProcessNotifyTicketSend::dispatch($dataEmail)->delay($time);
        }
        return $dataEmail;
    }

    public function view()
    {
        $registro_pago_combinado = RegistroPagoCombinado::inRandomOrder()->first();
        $representant = $registro_pago_combinado->representant;
        $subject = 'Recibo de Pago N.'.$registro_pago_combinado->id;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        return view('email.registropago.ticketPayment.messege',compact('subject','registro_pago_combinado','representant','institucion','autoridad1','autoridad2','toDate','lastDate'));
    }

}
