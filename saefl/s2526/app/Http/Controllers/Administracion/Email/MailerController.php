<?php

namespace App\Http\Controllers\Administracion\Email;

use App\Http\Controllers\Controller;
use App\Jobs\Email\ProcessNotifyMailer;
use App\Jobs\Queue\Meta\SendWhatsAppMessageJob;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\SenderMailer\Mailed;
use App\Models\app\SenderMailer\Mailer;
use Jenssegers\Date\Date;
/////////////////////////////////////////////////////////////
use App\Models\app\Estudiante\Enrollment;
use App\Services\SendEmailRotationService;
use Carbon\Carbon;

class MailerController extends Controller
{
    protected $emailRotationService;

    public function __construct()
    {
        $this->emailRotationService = app(SendEmailRotationService::class);
    }

    public function messegesSend(Mailer $mailer)
    {
        $subject = $mailer->subject;

        $time = $timeWhatsApp = $mailer->status_test === 'true' ? Carbon::now() : Carbon::parse($mailer->date);

        $datas = collect();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $director = Autoridad::getTipoAuthority('2'); //director

        $autoridad1 = Autoridad::getTipoAuthority('2'); //director
        $autoridad2 = Autoridad::getTipoAuthority('1'); //ACADEMICO

        $autoridad = $mailer->autoridad;

        $mail_cc_address = $autoridad->mail_cc_address ?? env('MAIL_CC_ADDRESS_TESTER') ?? env('MAIL_FROM_ADDRESS');

        $toDate = Date::now()->format('d F Y');

        $representants = $mailer->representants;

        //status_test
        $representants = ($mailer->status_test == 'true') ? $representants->take(5) : $representants;

        $addressEmail = null;
        foreach ($representants as $representant) {

            $allEmails = array();

            //status_test
            $email = ($mailer->status_test == 'true') ? env('MAIL_ADDRESS_TESTER') : $representant->email;
            // $email = 'noemdb@gmail.com';

            if (!in_array($email, $allEmails)) {
                if (validate_email($email)) {
                    $mailed = Mailed::where('mailer_id', $mailer->id)->where('representant_id', $representant->id)->first();
                    if (empty($mailed)) {

                        if ($mailer->status_email) {
                            $allEmails[] = $email;
                            $dataEmail = [
                                'mailCCAddress' => $mail_cc_address,
                                'subject' => $subject,
                                'address' => $email,
                                'representant' => $representant,
                                'mailer' => $mailer,
                                'institucion' => $institucion,
                                'director' => $director,
                                'autoridad' => $autoridad,
                                'autoridad1' => $autoridad1,
                                'autoridad2' => $autoridad2,
                                'toDate' => $toDate,
                                'view' => 'email.mailers.control.messege',
                            ];
                            ProcessNotifyMailer::dispatch($dataEmail)->delay($time);
                            $available_at = $time->timestamp;
                            $arr = [
                                'mailer_id' => $mailer->id,
                                'representant_id' => $representant->id,
                                'status' => 'true',
                                'available_at' => $available_at,
                            ];
                            $mailed = Mailed::create($arr);
                            $addressEmail .= $email . ' | ';

                            $time = $time->addSeconds(25);
                            $datas->push($dataEmail); //dd($datas,$time);
                        }
                    }

                    $general = $mailer->general;
                    if ($mailer->status_whatsapp && isset($general)) {
                        //Encola los mesnsajes de whatsApp
                        $ident = $representant->ci_representant;
                        $whatsapp = $representant->whatsapp;
                        $template = $mailer->template ?? 'notifications_coexistence'; //general,  notifications_agreement, notifications_coexistence
                        SendWhatsAppMessageJob::dispatch($ident, $whatsapp, $template, $general)->delay($timeWhatsApp);
                        $timeWhatsApp = $timeWhatsApp->addMinutes(1);
                    }
                }
            }
        }
        return $datas;
    }

