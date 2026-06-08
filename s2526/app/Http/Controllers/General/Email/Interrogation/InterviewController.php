<?php

namespace App\Http\Controllers\General\Email\Interrogation;

use App\Http\Controllers\Controller;
use App\Jobs\Email\Interrogation\ProcessNotifyInterview;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\interrogation\Interview;
use App\Models\app\interrogation\InterviewAnswer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class InterviewController extends Controller
{
    public function messegesSend($user_id, $interview_id)
    {
        $subject = 'Ticket de participación';
        $time = Carbon::now()->addSeconds(20);
        $dataEmail = Array();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $director = Autoridad::getTipoAuthority('2');//DIRECTOR GENERAL

        $toDate = Date::now()->format('d F Y');

        $user = User::findOrFail($user_id);
        $interview = Interview::findOrFail($interview_id);
        $interview_questions = $interview->getAnsweredQuestions($user_id);

        $email = $user->email;
        // $email = 'tester.saefl@gmail.com';
        if (validate_email($email)) {
            $dataEmail = [
                'mailCCAddress' => env('MAIL_CC_ADDRESS_ADMON', 'soporte.saefl@gmail.com'),
                'subject' => $subject,
                'address' => $email,
                'user' => $user,
                'interview' => $interview,
                'interview_questions' => $interview_questions,
                'institucion' => $institucion,
                'director' => $director,
                'toDate' => $toDate,
                'view' => 'email.interviews.messege',
            ];
            ProcessNotifyInterview::dispatch($dataEmail)->delay($time); 
        }

        return $dataEmail;

    }
}
