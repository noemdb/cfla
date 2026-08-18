<?php

namespace Tests\Feature\Navbar;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DirectorNavbarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_director_navbar_shows_director_items(): void
    {
        $user = User::factory()->director()->create();

        $this->actingAs($user)
            ->get('/app/director')
            ->assertOk()
            ->assertSee('Dirección')
            ->assertSee('Dashboard')
            ->assertSee('Pensums')
            ->assertSee('Carga Académica')
            ->assertSee('Actividades')
            ->assertSee('Lecciones')
            ->assertSee('Recursos')
            ->assertSee('Profesores')
            ->assertSee('Contenido LMS');
    }

    public function test_director_navbar_shows_planning_and_coordinacion_groups(): void
    {
        $user = User::factory()->director()->create();

        $this->actingAs($user)
            ->get('/app/director')
            ->assertOk()
            ->assertSee('Planificación')
            ->assertSee('Coordinación');
    }

    public function test_admin_can_see_director_navbar(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dirección');
    }
}
