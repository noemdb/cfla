<?php

namespace App\Http\Controllers\General\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\Catchment\ProcessNotifyCatchment;
use App\Jobs\Email\Resend\ProcessResendEmail;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Carbon\Carbon;
use Jenssegers\Date\Date;

class CatchmentController extends Controller
{

    public function messegesSendMailNoticeGeneralRememberFirst($id, $date = null)
    {
        $subject = 'Proceso de Matriculación Escolar 2025 -2026';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = Catchment::where('id', $id)->first();
        $list_comment = Catchment::COLUMN_COMMENTS;

        if ($catchment) {

            $email = $catchment->email;
            // $email = "noemdb@gmail.com";
            if (validate_email($email)) {
                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_TESTER', 'soporte.saefl@gmail.com'), //MAIL_CC_ADDRESS_CONTROL
                    'subject' => $subject,
                    'address' => $email,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.noticeRememberFirst',
                    'list_comment' => $list_comment,
                ];
                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }
        return $dataEmail;
    }

    public function messegesSendMailNoticeGenera($id, $date = null)
    {
        $subject = 'Proceso de Matriculación Escolar 2025 -2026';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = Catchment::where('id', $id)->first();
        $list_comment = Catchment::COLUMN_COMMENTS;

        if ($catchment) {

            $email = $catchment->email;
            // $email = "noemdb@gmail.com";

            if (validate_email($email)) {
                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                    // 'mailCCAddress' => env('MAIL_CC_ADDRESS_TESTER', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.noticeInterview',
                    'list_comment' => $list_comment,
                ]; //dd($dataEmail);
                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }
        return $dataEmail;
    }

    public function messegesSendInterviewReprogrammer($id, $date = null)
    {
        $subject = 'Reprogramación de la convocatoria';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = Catchment::where('id', $id)->first();
        $list_comment = Catchment::COLUMN_COMMENTS;

        if ($catchment) {

            $email = $catchment->email;

            if (validate_email($email)) {
                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.reprogrammer',
                    'list_comment' => $list_comment,
                ]; //dd($dataEmail);
                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }
        return $dataEmail;
    }

    public function messegesSend($id, $date = null)
    {
        $subject = 'Registro inicial';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = Catchment::where('id', $id)->first();
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;

        if ($catchment) {

            $email = $catchment->email;

            if (validate_email($email)) {

                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.messege',
                    'list_comment' => $list_comment,
                ]; //dd($dataEmail);
                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }

        return $dataEmail;
    }

    public function messegesSendRegister($id, $date = null)
    {
        $subject = 'Ticket de registro formalizado';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = Catchment::where('id', $id)->first();
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;

        if ($catchment) {

            $email = $catchment->email;

            if (validate_email($email)) {

                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.register',
                    'list_comment' => $list_comment,
                ]; //dd($dataEmail);
                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }

        return $dataEmail;
    }

    public function messegesInterview($id, $date = null)
    {
        $subject = 'Registro de la Entrevista';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = CatchmentInterview::where('id', $id)->first();
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;

        if ($catchment) {

            $email = $catchment->email;

            if (validate_email($email)) {
                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'interview' => $catchment,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.interview',
                    'list_comment' => $list_comment,
                ]; //dd($dataEmail);
                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }
        return $dataEmail;
    }

    public function messegesSendInterviewReprogrammerFaseOne($id, $date = null)
    {
        $subject = 'Reprogramación de la convocatoria';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = Catchment::where('id', $id)->first();
        $list_comment = Catchment::COLUMN_COMMENTS;

        if ($catchment) {

            $email = $catchment->email;
            if (validate_email($email)) {
                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.reprogrammerFaseOne',
                    'list_comment' => $list_comment,
                ]; //dd($dataEmail);
                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }
        return $dataEmail;
    }

    public function sendMailCatchmentAccepted($id, $date = null)
    {
        $subject = '¡Felicitaciones! Su solicitud ha sido aceptada';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = CatchmentInterview::where('id', $id)->first();
        $list_comment = Catchment::COLUMN_COMMENTS;

        if ($catchment) {
            $email = $catchment->email;
            $email = 'noemdb@gmail.com';
            if (validate_email($email)) {
                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.accepted',
                    'list_comment' => $list_comment,
                ];

                $catchment->status_notified = true;
                $catchment->save();

                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }
        return $dataEmail;
    }

    public function sendMailCatchmentStandby($id, $date = null)
    {
        $subject = 'Su representado ha sido incluido en la LISTA DE ESPERA';
        $time = ($date) ? $date : Carbon::now()->addSeconds(2);
        $dataEmail = array();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('1'); //DIRECTOR GENERAL
        $director = Autoridad::getTipoAuthority('2'); //DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $catchment = CatchmentInterview::where('id', $id)->first();
        $list_comment = Catchment::COLUMN_COMMENTS;

        if ($catchment) {

            $email = $catchment->email;
            // $email = 'noemdb@gmail.com';
            if (validate_email($email)) {
                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'catchment' => $catchment,
                    'institucion' => $institucion,
                    'director' => $director,
                    'autoridad' => $autoridad,
                    'toDate' => $toDate,
                    'view' => 'email.catchment.status_standby',
                    'list_comment' => $list_comment,
                ]; //dd($dataEmail);

                $catchment->status_notified = true;
                $catchment->save();

                ProcessNotifyCatchment::dispatch($dataEmail)->delay($time);
            }
        }
        return $dataEmail;
    }
}
