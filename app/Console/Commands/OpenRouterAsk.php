<?php

namespace App\Console\Commands;

use App\Services\OpenRouterService;
use Illuminate\Console\Command;

class OpenRouterAsk extends Command
{
    protected $signature = 'openrouter:ask
        {prompt : Mensaje para el modelo}
        {--system= : Instrucción del sistema (opcional)}
        {--model= : Modelo a usar (ej: qwen/qwen3-vl-30b-a3b-thinking)}
        {--max-tokens= : Máximo de tokens a generar}
        {--temperature= : Temperatura (0-2)}
        {--raw : Mostrar solo el contenido sin metadatos}';

    protected $description = 'Realiza una consulta one-shot a un modelo LLM vía OpenRouter';

    public function handle(OpenRouterService $service): int
    {
        $prompt = $this->argument('prompt');
        $system = $this->option('system') ?? 'Eres un asistente útil y respondes en español.';

        $overrides = [];
        foreach (['model', 'max-tokens', 'temperature'] as $opt) {
            if ($value = $this->option($opt)) {
                $overrides[ str_replace('-', '_', $opt) ] = is_numeric($value) ? $value + 0 : $value;
            }
        }

        $this->components->task('Enviando consulta a OpenRouter');

        $result = $service->ask($system, $prompt, $overrides);

        if (!$result['success']) {
            $this->components->error('Error: ' . $result['error']);
            return Command::FAILURE;
        }

        if ($this->option('raw')) {
            $this->line($result['content']);
        } else {
            $this->components->twoColumnDetail('<fg=green>Respuesta</>', '');
            $this->newLine();
            $this->line($result['content']);
            $this->newLine();

            $model = $result['model'] ?? config('openrouter.model', '—');
            $usage = $result['usage'] ?? [];

            $this->components->twoColumnDetail('Modelo', "<fg=cyan>{$model}</>");

            if (isset($usage['prompt_tokens'])) {
                $this->components->twoColumnDetail('Tokens (prompt/completado/total)',
                    sprintf('%d / %d / %d', $usage['prompt_tokens'], $usage['completion_tokens'] ?? 0, $usage['total_tokens'] ?? 0));
            }
        }

        return Command::SUCCESS;
    }
}
