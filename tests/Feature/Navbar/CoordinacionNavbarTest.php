<?php

namespace Tests\Feature\Navbar;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CoordinacionNavbarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_coordinacion_navbar_hides_lms_monitor_link(): void
    {
        $user = User::factory()->coordinacion()->create();

        $this->actingAs($user)
            ->get('/app/coordinacion')
            ->assertOk()
            ->assertDontSee('Contenido LMS')
            ->assertDontSee('app/planning/lms/monitor');
    }
}
