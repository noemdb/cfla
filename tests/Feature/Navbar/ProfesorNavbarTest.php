<?php

namespace Tests\Feature\Navbar;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfesorNavbarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_profesor_navbar_shows_profesor_items(): void
    {
        $user = User::factory()->profesor()->create();

        $this->actingAs($user)
            ->get('/app/profesors')
            ->assertOk()
            ->assertSee('Profesor')
            ->assertSee('Dashboard')
            ->assertSee('Actividades')
            ->assertSee('Diagnósticos')
            ->assertSee('Competencias')
            ->assertSee('Contenido LMS')
            ->assertSee('Comentarios')
            ->assertSee('Mi Bitácora')
            ->assertSee('Mi Horario')
            ->assertSee('Mis Suplencias');
    }

    public function test_profesor_navbar_shows_coordinacion_and_admin_groups(): void
    {
        $user = User::factory()->profesor()->create();

        $this->actingAs($user)
            ->get('/app/profesors')
            ->assertOk()
            ->assertSee('Coordinación')
            ->assertSee('Admin');
    }
}