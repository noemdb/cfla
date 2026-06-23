<?php

namespace App\Http\Controllers\Administracion\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\ProcessNotifyCollectJetMail;
use App\Jobs\Queue\Meta\SendWhatsAppMessageJob;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;

class CollectionScheduleEmailJetController extends Controller
{
    public $delay = 120; // Tiempo de espera entre envíos en segundos
    public $limit_mail = 170; // Límite de envíos por día
    public $view = 'email.collections.messege'; // Vista para el correo electrónico
    public $service = ['brevo','jetmail','sendpulse','resend']; // Servicio de correos transaccionales

    public function batchCollectionSendSchedule($id, $number = null, $status_whatsapp = false, $status_email = false)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $coll_messages = $coll_political->coll_messeges;
        $representants = $coll_political->representants;
        // $representants = $coll_political->representants->take(1);
        
        $time = Carbon::now();
        $sendMails = collect();
        $count = 0;
        $addDay = 0;
        
        foreach ($representants as $representant) {

            if ($count >= $this->limit_mail) {
                $count = 0;
                $addDay++;
                $time = Carbon::today()->addDays($addDay)->setTime(6, 0);
            }
            
            foreach ($coll_messages as $coll_message) {
                if ($coll_message->status == 'true') {
                    $subject = $number . ' ' . $coll_message->title;
                    $this->processRepresentantNotification(
                        $representant, 
                        $coll_message, 
                        $time, 
                        $subject, 
                        $status_whatsapp, 
                        $status_email
                    );
                    $time = $time->addSeconds(120); // Espaciado entre notificaciones
                    $count++;
                }
            }
        }
        
        return $sendMails;
    }

    protected function processRepresentantNotification($representant, $coll_message, $time, $subject, $status_whatsapp, $status_email)
    {
        $except = [/* tu lista de CIs excluidos */];
        
        if (in_array($representant->ci_representant, $except)) {
            return null;
        }

        // Configuración común
        $institucion = Institucion::latest()->first();
        $autoridad1 = Autoridad::getTipoAuthority('2'); // director
        $autoridad2 = Autoridad::getTipoAuthority('4'); // administrador
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        // Procesar email si está activado y es válido
        if ($status_email && validate_email($representant->email)) {
            $this->queueMailjetEmail(
                $representant,
                $coll_message,
                $subject,
                $institucion,
                $autoridad1,
                $autoridad2,
                $toDate,
                $lastDate,
                $time // Pasamos el tiempo para el delay del job
            );
        }

        // Procesar WhatsApp si está activado y es válido
        if ($status_whatsapp && $representant->status_whatsapp_verify) {
            $this->queueWhatsAppNotification(
                $representant,
                $time
            );
        }
    }

    protected function queueMailjetEmail($representant, $coll_message, $subject, $institucion, $autoridad1, $autoridad2, $toDate, $lastDate, $time)
    {
        $dataEmail = [
            'view' => $this->view,
            'subject' => $subject,
            'address' => $representant->email,
            // 'address' => 'noemdb@gmail.com',
            'mailCCAddress' => env('MAIL_CC_ADDRESS_ADMON'),
            'representant' => $representant,
            'coll_message' => $coll_message,
            'institucion' => $institucion,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
            'toDate' => $toDate,
            'lastDate' => $lastDate
        ];

        // Usar el Job específico para Mailjet
        ProcessNotifyCollectJetMail::dispatch($dataEmail)->delay($time);
    }    

    protected function queueWhatsAppNotification($representant, $time)
    {
        $whatsapp = $representant->whatsapp;
        $phonePattern = '/^\d{11,12}$/';
        
        if (preg_match($phonePattern, $whatsapp)) {
            SendWhatsAppMessageJob::dispatch(
                $representant->ci_representant,
                $whatsapp,
                'notice_collection'
            )->delay($time);
        }
    }

    protected function sendMailjetEmail($representant, $coll_message, $subject, $institucion, $autoridad1, $autoridad2, $toDate, $lastDate)
    {
        $mailjet = app('mailjet');
        
        $htmlContent = view($this->view, [
            'view' => $this->view,
            'representant' => $representant,
            'coll_message' => $coll_message,
            'institucion' => $institucion,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
            'toDate' => $toDate,
            'lastDate' => $lastDate,
            'subject' => $subject
        ])->render();

        $response = $mailjet->sendEmail([
            'To' => [
                [
                    'Email' => $representant->email,
                    'Name' => $representant->full_name
                ]
            ],
            'Cc' => [
                [
                    'Email' => env('MAIL_CC_ADDRESS_ADMON'),
                    'Name' => 'Administración'
                ]
            ],
            'Subject' => $subject,
            'HTMLPart' => $htmlContent,
            'TextPart' => strip_tags($htmlContent)
        ]);

        // Registrar el resultado
        if ($response['success']) {
            Log::info("Mailjet email sent to {$representant->email}", [
                'message_id' => $response['data']['Messages'][0]['MessageID'] ?? 'unknown',
                'subject' => $subject
            ]);
        } else {
            Log::error("Mailjet email failed to {$representant->email}", [
                'error' => $response['data'] ?? 'No response data',
                'status' => $response['status'] ?? 'unknown'
            ]);
        }
    }
}