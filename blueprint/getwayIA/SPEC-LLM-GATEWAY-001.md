# SPEC-LLM-GATEWAY-001 — Gateway general con fallback para unificar la lógica LLM

| | |
|---|---|
| **Estado** | Draft — listo para descomponer en tickets |
| **Stack** | Laravel 10 · PHP 8.2 · HTTP Client (`Illuminate\Http\Client`) · MariaDB (db `s2627`) |
| **Autor** | Staff Engineer spec para agente de código |
| **Punto de partida** | Servicios AI existentes: `OpenRouterService`, `NvidiaService`, `KimiService`, `TokenRouterService`, `QwenService`, `DeepSeekService`, `GeminiService` |
| **Servicios que hoy orquestan fallback** | `ActivityImprovementService`, `Lms\InfografiaGeneratorService`, `Lms\LmsAiOrchestrationService`, `Lms\LmsMermaidAiRepairService`, `Lms\LmsSvgAiRepairService` |
| **Supersede** | Nada; introduce una capa común sin romper los servicios existentes (migración incremental) |

---

## 1. Objetivo y alcance

Crear un **gateway general de LLM** (`LlmGateway`) que unifique la lógica de llama a modelos con **fallback** (por proveedor y por modelo), hoy duplicada en 5+ servicios. El gateway ofrece un contrato único y, sobre él, los servicios existentes se migran de forma incremental **sin cambiar su comportamiento externo**.

**Capacidades del gateway:**
1. Resolver proveedores (OpenRouter, Nvidia, Kimi, TokenRouter, Qwen, DeepSeek, Gemini…) con contrato común.
2. Fallback en cadena **(a)** entre modelos de un proveedor y **(b)** entre proveedores.
3. Saltar proveedores sin API key configurada (sin llamar).
4. Timeout y tope de intentos configurables.
5. Validación opcional de la respuesta (clave/JSON esperado) — si no cumple, sigue con el siguiente.
6. Recolección de **todos** los errores y reason (para diagnóstico).
7. Observabilidad: log de canal dedicado `llm` + `usage`/`cost` por llamada.
8. Compatibilidad con el shape de resultado ya usado: `[success, content, model, usage?]`.

**Fuera de alcance v1:** retry con backoff/tiempo de espera entre intentos, streaming, caching de respuestas, rate-limiting por clave, UI de métricas (solo log/registro).

---

## 2. Problema actual (motivación)

Cada servicio de orquestación replica la misma lógica con ligeras variantes:

| Servicio | Patrón de fallback | Duplicación |
|---|---|---|
| `ActivityImprovementService` | `callWithFallback()` + `safeCall()` + `openRouterModels()` (OpenRouter→Nvidia→Kimi→TokenRouter) | Orquesta por proveedor y por modelo |
| `Lms\InfografiaGeneratorService` | Array `$attempts` (OpenRouter→Nvidia→Kimi) con validación de esquema | Orquesta por proveedor |
| `Lms\LmsAiOrchestrationService` | `askProvider()` + cadena `model_fallback1..4` de OpenRouter | Orquesta por modelo + proveedor |
| `Lms\LmsMermaidAiRepairService` | Cadena `model_diagram_primary/fallback1/fallback2` | Orquesta por modelo |
| `Lms\LmsSvgAiRepairService` | (similar) | Orquesta por modelo/proveedor |

Además, cada wrapper devuelve un shape distinto:
- **OpenAI-compatibe** (OpenRouter, Nvidia, Kimi, TokenRouter, Qwen, DeepSeek): `{success, content, model, usage, error}` o `{content}`.
- **Gemini** (nativo Google): `{candidates[].content.parts[].text}`.

**Consecuencia:** 5 implementaciones de fallback, 6 wrappers con contratos heterogéneos, y agregar un proveedor o modelo nuevo requiere tocar varios archivos.

---

## 3. Diseño — Componentes del gateway

### 3.1 DTO de resultado: `App\Services\Llm\LlmResult`

```php
final class LlmResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $content,
        public readonly ?string $model = null,
        public readonly ?array $usage = null,
        public readonly ?string $error = null,
    ) {}

    public static function ok(string $content, ?string $model = null, ?array $usage = null): self
    public static function fail(string $error): self
}
```

### 3.2 Contrato de proveedor: `App\Services\Llm\Contracts\LlmProvider`

```php
interface LlmProvider
{
    public function key(): string;                    // 'openrouter' | 'nvidia' | ...
    public function isConfigured(): bool;             // api_key no vacía
    public function models(): array;                  // lista de modelos a probar (override + primario + fallbacks)
    public function ask(string $systemPrompt, string|array $message, array $overrides = []): LlmResult;
}
```

