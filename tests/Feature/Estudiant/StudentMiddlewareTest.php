<?php

namespace Tests\Feature\Estudiant;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function test_student_with_is_student_true_can_access(): void
    {
        $user = User::factory()->create(['is_student' => true]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(200);
    }

    public function test_student_with_is_student_false_is_blocked(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get(route('student.lms.home'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_bypass_middleware(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'is_student' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(200);
    }

    public function test_profile_route_requires_student(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.profile'));

        $response->assertStatus(403);
    }

    public function test_academic_route_requires_student(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.academic'));

        $response->assertStatus(403);
    }

    public function test_lessons_route_requires_student(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.lessons'));

        $response->assertStatus(403);
    }

    public function test_resources_route_requires_student(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.resources'));

        $response->assertStatus(403);
    }
}
