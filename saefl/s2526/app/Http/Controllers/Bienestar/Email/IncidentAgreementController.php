<?php

namespace App\Http\Controllers\Bienestar\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\Bienestar\ProcessNotifyIncidentAgreement;
use App\Models\app\Incident\Incident;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class IncidentAgreementController extends Controller
{
    public function messegesSend(Incident $incident)
    {
        $incident_agreements = $incident->incident_agreements;
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);
        $representant = $estudiant->representant;

        $profesor_guia = $estudiant->profesor_guia;
        $profesor = ($profesor_guia) ? $profesor_guia : $incident->profesor ;

        $subject = 'Notificación - Bienestar Estudiantil';
        $time = Carbon::now(); ;
        $datas = collect();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('1');//ACADEMICO
        $autoridad3 = Autoridad::getTipoAuthority('7');//ACADEMICO

        $toDate = Date::now()->format('d F Y');

        $email = $representant->email;
        if (validate_email($email)) {
            $email = (env('MAIL_MODE_TESTER',false)) ? env('MAIL_ADDRESS_TESTER') : $email;
            $mailCCAddress = (env('MAIL_MODE_TESTER',false)) ? env('MAIL_CC_ADDRESS_TESTER') : env('MAIL_USERNAME', 'soporte.saefl@gmail.com');

            $email = (validate_email($profesor->gsemail)) ? $profesor->gsemail : 'tester.saefl@gmail.com' ;
            $mailCCAddress = (validate_email($profesor->email)) ? $profesor->email : 'tester.cc.saefl@gmail.com' ;

            $dataEmail = [
                'mailCCAddress' => $mailCCAddress,
                'subject' => $subject,
                'address' => $email,
                'representant' => $representant,
                'incident' => $incident,
                'incident_agreements' => $incident_agreements,
                'institucion' => $institucion,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
                'autoridad3' => $autoridad3,
                'toDate' => $toDate,
                'view' => 'email.incidents.agreements',
            ]; //dd($dataEmail);
            ProcessNotifyIncidentAgreement::dispatch($dataEmail)->delay($time);
            $datas->push($dataEmail);
        }

        return $datas;
    }

    public function messegesSendClose(Incident $incident)
    {
        $incident_agreements = $incident->incident_agreements;
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);
        $representant = $estudiant->representant;

        $profesor_guia = $estudiant->profesor_guia;
        $profesor = ($profesor_guia) ? $profesor_guia : $incident->profesor ;

        $subject = 'Notificación - Bienestar Estudiantil';
        $time = Carbon::now(); ;
        $datas = collect();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('1');//ACADEMICO
        $autoridad3 = Autoridad::getTipoAuthority('7');

        $toDate = Date::now()->format('d F Y');

        $email = $representant->email;
        if (validate_email($email)) {
            $email = (env('MAIL_MODE_TESTER',false)) ? env('MAIL_ADDRESS_TESTER') : $email;
            $mailCCAddress = (env('MAIL_MODE_TESTER',false)) ? env('MAIL_CC_ADDRESS_TESTER') : env('MAIL_USERNAME', 'soporte.saefl@gmail.com');

            $email = (validate_email($profesor->gsemail)) ? $profesor->gsemail : 'tester.saefl@gmail.com' ;
            $mailCCAddress = (validate_email($profesor->email)) ? $profesor->email : 'tester.cc.saefl@gmail.com' ;

            $dataEmail = [
                'mailCCAddress' => $mailCCAddress,
                'subject' => $subject,
                'address' => $email,
                'representant' => $representant,
                'incident' => $incident,
                'incident_agreements' => $incident_agreements,
                'institucion' => $institucion,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
                'autoridad3' => $autoridad3,
                'toDate' => $toDate,
                'view' => 'email.incidents.close',
            ]; //dd($dataEmail);
            ProcessNotifyIncidentAgreement::dispatch($dataEmail)->delay($time);
            $datas->push($dataEmail);
        }

        return $datas;
    }
}
