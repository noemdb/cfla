<?php

namespace App\Services;

use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FacebookTokenService
{
    private const INSTITUTION_ID = 1;

    /**
     * Obtiene un access token válido desde BD (y lo refresca solo si ya venció).
     */
    public function getAccessToken(): string
    {
        $institucion = Institucion::find(self::INSTITUTION_ID);

        if (! $institucion || ! $institucion->facebook_access_token) {
            throw new Exception('El token de acceso no está disponible en BD.');
        }

        // ✅ Validar con la fecha almacenada en la BD
        if ($institucion->facebook_access_token_expire && Carbon::now()->lt($institucion->facebook_access_token_expire)) {
            // Aún está vigente → opcionalmente validar con Facebook:
            if ($this->validateAccessToken($institucion->facebook_access_token)) {
                return $institucion->facebook_access_token;
            }
        }

        // 🛠 Token vencido, intercambiar por uno nuevo
        $newToken = $this->exchangeLongLivedToken($institucion->facebook_access_token);

        return $newToken;
    }

    /**
     * Intercambia un token de corta duración por uno de larga duración y lo guarda en BD.
     */
    public function exchangeLongLivedToken(string $currentToken): string
    {
        $appId     = env('FACEBOOK_APP_ID');
        $appSecret = env('FACEBOOK_APP_SECRET');

        if (!$appId || !$appSecret) {
            throw new Exception('Faltan FACEBOOK_APP_ID o FACEBOOK_APP_SECRET en .env');
        }

        $response = Http::timeout(20)->get('https://graph.facebook.com/v23.0/oauth/access_token', [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $appId,
            'client_secret'     => $appSecret,
            'fb_exchange_token' => $currentToken,
        ]);

        if (!$response->successful()) {
            throw new Exception('Error al intercambiar el token: ' . $response->body());
        }

        $data = $response->json();
        $newToken = $data['access_token'] ?? null;
        $expiresIn = $data['expires_in']  ?? null;

        if (!$newToken) {
            throw new Exception('La respuesta no contiene access_token.');
        }

        // ➕ calcular fecha de expiración si viene expires_in
        $expireDate = $expiresIn ? Carbon::now()->addSeconds($expiresIn) : null;

        $this->persistNewToken($newToken, $expireDate);

        return $newToken;
    }

    /**
     * Valida un token vía /debug_token (solo se usa si aún no está vencido en BD).
     */
    public function validateAccessToken(string $accessToken): bool
    {
        if (!$accessToken) {
            return false;
        }

        $appId     = env('FACEBOOK_APP_ID');
        $appSecret = env('FACEBOOK_APP_SECRET');

        if (!$appId || !$appSecret) {
            throw new Exception('Faltan FACEBOOK_APP_ID o FACEBOOK_APP_SECRET en .env');
        }

        $response = Http::timeout(20)->get('https://graph.facebook.com/debug_token', [
            'input_token'  => $accessToken,
            'access_token' => "{$appId}|{$appSecret}",
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'] ?? [];
            return (bool)($data['is_valid'] ?? false);
        }

        return false;
    }

    /**
     * Lee token de BD.
     */
    private function getTokenFromDB(): string
    {
        $institucion = Institucion::find(self::INSTITUTION_ID);

        if (!$institucion || !$institucion->facebook_access_token) {
            throw new Exception('El token de acceso no está disponible en BD.');
        }

        return $institucion->facebook_access_token;
    }

    /**
     * Guarda el token (y la fecha de expiración) en BD.
     */
    private function persistNewToken(string $newAccessToken, $expireDate = null): void
    {
        DB::transaction(function () use ($newAccessToken, $expireDate) {
            $institucion = Institucion::find(self::INSTITUTION_ID);
            if (!$institucion) {
                throw new Exception('Institución no encontrada para guardar el token.');
            }
            $institucion->facebook_access_token = $newAccessToken;
            $institucion->facebook_access_token_date = Carbon::now();

            if ($expireDate) {
                $institucion->facebook_access_token_expire = $expireDate;
            }

            $institucion->save();
        });
    }

    /**
     * Envía mensaje usando template de WhatsApp.
     */
    public function sendTemplateMessage(
        string $ident,
        string $phone = '584145752242',
        string $template = 'general',
        string $text = 'Mensaje automático sin contenido asignado. Gracias por su atención.',
        string $mediaIdType = 'control'
    ): array {
        $representant = Representant::where('ci_representant', $ident)->first();
        if (! $representant) {
            throw new Exception("Representante con CI {$ident} no encontrado.");
        }

        $to   = $phone;
        $name = $representant->name;

        // ⚡ Obtener accessToken
        $accessToken = $this->getAccessToken();

        // Validar / recargar media IDs si fuera necesario
        $this->validateAndRefreshMediaIds($accessToken);

        $institucion = Institucion::first();
        if (! $institucion) {
            throw new Exception("No se encontró Institución");
        }

        $mediaId = ($mediaIdType === 'admon')
            ? $institucion->facebook_media_id_admon
            : $institucion->facebook_media_id_control;

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $to,
            'type' => 'template',
            'template' => [
                'name'     => $template,
                'language' => ['code' => 'es_ES'],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type'  => 'image',
                                'image' => ['id' => $mediaId],
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $name],
                            ['type' => 'text', 'text' => $text]
                        ]
                    ]
                ]
            ]
        ];

        $url = env('FACEBOOK_URL');

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if (! $response->successful()) {
            throw new \Exception('Error al enviar mensaje: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Valida los media_id y los vuelve a generar si fueron eliminados.
     */
    public function validateAndRefreshMediaIds(string $accessToken): array
    {
        $institucion = Institucion::first();
        if (! $institucion) {
            throw new \Exception("No se encontró registro de Institución");
        }

        $phoneId = env('FACEBOOK_NUM_ID');

        $isMediaIdValid = function (string $mediaId) use ($accessToken): bool {
            if (! $mediaId) {
                return false;
            }
            $check = Http::withToken($accessToken)
                ->get("https://graph.facebook.com/v23.0/{$mediaId}");
            return $check->successful();
        };

        $needsSave = false;

        if (! $isMediaIdValid($institucion->facebook_media_id_admon)) {
            $response = Http::withToken($accessToken)
                ->attach('file', file_get_contents(public_path('images/brand/saefl/bgBlankCFLAAdmon.jpg')), 'image.jpg')
                ->post("https://graph.facebook.com/v23.0/{$phoneId}/media", [
                    'messaging_product' => 'whatsapp',
                    'type' => 'image',
                ]);
            $institucion->facebook_media_id_admon = $response->json()['id'] ?? null;
            $needsSave = true;
        }

        if (! $isMediaIdValid($institucion->facebook_media_id_control)) {
            $response = Http::withToken($accessToken)
                ->attach('file', file_get_contents(public_path('images/brand/saefl/bgBlankCFLAControl.jpg')), 'image.jpg')
                ->post("https://graph.facebook.com/v23.0/{$phoneId}/media", [
                    'messaging_product' => 'whatsapp',
                    'type' => 'image',
                ]);
            $institucion->facebook_media_id_control = $response->json()['id'] ?? null;
            $needsSave = true;
        }

        if ($needsSave) {
            $institucion->save();
        }

        return [
            'facebook_media_id_admon'   => $institucion->facebook_media_id_admon,
            'facebook_media_id_control' => $institucion->facebook_media_id_control
        ];
    }

    /**
     * Verifica si el token está por expirar (1 día antes) y lo renueva si es necesario
     */
    public function checkAndRenewTokenIfNeeded(): bool
    {
        $institucion = Institucion::find(self::INSTITUTION_ID);

        if (!$institucion || !$institucion->facebook_access_token) {
            return false;
        }

        // Verificar si el token expira en menos de 24 horas
        if (
            $institucion->facebook_access_token_expire &&
            Carbon::now()->addDay()->gt($institucion->facebook_access_token_expire)
        ) {

            Log::info('Facebook token cerca de expirar, renovando automáticamente');

            try {
                $newToken = $this->exchangeLongLivedToken($institucion->facebook_access_token);
                Log::info('Facebook token renovado exitosamente');
                return true;
            } catch (Exception $e) {
                Log::error('Error al renovar token automáticamente: ' . $e->getMessage());
                return false;
            }
        }

        Log::info('Facebook token aún vigente, no requiere renovación');
        return false;
    }
}
