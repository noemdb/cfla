<?php

namespace App\Jobs\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\MailjetService;
use Illuminate\Support\Facades\Log;

class ProcessNotifyCollectJetMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $mailjet = app('mailjet');
            
            // Preparar contenido HTML
            $htmlContent = view($this->data['view'], [
                'representant' => $this->data['representant'],
                'coll_message' => $this->data['coll_message'],
                'institucion' => $this->data['institucion'],
                'autoridad1' => $this->data['autoridad1'],
                'autoridad2' => $this->data['autoridad2'],
                'toDate' => $this->data['toDate'],
                'lastDate' => $this->data['lastDate'],
                'subject' => $this->data['subject']
            ])->render();

            // Enviar email via Mailjet
            $response = $mailjet->sendEmail([
                'To' => [
                    [
                        'Email' => $this->data['address'],
                        'Name' => $this->data['representant']->full_name
                    ]
                ],
                'Cc' => [
                    [
                        'Email' => $this->data['mailCCAddress'],
                        'Name' => 'Administración'
                    ]
                ],
                'Subject' => $this->data['subject'],
                'HTMLPart' => $htmlContent,
                'TextPart' => strip_tags($htmlContent)
            ]);

            if (!$response['success']) {
                throw new \Exception("Mailjet API error: " . json_encode($response['data']));
            }

            Log::info("Email enviado via Mailjet a {$this->data['address']}", [
                'message_id' => $response['data']['Messages'][0]['MessageID'] ?? 'unknown',
                'subject' => $this->data['subject']
            ]);

        } catch (\Exception $e) {
            Log::error("Error en ProcessNotifyCollectJetMail: " . $e->getMessage(), [
                'email' => $this->data['address'] ?? 'unknown',
                'error' => $e->getTraceAsString()
            ]);
            throw $e; // Para permitir reintentos
        }
    }
}