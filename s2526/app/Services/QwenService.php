<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QwenService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('qwen.api_key');
        $this->apiUrl = config('qwen.api_url');
    }

    public function sendMessage(array $messages, string $model = 'qwen-plus')
    {
        $payload = [
            'model' => $model,
            'messages' => $messages,
        ];

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Dashscope API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'error' => 'API request failed',
                'status' => $response->status(),
                'message' => $response->body(),
            ];
        } catch (Exception $e) {
            Log::error('Dashscope API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'error' => 'Exception occurred',
                'message' => $e->getMessage(),
            ];
        }
    }
}
