<?php

namespace Tests\Feature\Coordinacion;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CoordinacionMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function test_coordinacion_user_can_access_dashboard(): void
    {
        $user = User::factory()->coordinacion()->create();

        $response = $this->actingAs($user)->get(route('app.coordinacion.index'));

        $response->assertStatus(200);
    }

    public function test_non_coordinacion_user_gets_403(): void
    {
        $user = User::factory()->create(['is_coordinacion' => false]);

        $response = $this->actingAs($user)->get(route('app.coordinacion.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_coordinacion_routes(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->get(route('app.coordinacion.index'));

        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('app.coordinacion.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_coordinacion_user_can_access_pensums(): void
    {
        $user = User::factory()->coordinacion()->create();

        $response = $this->actingAs($user)->get(route('app.coordinacion.pensums'));

        $response->assertStatus(200);
    }

    public function test_coordinacion_user_can_access_carga_academica(): void
    {
        $user = User::factory()->coordinacion()->create();

        $response = $this->actingAs($user)->get(route('app.coordinacion.carga-academica'));

        $response->assertStatus(200);
    }

    public function test_coordinacion_user_can_access_activities(): void
    {
        $user = User::factory()->coordinacion()->create();

        $response = $this->actingAs($user)->get(route('app.coordinacion.activities'));

        $response->assertStatus(200);
    }

    public function test_coordinacion_user_can_access_lessons(): void
    {
        $user = User::factory()->coordinacion()->create();

        $response = $this->actingAs($user)->get(route('app.coordinacion.lessons'));

        $response->assertStatus(200);
    }

    public function test_coordinacion_user_can_access_resources(): void
    {
        $user = User::factory()->coordinacion()->create();

        $response = $this->actingAs($user)->get(route('app.coordinacion.resources'));

        $response->assertStatus(200);
    }
}
