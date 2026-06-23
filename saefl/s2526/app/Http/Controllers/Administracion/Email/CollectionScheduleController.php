<?php

namespace App\Http\Controllers\Administracion\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Jobs\Email\ProcessNotifyCollect;
use App\Models\app\Cobranzas\CollCalendar;
use App\Models\app\Cobranzas\CollMessege;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use Illuminate\Console\Scheduling\Schedule;

use App\Jobs\Queue\Meta\SendWhatsAppMessageJob;

class CollectionScheduleController extends Controller
{
    public Schedule $schedule;

    public function bacthCollectionSendScheduleTestCalendar()
    {
        $schedule = new Schedule;

        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i') . ':00';
        // $coll_calendar = CollCalendar::active(true)->where('date',$date)->where('time',$time)->orderBy('time','asc')->first(); //dd($date,$time,$coll_calendar);
        $coll_calendar = CollCalendar::active(true)->where('date', $date)->orderBy('time', 'asc')->first(); //dd($date,$time,$coll_calendar);
        if ($coll_calendar) {
            $coll_political = $coll_calendar->coll_political; //dd($coll_political);
            if ($coll_political) {
                $jobSend = new CollectionScheduleController(); //dd($jobSend);
                $jobSend->bacthCollectionSendSchedule($coll_political->id, $coll_calendar->name);
            }
        }
    }

    public function bacthCollectionSendScheduleTest()
    {
        $coll_political = CollPolitical::collPoliticalNumberBills(1); //dd($coll_political);
        if ($coll_political) {
            return $this->bacthCollectionSendSchedule($coll_political->id, '1RA');
        }
    }
    public function bacthCollectionSendSchedule($id, $number = null, $status_whatsapp = false, $status_email = false)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $coll_messeges = $coll_political->coll_messeges;
        $representants = $coll_political->representants; //dd($representants);
        $time = Carbon::now();
        $sendMails = collect();
        $allEmails = array();
        foreach ($representants as $representant) {
            $ci_representant = $representant->ci_representant;
            $email = $representant->email;
            foreach ($coll_messeges as $coll_message) {
                $id = $coll_message->id;
                if ($coll_message->status == 'true') {
                    $allEmails[] = $email;
                    $subject = $number . ' ' . $coll_message->title;
                    $addressEmail = $this->collectionSendSchedule($representant->id, $coll_message, $time, $subject, $status_whatsapp, $status_email);
                    $time = $time->seconds(100);
                    $sendMails->push($addressEmail);
                }
            }
        }
        return $sendMails;
    }

    public function collectionSendSchedule($representant_id, $coll_message, ?Carbon $time = null, ?string $subject = null, $status_whatsapp = false, $status_email = false)
    {
        $representant = Representant::findOrFail($representant_id);
        // $except = ['8510239','8514287','8515023','9846151'];
        $except = [];

        $time = ($time) ? $time : Carbon::now()->addMinutes(1);
        $timeWhatsApp = $time->copy();
        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2'); //director
        $autoridad2 = Autoridad::getTipoAuthority('4'); //ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');
        $sendMail = null;
        $email = $representant->email;
        if (validate_email($email)) {
            $dataEmail = [
                'subject' => $subject,
                'address' => $email,
                'mailCCAddress' => env('MAIL_CC_ADDRESS_ADMON'),
                'representant' => $representant,
                'coll_message' => $coll_message,
                'institucion' => $institucion,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
                'toDate' => $toDate,
                'lastDate' => $lastDate
            ];
            if (!in_array($representant->ci_representant, $except)) {

                if ($status_email) {
                    ProcessNotifyCollect::dispatch($dataEmail)->delay($time);
                }

                //Encolar los mesnsajes de whatsApp
                if ($status_whatsapp) {
                    $status_whatsapp_verify = $representant->status_whatsapp_verify;
                    if ($status_whatsapp_verify) {
                        $whatsapp = $representant->whatsapp;
                        $phonePattern = '/^\d{11,12}$/';
                        $valid = preg_match($phonePattern, $whatsapp);
                        if ($valid) {
                            $template = 'notice_collection';
                            $ident = $representant->ci_representant;
                            SendWhatsAppMessageJob::dispatch($ident, $whatsapp, $template)->delay($timeWhatsApp);
                            $timeWhatsApp = $timeWhatsApp->seconds(30);
                        }
                    }
                }
            }
            $sendMail = $email;
            return $sendMail;
        }
    }
}
