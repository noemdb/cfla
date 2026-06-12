<?php

namespace App\Console\Commands;

use App\Services\NvidiaService;
use Illuminate\Console\Command;

class NvidiaAsk extends Command
{
    protected $signature = 'nvidia:ask
        {prompt : Mensaje para el modelo}
        {--system= : Instrucción del sistema (opcional)}
        {--model= : Modelo a usar (ej: qwen/qwen3.5-122b-a10b)}
        {--max-tokens= : Máximo de tokens a generar}
        {--temperature= : Temperatura (0-2)}
        {--raw : Mostrar solo el contenido sin metadatos}';

    protected $description = 'Realiza una consulta one-shot a un modelo LLM vía NVIDIA build.nvidia.com';

    public function handle(NvidiaService $service): int
    {
        $prompt = $this->argument('prompt');
        $system = $this->option('system') ?? 'Eres un asistente útil y respondes en español.';

        $overrides = [];
        foreach (['model', 'max-tokens', 'temperature'] as $opt) {
            if ($value = $this->option($opt)) {
                $overrides[ str_replace('-', '_', $opt) ] = is_numeric($value) ? $value + 0 : $value;
            }
        }

        // Mostrar configuración de la consulta antes de enviar
        $this->components->twoColumnDetail('<fg=yellow>Parámetros de la consulta</>', '');
        $this->components->twoColumnDetail('  Modelo solicitado',
            '<fg=cyan>' . ($overrides['model'] ?? config('nvidia.model')) . '</>');
        $this->components->twoColumnDetail('  Max tokens',
            (string) ($overrides['max_tokens'] ?? config('nvidia.max_tokens', 2048)));
        $this->components->twoColumnDetail('  Temperatura',
            (string) ($overrides['temperature'] ?? config('nvidia.temperature', 0.7)));

        $startTime = microtime(true);

        $this->components->task('Enviando consulta a NVIDIA build.nvidia.com');

        $result = $service->ask($system, $prompt, $overrides);

        $elapsed = microtime(true) - $startTime;

        if (!$result['success']) {
            $this->components->error('Error: ' . $result['error']);
            return Command::FAILURE;
        }

        if ($this->option('raw')) {
            $this->line($result['content']);
        } else {
            // ── Respuesta ──────────────────────────────────────────
            $this->components->twoColumnDetail('<fg=green>Respuesta</>', '');
            $this->newLine();
            $this->line(wordwrap($result['content'], 80, "\n", true));
            $this->newLine();

            // ── Separador ──────────────────────────────────────────
            $this->components->twoColumnDetail('<fg=gray>─── Detalles de la inferencia ───</>', '');

            // Modelo
            $model = $result['model'] ?? config('nvidia.model', '—');
            $this->components->twoColumnDetail('Modelo usado', "<fg=cyan>{$model}</>");

            // Request ID (truncado)
            $requestId = $result['meta']['id'] ?? null;
            if ($requestId) {
                $shortId = strlen($requestId) > 30 ? substr($requestId, 0, 30) . '…' : $requestId;
                $this->components->twoColumnDetail('ID de solicitud', "<fg=gray>{$shortId}</>");
            }

            // Tiempo de respuesta
            $this->components->twoColumnDetail('Tiempo de respuesta',
                sprintf('<fg=yellow>%.2f</> <fg=gray>seg</>', $elapsed));

            // Finish reason
            $finishReason = $result['meta']['finish_reason'] ?? null;
            if ($finishReason) {
                $reasonLabels = [
                    'stop'          => '<fg=green>stop</> — respuesta completa',
                    'length'        => '<fg=red>length</> — se alcanzó max_tokens',
                    'content_filter'=> '<fg=yellow>content_filter</> — filtro de contenido',
                    'tool_calls'    => '<fg=cyan>tool_calls</> — llamada a herramientas',
                ];
                $this->components->twoColumnDetail('Razón de finalización',
                    $reasonLabels[$finishReason] ?? "<fg=gray>{$finishReason}</>");
            }

            // Timestamp del servidor
            $created = $result['meta']['created'] ?? null;
            if ($created) {
                $this->components->twoColumnDetail('Generado el',
                    '<fg=gray>' . date('d/m/Y H:i:s', $created) . '</>');
            }

            // ── Uso de tokens ──────────────────────────────────────
            $usage = $result['usage'] ?? [];
            if (isset($usage['prompt_tokens'])) {
                $this->components->twoColumnDetail('<fg=gray>─── Tokens ───</>', '');

                $promptTokens  = $usage['prompt_tokens'];
                $completion    = $usage['completion_tokens'] ?? 0;
                $total         = $usage['total_tokens'] ?? 0;

                $this->components->twoColumnDetail('Prompt', number_format($promptTokens));
                $this->components->twoColumnDetail('Completado', number_format($completion));
                $this->components->twoColumnDetail('Total', "<fg=white>" . number_format($total) . "</>");

                // Velocidad
                if ($completion > 0 && $elapsed > 0) {
                    $tps = round($completion / $elapsed, 1);
                    $this->components->twoColumnDetail('Velocidad',
                        "<fg=magenta>{$tps}</> <fg=gray>tok/s</>");
                }
            }

            // ── Prompt usado (resumido) ────────────────────────────
            $this->newLine();
            $this->components->twoColumnDetail('<fg=gray>─── Prompt enviado (resumido) ───</>', '');
            $this->components->twoColumnDetail('  System', '<fg=gray>' . mb_strimwidth($system, 0, 120, '…') . '</>');
            $this->components->twoColumnDetail('  User', '<fg=gray>' . mb_strimwidth($prompt, 0, 120, '…') . '</>');
        }

        return Command::SUCCESS;
    }
}
