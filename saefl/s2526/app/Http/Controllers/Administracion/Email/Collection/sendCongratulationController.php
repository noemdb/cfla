<?php

namespace App\Http\Controllers\Administracion\Email\Collection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Jobs\Email\ProcessNotifyCongratulations;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Carbon\Carbon;
use Jenssegers\Date\Date;

class sendCongratulationController extends Controller
{
    public function sendCongratulations()
    {
        $representants = Representant::formalySolvents(); //dd($representants);
        // $representants = Representant::representantFormaly()->random(1); //dd($representants);
        // $representants = Representant::formalySolvents()->random(1); //dd($representants);
        $time = Carbon::now();
        $allEmails = array();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//DIRECTOR
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        foreach ($representants as $representant) {
            $time = $time->addSeconds(55);
            $email = $representant->email;
            if (!in_array($email,$allEmails)) {
                if (validate_email($email)) {
                    $allEmails[]=$email;
                    $dataEmail = [
                        'view' => 'email.collections.congratulations.messege',
                        'subject' => 'Nota de agradecimiento',
                        'address' => $email,
                        'mailCCAddress' => env('MAIL_CC_ADDRESS_ADMON', 'soporte.saefl@gmail.com'),
                        // 'address' => env('MAIL_ADDRESS_TESTER', 'soporte.saefl@gmail.com'),
                        // 'mailCCAddress' => env('MAIL_CC_ADDRESS_TESTER', 'soporte.saefl@gmail.com'),
                        'representant' => $representant,
                        'institucion' => $institucion,
                        'autoridad1' => $autoridad1,
                        'autoridad2' => $autoridad2,
                        'toDate' => $toDate,
                        'lastDate' => $lastDate,
                    ]; //dd($dataEmail);
                    ProcessNotifyCongratulations::dispatch($dataEmail)->delay($time);
                }
            }
        }
        return $allEmails;
    }

    public function view()
    {
        $subject = 'Nota de agradecimiento';
        $representant = Representant::representantFormaly()->random(1)->first();// dd($representant);
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');
        return view('email.collections.congratulations.messege',compact('subject','representant','institucion','autoridad1','autoridad2','toDate','lastDate'));
    }
}

/*
    'subject','representant','institucion','autoridad1','autoridad2','toDate','lastDate'
*/