Wrappers de adaptación:
- `OpenAIServiceAdapter` (reusa la lógica OpenAI-compatible de OpenRouter/Nvidia/Kimi/TokenRouter/Qwen/DeepSeek).
- `GeminiServiceAdapter` (traduce a `candidates[].content.parts[].text`).
- Los servicios existentes (`OpenRouterService`, etc.) se **envuelven** con adaptadores, **sin reescribir su lógica interna** en v1.

### 3.3 Config unificada: `config/llm.php`

Un mapa por proveedor que centraliza claves/modelos (los archivos `config/kimi.php`, `config/openrouter.php`, etc. se mantienen como fuente por compatibilidad, pero `config/llm.php` es el contract del gateway):

```php
return [
    'providers' => [
        'openrouter' => [
            'api_key' => env('OPENROUTER_API_KEY'),
            'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
            'models'   => [
                env('OPENROUTER_MODEL_PRIMARY', 'qwen/qwen3-32b'),
                env('OPENROUTER_MODEL_FALLBACK1', 'mistralai/mistral-large'),
                // ...fallback2..4
            ],
            'make' => fn () => new \App\Services\OpenRouterService(),
        ],
        'nvidia'   => [ 'api_key' => env('NVIDIA_API_KEY'), 'models' => [env('NVIDIA_MODEL')], 'make' => ... ],
        'kimi'     => [ ... ],
        'tokenrouter' => [ 'api_key' => env('TOKENROUTER_API_KEY'), 'base_url' => env('TOKENROUTER_BASE_URL', 'https://api.tokenrouter.com/v1'), 'models' => [env('TOKENROUTER_MODEL_PRIMARY'), ...], 'make' => ... ],
        'qwen'     => [ ... ],
        'deepseek' => [ ... ],
    ],
    'timeout' => 60,
    'max_attempts' => 8,          // tope global de llamadas al gateway
    'log_channel' => 'llm',
];
```

### 3.4 Gateway: `App\Services\Llm\LlmGatewayService`

```php
final class LlmGatewayService
{
    public function __construct(private array $providers, private LlmLogger $logger) {}

    /**
     * Prueba proveedores en orden; dentro de cada uno, modelos en orden.
     * @param string|array $message
     * @param array $overrides  model, max_tokens, temperature, timeout, require_key (JSON a validar)
     * @param list<string> $providerOrder  opcional; default: orden de config
     */
    public function ask(
        string $systemPrompt,
        string|array $message,
        array $overrides = [],
        ?array $providerOrder = null,
    ): LlmResult;
}
```

**Algoritmo:**
1. Recorrer `$providerOrder` (o `array_keys(config('llm.providers'))`).
2. Por proveedor: si `! isConfigured()` → `errors[] = "{key}: sin API key"` y continuar.
3. Por modelo de `$provider->models()`: llamar `ask(..., $overrides + ['model' => $modelo])` con timeout de `config('llm.timeout')`.
4. Si `success && content` no vacío:
   - Si `$overrides['require_key']` existe → validar que el JSON tenga esa clave. Si no, tratar como fallo.
   - Si cumple → retornar `LlmResult::ok(...)`.
5. En cada fallo, acumular `"{key}({modelo}): {error}"` y `$logger->info('llm.fallback', ...)`.
6. Si se supera `max_attempts` o se agotaron todas las opciones → `LlmResult::fail(implode(' | ', array_slice($errors, 0, 8)))` + `$logger->error('llm.exhausted', ...)`.

**Observabilidad (`LlmLogger`):** canal `llm` (nuevo en `config/logging.php`). Loguea `provider`, `model`, `success`, `usage`, `cost` (si `usage.cost`), `elapsed_ms` y `correlation_id` (generado por llamada).

---

## 4. Contratos de uso (ejemplos de migración)

### 4.1 `ActivityImprovementService` (→ gateway)

```php
$result = $this->gateway->ask($systemPrompt, $userPrompt, [
    'temperature' => 0.5,
    'max_tokens'  => 4096,
]);
// Reemplaza callWithFallback/safeCall/openRouterModels.
```

### 4.2 `InfografiaGeneratorService` (con validación de esquema)

```php
$result = $this->gateway->ask($prompt, $userPrompt, ['require_key' => 'estructura']);
```

### 4.3 `LmsAiOrchestrationService` (cadena por modelo)

```php
$result = $this->gateway->ask($systemPrompt, $userPrompt, [
    'models' => [
        config('openrouter.model_primary'),
        config('openrouter.model_fallback1'),
        // ...
    ],
    'providerOrder' => ['openrouter', 'nvidia', 'kimi'],
]);
```

---

## 5. Migración incremental (no romper)

