<?php

namespace Tests\Unit\Lms;

use App\Services\Lms\LmsAiOrchestrationService;
use App\Services\NvidiaService;
use App\Services\OpenRouterService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LmsAiOrchestrationServiceTest extends TestCase
{
    private array $askResults = [];

    private array $askCalls = [];

    private array $nvidiaResults = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->askResults = [];
        $this->askCalls = [];
        $this->nvidiaResults = [];

        $fake = new class extends OpenRouterService
        {
            public array $results = [];

            public array $calls = [];

            public function ask(string $systemPrompt, string $userMessage, array $overrides = []): array
            {
                $this->calls[] = [
                    'prompt' => $userMessage,
                    'model' => $overrides['model'] ?? null,
                ];

                return array_shift($this->results) ?? ['success' => true, 'content' => 'respuesta', 'model' => $overrides['model'] ?? null];
            }
        };

        $fake->results = &$this->askResults;
        $fake->calls = &$this->askCalls;

        $this->app->instance(OpenRouterService::class, $fake);

        // Stub de Nvidia para controlar la compactación (P7).
        $fakeNvidia = new class extends NvidiaService
        {
            public array $results = [];

            public function ask(string $systemPrompt, string $userMessage, array $overrides = []): array
            {
                return array_shift($this->results) ?? ['success' => false, 'content' => null, 'error' => 'no stub'];
            }
        };
        $fakeNvidia->results = &$this->nvidiaResults;
        $this->app->instance(NvidiaService::class, $fakeNvidia);
    }

    private function service(): LmsAiOrchestrationService
    {
        return $this->app->make(LmsAiOrchestrationService::class);
    }

    private function chain(string ...$models): array
    {
        return collect($models)->map(fn (string $m) => ['model' => $m, 'label' => $m])->all();
    }

    #[Test]
    public function reintenta_el_mismo_modelo_con_feedback_cuando_el_validador_lo_rechaza()
    {
        config()->set('lms.repair_attempts', 1);

        $this->askResults = [
            ['success' => true, 'content' => '//INICIO'."\n".'Hola'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/primario'],
            ['success' => true, 'content' => '//INICIO'."\n".'Bloque ampliado'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/primario'],
        ];

        $validator = function (string $content): true|string {
            if (str_contains($content, 'Bloque ampliado')) {
                return true;
            }

            return 'Un bloque de //DESARROLLO es demasiado corto.';
        };

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            ['max_tokens' => 4096, 'timeout' => 180],
            3500,
            $validator,
            $this->chain('test/primario'),
        );

        $this->assertTrue($result['success']);
        $this->assertSame('test/primario', $result['model']);
        $this->assertCount(2, $this->askCalls);
        $this->assertSame('test/primario', $this->askCalls[0]['model']);
        $this->assertSame('test/primario', $this->askCalls[1]['model']);
        $this->assertStringContainsString('Un bloque de //DESARROLLO es demasiado corto', $this->askCalls[1]['prompt']);
    }

    #[Test]
    public function valida_al_primer_intento_sin_reparar()
    {
        config()->set('lms.repair_attempts', 1);

        $this->askResults = [
            ['success' => true, 'content' => '//INICIO'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/primario'],
        ];

        $validator = fn (string $content): true => true;

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario'),
        );

        $this->assertTrue($result['success']);
        $this->assertCount(1, $this->askCalls);
    }

    #[Test]
    public function pasa_al_siguiente_modelo_cuando_se_agotan_las_reparaciones()
    {
        config()->set('lms.repair_attempts', 1);

        $this->askResults = [
            ['success' => true, 'content' => 'invalido', 'model' => 'test/primario'],
            ['success' => true, 'content' => 'invalido', 'model' => 'test/primario'],
            ['success' => true, 'content' => 'invalido', 'model' => 'test/fallback'],
            ['success' => true, 'content' => 'invalido', 'model' => 'test/fallback'],
        ];

        $validator = fn (string $content): string => 'Contenido inválido: no cumple la estructura.';

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario', 'test/fallback'),
        );

        $this->assertFalse($result['success']);
        $this->assertCount(4, $this->askCalls);
        $this->assertSame(['test/primario', 'test/primario', 'test/fallback', 'test/fallback'], array_column($this->askCalls, 'model'));
        // El segundo modelo recibe el refuerzo de fallback
        $this->assertStringContainsString('CORRECCIÓN', $this->askCalls[2]['prompt']);
    }

    #[Test]
    public function soporta_validadores_que_devuelven_bool()
    {
        config()->set('lms.repair_attempts', 1);

        $this->askResults = [
            ['success' => true, 'content' => 'invalido', 'model' => 'test/primario'],
            ['success' => true, 'content' => '//INICIO'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/primario'],
        ];

        $validator = function (string $content): bool {
            return ! str_contains($content, 'invalido');
        };

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario'),
        );

        $this->assertTrue($result['success']);
        $this->assertCount(2, $this->askCalls);
        $this->assertStringContainsString('no cumple la estructura requerida', $this->askCalls[1]['prompt']);
    }

    #[Test]
    public function si_el_modelo_falla_por_error_api_no_repara_y_pasa_al_siguiente()
    {
        config()->set('lms.repair_attempts', 3);
        // Evitar dormir en tests por el backoff del 429.
        config()->set('lms.backoff_max_ms', 0);

        $this->askResults = [
            ['success' => false, 'content' => null, 'model' => 'test/primario', 'error' => 'HTTP 429'],
            ['success' => true, 'content' => '//INICIO'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/fallback'],
        ];

        $validator = fn (string $content): true => true;

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario', 'test/fallback'),
        );

        $this->assertTrue($result['success']);
        $this->assertSame('test/fallback', $result['model']);
        $this->assertCount(2, $this->askCalls);
    }

    #[Test]
    public function clasifica_los_errores_por_categoria()
    {
        $service = $this->service();

        $this->assertSame('rate_limit', $service->classifyError('HTTP 429 Too Many Requests'));
        $this->assertSame('credits', $service->classifyError('402 Insufficient credits'));
        $this->assertSame('auth', $service->classifyError('HTTP 401 Unauthorized'));
        $this->assertSame('not_found', $service->classifyError('404 model not found'));
        $this->assertSame('timeout', $service->classifyError('cURL error 28: Operation timed out'));
        $this->assertSame('server', $service->classifyError('HTTP 503 Service Unavailable'));
        $this->assertSame('connection', $service->classifyError('Connection refused'));
        $this->assertSame('safety', $service->classifyError('El modelo rechazó la solicitud'));
        $this->assertSame('empty', $service->classifyError('el modelo finalizó sin generar contenido'));
        $this->assertSame('unknown', $service->classifyError('Algo extraño ocurrió'));
    }

    #[Test]
    public function mapa_de_estrategia_por_categoria()
    {
        $service = $this->service();
        $strategy = fn (string $c) => $service->strategyForError($c);

        // Configuración global inválida → abortar cadena
        $this->assertSame('SKIP_CHAIN', $strategy('auth'));
        $this->assertSame('SKIP_CHAIN', $strategy('credits'));

        // Modelo concreto no disponible (404) → saltar al siguiente
        $this->assertSame('NEXT', $strategy('not_found'));

        // Transitorios → reintentar una vez
        $this->assertSame('RETRY_ONCE', $strategy('timeout'));
        $this->assertSame('RETRY_ONCE', $strategy('server'));

        // Limitación/caída → backoff y siguiente
        $this->assertSame('BACKOFF_THEN_NEXT', $strategy('rate_limit'));
        $this->assertSame('BACKOFF_THEN_NEXT', $strategy('connection'));

        // Vacio/seguridad/genérico → saltar al siguiente
        $this->assertSame('NEXT', $strategy('empty'));
        $this->assertSame('NEXT', $strategy('safety'));
        $this->assertSame('NEXT', $strategy('unknown'));
    }

    #[Test]
    public function error_401_aborta_la_cadena_sin_recorrer_el_resto()
    {
        config()->set('lms.repair_attempts', 2);
        config()->set('lms.backoff_max_ms', 0);

        $this->askResults = [
            ['success' => false, 'content' => null, 'model' => 'test/primario', 'error' => 'HTTP 401 Unauthorized'],
        ];

        $validator = fn (string $content): true => true;

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario', 'test/fallback1', 'test/fallback2'),
        );

        // Sin reintento ni recorrido: sólo 1 llamada al primer modelo.
        $this->assertFalse($result['success']);
        $this->assertCount(1, $this->askCalls);
    }

    #[Test]
    public function error_404_modelo_no_disponible_salta_al_siguiente_modelo()
    {
        config()->set('lms.repair_attempts', 2);
        config()->set('lms.backoff_max_ms', 0);

        // El modelo primario devuelve 404 "no longer available" (como Ling-2.6).
        // NO debe abortar la cadena: debe saltar al siguiente modelo.
        $this->askResults = [
            ['success' => false, 'content' => null, 'model' => 'test/primario', 'error' => 'HTTP 404: {"error":{"message":"Ling-2.6-flash is no longer available as a free model","code":404}}'],
            ['success' => true, 'content' => '//INICIO'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/fallback'],
        ];

        $validator = fn (string $content): true => true;

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario', 'test/fallback'),
        );

        // La cadena continúa: el fallback resuelve la generación.
        $this->assertTrue($result['success']);
        $this->assertSame('test/fallback', $result['model']);
        $this->assertCount(2, $this->askCalls);
    }

    #[Test]
    public function error_timeout_reintenta_una_vez_el_mismo_modelo_y_luego_pasa()
    {
        config()->set('lms.repair_attempts', 2);
        config()->set('lms.backoff_max_ms', 0);

        // 1º: timeout (transitorio) → 2º: mismo modelo timeout de nuevo
        // (ya se retentó) → siguiente modelo OK.
        $this->askResults = [
            ['success' => false, 'content' => null, 'model' => 'test/primario', 'error' => 'cURL error 28: Operation timed out'],
            ['success' => false, 'content' => null, 'model' => 'test/primario', 'error' => 'cURL error 28: Operation timed out'],
            ['success' => true, 'content' => '//INICIO'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/fallback'],
        ];

        $validator = fn (string $content): true => true;

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario', 'test/fallback'),
        );

        $this->assertTrue($result['success']);
        $this->assertSame('test/fallback', $result['model']);
        // 2 intentos en el modelo primario + 1 en el fallback
        $this->assertCount(3, $this->askCalls);
    }

    #[Test]
    public function error_429_usa_backoff_y_pasa_al_siguiente_modelo()
    {
        config()->set('lms.repair_attempts', 2);
        // backoff_max_ms = 0 → la espera se descarta en tests
        config()->set('lms.backoff_max_ms', 0);

        $this->askResults = [
            ['success' => false, 'content' => null, 'model' => 'test/primario', 'error' => 'HTTP 429 Too Many Requests'],
            ['success' => true, 'content' => '//INICIO'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/fallback'],
        ];

        $validator = fn (string $content): true => true;

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario', 'test/fallback'),
        );

        $this->assertTrue($result['success']);
        $this->assertSame('test/fallback', $result['model']);
        // 1 intento (no reintenta) en primario + 1 en fallback
        $this->assertCount(2, $this->askCalls);
    }

    #[Test]
    public function backoff_respeta_el_tope_global()
    {
        config()->set('lms.backoff_max_ms', 0);

        // Con tope 0 no debe dormir y retorna 0.
        $this->assertSame(0, $this->service()->backoffSleep(3));
    }

    #[Test]
    public function backoff_respeta_el_presupuesto_por_generacion()
    {
        // Budget por-espera alto, pero presupuesto de generación pequeño: la
        // espera se recorta al presupuesto restante. Con budget 1 ms la
        // espera real (usleep) es despreciable y asertamos el clamp.
        config()->set('lms.backoff_max_ms', 100000);

        $slept = $this->service()->backoffSleep(3, 0, 1);
        $this->assertLessThanOrEqual(1, $slept);
    }

    #[Test]
    public function backoff_sin_presupuesto_se_domina_con_el_tope_max()
    {
        config()->set('lms.backoff_max_ms', 1);

        // Sin presupuesto de generación (0): solo gobierna el tope por espera.
        $slept = $this->service()->backoffSleep(10, 0, 0);
        $this->assertLessThanOrEqual(1, $slept);
    }

    #[Test]
    public function timeout_reintenta_consumiendo_el_presupuesto_de_backoff()
    {
        config()->set('lms.repair_attempts', 2);
        // Presupuesto grande pero por-espera acotado para no dormir en tests.
        config()->set('lms.backoff_max_ms', 0);
        config()->set('lms.backoff_generation_max_ms', 8000);

        $this->askResults = [
            ['success' => false, 'content' => null, 'model' => 'test/primario', 'error' => 'cURL error 28: Operation timed out'],
            ['success' => true, 'content' => '//INICIO'."\n\n".'//DESARROLLO'."\n\n".'//CIERRE', 'model' => 'test/primario'],
        ];

        $validator = fn (string $content): true => true;

        $result = $this->service()->askWithCompaction(
            'Sistema',
            'Usuario',
            [],
            3500,
            $validator,
            $this->chain('test/primario'),
        );

        // El timeout se reintentó 1 vez (transitorio) y luego el 2º fue válido.
        $this->assertTrue($result['success']);
        $this->assertSame('test/primario', $result['model']);
        $this->assertCount(2, $this->askCalls);

        // El fallo técnico reinteció sin gastar el presupuesto de reparación:
        // el prompt del 2º intento NO lleva el refuerzo de reparación.
        $this->assertStringNotContainsString('CORRECCIÓN', $this->askCalls[1]['prompt']);
    }

    // ─── Compactación robusta (P7) ──────────────────────────────

    private const COMPACT_ORIGINAL = <<<'TXT'
