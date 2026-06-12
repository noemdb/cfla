<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use App\Services\Gemini\GeminiHttpClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\StreamInterface;

class GeminiService
{
	protected $client;
	protected $apiKey;
	protected $apiUrl;

	public function __construct()
	{
		$this->client = GeminiHttpClient::make();
		$this->apiKey = config('services.gemini.api_key');
		$this->apiUrl = config('services.gemini.api_url');
	}

	/**
	 * Genera contenido usando Gemini
	 *
	 * @param string $prompt
	 * @param string $model
	 * @return array
	 */
	public function generateContent(string $prompt, string $model = 'gemini-2.5-flash'): array
	{
		$endpoint = $this->buildEndpoint("models/{$model}:generateContent");

		$payload = [
			'contents' => [[
				'parts' => [
					['text' => $prompt]
				]
			]],
			'generationConfig' => [
				'temperature' => 0.7,
				'topK' => 40,
				'topP' => 0.95,
				'maxOutputTokens' => 8192,
			]
		];

		$body = $this->callApi($endpoint, $payload);

		return [
			'success' => true,
			'data' => $body,
			'text' => $this->extractText($body),
		];
	}


	/**
	 * Genera contenido con streaming
	 *
	 * @param string $prompt
	 * @param string $model
	 * @return array
	 */
	public function generateContentStream($prompt, $model = 'gemini-2.5-flash')
	{
		try {
			$url = "{$this->apiUrl}/models/{$model}:streamGenerateContent?key={$this->apiKey}&alt=sse";

			$response = $this->client->post($url, [
				'json' => [
					'contents' => [
						[
							'parts' => [
								['text' => $prompt]
							]
						]
					]
				],
				'stream' => true
			]);

			return [
				'success' => true,
				'stream' => $response->getBody()
			];
		} catch (GuzzleException $e) {
			Log::error('Gemini Stream Error: ' . $e->getMessage());

			return [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	/**
	 * Analiza imágenes con Gemini Pro Vision
	 *
	 * @param string $prompt
	 * @param string $imageBase64
	 * @param string $mimeType
	 * @return array
	 */
	public function analyzeImage(string $prompt, string $imageBase64, string $mimeType): array
	{
		$endpoint = $this->buildEndpoint("models/gemini-2.5-flash:generateContent");

		$payload = [
			'contents' => [[
				'parts' => [
					['text' => $prompt],
					[
						'inline_data' => [
							'mime_type' => $mimeType,
							'data' => $imageBase64
						]
					]
				]
			]]
		];

		$body = $this->callApi($endpoint, $payload);

		return [
			'success' => true,
			'data' => $body,
			'text' => $this->extractText($body),
		];
	}


	/**
	 * Chat con contexto (conversación)
	 *
	 * @param array $messages Historial de mensajes
	 * @param string $model
	 * @return array
	 */
	public function chat(array $messages, string $model = 'gemini-2.5-flash'): array
	{
		$endpoint = $this->buildEndpoint("models/{$model}:generateContent");

		$contents = collect($messages)->map(fn($m) => [
			'role' => $m['role'] ?? 'user',
			'parts' => [
				['text' => $m['text']]
			]
		])->toArray();

		$body = $this->callApi($endpoint, [
			'contents' => $contents
		]);

		return [
			'success' => true,
			'data' => $body,
			'text' => $this->extractText($body),
		];
	}


	/**
	 * Cuenta los tokens de un prompt
	 *
	 * @param string $prompt
	 * @param string $model
	 * @return array
	 */
	public function countTokens(string $prompt, string $model = 'gemini-2.5-flash'): array
	{
		$endpoint = $this->buildEndpoint("models/{$model}:countTokens");

		$body = $this->callApi($endpoint, [
			'contents' => [[
				'parts' => [
					['text' => $prompt]
				]
			]]
		]);

		return [
			'success' => true,
			'total_tokens' => $body['totalTokens'] ?? 0,
		];
	}


	/**
	 * Lista los modelos disponibles
	 *
	 * @return array
	 */
	public function listModels()
	{
		try {
			$url = "{$this->apiUrl}/models?key={$this->apiKey}";

			$response = $this->client->get($url);
			$body = json_decode($response->getBody(), true);

			return [
				'success' => true,
				'models' => $body['models'] ?? []
			];
		} catch (GuzzleException $e) {
			Log::error('Gemini List Models Error: ' . $e->getMessage());

			return [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function openSseStream(string $prompt, string $model = 'gemini-2.5-flash'): StreamInterface
	{
		$url = "{$this->apiUrl}/models/{$model}:streamGenerateContent?key={$this->apiKey}&alt=sse";

		$response = $this->client->post($url, [
			'json' => [
				'contents' => [[
					'parts' => [
						['text' => $prompt]
					]
				]]
			],
			'headers' => [
				'Accept' => 'text/event-stream',
			],
			'stream' => true,
			// Importante para streams largos:
			'read_timeout' => 0,
		]);

		return $response->getBody();
	}

	/**
	 * Ejecuta una llamada HTTP genérica a Gemini
	 */
	private function callApi(string $endpoint, array $payload)
	{
		try {
			$response = $this->client->post($endpoint, [
				'json' => $payload
			]);

			return json_decode($response->getBody(), true);
		} catch (GuzzleException $e) {
			$this->handleException($e);
		}
	}

	/**
	 * Construye el endpoint con API Key
	 */
	private function buildEndpoint(string $path): string
	{
		return "{$this->apiUrl}/{$path}?key={$this->apiKey}";
	}

	/**
	 * Extrae texto estándar de la respuesta Gemini
	 */
	private function extractText(?array $body): ?string
	{
		return $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
	}

	/**
	 * Manejo centralizado de excepciones
	 */
	private function handleException(GuzzleException $e)
	{
		$response = null;
		if ($e instanceof RequestException && $e->getResponse() !== null) {
			$response = $e->getResponse()->getBody()->getContents();
		}

		Log::error('Gemini API Error', [
			'message' => $e->getMessage(),
			'response' => $response,
		]);

		throw $e;
	}
}
