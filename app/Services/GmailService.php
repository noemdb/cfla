<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class GmailService
{
    private $client;
    private $token;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Laravel Gmail API');
        $this->client->setScopes(Gmail::GMAIL_SEND);

        $authConfig = storage_path('app/google/credentials.json');
        if (file_exists($authConfig)) {
            $this->client->setAuthConfig($authConfig);
        }

        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setRedirectUri(route('google.callback'));

        $this->loadToken();
    }

    private function loadToken()
    {
        // Intentar obtener el token del caché primero
        $this->token = Cache::get('gmail_token');

        if (!$this->token) {
            // Si no está en caché, intentar obtenerlo del almacenamiento
            if (Storage::exists('google/token.json')) {
                $this->token = json_decode(Storage::get('google/token.json'), true);
                // Guardar en caché por 1 hora
                Cache::put('gmail_token', $this->token, now()->addHour());
            }
        }

        if ($this->token) {
            $this->client->setAccessToken($this->token);
        }
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback($code)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->saveToken($token);
        return $token;
    }

    private function saveToken($token)
    {
        $this->token = $token;
        Storage::put('google/token.json', json_encode($token));
        Cache::put('gmail_token', $token, now()->addHour());
    }

    public function refreshToken()
    {
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $token = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $this->saveToken($token);
                return true;
            }
            return false;
        }
        return true;
    }

    public function sendEmail($to, $subject, $body)
    {
        if (!$this->refreshToken()) {
            throw new \Exception('Token expirado y no se puede renovar. Se requiere nueva autenticación.');
        }

        $service = new Gmail($this->client);
        $message = new \Google\Service\Gmail\Message();

        $rawMessageString = "To: {$to}\r\n";
        $rawMessageString .= "From: soporte.saefl@example.com\r\n";
        $rawMessageString .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $rawMessageString .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $rawMessageString .= base64_encode($body);

        $encodedMessage = base64_encode($rawMessageString);
        $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
        $message->setRaw($encodedMessage);

        return $service->users_messages->send("me", $message);
    }
}
