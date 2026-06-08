<?php

namespace App\Http\Controllers\Bienestar\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\Bienestar\ProcessNotifyIncident;
use App\Models\app\Incident\Incident;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\SenderMailer\Mailer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class IncidentController extends Controller
{
    public function messegesSend(Incident $incident)
    {
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);
        $representant = $estudiant->representant;
        
        $profesor_guia = $estudiant->profesor_guia;
        $profesor = ($profesor_guia) ? $profesor_guia : $incident->profesor ;

        $subject = 'Notificación, Profesor - Gestión de Incidencias Estudiantiles';
        $time = Carbon::now(); ;
        $datas = collect();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('1');//ACADEMICO
        $autoridad3 = Autoridad::getTipoAuthority('7'); //dd($autoridad3);

        $toDate = Date::now()->format('d F Y');

        $email = $representant->email;        
        if (validate_email($email)) {
            $email = (env('MAIL_MODE_TESTER',false)) ? env('MAIL_ADDRESS_TESTER') : $email;
            $mailCCAddress = (env('MAIL_MODE_TESTER',false)) ? env('MAIL_CC_ADDRESS_TESTER') : env('MAIL_USERNAME', 'soporte.saefl@gmail.com');
            //dd($email,$mailCCAddress);

            $email = (validate_email($profesor->gsemail)) ? $profesor->gsemail : 'tester.saefl@gmail.com' ;
            $mailCCAddress = (validate_email($profesor->email)) ? $profesor->email : 'tester.cc.saefl@gmail.com' ;

            $dataEmail = [
                // 'mailCCAddress' => $mailCCAddress,
                'mailCCAddress' => env('MAIL_ADDRESS_TESTER'),
                'subject' => $subject,
                // 'address' => $email,
                'address' => env('MAIL_ADDRESS_TESTER'),
                'representant' => $representant,
                'incident' => $incident,
                'institucion' => $institucion,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
                'autoridad3' => $autoridad3,
                'toDate' => $toDate,
                'view' => 'email.incidents.messege',
            ]; //dd($dataEmail);
            ProcessNotifyIncident::dispatch($dataEmail)->delay($time);
            $datas->push($dataEmail);
        }

        return $datas;
    }

    public function messegesSendProfesor(Incident $incident)
    {
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);
        $representant = $estudiant->representant; 
        $profesor_guia = $estudiant->profesor_guia;
        $profesor = ($profesor_guia) ? $profesor_guia : $incident->profesor ;       

        $subject = 'Notificación, Profesor - Gestión de Incidencias Estudiantiles';
        $time = Carbon::now();
        $datas = collect();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2'); //director
        $autoridad2 = Autoridad::getTipoAuthority('1'); //ACADEMICO
        $autoridad3 = Autoridad::getTipoAuthority('7'); //dd($autoridad3);

        $toDate = Date::now()->format('d F Y');

        $email = $profesor->gsemail;
        $mailCCAddress = $profesor->email;        

        $email = (validate_email($email)) ? $email : env('MAIL_ADDRESS_BIENESTAR') ;
        $mailCCAddress = (validate_email($mailCCAddress)) ? $mailCCAddress : env('MAIL_ADDRESS_BIENESTAR') ;

        $email = (env('MAIL_MODE_TESTER',false)) ? env('MAIL_ADDRESS_TESTER') : $email;
        $mailCCAddress = (env('MAIL_MODE_TESTER',false)) ? env('MAIL_CC_ADDRESS_TESTER') : $mailCCAddress;

        $email = (validate_email($profesor->gsemail)) ? $profesor->gsemail : 'tester.saefl@gmail.com' ;
        $mailCCAddress = (validate_email($profesor->email)) ? $profesor->email : 'tester.cc.saefl@gmail.com' ;

        $dataEmail = [
            // 'mailCCAddress' => $mailCCAddress,
            'mailCCAddress' => env('MAIL_CC_ADDRESS_TESTER'),
            'subject' => $subject,
            // 'address' => $email,
            'address' => env('MAIL_ADDRESS_TESTER'),
            'representant' => $representant,
            'incident' => $incident,
            'institucion' => $institucion,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
            'autoridad3' => $autoridad3,
            'toDate' => $toDate,
            'view' => 'email.incidents.notifications.profesor',
        ]; 
        ProcessNotifyIncident::dispatch($dataEmail)->delay($time);
        $datas->push($dataEmail);

        return $datas;
    }
}
