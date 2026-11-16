<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\Planning\SystemPromptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Entrega, compone y refina el system prompt de consolidado para el botón
 * “Copiar prompt” de /app/planning/activities.
 */
class SystemPromptController extends Controller
{
    public function show(): Response
    {
        $path = base_path('blueprint/activities/alimentacion/systemPrompt.md');

        abort_unless(is_file($path), 404, 'El system prompt no está disponible.');

        $contents = (string) file_get_contents($path);

        abort_if(trim($contents) === '', 404, 'El system prompt está vacío.');

        return response($contents, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Cache-Control' => 'private, no-cache',
        ]);
    }

    /**
     * Prompt base + parámetros de la ejecución (sin IA).
     */
    public function compose(Request $request, SystemPromptService $service): JsonResponse
    {
        $params = $this->validatedParams($request);

        try {
            return response()->json([
                'success' => true,
                'prompt' => $service->compose($params),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'prompt' => null,
                'error' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Bloque de refinamiento generado por IA para los parámetros actuales.
     */
    public function refine(Request $request, SystemPromptService $service): JsonResponse
    {
        $params = $this->validatedParams($request);

        try {
            $result = $service->refine($params);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'prompt' => null,
                'model' => null,
                'error' => $e->getMessage(),
            ], 503);
        }

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /**
     * @return array{eje:string,areas?:?string,nivel?:?string,periodo?:?string,titulo?:?string,instrucciones?:?string,refinamiento?:?string}
     */
    private function validatedParams(Request $request): array
    {
        return $request->validate([
            'eje' => ['required', 'string', 'max:300'],
            'areas' => ['nullable', 'string', 'max:300'],
            'nivel' => ['nullable', 'string', 'max:300'],
            'periodo' => ['nullable', 'string', 'max:300'],
            'titulo' => ['nullable', 'string', 'max:300'],
            'instrucciones' => ['nullable', 'string', 'max:2000'],
            'refinamiento' => ['nullable', 'string', 'max:12000'],
        ]);
    }
}
