<?php

namespace Tests\Feature\Planning;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PdfRoutesSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_planning_format_and_resume_return_pdf(): void
    {
        $user = User::factory()->create(['is_planner' => true]);
        $pev = \App\Models\app\Academy\Pevaluacion::has('activities')->firstOrFail();

        foreach (['format', 'resume'] as $kind) {
            $response = $this->actingAs($user)
                ->get(route("app.planning.activities.{$kind}", $pev->id));
            $response->assertOk();
            $this->assertStringContainsString(
                'pdf',
                strtolower($response->headers->get('Content-Type') ?? '')
            );
        }
    }
}
