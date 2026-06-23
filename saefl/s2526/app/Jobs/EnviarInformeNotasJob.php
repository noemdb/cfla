<?php

namespace App\Jobs;

use App\Mail\ReporteNotasEstudiant;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\ReporteNotasEstudiante;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Services\SendWhatsappMessageService;
use App\Models\app\Institucion;

class EnviarInformeNotasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $estudiant_id;
    protected $lapso_id;
    protected $overrideEmail;
    protected $sendWhatsappNotifications;

    public function __construct($estudiant_id, $lapso_id, $overrideEmail = null, $sendWhatsappNotifications = false)
    {
        $this->estudiant_id = $estudiant_id;
        $this->lapso_id = $lapso_id;
        $this->overrideEmail = $overrideEmail;
        $this->sendWhatsappNotifications = $sendWhatsappNotifications;
    }

    public function handle(SendWhatsappMessageService $whatsappService)
    {
        $estudiant = Estudiant::findOrFail($this->estudiant_id);

        $inscripcion = $estudiant->inscripcion;

        $representant = $estudiant->representant;

        $email = $this->overrideEmail ?? $representant->email ?? null;

        if (!$inscripcion) {
            Log::info("El estudiante {$estudiant->id} no tiene inscripción activa, no se enviará el boletín.");
            return;
        }

        // Generar PDF temporal
        $pdfInfo = $inscripcion->generatePdfPathBoletinPDFEmail($this->estudiant_id, $this->lapso_id);

        // Enviar correo
        Mail::to($email)->send(new ReporteNotasEstudiant($estudiant, $pdfInfo['path'], $pdfInfo['filename'], $this->lapso_id));

        // (Opcional) Borrar el archivo luego de enviar
        Storage::disk('public')->delete("tmp/PDF/{$pdfInfo['filename']}");

        // Enviar notificación WhatsApp
        if ($this->sendWhatsappNotifications && $representant->whatsapp && !empty(trim($representant->whatsapp))) {
            try {
                $institucion = Institucion::first();
                $mediaId = $institucion ? $institucion->facebook_media_id_control : null;

                $name = $this->cleanText($representant->name);
                $bodyParams = [
                    $name,
                    'Hemos enviado su Boletín de Notas a su correo electrónico.'
                ];

                $whatsappService->sendDynamicTemplate(
                    to: $representant->whatsapp,
                    templateName: 'general',
                    bodyParams: $bodyParams,
                    mediaId: $mediaId,
                    language: 'es_ES',
                    useQueue: false
                );

                Log::info("Notificación WhatsApp enviada a {$representant->whatsapp} para estudiante {$estudiant->id}");
            } catch (\Throwable $e) {
                Log::error("Error al enviar notificación WhatsApp para estudiante {$estudiant->id}: " . $e->getMessage());
            }
        }
    }

    protected function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x20-\x7EñÑáéíóúÁÉÍÓÚüÜ¡¿]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