### Contexto

**Curso:** 6to Grado · Matemáticas · Sec. B

**Actividad pedagogica:**
• Tema generador: Fracciones
• Tejido temático: operaciones con fracciones equivalentes
• Enseñanza: explicar mediante ejemplos
• Aprendizaje esperado: resolver sumas de fracciones
• Referentes teóricos: manual del docente
• ODS/Sistematización: educación de calidad

**Indicadores de logro:**
• Compara fracciones equivalentes
• Resuelve problemas con denominadores comunes

**Referentes normativos:**
Referente: Currículo Nacional (CN)
  Competencia: Razonamiento matemático
    - Resuelve problemas
    - Aplica estrategias
TXT;

    #[Test]
    public function nvidia_compacta_preservando_campos_criticos()
    {
        config()->set('lms.backoff_max_ms', 0);

        // Nvidia devuelve una versión reducida que conserva las cabeceras.
        $this->nvidiaResults = [
            ['success' => true, 'content' => "**Curso:** 6to Grado · Matemáticas\n**Tema generador:** Fracciones\n**Indicadores:** comparar\n**Referentes normativos:** CN", 'model' => 'nvidia'],
        ];

        $report = $this->service()->compactPrompt(self::COMPACT_ORIGINAL);

        $this->assertSame('nvidia', $report['method']);
        $this->assertLessThan(strlen(self::COMPACT_ORIGINAL), $report['final_size']);
        $this->assertSame([], $report['lost_fields']);
    }

    #[Test]
    public function cae_al_compactador_local_si_nvidia_pierde_campos_criticos()
    {
        config()->set('lms.backoff_max_ms', 0);

        // Nvidia devuelve una respuesta reducida PERO sin "Tema generador":
        // el validador curricular la rechaza y debe caer al compactador local.
        $this->nvidiaResults = [
            ['success' => true, 'content' => "**Curso:** 6to · Matemáticas\n**Indicadores:** comparar\n**Referentes normativos:** CN", 'model' => 'nvidia'],
        ];

        $report = $this->service()->compactPrompt(self::COMPACT_ORIGINAL);

        $this->assertNotSame('nvidia', $report['method']);
        $this->assertSame([], $report['lost_fields']);
    }

    #[Test]
    public function cae_al_truncado_por_secciones_si_nada_conserva_lo_critico()
    {
        config()->set('lms.backoff_max_ms', 0);

        // Nvidia y el compactador local fallan en conservar todo; el truncado
        // por secciones debe ser el último recurso antes del original.
        $this->nvidiaResults = [
            ['success' => false, 'content' => null, 'error' => 'HTTP 500 Server Error'],
            ['success' => false, 'content' => null, 'error' => 'HTTP 500 Server Error'],
        ];

        $report = $this->service()->compactPrompt(self::COMPACT_ORIGINAL);

        $this->assertContains($report['method'], ['truncate_sections', 'none']);
    }

    #[Test]
    public function lost_curricular_fields_detecta_los_marcadores_perdidos()
    {
        $service = $this->service();

        $compacted = "**Curso:** 6to Grado · Matemáticas\n**Indicadores:** comparar\n";

        $lost = $service->lostCurricularFields(self::COMPACT_ORIGINAL, $compacted);

        // Falta "Tema generador" y "Referentes normativos" → ambos perdidos.
        $this->assertContains('tema_generador', $lost);
        $this->assertContains('referentes', $lost);
        // Curso e indicadores siguen presentes → no perdidos.
        $this->assertNotContains('grado', $lost);
        $this->assertNotContains('indicadores', $lost);
    }

    #[Test]
    public function nvidia_reintenta_una_vez_ante_fallo_transitorio()
    {
        config()->set('lms.backoff_max_ms', 0);

        // 1º timeout (transitorio) → 2º éxito.
        $this->nvidiaResults = [
            ['success' => false, 'content' => null, 'error' => 'cURL error 28: Operation timed out'],
            ['success' => true, 'content' => "**Curso:** 6to Grado · Matemáticas\n**Tema generador:** Fracciones\n**Indicadores:** comparar\n**Referentes normativos:** CN", 'model' => 'nvidia'],
        ];

        $report = $this->service()->compactPrompt(self::COMPACT_ORIGINAL);

        $this->assertSame('nvidia', $report['method']);
    }
}
