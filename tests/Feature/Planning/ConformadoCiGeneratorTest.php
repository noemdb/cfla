<?php

namespace Tests\Feature\Planning;

use App\Models\app\Learner\Estudiant;
use App\Models\app\Learner\Representant;
use App\Services\Planning\ConformadoCiGenerator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConformadoCiGeneratorTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_generates_ci_with_7_digits_plus_3_uppercase_letters(): void
    {
        $generator = app(ConformadoCiGenerator::class);
        $reserved = [];

        for ($i = 0; $i < 50; $i++) {
            $ci = $generator->generate($reserved);

            $this->assertMatchesRegularExpression('/^\d{7}[A-Z]{3}$/', $ci);
            $this->assertTrue(ConformadoCiGenerator::isConformado($ci));
        }
    }

    /** @test */
    public function it_generates_unique_non_consecutive_ci(): void
    {
        $generator = app(ConformadoCiGenerator::class);
        $reserved = [];
        $generated = [];

        for ($i = 0; $i < 500; $i++) {
            $generated[] = $generator->generate($reserved);
        }

        // Únicos en la ejecución (nunca consecutivos/secuenciales).
        $this->assertCount(500, array_unique($generated));

        // No es una secuencia monótona: los valores numéricos no crecen de 1 en 1.
        $numbers = array_map(fn ($ci) => (int) substr($ci, 0, 7), $generated);
        $sequentialSteps = 0;
        for ($i = 1; $i < count($numbers); $i++) {
            if ($numbers[$i] === $numbers[$i - 1] + 1) {
                $sequentialSteps++;
            }
        }
        $this->assertLessThan(count($numbers) - 1, $sequentialSteps);
    }

    /** @test */
    public function it_never_reuses_ci_from_database_or_reserved(): void
    {
        $representant = Representant::factory()->create();
        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Plan de Prueba',
            'description' => 'Plan de pago de prueba',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Estudiant::create([
            'ci_estudiant' => '1234567ABC',
            'name' => 'X',
            'lastname' => 'Y',
            'representant_id' => $representant->id,
            'planpago_id' => $planpagoId,
            'type_ci_id' => 1,
            'status_active' => 'true',
            'status_blacklist' => 'false',
        ]);

        $generator = app(ConformadoCiGenerator::class);
        $reserved = ['7654321ZZZ' => true];

        for ($i = 0; $i < 100; $i++) {
            $ci = $generator->generate($reserved);

            $this->assertNotSame('1234567ABC', $ci);
            $this->assertNotSame('7654321ZZZ', $ci);
        }
    }

    /** @test */
    public function it_validates_conformado_format(): void
    {
        $this->assertTrue(ConformadoCiGenerator::isConformado('4829137XKQ'));
        $this->assertFalse(ConformadoCiGenerator::isConformado('482913XKQ'));
        $this->assertFalse(ConformadoCiGenerator::isConformado('4829137xkq'));
        $this->assertFalse(ConformadoCiGenerator::isConformado('4829137XKQ1'));
        $this->assertFalse(ConformadoCiGenerator::isConformado(''));
    }
}
