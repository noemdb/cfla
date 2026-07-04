<?php

namespace App\Http\Controllers\Academico\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\ProcessNotifyMailer;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\SenderMailer\Mailed;
use App\Models\app\SenderMailer\Mailer;
use Jenssegers\Date\Date;

class MailerController extends Controller
{
    public function messegesSend(Mailer $mailer)
    {        
        $subject = $mailer->subject;
        $time = $mailer->date ;
        $datas = collect();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('1');//ACADEMICO

        $toDate = Date::now()->format('d F Y');
                
        $representants = $mailer->representants; 
        $addressEmail = null;
        foreach ($representants as $representant) {
            $allEmails = array();
            $email = $representant->email;
            if (!in_array($email,$allEmails)) {
                if (validate_email($email)) {
                    $allEmails[]=$email;
                    $dataEmail = [
                        'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                        'subject' => $subject,
                        'address' => $email,
                        'representant' => $representant,
                        'mailer' => $mailer,
                        'institucion' => $institucion,
                        'autoridad1' => $autoridad1,
                        'autoridad2' => $autoridad2,
                        'toDate' => $toDate,
                        'view' => 'email.mailers.messege',
                    ];
                    // ProcessNotifyMailer::dispatchSync($dataEmail); //dispatch
                    //dd($dataEmail);
                    ProcessNotifyMailer::dispatch($dataEmail)->delay($time);
                    $available_at = $time->timestamp;
                    $arr = [
                        'mailer_id' => $mailer->id,
                        'representant_id' => $representant->id,
                        'status' => 'true',
                        'available_at' => $available_at,
                    ]; //dd($arr);
                    $mailed = Mailed::create($arr);
                    $addressEmail .= $email.' | ';
                    $time = $time->addMinutes(1);
                    $datas->push($dataEmail);
                }                    
            }
        }

        return $datas;
    }
}
