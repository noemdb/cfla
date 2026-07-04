<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class DeepSeekService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('DEEPSEEK_API_URL'),
            'headers' => [
                'Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function generateText(array $payload)
    {
        try {
            $response = $this->client->post('chat/completions', [
                'json' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception("Error connecting to DeepSeek API: " . $e->getMessage());
        }
    }
}
