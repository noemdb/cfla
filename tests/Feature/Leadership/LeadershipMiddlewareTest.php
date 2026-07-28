<?php

namespace Tests\Feature\Leadership;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LeadershipMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function test_leadership_user_can_access_dashboard(): void
    {
        $user = User::factory()->leadership()->create();

        $response = $this->actingAs($user)->get(route('app.leadership.dashboard'));

        $response->assertStatus(200);
    }

    public function test_non_leadership_user_gets_403(): void
    {
        $user = User::factory()->create(['is_leadership' => false]);

        $response = $this->actingAs($user)->get(route('app.leadership.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_leadership_routes(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->get(route('app.leadership.dashboard'));

        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('app.leadership.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_leadership_user_can_access_activities(): void
    {
        $user = User::factory()->leadership()->create();

        $response = $this->actingAs($user)->get(route('app.leadership.activities'));

        $response->assertStatus(200);
    }

    public function test_leadership_user_can_access_lessons(): void
    {
        $user = User::factory()->leadership()->create();

        $response = $this->actingAs($user)->get(route('app.leadership.lessons'));

        $response->assertStatus(200);
    }

    public function test_leadership_user_can_access_profesores(): void
    {
        $user = User::factory()->leadership()->create();

        $response = $this->actingAs($user)->get(route('app.leadership.profesores'));

        $response->assertStatus(200);
    }
}
