<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailjetService;

class TestMailjetCommand extends Command
{
    protected $signature = 'mailjet:test {email} {name}';
    protected $description = 'Prueba el servicio Mailjet';

    public function handle()
    {
        $mailjet = app(MailjetService::class);
        
        $email = $this->argument('email');
        $name = $this->argument('name');

        // Validación del email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("❌ El email '$email' no es válido");
            return;
        }

        try {
            $response = $mailjet->sendEmail([
                'To' => [
                    [
                        'Email' => $email,
                        'Name' => $name
                    ]
                ],
                'Subject' => 'Prueba desde Artisan Command 🚀',
                'TextPart' => 'Este es un correo de prueba enviado desde Laravel.',
                'HTMLPart' => view('email.test.mailjet', [
                    'name' => $name,
                    'date' => now()->format('d/m/Y H:i')
                ])->render()
            ]); // 

            if ($response['success']) {
                $this->info('✅ Correo enviado con éxito!');
                
                // Manejo más seguro de la respuesta
                if (isset($response['data']['Messages'][0]['MessageID'])) {
                    $this->line('Message ID: '.$response['data']['Messages'][0]['MessageID']);
                } else {
                    $this->line('Respuesta completa:');
                    dump($response['data']);
                }
            } else {
                $this->error('❌ Error al enviar el correo');
                $this->error('Status: '.$response['status']);
                $this->line('Respuesta completa:');
                dump($response['data']);
            }
        } catch (\Exception $e) {
            $this->error('⚠️ Excepción: '.$e->getMessage());
        }
    }
}