<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SendWhatsappMessageService;
use App\Models\app\Institucion;

class TestWhatsappTemplate extends Command
{
    protected $signature = 'whatsapp:test-template
                            {phone : Número de destino en formato E164}
                            {template : Nombre del template}
                            {--params=* : Parámetros del body separados como argumentos}';

    protected $description = 'Envia un mensaje de prueba utilizando SendWhatsappMessageService';

    protected SendWhatsappMessageService $sendWhatsappMessageService;

    //php artisan whatsapp:test-template 584145752242 general --params="Juan Pérez" --params="Su pago fue procesado"

    public function __construct(SendWhatsappMessageService $sendWhatsappMessageService)
    {
        parent::__construct();
        $this->sendWhatsappMessageService = $sendWhatsappMessageService;
    }

    public function handle()
    {
        $phone    = $this->argument('phone');
        $template = $this->argument('template');
        $params   = $this->option('params');

        // puedes obtener media_id desde BD si quieres incluir una imagen
        $institucion = Institucion::first();
        $mediaId = $institucion?->facebook_media_id_control; // o facebook_media_id_control, facebook_media_id_admon, null

        try {
            $result = $this->sendWhatsappMessageService->sendDynamicTemplate(
                $phone,
                $template,
                $params,
                $mediaId, // puedes cambiarlo por null si no deseas el header
                // 'es_ES',
                // true
            );

            $this->info('✅ Mensaje enviado correctamente: ' . json_encode($result));
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
