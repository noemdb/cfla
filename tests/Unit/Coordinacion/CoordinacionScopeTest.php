<?php

namespace Tests\Unit\Coordinacion;

use App\Models\User;
use App\Services\Lms\CoordinacionScopeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CoordinacionScopeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_with_no_peducativos_gets_empty_collection(): void
    {
        $user = User::factory()->coordinacion()->create();
        $service = new CoordinacionScopeService($user);

        $peducativoIds = $service->getPeducativoIds();

        $this->assertTrue($peducativoIds->isEmpty());
    }

    public function test_user_model_has_is_coordinacion_method(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $this->assertTrue($user->isCoordinacion());
    }

    public function test_non_coordinacion_user_returns_false(): void
    {
        $user = User::factory()->create(['is_coordinacion' => false]);

        $this->assertFalse($user->isCoordinacion());
    }

    public function test_role_label_returns_coordinacion(): void
    {
        $user = User::factory()->coordinacion()->create();

        $this->assertEquals('Coordinación', $user->role_label);
    }

    public function test_coordinacion_does_not_inherit_planner(): void
    {
        $user = User::factory()->coordinacion()->create();

        $this->assertFalse($user->is_planner);
    }

    public function test_memoization_prevents_duplicate_queries(): void
    {
        $user = User::factory()->coordinacion()->create();
        $service = new CoordinacionScopeService($user);

        // First call
        $first = $service->getPeducativoIds();
        // Second call — should use memoization
        $second = $service->getPeducativoIds();

        $this->assertSame($first, $second);
    }
}
