<?php

namespace App\Console\Commands;

use App\Models\app\Institucion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RefreshFacebookToken extends Command
{
    protected $signature = 'facebook:refresh-token';
    protected $description = 'Renueva automáticamente el token de acceso de Facebook';

    public function handle()
    {
        // Obtén el registro de la institución (puedes ajustar el filtro si hay varias instituciones)
        $institucion = Institucion::first();

        if (!$institucion || !$institucion->facebook_access_token) {
            $this->error('No se encontró un token de acceso para renovar.');
            return;
        }

        $appId = env('FACEBOOK_APP_ID');
        $appSecret = env('FACEBOOK_APP_SECRET');
        $wabaId = env('FACEBOOK_NUM_ID');
        $currentToken = $institucion->facebook_access_token; //dd($appId,$appSecret,$currentToken);

        $response = Http::get('https://graph.facebook.com/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $currentToken,
        ]);

        if ($response->successful()) {
            $newToken = $response->json('access_token');
            $responseTier = Http::withToken($newToken)->get("https://graph.facebook.com/v23.0/{$wabaId}");
            $messageTier = 'Unknown';
            if ($responseTier->successful()) {
                $data = $responseTier->json();
                // $messageTier = $data['message_tier'] ?? 'Unknown';
                $messageTier = $data ?? 'Unknown';
            } else {
                $messageTier = "Error: " . $responseTier->body();
            }

            $phone_id = env('FACEBOOK_NUM_ID');
            $response = Http::withToken($newToken)
                ->attach('file', file_get_contents( public_path().'/images/brand/saefl/bgBlankCFLAAdmon.jpg'), 'image.jpg')
                ->post('https://graph.facebook.com/v23.0/'.$phone_id.'/media', [
                    'messaging_product' => 'whatsapp',
                    'type' => 'image',
                ]); //dd($response->json());
            $mediaId = $response->json()['id'];
            $institucion->facebook_media_id_admon = $mediaId;

            $response = Http::withToken($newToken)
                ->attach('file', file_get_contents( public_path().'/images/brand/saefl/bgBlankCFLAControl.jpg'), 'image.jpg')
                ->post('https://graph.facebook.com/v23.0/'.$phone_id.'/media', [
                    'messaging_product' => 'whatsapp',
                    'type' => 'image',
                ]); //dd($response->json());
            $mediaId = $response->json()['id'];
            $institucion->facebook_media_id_control = $mediaId;

            // Actualiza el token en la base de datos
            $institucion->facebook_access_token = $newToken;
            $institucion->facebook_access_token_date = Carbon::now();
            $institucion->facebook_tier = $messageTier;

            $institucion->save();

            $this->info('Token de acceso renovado y almacenado en la base de datos con éxito.'.$newToken);
        } else {
            $this->error('Error al renovar el token: ' . $response->body().$appId.' - '.$appSecret.' - '.$currentToken);
        }
    }
}
