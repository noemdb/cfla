<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_users_can_authenticate_with_username(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_admin_user_redirects_to_admin(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }

    public function test_diagnostic_user_redirects_to_admin(): void
    {
        $user = User::factory()->create([
            'is_diagnostic' => true,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }

    public function test_planner_user_redirects_to_planning(): void
    {
        $user = User::factory()->create([
            'is_planner' => true,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('app.planning.index'));
    }

    public function test_leadership_user_redirects_to_leadership_dashboard(): void
    {
        $user = User::factory()->leadership()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('app.leadership.dashboard'));
    }

    public function test_coordinacion_user_redirects_to_coordinacion(): void
    {
        $user = User::factory()->coordinacion()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('app.coordinacion.index'));
    }

    public function test_profesor_user_redirects_to_profesor_home(): void
    {
        $user = User::factory()->create([
            'is_profesor' => true,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect('/app/profesors/home');
    }

    public function test_student_user_redirects_to_estudiante_home(): void
    {
        $user = User::factory()->create([
            'is_student' => true,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect('/app/estudiante/home');
    }

    public function test_user_without_role_redirects_to_home(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
    }
}
