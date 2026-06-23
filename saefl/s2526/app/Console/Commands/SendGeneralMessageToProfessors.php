<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\app\Pescolar\Profesor;
use App\Services\SendWhatsappMessageService;
use App\Models\app\Institucion;

class SendGeneralMessageToProfessors extends Command
{
    protected $signature = 'whatsapp:send-general-professors {--queue} {--test=} {--ci_profesor=}';
    protected $description = 'Envía mensajes de bienvenida y credenciales a todos los profesores';

    protected SendWhatsappMessageService $whatsappService;

    public function __construct(SendWhatsappMessageService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Limpia el texto para WhatsApp: sin saltos de línea ni emojis.
     * Conserva negritas (*), cursivas (_), monospace (`).
     */
    protected function cleanText(string $text): string
    {
        // Eliminar emojis y caracteres fuera del rango básico
        $text = preg_replace('/[^\x20-\x7EñÑáéíóúÁÉÍÓÚüÜ¡¿]/u', '', $text);
        // Reemplazar saltos de línea y múltiples espacios por uno solo
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    public function handle()
    {
        $institucion = Institucion::first();
        $mediaId = $institucion?->facebook_media_id_control; // Header opcional

        $useQueue = $this->option('queue');
        $testNumber = $this->option('test');
        $ciProfesor = $this->option('ci_profesor');

        // 📌 Query optimizada: profesores activos con al menos una asignación en primer lapso
        $profesorsQuery = Profesor::where('status_active', true)
            ->whereHas('pevaluacions', function ($q) {
                $q->where('lapso_id', 1);
            })
            ->with([
                'pevaluacions' => function ($q) {
                    $q->where('lapso_id', 1)
                        ->with(['pensum.asignatura', 'seccion']);
                },
                'user'
            ]);

        if ($ciProfesor) {
            $profesorsQuery->where('ci_profesor', $ciProfesor);
            $this->info("🔎 Enviando mensaje SOLO al profesor con CI {$ciProfesor}");
        }

        if ($testNumber && ! $ciProfesor) {
            $profesors = $profesorsQuery->inRandomOrder()->limit(1)->get();
            $this->info("🔎 Modo TEST: se enviará SOLO 1 mensaje al número {$testNumber}");
        } else {
            $profesors = $profesorsQuery->get();
        }

        foreach ($profesors as $profesor) {
            $to = $testNumber ?: $profesor->whatsapp;

            if (! $to) {
                $this->warn("⚠️ Profesor sin WhatsApp: {$profesor->id} - {$profesor->name} {$profesor->lastname}");
                continue;
            }

            // 📌 Usuario y correo
            $username = $profesor->user->username ?? 'N/A';
            $correo   = $profesor->gsemail ?? 'N/A';

            // 📌 Asignaciones del primer lapso
            $asignaciones = $profesor->pevaluacions
                ->map(function ($eva) {
                    $nombre = $eva->pensum?->asignatura?->name ?? 'Asignatura';
                    $codigo = $eva->pensum?->asignatura?->code ?? 'N/A';

                    $seccion = $eva->seccion?->name ?? null;

                    $nombreCorto = mb_substr($nombre, 0, 10);
                    return "({$codigo} {$seccion})";
                })
                ->implode(' | ');

            if (empty($asignaciones)) {
                $asignaciones = "No tiene asignaciones registradas para el primer lapso.";
            }

            // 📌 Construcción de params del template
            $param1 = $this->cleanText("{$profesor->name} {$profesor->lastname}");

            $param2 = <<<EOT
Nos complace informarles que ya han sido asignados sus respectivos accesos al *SAEFL*, los cuales incluyen:
*Usuario SAEFL:* {$username} | *Correo:* {$correo} (Classroom) | *Asignaciones 1er Momento:* {$asignaciones}. Quienes han recibido nuevos correos institucionales usarán la contraseña por defecto .saefl.2526, los demás podrán ingresar con su clave habitual. Les invitamos a verificar sus credenciales y accesos. Ante cualquier duda, pueden comunicarse con la jefatura o coordinación correspondiente. *Recordatorio*: los docentes que aún no han participado en la inducción están convocados al segundo año del módulo de planificación.
EOT;



            $param2 = $this->cleanText($param2);

            // 🚨 Validar longitud máxima (1024 caracteres)
            if (strlen($param2) > 1000) {
                $param2 = substr($param2, 0, 1000) . '...';
            }

            try {
                $this->whatsappService->sendDynamicTemplate(
                    $to,
                    'general_profesors',
                    [$param1, $param2],
                    $mediaId,
                    'es_ES',
                    $useQueue
                );

                $this->info("✅ Mensaje enviado a {$to} ({$param1})");

                if ($testNumber || $ciProfesor) {
                    break; // En test o por CI → solo uno
                }
            } catch (\Exception $e) {
                $this->error("❌ Error con {$profesor->id}: " . $e->getMessage());
            }
        }
    }
}
