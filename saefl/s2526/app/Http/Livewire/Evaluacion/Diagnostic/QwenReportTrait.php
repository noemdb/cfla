<?php

namespace App\Http\Livewire\Evaluacion\Diagnostic;

use App\Services\QwenService;
use App\Models\app\AI\AiPrompt;
use Exception;
use Illuminate\Support\Facades\Log;

trait QwenReportTrait
{
    /**
     * Generate Diagnostic Report using Qwen
     *
     * @param array $payload
     * @return array|null
     */
    public function qwGenerateReport($payload)
    {
        // 1. Fetch Active Prompts
        $systemPrompt = AiPrompt::where('prompt_type', 'system')->where('active', 1)->first();
        $userPrompt = AiPrompt::where('prompt_type', 'user')->where('active', 1)->first();

        if (!$systemPrompt || !$userPrompt) {
            Log::error('Qwen Report: Missing active system or user prompts.');
            $this->emit('show-notification', [
                'type' => 'error',
                'message' => 'Error: No se encontraron prompts activos (System/User) para generar el informe.'
            ]);
            return null;
        }

        // 2. Prepare Payload
        $jsonPayload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // 3. Replace Placeholder in User Prompt
        // 3. Replace Placeholder in User Prompt (Support V1 and V2)
        $promptContent = str_replace(
            ['{{ payload_json }}', '{{PAYLOAD_DIAGNOSTICO_COMPLETO}}'],
            $jsonPayload,
            $userPrompt->content
        );

        // 4. Build API Payload
        $qwen = app(QwenService::class);
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt->content],
            ['role' => 'user', 'content' => $promptContent]
        ];

        // 5. Call API
        try {
            $response = $qwen->sendMessage($messages);

            $content = null;
            if (isset($response['choices'][0]['message']['content'])) {
                $content = $response['choices'][0]['message']['content'];
            } elseif (isset($response['output']['text'])) {
                $content = $response['output']['text'];
            }

            if ($content) {
                // 1. First method: Robust JSON regex extraction
                if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
                    $jsonContent = $matches[0];
                    $decoded = json_decode($jsonContent, true);
                    if ($decoded) {
                        return $decoded;
                    } else {
                        Log::warning('Qwen: Regex found potential JSON but parse failed.', ['error' => json_last_error_msg()]);
                    }
                }

                // 2. Second method: Direct cleanup (strip markdown)
                $cleanContent = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
                $cleanContent = preg_replace('/^```\s*|\s*```$/i', '', trim($cleanContent));

                $decoded = json_decode($cleanContent, true);
                if ($decoded) {
                    Log::info('Qwen: JSON extracted via direct cleanup method.');
                    return $decoded;
                }

                // If both failed, log full error context
                Log::error('Qwen: No valid JSON found.', [
                    'full_content' => substr($content, 0, 2000), // Increased logging
                    'json_error' => json_last_error_msg()
                ]);
            }
        } catch (Exception $e) {
            Log::error('Qwen Report Generation Error: ' . $e->getMessage());
            $this->emit('show-notification', [
                'type' => 'error',
                'message' => 'Error al comunicar con Qwen: ' . $e->getMessage()
            ]);
        }

        return null;
    }
}