| Fase | Acción | Riesgo |
|---|---|---|
| **F1** | Crear `App\Services\Llm\*` (LlmResult, contracts, adapters, LlmLogger, LlmGatewayService) + `config/llm.php` + canal `llm`. Sin tocar servicios existentes. | Bajo |
| **F2** | Adaptadores para los proveedores existentes (wrappers). | Bajo |
| **F3** | Migrar `ActivityImprovementService` al gateway (paridad de comportamiento: OpenRouter+Nvidia+Kimi+TokenRouter). | Medio |
| **F4** | Migrar `InfografiaGeneratorService`, `LmsMermaidAiRepairService`, `LmsSvgAiRepairService`, `LmsAiOrchestrationService`. | Medio/Alto |
| **F5** | Tests + seeder de dataset sintético + documentación. | Bajo |

**Garantía:** en cada fase, los tests de los servicios migrados deben pasar sin cambios de contrato hacia afuera (los métodos públicos `improve()`, `generateInfografia()`, etc. no cambian su firma).

---

## 6. Tests

| Nivel | Qué cubre |
|---|---|
| **Unit — LlmResult** | Accessors ok/fail. |
| **Unit — adapters** | `OpenAIServiceAdapter` traduce payload/respuesta; `GeminiServiceAdapter` traduce `parts[].text`. |
| **Unit — LlmGatewayService** | `Http::fake()`: 1º proveedor éxito; 1º falla→ 2º éxito; sin key salta; `require_key` inválida → siguiente; timeout → error; agotado → `LlmResult::fail` con la lista de errores; `max_attempts` respetado. |
| **Feature — command** | `ai:test-tokenrouter` y un comando genérico `ai:test-llm --provider=... --prompt=...`. |
| **Feature — migración** | Al migrar `ActivityImprovementService`, el test `ActivityImprovementTest` (mock) y un test con fake del gateway deben pasar. |

---

## 7. Tickets de descomposición

| Ticket | Alcance | Aceptación |
|---|---|---|
| **LLM-001a** | `LlmResult`, `Contracts\LlmProvider`, adapters (`OpenAIServiceAdapter`, `GeminiServiceAdapter`), `config/llm.php`, canal `llm` | Cada adaptador traduce correctamente su proveedor; config resuelve claves/modelos |
| **LLM-001b** | `LlmGatewayService` + `LlmLogger` | Fallback por proveedor y por modelo; salto por `api_key`; `require_key`; log con `usage`/`cost` |
| **LLM-001c** | Comando `ai:test-llm` (multi-proveedor) | Prueba cada proveedor/mostrar resultado o error |
| **LLM-001d** | Migrar `ActivityImprovementService` | Paridad de comportamiento; tests verdes |
| **LLM-001e** | Migrar `InfografiaGeneratorService`, `LmsMermaidAiRepairService`, `LmsSvgAiRepairService`, `LmsAiOrchestrationService` | Paridad; tests del módulo verdes |
| **LLM-001f** | Tests unitarios/feature + Pint | Suite completa verde |

---

## 8. Errores y mensajes

- **Sin API key:** el gateway lo salta y el mensaje indica cuál falta: `Openrouter: sin API key`.
- **Todos fallan:** `Todos los proveedores LLM fallaron. Detalle: <8 primeros errores>`.
- **Respuesta inválida (require_key):** `{key}({modelo}): respuesta inválida (falta clave "estructura")`.
- **Excepción HTTP:** `{key}({modelo}): HTTP 4xx/5xx: <body> <300 chars>`.

---

## 9. ADRs (decisiones de arquitectura)

| ADR | Decisión | Por qué |
|---|---|---|
| **ADT-001** | Los wrappers existentes se **envuelven** con adaptadores, no se reescriben | Migración segura; el shape OpenAI-compatible se normaliza a `LlmResult` |
| **ADT-002** | El gateway orquesta **proveedor → modelo** (dos niveles) | Cubre tanto "cambio de proveedor" como "fallback de modelos" dentro del mismo provider |
| **ADT-003** | `require_key` (validación de respuesta) es **opcional y por llamada** | Algunos servicios validan JSON, otros solo necesitan texto |
| **ADT-004** | `config/llm.php` es el contrato del gateway; los `config/{provider}.php` se mantienen como fuente por compatibilidad | Evita romper consumidores actuales de `config('openrouter.*')` |
| **ADT-005** | Observabilidad vía canal `llm` (log) en vez de tabla nueva | Bajo costo, sin requerimiento de dashboard en v1 |

---

## 10. Riesgos

- **Acoplar demasiado**: el gateway no debe conocer la semántica de cada servicio (prompts, validaciones); eso queda en el servicio que lo usa.
- **Regresiones en la migración**: cada fase migrada debe mantener paridad de comportamiento; por eso F1-F2 son solo capa nueva y la migración es incremental con tests.
- **Proveedores nativos (Gemini)**: requieren adapter distinto (no OpenAI-compatible); cubierto por `GeminiServiceAdapter`.
