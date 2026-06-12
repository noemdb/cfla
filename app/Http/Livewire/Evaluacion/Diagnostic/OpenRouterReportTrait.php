<?php

namespace App\Http\Livewire\Evaluacion\Diagnostic;

use App\Services\OpenRouterService;
use App\Models\app\AI\AiPrompt;
use Exception;
use Illuminate\Support\Facades\Log;

trait OpenRouterReportTrait
{
    /**
     * Generate Diagnostic Report using OpenRouter
     *
     * @param array $payload
     * @return array|null
     */
    public function orGenerateReport($payload)
    {
        // 1. Fetch Active Prompts
        $systemPrompt = AiPrompt::where('prompt_type', 'system')->where('active', 1)->first();
        $userPrompt = AiPrompt::where('prompt_type', 'user')->where('active', 1)->first();

        if (!$systemPrompt || !$userPrompt) {
            Log::error('OpenRouter Report: Missing active system or user prompts.');
            $this->emit('show-notification', [
                'type' => 'error',
                'message' => 'Error: No se encontraron prompts activos (System/User) para generar el informe.'
            ]);
            return null;
        }

        // 2. Prepare Payload
        $jsonPayload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // 3. Replace Placeholder in User Prompt (Support V1 and V2)
        $promptContent = str_replace(
            ['{{ payload_json }}', '{{PAYLOAD_DIAGNOSTICO_COMPLETO}}'],
            $jsonPayload,
            $userPrompt->content
        );

        // 4. Build API Payload
        $openRouter = app(OpenRouterService::class);
        $apiPayload = [
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt->content],
                ['role' => 'user', 'content' => $promptContent]
            ],
            'temperature' => 0.7
        ];

        // 5. Call API
        try {
            $response = $openRouter->generateText($apiPayload);
            $content = $response['choices'][0]['message']['content'] ?? null;

            if ($content) {
                // 1. First method: Robust JSON regex extraction
                if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
                    $jsonContent = $matches[0];
                    $decoded = json_decode($jsonContent, true);
                    if ($decoded) {
                        return $decoded;
                    } else {
                        Log::warning('OpenRouter: Regex found potential JSON but parse failed.', ['error' => json_last_error_msg()]);
                    }
                }

                // 2. Second method: Direct cleanup (strip markdown)
                $cleanContent = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
                $cleanContent = preg_replace('/^```\s*|\s*```$/i', '', trim($cleanContent));

                $decoded = json_decode($cleanContent, true);
                if ($decoded) {
                    Log::info('OpenRouter: JSON extracted via direct cleanup method.');
                    return $decoded;
                }

                // If both failed, log full error context
                Log::error('OpenRouter: No valid JSON found.', [
                    'full_content' => substr($content, 0, 2000),
                    'json_error' => json_last_error_msg()
                ]);
            }
        } catch (Exception $e) {
            Log::error('OpenRouter Report Generation Error: ' . $e->getMessage());
            $this->emit('show-notification', [
                'type' => 'error',
                'message' => 'Error al comunicar con OpenRouter: ' . $e->getMessage()
            ]);
        }

        return null;
    }
}
