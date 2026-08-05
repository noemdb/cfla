<?php

namespace Tests\Feature\Director;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DirectorMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function test_director_user_can_access_dashboard(): void
    {
        $user = User::factory()->director()->create();

        $this->actingAs($user)
            ->get('/app/director')
            ->assertOk();
    }

    public function test_non_director_user_gets_403(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_director' => false]);

        $this->actingAs($user)
            ->get('/app/director')
            ->assertForbidden();
    }

    public function test_admin_bypasses_director_check(): void
    {
        // getIsDirectorAttribute() hace que is_director sea true para admins.
        $user = User::factory()->create(['is_admin' => true, 'is_director' => false]);

        $this->actingAs($user)
            ->get('/app/director')
            ->assertOk();
    }
}
