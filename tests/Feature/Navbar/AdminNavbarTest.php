<?php

namespace Tests\Feature\Navbar;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminNavbarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_navbar_shows_admin_items(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Admin')
            ->assertSee('Dashboard')
            ->assertSee('Usuarios');
    }

    public function test_admin_navbar_shows_admin_only_items_for_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Votaciones')
            ->assertSee('Logs')
            ->assertSee('Bitácora');
    }

    public function test_admin_navbar_hides_admin_only_items_for_diagnostic(): void
    {
        $user = User::factory()->diagnostic()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Admin')
            ->assertDontSee('Votaciones')
            ->assertDontSee('Logs')
            ->assertDontSee('Bitácora');
    }
}