<?php

namespace Tests\Feature\Navbar;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CoordinacionNavbarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_coordinacion_navbar_shows_coordinacion_items(): void
    {
        $user = User::factory()->coordinacion()->create();

        $this->actingAs($user)
            ->get('/app/coordinacion')
            ->assertOk()
            ->assertSee('Coordinación')
            ->assertSee('Dashboard')
            ->assertSee('Pensums')
            ->assertSee('Profesores')
            ->assertSee('Carga Académica')
            ->assertSee('Actividades')
            ->assertSee('Lecciones')
            ->assertSee('Recursos')
            ->assertSee('Horario')
            ->assertSee('Suplencias');
    }

    public function test_coordinacion_navbar_hides_lms_monitor_link(): void
    {
        $user = User::factory()->coordinacion()->create();

        $this->actingAs($user)
            ->get('/app/coordinacion')
            ->assertOk()
            ->assertDontSee('Contenido LMS')
            ->assertDontSee('app/planning/lms/monitor');
    }

    public function test_coordinacion_navbar_shows_planning_group(): void
    {
        $user = User::factory()->coordinacion()->create();

        $this->actingAs($user)
            ->get('/app/coordinacion')
            ->assertOk()
            ->assertSee('Planificación');
    }

    public function test_admin_can_see_coordinacion_navbar(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Coordinación');
    }
}