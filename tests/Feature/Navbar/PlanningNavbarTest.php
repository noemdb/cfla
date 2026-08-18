<?php

namespace Tests\Feature\Navbar;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PlanningNavbarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_planning_navbar_shows_planning_mega_menu(): void
    {
        $user = User::factory()->planner()->create();

        $this->actingAs($user)
            ->get('/app/planning')
            ->assertOk()
            ->assertSee('Planificación')
            ->assertSee('Indicadores')
            ->assertSee('Actividades')
            ->assertSee('Carga Académica')
            ->assertSee('Lapsos Académicos')
            ->assertSee('Programas Educativos')
            ->assertSee('Planes de Estudio')
            ->assertSee('Áreas de Conocimiento')
            ->assertSee('Asignaturas')
            ->assertSee('Grados')
            ->assertSee('Secciones')
            ->assertSee('Inscripciones')
            ->assertSee('Pensums')
            ->assertSee('Diagnóstico')
            ->assertSee('Referentes')
            ->assertSee('Profesores')
            ->assertSee('Competiciones')
            ->assertSee('Contenido LMS')
            ->assertSee('Diagramas de Flujo')
            ->assertSee('Horario')
            ->assertSee('Suplencias');
    }

    public function test_planning_navbar_shows_profesor_and_coordinacion_groups(): void
    {
        $user = User::factory()->planner()->create();

        $this->actingAs($user)
            ->get('/app/planning')
            ->assertOk()
            ->assertSee('Profesor')
            ->assertSee('Coordinación');
    }

    public function test_admin_can_see_planning_navbar(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Planificación');
    }
}