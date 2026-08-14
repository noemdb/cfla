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
}
