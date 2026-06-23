<?php

namespace App\Services\Bot;

use Illuminate\Support\Facades\Http;

class AutoresponderService
{
    /**
     * Base URL de la API de saefl (s2526).
     * Se define desde APP_URL_SAEFL en el .env, con fallback a localhost.
     */
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('APP_URL_SAEFL', 'http://localhost:2526'), '/') . '/api/bot/autoresponder';
    }

    /**
     * Procesa el mensaje del chatbot redirigiendo al endpoint
     * correspondiente de la API de saefl.
     *
     * - Números 1-8 y default → POST /main (menú/opciones)
     * - Cédulas (V/E-12345 o número ≥6 dígitos) → POST /info/debs
     * - Palabras clave "tasa/cambio/bcv/dólar" → POST /exchange/rate
     */
    public function main(string $message): string
    {
        $message = trim($message);

        // Cédula con formato V-12345678 o E-12345678
        if (preg_match('/^[VE]\-?\d{5,10}$/i', $message)) {
            $ci = preg_replace('/[^0-9]/', '', $message);
            return $this->postTo('info/debs', $ci);
        }

        // Número largo (cédula sin prefijo)
        if (is_numeric($message) && strlen($message) >= 6) {
            return $this->postTo('info/debs', $message);
        }

        // Consulta de tasa de cambio
        if (preg_match('/\b(tasa|cambio|bcv|dolar|dólar)\b/i', $message)) {
            return $this->postTo('exchange/rate', $message);
        }

        // Default: menú principal y opciones numéricas (1-8)
        return $this->postTo('main', $message);
    }

    /**
     * Envía POST al endpoint de la API de saefl y devuelve
     * el texto de la respuesta (formato WhatsApp-style markdown).
     */
    protected function postTo(string $endpoint, string $message): string
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/{$endpoint}", [
                'appPackageName' => 'cfla_web_chatbot',
                'messengerPackageName' => 'web',
                'query' => [
                    'sender' => 'web_user',
                    'message' => $message,
                ],
            ]);

            if ($response->failed()) {
                return 'Servicio no disponible en este momento.';
            }

            $data = $response->json();

            return $data['replies'][0]['message'] ?? 'Sin respuesta.';
        } catch (\Exception $e) {
            return 'Error de conexión con el servicio. Intente más tarde.';
        }
    }

    /**
     * Obtiene el menú principal desde la API.
     */
    public function getMenu(): string
    {
        return $this->postTo('main', 'menu');
    }

    /**
     * Guarda el mensaje en el log local.
     *
     * Nota: La API de saefl ya registra los mensajes automáticamente
     * al procesar cada petición. Este método se mantiene por si se
     * requiere un registro local adicional.
     */
    public function saveMessage(string $sender, string $message, ?string $appPackageName = null, ?string $messengerPackageName = null): void
    {
        // La API de saefl (s2526) ya guarda el mensaje en bmesseges
        // al procesar cada endpoint. No es necesario duplicar aquí.
    }
}
