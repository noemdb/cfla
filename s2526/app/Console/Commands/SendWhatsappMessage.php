<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FacebookTokenService;
use Throwable;

class SendWhatsappMessage extends Command
{
    /**
     * Signature:
     *  ident     → obligatorio
     *  phone     → obligatorio
     * Opciones:
     *  --template=general
     *  --text="..."
     *  --media=control
     * php artisan whatsapp:send ident=14608133 phone=584145752242
     */
    protected $signature = 'whatsapp:send
                            {ident : CI del representante}
                            {phone : Número destino (E.164)}
                            {--template=general : Nombre de la plantilla a utilizar}
                            {--text="Texto de prueba, funcionalidad META API" : Texto que se enviará como parámetro {{2}}}
                            {--media=control : Tipo de media a usar: control|admon}';

    protected $description = 'Envía un mensaje de WhatsApp usando una plantilla (template)';

    public function __construct(private FacebookTokenService $tokenService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $ident   = $this->argument('ident');
        $phone   = $this->argument('phone');
        $template    = $this->option('template');
        $text        = $this->option('text');
        $mediaIdType = $this->option('media');

        try {
            $response = $this->tokenService->sendTemplateMessage(
                $ident,
                $phone,
                $template,
                $text,
                $mediaIdType
            );

            $this->info('Mensaje enviado correctamente.');
            $this->line(json_encode($response, JSON_PRETTY_PRINT));
        } catch (Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
