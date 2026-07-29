<?php

namespace Tests\Feature\Estudiant;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_is_student_returns_true_when_column_is_true(): void
    {
        $user = User::factory()->create(['is_student' => true]);

        $this->assertTrue($user->isStudent());
    }

    public function test_is_student_returns_false_when_column_is_false(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $this->assertFalse($user->isStudent());
    }

    public function test_is_student_defaults_to_false(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $this->assertFalse($user->isStudent());
        $this->assertFalse($user->is_student);
    }

    public function test_get_role_label_returns_estudiante(): void
    {
        $user = User::factory()->create(['is_student' => true]);

        $this->assertEquals('Estudiante', $user->getRoleLabelAttribute());
    }

    public function test_get_role_label_does_not_return_estudiante_for_non_student(): void
    {
        $user = User::factory()->create([
            'is_student' => false,
            'is_admin' => false,
            'is_planner' => false,
            'is_profesor' => false,
            'is_diagnostic' => false,
            'is_leadership' => false,
        ]);

        $this->assertEquals('Usuario Estándar', $user->getRoleLabelAttribute());
    }
}
