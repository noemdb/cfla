<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Gmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GmailController extends Controller
{
    public function redirectToGoogle()
    {
        $client = $this->getGoogleClient();
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = $this->getGoogleClient();
        $token = $client->fetchAccessTokenWithAuthCode($request->code);
        Storage::put('google/token.json', json_encode($token));
        return 'Autenticado con Gmail';
    }

    public function sendEmail()
    {
        $client = $this->getGoogleClient();
        $token = json_decode(Storage::get('google/token.json'), true);
        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            return 'Token expirado, vuelve a autenticar.';
        }

        $service = new Gmail($client);
        $message = new \Google\Service\Gmail\Message();

        $rawMessageString = "To: noemdb@gmail.com\r\n";
        $rawMessageString .= "From: soporte.saefl@example.com\r\n";
        $rawMessageString .= "Subject: Prueba desde Laravel Gmail API\r\n";
        $rawMessageString .= "Content-Type: text/plain; charset=utf-8\r\n\r\n";
        $rawMessageString .= "Este es un correo enviado desde Laravel usando Gmail API.";

        $encodedMessage = base64_encode($rawMessageString);
        $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
        $message->setRaw($encodedMessage);

        $service->users_messages->send("me", $message);
        return 'Correo enviado correctamente';
    }

    private function getGoogleClient(): Client
    {
        $client = new Client();
        $client->setApplicationName('Laravel Gmail API');
        $client->setScopes(Gmail::GMAIL_SEND);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(route('google.callback'));
        return $client;
    }
}
