<?php

namespace Tests\Unit\Leadership;

use App\Models\User;
use App\Services\Leadership\LeadershipService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LeadershipScopeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_with_no_areas_gets_empty_collection(): void
    {
        $user = User::factory()->leadership()->create();
        $service = new LeadershipService($user);

        $areaIds = $service->getAssignedAreaIds();

        $this->assertTrue($areaIds->isEmpty());
    }

    public function test_admin_returns_empty_collection_signal(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $service = new LeadershipService($user);

        $areaIds = $service->getAssignedAreaIds();

        // Admin returns empty collection as "no restriction" signal
        $this->assertTrue($areaIds->isEmpty());
    }

    public function test_user_model_has_is_leadership_accessor(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_leadership' => true]);

        $this->assertTrue($user->is_leadership);
        $this->assertTrue($user->isLeadership());
    }

    public function test_admin_inherits_is_leadership(): void
    {
        $user = User::factory()->create(['is_admin' => true, 'is_leadership' => false]);

        $this->assertTrue($user->is_leadership);
        $this->assertFalse($user->isLeadership());
    }

    public function test_role_label_returns_jefe_de_area(): void
    {
        $user = User::factory()->leadership()->create();

        $this->assertEquals('Jefe de Área', $user->role_label);
    }

    public function test_memoization_prevents_duplicate_queries(): void
    {
        $user = User::factory()->leadership()->create();
        $service = new LeadershipService($user);

        // First call
        $first = $service->getAssignedAreaIds();
        // Second call — should use memoization
        $second = $service->getAssignedAreaIds();

        $this->assertSame($first, $second);
    }
}
