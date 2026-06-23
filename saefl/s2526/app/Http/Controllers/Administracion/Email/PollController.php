<?php
namespace App\Http\Controllers\Administracion\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\PollMain\ProcessNotifyAttendee;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Poll\PollMain;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

class PollController extends Controller
{
    public function notifySend(PollMain $poll_main)
    {
        $subject = 'Invitación a participar en un proceso de consultas';
        $start   = $poll_main->start;
        $now     = Carbon::now();
        $time    = ($now > $start) ? $now->addMinute(2) : $start;
        $time    = Carbon::now()->setTime(13, 0, 0);
        $datas   = collect();

        $institucion = Institucion::select('institucions.name')->OrderBy('created_at', 'DESC')->first();
        $director    = Autoridad::getTipoAuthority('2');

        $toDate = Date::now()->format('d F Y');

        $attendees = $poll_main->attendees;

        $poll_questions = $poll_main->poll_questions;

        $addressEmail = null;
        foreach ($attendees as $attendee) {
            $poll_answers = $attendee->getPollAnswers($poll_main->id); //dd($poll_answers);
            if ($poll_answers->isEmpty()) {
                $allEmails = [];
                $email     = ($poll_main->status_test == "true") ? env('MAIL_ADDRESS_TESTER') : $attendee->email;
                if (! in_array($email, $allEmails)) {
                    if (validate_email($email)) {
                        $poll_token = $poll_main->poll_tokens->where('user_id', $attendee->id)->first();
                        if ($poll_token) {
                            $allEmails[]  = $email;
                            $dataEmail    = [
                                'mailCCAddress'  => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                                'subject'        => $subject,
                                'address'        => $email,
                                'poll_main'      => $poll_main,
                                'poll_questions' => $poll_questions,
                                'poll_token'     => $poll_token,
                                'attendee'       => $attendee,
                                'institucion'    => $institucion,
                                'director'       => $director,
                                'toDate'         => $toDate,
                                'view'           => 'email.polls.notify',
                            ];
                            // dd($dataEmail);
                            ProcessNotifyAttendee::dispatch($dataEmail)->delay($time);
                            $poll_token->update(['status_notifiled' => 'true']);
                            $addressEmail .= $email . ' | ';
                            $time          = $time->addSecond(20);
                            $datas->push($dataEmail);
                            // }

                        }
                    }
                }
            }
        }
        //dd($datas);
        return $datas;
    }

    public function sendIndividual(PollMain $poll_main, $user_id)
    {
        $subject = 'Invitación a participar en un proceso de consultas - [Individual]';
        // $time = $poll_main->start ;
        $time         = Carbon::now();
        $datas        = collect();
        $user         = User::find(Auth::id());
        $autoridad2   = null;
        $allEmails    = [];
        $addressEmail = null;

        $institucion = Institucion::select('institucions.name')->OrderBy('created_at', 'DESC')->first();
        $director    = Autoridad::getTipoAuthority('2'); //director

        $toDate = Date::now()->format('d F Y');

        $attendees = $poll_main->attendees; //dd($attendees);

        $attendee = ($attendees->count()) ? $attendees->where('id', $user_id)->first() : null; //dd($attendee);

        if ($attendee) {
            $email = $attendee->email;
            if (! in_array($email, $allEmails)) {
                if (validate_email($email)) {
                    $poll_questions = $poll_main->poll_questions;
                    $poll_token     = $poll_main->poll_tokens->where('user_id', $attendee->id)->first();
                    $allEmails[]    = $email;
                    $email          = ($poll_main->status_test == "true") ? env('MAIL_ADDRESS_TESTER', 'hello@test.com') : $email;
                    $dataEmail      = [
                        'mailCCAddress'  => env('MAIL_CC_ADDRESS_CONTROL', 'soporte.saefl@gmail.com'),
                        'subject'        => $subject,
                        'address'        => $email,
                        'poll_main'      => $poll_main,
                        'poll_questions' => $poll_questions,
                        'poll_token'     => $poll_token,
                        'attendee'       => $attendee,
                        'institucion'    => $institucion,
                        'director'       => $director,
                        'toDate'         => $toDate,
                        'view'           => 'email.polls.notify',
                    ];

                    ProcessNotifyAttendee::dispatch($dataEmail)->delay($time);
                    $addressEmail .= $email . ' | ';
                    $datas->push($dataEmail);
                }
            }
        }

        //dd($datas);

        return $datas;
    }

}
