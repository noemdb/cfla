<?php

namespace App\Http\Controllers\General\Email;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Poll\PollToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

use App\Jobs\Email\ProcessNotifyPoll;

class PollController extends Controller
{
    public function messegesSend($token)
    {
        $subject = 'Ticket de participación';
        $time = Carbon::now()->addSeconds(20);
        $dataEmail = Array();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $director = Autoridad::getTipoAuthority('2');//DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $poll_token =PollToken::where('token',$token)->first();
        $poll_main = $poll_token->poll_main;
        $poll_questions = $poll_main->poll_questions;

        if ($poll_token) {

            $user = $poll_token->user;

            $attendees = $poll_main->attendees;

            $attendee = ($attendees->count()) ? $attendees->where('id',$user->id)->first() : null; //dd($attendee);

            if (empty($attendee)) {
                if ($user->IsEstudiant()) {
                    $estudiant = $user->estudiant;
                    if ($estudiant) {
                        $representant = $estudiant->representant;
                        $attendee = ($attendees->count()) ? $attendees->where('id',$representant->user_id)->first() : null; //dd($attendee);
                        if ($attendee) {
                            $attendee->fullname = $attendee->fullname.' - EST: '.$estudiant->fullname;
                        }
                    }
                }
            }

            $email = $attendee->email;

            if (validate_email($email)) {

                $email = ($poll_main->status_test == "true") ? env('MAIL_ADDRESS_TESTER','hello@test.com') : $email;

                $dataEmail = [
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_ADMON', 'soporte.saefl@gmail.com'),
                    'subject' => $subject,
                    'address' => $email,
                    'attendee' => $attendee,
                    'poll_token' => $poll_token,
                    'poll_main' => $poll_main,
                    'poll_questions' => $poll_questions,
                    'institucion' => $institucion,
                    'director' => $director,
                    'toDate' => $toDate,
                    'view' => 'email.polls.messege',
                ];
                ProcessNotifyPoll::dispatch($dataEmail)->delay($time);
            }
        }

        return $dataEmail;

    }
}
