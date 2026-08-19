<?php

namespace Tests\Unit\Lms;

use App\Services\Lms\LmsAiOrchestrationService;
use App\Services\OpenRouterService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LmsAiOrchestrationServiceTest extends TestCase
{
    private array $askResults = [];

    private array $askCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->askResults = [];
        $this->askCalls = [];

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
}
