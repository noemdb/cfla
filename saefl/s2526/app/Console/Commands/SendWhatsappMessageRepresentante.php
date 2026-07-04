<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SendWhatsappMessageService;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use Throwable;

class SendWhatsappMessageRepresentante extends Command
{
    /*
    * Comando dual para gestión de tokens Facebook: consulta estado actual con validación en tiempo real o genera nuevos tokens de larga duración.
    * Centraliza diagnóstico y renovación automática, integrado con el sistema WhatsApp Business para mantenimiento eficiente y sin errores.
    * php artisan whatsapp:send-bulk "14608133,14608133" --text="Mensaje urgente" --queue --template=general
    */
    protected $signature = 'whatsapp:send-bulk
                            {cises : CIs de representantes separados por coma}
                            {--template=general : Nombre de la plantilla}
                            {--text= : Texto personalizado para el parámetro {{2}}}
                            {--media=control : Tipo de media: control|admon}
                            {--queue : Forzar envío inmediato sin cola (por defecto usa cola)}';

    protected $description = 'Envía mensajes WhatsApp masivos a múltiples representantes usando el nuevo service';

    public function __construct(private SendWhatsappMessageService $whatsappService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $cisList = $this->argument('cises');
        $template = $this->option('template');
        $text = $this->option('text');
        $mediaType = $this->option('media');
        $useQueue = $this->option('queue');

        // Convertir string de CIs a array
        $cisArray = array_map('trim', explode(',', $cisList));

        $this->info("📤 Iniciando envío masivo a " . count($cisArray) . " representantes...");
        $this->info("📋 Plantilla: {$template}");
        $this->info("🖼️ Media: {$mediaType}");
        $this->info("🚀 Modo: " . ($useQueue ? "COLA (asíncrono)" : "INMEDIATO (síncrono)"));

        $validCount = 0;
        $invalidCount = 0;
        $invalidCis = [];

        $mediaId = $this->getMediaId($mediaType);

        foreach ($cisArray as $ci) {
            $representant = Representant::where('ci_representant', $ci)->first();

            if ($this->isValidRepresentant($representant)) {
                try {
                    // ✅ SOLUCIÓN: Enviar SOLO 2 parámetros como el FacebookTokenService
                    $bodyParams = [
                        $this->cleanText($representant->name), // {{1}} - Nombre
                        $text ?? 'Mensaje automático del sistema' // {{2}} - Texto personalizado
                    ];

                    $response = $this->whatsappService->sendDynamicTemplate(
                        to: $representant->whatsapp,
                        templateName: $template,
                        bodyParams: $bodyParams,
                        mediaId: $mediaId,
                        language: 'es_ES',
                        useQueue: $useQueue
                    );

                    $validCount++;

                    if ($useQueue) {
                        $this->line("✅ Encolado: CI {$ci} → {$representant->whatsapp}");
                    } else {
                        $messageId = $response['messages'][0]['id'] ?? 'unknown';
                        $this->line("✅ Enviado: CI {$ci} → {$representant->whatsapp} (ID: {$messageId})");
                    }
                } catch (Throwable $e) {
                    $invalidCount++;
                    $invalidCis[] = $ci;
                    $this->error("❌ Error con CI {$ci}: " . $e->getMessage());
                }
            } else {
                $invalidCount++;
                $invalidCis[] = $ci;
                $this->error("❌ No encontrado o sin teléfono: {$ci}");
            }
        }

        // Resumen
        $this->line(str_repeat('=', 60));
        $this->info("📊 RESUMEN DE ENVÍO MASIVO:");
        $this->info("✅ Representantes procesados: {$validCount}");
        $this->info("❌ Representantes con error: {$invalidCount}");

        if (!empty($invalidCis)) {
            $this->warn("📋 CIs con problemas: " . implode(', ', $invalidCis));
        }

        if ($useQueue) {
            $this->info("🚀 Jobs encolados correctamente");
            $this->info("💡 Ejecutar: php artisan queue:work");
        }
    }

    /**
     * Verifica si el representante es válido
     */
    private function isValidRepresentant(?Representant $representant): bool
    {
        return $representant &&
            $representant->whatsapp && // ✅ Usar el mismo campo que en el envío
            !empty(trim($representant->whatsapp));
    }

    /**
     * Obtiene el media ID según el tipo
     */
    private function getMediaId(string $mediaType): ?string
    {
        $institucion = Institucion::first();
        return $mediaType === 'admon'
            ? $institucion->facebook_media_id_admon
            : $institucion->facebook_media_id_control;
    }

    protected function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x20-\x7EñÑáéíóúÁÉÍÓÚüÜ¡¿]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
