<?php

namespace Tests\Feature\Director;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class Fase6RenderSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_director_routes_render_as_get_only(): void
    {
        $user = User::factory()->create(['is_director' => true]);

        $routes = [
            '/app/director'                 => 200,
            '/app/director/pensums'         => 200,
            '/app/director/carga-academica' => 200,
            '/app/director/activities'      => 200,
            '/app/director/lecciones'       => 200,
            '/app/director/recursos'        => 200,
            '/app/director/profesores'      => 200,
        ];

        foreach ($routes as $url => $status) {
            $response = $this->actingAs($user)->get($url);
            $this->assertEquals($status, $response->getStatusCode(), "GET $url failed");
        }

        $this->assertTrue(true);
    }
}
