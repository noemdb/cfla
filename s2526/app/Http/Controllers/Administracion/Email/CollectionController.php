<?php

namespace App\Http\Controllers\Administracion\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\ProcessNotifyCollect;
use App\Mail\EmailMessage;
use App\Mail\Queue\Collection\EmailForQueuing;
use App\Mail\SendNotifyCollectPolitical;
use App\Models\app\Cobranzas\CollMessege;
use App\Models\app\Cobranzas\CollNivel;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class CollectionController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth','is_admon']);
    }

    public function bacthCollectionMessegeSend($id)
    {
        $coll_message = CollMessege::findOrFail($id); //dd($coll_message);
        //$datas = collect();
        if ($coll_message->status == 'true') {
            $coll_nivel = $coll_message->coll_nivel; //dd($coll_nivel);
            $coll_political = $coll_nivel->coll_political; //dd($coll_political);
            $representants = $coll_political->representants; //dd($representants);
            $time = ($coll_political->finicial) ? Carbon::parse($coll_political->finicial) : Carbon::now() ;
            // $time = Carbon::now() ;
            $count = null;
            foreach ($representants as $representant) {
                $time = $time->addSeconds(10); //addSeconds;
                $this->collectionSend($representant->id,$coll_message->id,$time);
                $count++;
                //$datas->push(['representant_id'=>$representant->id,'coll_message_id'=>$coll_message->id]);
            }
            $sesionMessege = ($count) ?  trans('email.sendEmail_ok').'. '.$count. ' mensajes enviados en cola para ser enviados': 'Ningún mensaje enviado' ;
            Session::flash('operp_ok',$sesionMessege);
        }
        //dd($datas);
        return redirect()->back();
    }

    public function bacthCollectionNivelSend($id)
    {
        $coll_nivel = CollNivel::findOrFail($id);
        $coll_political = $coll_nivel->coll_political; //dd($coll_political);
        $coll_mensseges = $coll_nivel->coll_mensseges; //dd($coll_mensseges);
        $representants = $coll_political->representants; //dd($representants);
        $time = ($coll_political->finicial) ? Carbon::parse($coll_political->finicial) : Carbon::now() ;
        // $time = Carbon::now() ;
        $count = null;
        foreach ($representants as $representant) {
            foreach ($coll_mensseges as $coll_message) {
                if ($coll_message->status == 'true') {
                    $time = $time->addSeconds(10); //addSeconds;
                    $this->collectionSend($representant->id,$coll_message->id,$time);
                    $count++;
                }
            }
        }
        $sesionMessege = ($count) ?  trans('email.sendEmail_ok').'. '.$count. ' mensajes enviados en cola para ser enviados': 'Ningún mensaje enviado' ;
        Session::flash('operp_ok',$sesionMessege);
        return redirect()->back();
    }

    public function bacthCollectionSend($id, $status_queuing = false)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $coll_messeges = $coll_political->coll_messeges; //dd('coll_messeges',$coll_messeges);
        $representants = $coll_political->representants; //dd($representants);
        $time = ($coll_political->finicial && $status_queuing) ? Carbon::parse($coll_political->finicial) : Carbon::now() ;
        $count = null;
        foreach ($representants as $representant) {
            foreach ($coll_messeges as $coll_message) {
                if ($coll_message->status == 'true') {
                    $this->collectionSend($representant->id,$coll_message->id,$time);
                    $time = $time->addSeconds(2); //dd($representant->id,$coll_message->id,$time);
                    $count++;
                }
            }
        }
        $sesionMessege = ($count) ?  trans('email.sendEmail_ok').'. '.$count. ' mensajes posicionados en la cola para ser enviados': 'Ningún mensaje enviado' ;
        Session::flash('operp_ok',$sesionMessege);
        return redirect()->back();
    }    

    public function collectionSend($representant_id,$messege_id, Carbon $time = null)
    {
        $representant = Representant::findOrFail($representant_id);

        // $except = ["3913918","3913918"];
        
        $coll_message = CollMessege::findOrFail($messege_id);

        $subject =  $coll_message->subject;

        $time = ($time) ? $time : Carbon::now()->addMinutes(1) ;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR

        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        $addressEmail = null;

        $allEmails = array();

        $email = $representant->email;

        if (!in_array($email,$allEmails)) {
            if (validate_email($email)) {
                $allEmails[]=$email;
                $dataEmail = [
                    'subject' => $subject,
                    // 'address' => $email,
                    'address' => "noemdb@gmail.com",
                    'mailCCAddress' => env('MAIL_CC_ADDRESS_ADMON', 'soporte.saefl@gmail.com'),
                    'representant' => $representant,
                    'coll_message' => $coll_message,
                    'institucion' => $institucion,
                    'autoridad1' => $autoridad1,
                    'autoridad2' => $autoridad2,
                    'toDate' => $toDate,
                    'lastDate' => $lastDate
                ]; //dd($dataEmail,$time);
                ProcessNotifyCollect::dispatch($dataEmail)->delay($time);
                $addressEmail .= $email.' | ';
                $time = $time->addMinutes(1);
            }   
        }

        //dd($emails);

        Session::flash('operp_ok',trans('email.sendEmail_ok').' - '.$addressEmail);
        return redirect()->back();
    }

}