    // método para envío con rotación
    public function messegesSendWithRotation(Mailer $mailer)
    {
        $subject = $mailer->subject;
        $time = $timeWhatsApp = $mailer->status_test === 'true' ? Carbon::now() : Carbon::parse($mailer->date);
        $emailsData = collect();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $director = Autoridad::getTipoAuthority('2'); // director
        $autoridad1 = Autoridad::getTipoAuthority('2'); // director
        $autoridad2 = Autoridad::getTipoAuthority('1'); // ACADEMICO
        $autoridad = $mailer->autoridad;
        $mail_cc_address = $autoridad->mail_cc_address ?? env('MAIL_CC_ADDRESS_TESTER') ?? env('MAIL_FROM_ADDRESS');
        $toDate = Date::now()->format('d F Y');

        $representants = $mailer->representants;
        $representants = ($mailer->status_test == 'true') ? $representants->take(5) : $representants;

        $allEmails = array();
        $processedEmails = 0;
        $errors = [];

        // $representants = $representants->take(5);

        foreach ($representants as $representant) {
            $email = ($mailer->status_test == 'true') ? env('MAIL_ADDRESS_TESTER') : $representant->email;

            if (!in_array($email, $allEmails) && validate_email($email)) {
                $mailed = Mailed::where('mailer_id', $mailer->id)
                    ->where('representant_id', $representant->id)
                    ->first();

                if (empty($mailed) && $mailer->status_email) {
                    $allEmails[] = $email;

                    // Preparar datos para el servicio de rotación
                    // $email = 'tester.saefl@gmail.com';
                    // $mail_cc_address = 'tester.saefl@gmail.com';
                    $emailData = [
                        'to' => $email,
                        'to_name' => $representant->name_full,
                        'subject' => $subject,
                        'html' => $this->renderEmailContent($mailer, $representant, $institucion, $director, $autoridad, $autoridad1, $autoridad2, $toDate),
                        'text' => strip_tags($this->renderEmailContent($mailer, $representant, $institucion, $director, $autoridad, $autoridad1, $autoridad2, $toDate)),
                        'cc' => $mail_cc_address,
                        'collection_political_id' => $mailer->id,
                        'representant_ci' => $representant->ci_representant,
                        'message_type' => 'institutional_notification'
                    ];

                    // Usar el servicio de rotación para programar el email
                    $result = $this->emailRotationService->queueRotationEmail($emailData, $time);

                    if ($result['success']) {
                        // Crear registro en Mailed
                        $mailed = Mailed::create([
                            'mailer_id' => $mailer->id,
                            'representant_id' => $representant->id,
                            'status' => 'queued_rotation',
                            'available_at' => $time->timestamp,
                            'service_provider' => $result['service'] ?? 'rotation',
                            'mail_log_id' => $result['mail_log_id'] ?? null
                        ]);

                        $emailsData->push($emailData);
                        $processedEmails++;
                        $time = $time->addMinutes(2); // Incrementar tiempo para el siguiente
                    } else {
                        $errors[] = [
                            'email' => $email,
                            'error' => $result['message']
                        ];
                    }
                }

                // Manejar WhatsApp si está habilitado
                // if ($mailer->status_whatsapp && isset($mailer->general)) {
                //     $ident = $representant->ci_representant;
                //     $whatsapp = $representant->whatsapp;
                //     $template = $mailer->template ?? 'notifications_coexistence';

                //     SendWhatsAppMessageJob::dispatch($ident, $whatsapp, $template, $mailer->general)
                //                         ->delay($timeWhatsApp);
                //     $timeWhatsApp = $timeWhatsApp->addMinutes(1);
                // }
            }
        }

        return [
            'success' => $processedEmails > 0,
            'total_emails' => $processedEmails,
            'service' => 'rotation_service',
            'errors' => $errors,
            'message' => $processedEmails > 0 ?
                "Se programaron {$processedEmails} emails exitosamente" :
                "No se pudieron programar emails"
        ];
    }

    // Método auxiliar para renderizar el contenido del email
    private function renderEmailContent($mailer, $representant, $institucion, $director, $autoridad, $autoridad1, $autoridad2, $toDate)
    {
        return view('email.mailers.control.messege', compact(
            'mailer',
            'representant',
            'institucion',
            'director',
            'autoridad',
            'autoridad1',
            'autoridad2',
            'toDate'
        ))->render();
    }
}
