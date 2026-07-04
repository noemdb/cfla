<?php

namespace App\Http\Controllers\Administracion\Email\Catchment;

use App\Http\Controllers\Controller;
use App\Models\app\Enrollment\Catchment;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\General\Email\CatchmentController;
use App\Jobs\SendMessageJobMetaCatchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Institucion;
use App\Models\app\Integration\MetaResponse;
use App\Services\FacebookTokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CatchmentSendNotificationsController extends Controller
{

    public function sendMailNoticeRememberFirst()
    {
        $now = Carbon::now()->format('Y-m-d');
        $delay = 0; // Retraso acumulado en segundos
        $catchments = Catchment::where('day_appointment', '2025-04-04')
            ->where('status_active', true)
            ->get()
            // ->take(1)
        ; //dd($catchments);

        $startTime = Carbon::now()->addMinutes(5);
        // $startTime = Carbon::now();
        foreach ($catchments as $item) {
            $jobSend = new CatchmentController();
            $dataEmail = $jobSend->messegesSendMailNoticeGeneralRememberFirst($item->id, $startTime->copy()->addSeconds($delay));
            $delay += 60;
        }

        return response()->json(['message' => 'Correos en proceso de envío.']);
    }

    public function sendMailNoticeGenera()
    {
        $now = Carbon::now()->format('Y-m-d');
        $delay = 0; // Retraso acumulado en segundos
        $catchments = Catchment::where('day_appointment', '>=', $now)
            ->where('status_active', true)
            ->get()
            // ->take(1)
        ;

        $startTime = Carbon::now()->addMinutes(10);
        foreach ($catchments as $item) {
            $jobSend = new CatchmentController();
            $dataEmail = $jobSend->messegesSendMailNoticeGenera($item->id, $startTime->copy()->addSeconds($delay));
            $delay += 60;
        }

        return response()->json(['message' => 'Correos en proceso de envío.']);
    }

    public function sendMetaNotifications()
    {
        $catchments = Catchment::where('status_active', true)->get();
        $delay = 0; // Retraso acumulado en segundos

        // Hora inicial: 10 minutos después de la hora actual
        $startTime = Carbon::now()->addMinutes(10);

        foreach ($catchments as $item) {
            $representant_ci = $item->representant_ci;
            $representant_phone = $item->representant_phone;

            // Despachar el job con un retraso incremental
            SendMessageJobMetaCatchment::dispatch($representant_ci, $representant_phone)
                ->delay($startTime->copy()->addSeconds($delay));

            // Incrementar el retraso en 60 segundos para el siguiente mensaje
            $delay += 60;
        }

        return response()->json(['message' => 'Proceso de envío iniciado.']);
    }

    public function sendMailReminderNotice()
    {
        $catchments = Catchment::whereNull('grade')->get();

        $time = Carbon::now()->addSeconds(2);

        foreach ($catchments as $item) {
            $jobSend = new CatchmentController();
            $dataEmail = $jobSend->messegesSend($item->id, $time->addSeconds(40));
        }
    }

    public function sendMailInterviewReprogrammer()
    {
        $catchments = Catchment::getCatchmentReprogrammer(null, [7, 8, 11]);  //1er,2do y 5to Año;

        $datas = collect();

        $time = Carbon::now()->addSeconds(2);

        foreach ($catchments as $item) {
            $jobSend = new CatchmentController();
            $dataEmail = $jobSend->messegesSendInterviewReprogrammer($item->id, $time->addSeconds(40));
            $datas->push($item);
        }

        return $datas;
    }

    public function sendMailInterviewReprogrammerFaseOne()
    {
        $catchments = Catchment::getCatchmentReprogrammerFaseOne();  //dd($catchments);
        $datas = collect();
        $time = Carbon::now()->addSeconds(2);
        foreach ($catchments as $item) {
            $jobSend = new CatchmentController();
            $dataEmail = $jobSend->messegesSendInterviewReprogrammerFaseOne($item->id, $time->addSeconds(40));
            $datas->push($item);
        }
        return $datas;
    }

    public function sendMailCatchmentAccepted()
    {
        $catchments = CatchmentInterview::getAccepteds(); // dd($catchments);
        $datas = collect();
        $time = Carbon::now()->addSeconds(2);
        foreach ($catchments as $item) {
            $jobSend = new CatchmentController();
            $dataEmail = $jobSend->sendMailCatchmentAccepted($item->id, $time->addSeconds(40));
            $datas->push($item);
        }
        return $datas;
    }


    public function sendMessegeMetaTemplateGeneral(FacebookTokenService $tokenService, $ident, $phone)
    {
        try {
            // Validar identificador
            $catchment = Catchment::where('representant_ci', $ident)->firstOrFail();

            // Obtener datos del destinatario
            $name = '*' . $catchment->representant_name . ' ' . $catchment->representant_lastname . '*';
            $appointmentDate = $catchment->day_appointment ? f_date($catchment->day_appointment) : 'una fecha de su preferencia';

            // Construir el mensaje
            $text = "Le recordamos que su cita para el Censo Escolar 2025-2026 en el Colegio Fray Luis Amigo está programada para el {$appointmentDate} a las 2:00 PM. Su asistencia es fundamental para continuar con el proceso de inscripción.";
            $cleanText = $this->cleanText($text);

            // Configuración de la API
            $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph
            $accessToken = $tokenService->getAccessToken();
            $institucion = Institucion::first();
            $mediaId = ($institucion) ? $institucion->facebook_media_id_control : env('FACEBOOK_IMAGE_ACADEMIC_ID');

            // $phone = "584145752242";

            // Estructura de la solicitud
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone ?: '584145752242',
                    'type' => 'template',
                    'template' => [
                        'name' => 'general',
                        'language' => ['code' => 'es_ES'],
                        'components' => [
                            [
                                'type' => 'header',
                                'parameters' => [['type' => 'image', 'image' => ['id' => $mediaId]]],
                            ],
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $name],
                                    ['type' => 'text', 'text' => $cleanText],
                                ],
                            ],
                        ],
                    ],
                ]);

            // Manejar la respuesta
            if ($response->successful()) {
                return response()->json(['message' => 'Notificación enviada con éxito.', 'response' => $response->json()]);
            } else {
                Log::error('Error al enviar notificación:', ['response' => $response->json()]);
                return response()->json(['message' => 'Error al enviar la notificación.', 'response' => $response->json()], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Excepción al enviar notificación:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error interno del servidor.', 'error' => $e->getMessage()], 500);
        }
    }

    private function cleanText($text)
    {
        // Eliminar saltos de línea, tabulaciones y reducir espacios consecutivos
        $text = str_replace(["\n", "\t"], ' ', $text);
        $text = preg_replace('/\s{5,}/', '    ', $text);
        return trim($text);
    }
}
