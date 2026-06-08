<?php

namespace App\Models\app\Meta;

use App\Models\app\Estudiante\Representant;
use App\Services\FacebookTokenService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WebhookMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',               // Número de WhatsApp que envió el mensaje
        'to',                 // Número de destino (tu número de negocio)
        'wa_id',              // ID de WhatsApp del contacto
        'profile_name',       // Nombre del perfil del contacto
        'message_id',         // ID único del mensaje
        'body',               // Contenido del mensaje
        'type',               // Tipo de mensaje (texto, imagen, etc.)
        'timestamp',          // Timestamp del mensaje
        'messaging_product',  // Producto de mensajería (WhatsApp)
        'phone_number_id',    // ID del número de teléfono asociado
        'metadata',           // Datos adicionales en formato JSON
    ];

    protected $dates = ['created_at','updated_at','timestamp'];

    const COLUMN_COMMENTS = [
        'from' => 'Num. Emisor',
        'to' => 'Num. Destinatario',
        'wa_id' => 'ID único de WhatsApp del contacto',
        'profile_name' => 'Nombre del perfi',
        'message_id' => 'ID único del mensaje',
        'body' => 'Mensaje',
        'type' => 'Tipo de mensaje (texto, imagen, audio, etc.)',
        'timestamp' => 'Fecha y hora',
        'messaging_product' => 'Producto de mensajería (por defecto, WhatsApp)',
        'phone_number_id' => 'ID del número de teléfono asociado a tu negocio',
        'metadata' => 'Datos adicionales relacionados con el mensaje (en formato JSON)',
        'answer' => 'Autorespuesta',
        'representant' => 'Representante',
    ];

    public function getRepresentantAttribute()
    {
        return Representant::query()
            ->select('representants.*')
            ->join('webhook_messages', 'webhook_messages.from', '=', 'representants.whatsapp')
            ->where('representants.whatsapp',$this->from)
            ->orderBy('webhook_messages.created_at','desc')
            ->first();
    }

    public function getAnswerAttribute()
    {
        return self::query()
            ->select('webhook_messages.*')
            ->where('webhook_messages.wa_id','wa_id.'.$this->wa_id)
            ->first();
    }

    public static function getAllContacts()
    {
        return self::query()
            ->select('webhook_messages.*')
            ->selectRaw('CONCAT(representants.ci_representant, " - ", representants.name, " ", webhook_messages.profile_name) as profile_fullname')
            ->leftJoin('representants', 'webhook_messages.from', '=', 'representants.whatsapp')
            ->whereNotNull('representants.ci_representant') // Excluir registros donde ci_representant sea nulo
            ->whereNotNull('representants.name') // Excluir registros donde name sea nulo
            ->whereNotNull('webhook_messages.profile_name') // Excluir registros donde profile_name sea nulo
            ->orderBy('webhook_messages.timestamp')
            ->groupBy('webhook_messages.profile_name')
            ->get();
    }

    public static function listContacts()
    {
        return self::getAllContacts()->pluck('profile_fullname', 'from');
    }

    public function processImageMessage()
    {
        $body = json_decode($this->body, true);
        $mediaId = $body['media_id'];
        $caption = $body['caption'] ?? 'Sin descripción';

        $tokenService = New FacebookTokenService;
        $accessToken = $tokenService->getAccessToken();

        // Obtener la URL del medio
        $url = "https://graph.facebook.com/v23.0/{$mediaId}"; //https://graph.facebook.com/v23.0/
        $response = Http::withToken($accessToken)->get($url);

        if ($response->successful()) {
            $mediaUrl = $response->json()['url'];

            // Descargar la imagen
            $imageResponse = Http::withToken($accessToken)->get($mediaUrl);

            if ($imageResponse->successful()) {
                $fileName = "image_" . now()->timestamp . ".jpg";
                $url = Storage::disk('public')->put("tmp/{$fileName}", $imageResponse->body());

                return view('meta.show_image', [
                    'mediaId' => $mediaId,
                    'caption' => $caption,
                    'image_url' => Storage::disk('public')->url("tmp/{$fileName}"),
                ]);
            } else {
                return 'Img no encontrada.';
            }
        } else {
            return 'Img no encontrada.';
        }
    }

    public static function getRepresentantForFrom($from)
    {
        return Representant::query()
            ->select('representants.*')
            ->join('webhook_messages', 'webhook_messages.from', '=', 'representants.whatsapp')
            ->where('representants.whatsapp',$from)
            ->orderBy('webhook_messages.created_at','desc')
            ->first();
    }


}
/*
from: Número del usuario que envió el mensaje.
to: Número de destino (el número de tu negocio).
wa_id: Identificador único de WhatsApp del contacto.
profile_name: Nombre del perfil del contacto que envió el mensaje.
message_id: Identificador único del mensaje.
body: Contenido del mensaje (si es texto).
type: Tipo de mensaje. Por ejemplo, text, image, audio.
timestamp: Fecha y hora del mensaje.
messaging_product: Producto de mensajería (en este caso, siempre será whatsapp).
phone_number_id: ID del número de teléfono asociado.
metadata: JSON con información adicional (como datos de metadata).
*/
