<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_perfil_page_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/perfil')
            ->assertOk()
            ->assertSee('Perfil de Usuario')
            ->assertSee('Información de Perfil')
            ->assertSee('Cambiar Contraseña')
            ->assertSee('Eliminar Cuenta');
    }

    public function test_perfil_requires_authentication(): void
    {
        $this->get('/perfil')->assertRedirect(route('login'));
    }
}
