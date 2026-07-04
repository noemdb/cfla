<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GeminiController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Genera texto simple
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:10000',
            'model' => 'nullable|string|in:gemini-2.5-flash,gemini-2.5-pro,gemini-2.0-flash,gemini-2.0-flash-001,gemini-2.0-flash-lite,gemini-2.0-flash-lite-001,gemini-2.5-flash-lite'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->geminiService->generateContent(
            $request->prompt,
            $request->model ?? 'gemini-2.5-flash'
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'response' => $result['text'],
                'full_data' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Analiza una imagen
     */
    public function analyzeImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:5000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Convertir imagen a base64
        $image = $request->file('image');
        $imageData = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType();

        $result = $this->geminiService->analyzeImage(
            $request->prompt,
            $imageData,
            $mimeType
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'response' => $result['text']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Chat conversacional
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'messages' => 'required|array',
            'messages.*.text' => 'required|string',
            'messages.*.role' => 'nullable|string|in:user,model'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->geminiService->chat($request->messages);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'response' => $result['text']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Contar tokens
     */
    public function countTokens(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->geminiService->countTokens($request->prompt);

        return response()->json($result);
    }

    public function stream(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'prompt' => 'required|string|max:10000',
            'model'  => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $prompt = $request->input('prompt');
        $model  = $request->input('model', 'gemini-2.5-flash');

        return response()->stream(function () use ($prompt, $model) {
            // Desactivar buffering PHP
            while (ob_get_level() > 0) { @ob_end_flush(); }
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');

            echo ": connected\n\n";
            @ob_flush(); @flush();

            try {
                $stream = $this->geminiService->openSseStream($prompt, $model);

                $buffer = '';

                while (!$stream->eof()) {
                    $chunk = $stream->read(8192);

                    if ($chunk === '') {
                        usleep(10_000);
                        continue;
                    }

                    // DEBUG: confirma que llegan bytes
                    \Log::info('GEMINI_SSE_CHUNK', ['len' => strlen($chunk), 'head' => substr($chunk, 0, 120)]);

                    $buffer .= $chunk;

                    // Normalizar CRLF -> LF
                    $buffer = str_replace("\r\n", "\n", $buffer);

                    while (($pos = strpos($buffer, "\n\n")) !== false) {
                        $rawEvent = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 2);

                        $eventName = 'message';
                        $dataLines = [];

                        foreach (explode("\n", $rawEvent) as $line) {
                            $line = rtrim($line);

                            if ($line === '' || $line[0] === ':') continue;

                            if (strpos($line, 'event:') === 0) {
                                $eventName = trim(substr($line, 6));
                                continue;
                            }

                            if (strpos($line, 'data:') === 0) {
                                $dataLines[] = ltrim(substr($line, 5));
                                continue;
                            }
                        }

                        if (!$dataLines) continue;

                        $payload = implode("\n", $dataLines);

                        if ($payload === '[DONE]') {
                            echo "event: done\n";
                            echo "data: {}\n\n";
                            @ob_flush(); @flush();
                            return;
                        }

                        $json = json_decode($payload, true);
                        if (!is_array($json)) {
                            \Log::warning('GEMINI_SSE_BAD_JSON', ['payload_head' => substr($payload, 0, 200)]);
                            continue;
                        }

                        $parts = data_get($json, 'candidates.0.content.parts', []);
                        foreach ($parts as $part) {
                            if (!empty($part['text'])) {
                                echo "event: token\n";
                                echo "data: " . json_encode(['text' => $part['text']], JSON_UNESCAPED_UNICODE) . "\n\n";
                                @ob_flush(); @flush();
                            }
                        }
                    }
                }

                echo "event: done\n";
                echo "data: {}\n\n";
                @ob_flush(); @flush();

            } catch (\Throwable $e) {
                echo "event: error\n";
                echo "data: " . json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE) . "\n\n";
                @ob_flush(); @flush();
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream; charset=UTF-8',
            'Cache-Control'     => 'no-cache, no-transform',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }


}