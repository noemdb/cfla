<?php

namespace App\Console\Commands;

use App\Services\TokenRouterService;
use Illuminate\Console\Command;

/**
 * Prueba la integración con TokenRouter (gateway multi-modelo OpenAI-compatible).
 *
 * Llama a TokenRouterService::ask() con un prompt de ejemplo (o el que se pase)
 * y muestra el contenido generado o el error devuelto por el gateway.
 *
 * Uso:
 *   php8.2 artisan ai:test-tokenrouter
 *   php8.2 artisan ai:test-tokenrouter --prompt="Resume la teoría del Big Bang"
 *   php8.2 artisan ai:test-tokenrouter --model=gpt-4o-mini --max-tokens=512 --temperature=0.2
 */
class TestTokenRouter extends Command
{
    protected $signature = 'ai:test-tokenrouter
        {--prompt= : Prompt a enviar al modelo (default: prompt de prueba de la integración)}
        {--model= : Modelo a usar (override; si se omite se usa la cadena model_primary → fallbacks)}
        {--max-tokens= : Límite de tokens de salida}
        {--temperature= : Temperatura de generación}
        {--timeout= : Timeout de la petición en segundos}';

    protected $description = 'Prueba la integración del servicio AI con TokenRouter';

    public function handle(TokenRouterService $tokenRouter): int
    {
        $prompt = $this->option('prompt') ?: 'Actúa como un asistente experto. Responde con un párrafo breve y claro: ¿qué es un horario escolar y por qué es útil planificarlo?';

        $this->info('Configuración de TokenRouter:');
        $this->line('  base_url   : '.config('tokenrouter.base_url'));
        $this->line('  model      : '.($this->option('model') ?? (config('tokenrouter.model_primary') ?? config('tokenrouter.model'))));
        $this->line('  api_key    : '.(config('tokenrouter.api_key') ? 'configurada ('.strlen((string) config('tokenrouter.api_key')).' chars)' : 'NO CONFIGURADA'));
        $this->line('  max_tokens : '.($this->option('max-tokens') ?? config('tokenrouter.max_tokens')));
        $this->line('  temperature: '.($this->option('temperature') ?? config('tokenrouter.temperature')));
        $this->newLine();

        if (! config('tokenrouter.api_key')) {
            $this->error('TOKENROUTER_API_KEY no está configurada. Define la clave en .env y recarga la config (php artisan config:cache).');

            return self::FAILURE;
        }

        $overrides = [];

        if ($this->option('model')) {
            $overrides['model'] = $this->option('model');
        }
        if ($this->option('max-tokens')) {
            $overrides['max_tokens'] = (int) $this->option('max-tokens');
        }
        if ($this->option('temperature')) {
            $overrides['temperature'] = (float) $this->option('temperature');
        }
        if ($this->option('timeout')) {
            $overrides['timeout'] = (int) $this->option('timeout');
        }

        $this->info('Enviando prompt:');
        $this->line('  '.$prompt);
        $this->newLine();

        $this->info('Consultando TokenRouter…');
        $result = $tokenRouter->ask('Eres un asistente útil.', $prompt, $overrides);

        if (! $result['success']) {
            $this->error('TokenRouter respondió con error:');
            $this->error($result['error'] ?? 'Error desconocido');

            return self::FAILURE;
        }

        $this->info('Respuesta:');
        $this->line((string) $result['content']);
        $this->newLine();
        $this->info('Modelo utilizado: '.($result['model'] ?? 'desconocido').' | tokens: '.json_encode($result['usage'] ?? null));

        return self::SUCCESS;
    }
}
