<?php

namespace Tests\Feature\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_see_home(): void
    {
        $student = User::factory()->create(['is_student' => true]);

        $response = $this->actingAs($student)
            ->get(route('student.lms.home'));

        $response->assertStatus(200);
    }

    public function test_non_student_cannot_access_student_routes(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(403);
    }

    public function test_student_cannot_access_unpublished_activity(): void
    {
        $student = User::factory()->create(['is_student' => true]);
        $activity = Activity::factory()->create();

        $response = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity));

        $response->assertStatus(404);
    }

    public function test_student_can_access_published_activity(): void
    {
        $student = User::factory()->create(['is_student' => true]);
        $activity = Activity::factory()->create();

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity));

        $response->assertStatus(200);
    }
}
