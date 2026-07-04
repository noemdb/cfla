<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DeepSeekService;

class DeepSeekController extends Controller
{
    protected $deepSeekService;

    public function __construct(DeepSeekService $deepSeekService)
    {
        $this->deepSeekService = $deepSeekService;
    }

    public function generateText(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        $payload = [
            'prompt' => $request->input('prompt'),
            'options' => [
                'max_tokens' => $request->input('max_tokens', 100),
                'temperature' => $request->input('temperature', 0.7),
            ],
        ];

        try {
            $result = $this->deepSeekService->generateText($payload);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
