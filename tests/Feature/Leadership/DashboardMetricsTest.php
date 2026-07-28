<?php

namespace Tests\Feature\Leadership;

use App\Models\User;
use App\Services\Leadership\LeadershipService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dashboard_returns_metrics_structure(): void
    {
        $user = User::factory()->leadership()->create();

        $service = new LeadershipService($user);
        $metrics = $service->dashboardMetrics();

        $this->assertArrayHasKey('total_areas', $metrics);
        $this->assertArrayHasKey('total_asignaturas', $metrics);
        $this->assertArrayHasKey('total_pevas', $metrics);
        $this->assertArrayHasKey('activities_in_review', $metrics);
        $this->assertArrayHasKey('total_profesores', $metrics);
        $this->assertArrayHasKey('areas', $metrics);
    }

    public function test_dashboard_page_renders(): void
    {
        $user = User::factory()->leadership()->create();

        $response = $this->actingAs($user)->get(route('app.leadership.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Panel de Seguimiento');
    }
}
