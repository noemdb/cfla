<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_validates_username_not_email(): void
    {
        $this->post('/login', ['password' => 'x'])
            ->assertSessionHasErrors('username')
            ->assertSessionDoesntHaveErrors('email');
    }

    public function test_login_succeeds_with_username_and_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ])->assertRedirect('/');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'username' => $user->username,
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('username');
    }

    // ─── Redirect post-login según rol ─────────────────────────────

    /** @test */
    public function un_profesor_es_redirigido_a_su_home(): void
    {
        $user = User::factory()->create(['is_profesor' => true, 'is_inicial' => false]);

        $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ])->assertRedirect('/app/profesors/home');
    }

    /** @test */
    public function un_docente_inicial_es_redirigido_al_modulo(): void
    {
        $user = User::factory()->create(['is_profesor' => false, 'is_inicial' => true]);

        $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ])->assertRedirect('/app/inicials');
    }

    /**
     * El docente con ambos roles (profesor + inicial) va al panel del profesor:
     * isProfesor() se comprueba antes que isInicial() en el LoginController.
     *
     * @test
     */
    public function un_usuario_con_ambos_roles_va_al_home_del_profesor(): void
    {
        $user = User::factory()->create(['is_profesor' => true, 'is_inicial' => true]);

        $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ])->assertRedirect('/app/profesors/home');
    }
}
