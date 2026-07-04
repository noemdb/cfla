<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = config('services.openrouter.base_url');
        $this->model = config('services.openrouter.model');

        $this->client = new Client([
            'timeout' => config('services.openrouter.timeout', 120),
            'connect_timeout' => config('services.openrouter.connect_timeout', 10),
        ]);
    }

    /**
     * Generate text using OpenRouter chat completions
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function generateText(array $payload): array
    {
        try {
            $response = $this->client->post($this->baseUrl . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ],
                'json' => array_merge([
                    'model' => $this->model,
                ], $payload),
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Simple chat completion with a single prompt
     *
     * @param string $prompt
     * @param string|null $model
     * @return string
     */
    public function chat(string $prompt, ?string $model = null): string
    {
        $payload = [
            'model' => $model ?? $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ];

        $response = $this->generateText($payload);
        return $response['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Chat with system and user messages
     *
     * @param string $systemMessage
     * @param string $userMessage
     * @param string|null $model
     * @param array $options
     * @return array
     */
    public function chatWithSystem(
        string $systemMessage,
        string $userMessage,
        ?string $model = null,
        array $options = []
    ): array {
        $payload = array_merge([
            'model' => $model ?? $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage],
                ['role' => 'user', 'content' => $userMessage],
            ],
        ], $options);

        return $this->generateText($payload);
    }

    /**
     * Chat with multiple messages (conversation history)
     *
     * @param array $messages
     * @param string|null $model
     * @param array $options
     * @return array
     */
    public function chatWithMessages(
        array $messages,
        ?string $model = null,
        array $options = []
    ): array {
        $payload = array_merge([
            'model' => $model ?? $this->model,
            'messages' => $messages,
        ], $options);

        return $this->generateText($payload);
    }

    /**
     * Handle exceptions from API requests
     *
     * @param GuzzleException $e
     * @throws \Exception
     */
    private function handleException(GuzzleException $e)
    {
        $response = null;
        $errorDetails = null;

        if ($e instanceof RequestException && $e->getResponse() !== null) {
            $response = $e->getResponse()->getBody()->getContents();
            $errorDetails = json_decode($response, true);
        }

        Log::error('OpenRouter API Error', [
            'message' => $e->getMessage(),
            'response' => $response,
            'error_details' => $errorDetails,
        ]);

        // Provide helpful error message
        if ($errorDetails && isset($errorDetails['error']['message'])) {
            throw new \Exception('OpenRouter API Error: ' . $errorDetails['error']['message']);
        }

        throw $e;
    }
}
