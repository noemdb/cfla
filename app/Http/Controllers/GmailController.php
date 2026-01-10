<?php

namespace App\Http\Controllers;

use App\Services\GmailService;
use Google\Client;
use Google\Service\Gmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GmailController extends Controller
{
    protected $gmailService;

    public function __construct()
    {
        $this->gmailService = new GmailService();
    }

    public function redirectToGoogle()
    {
        return redirect($this->gmailService->getAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $this->gmailService->handleCallback($request->code);
        return redirect()->route('home')->with('success', 'Autenticación con Gmail completada');
    }

    public function sendEmail()
    {
        $authConfig = storage_path('app/google/credentials.json');
        if (!file_exists($authConfig)) {
            return 'Error: Archivo de credenciales de Google no encontrado en ' . $authConfig;
        }

        $client = $this->getGoogleClient();

        if (!Storage::exists('google/token.json')) {
            return 'Error: Token no encontrado. Vuelve a autenticar.';
        }

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

        $authConfig = storage_path('app/google/credentials.json');
        if (file_exists($authConfig)) {
            $client->setAuthConfig($authConfig);
        }

        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(route('google.callback'));
        return $client;
    }
}
