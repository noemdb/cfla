<?php

namespace Database\Factories;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityCommentFactory extends Factory
{
    protected $model = ActivityComment::class;

    public function definition(): array
    {
        return [
            'activity_id' => Activity::factory(),
            'user_id'     => User::factory(),
            'body'        => $this->faker->paragraph(),
            'is_approved' => false,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved'  => false,
            'approved_at'  => null,
            'approved_by'  => null,
            'rejected_at'  => null,
            'rejected_by'  => null,
            'rejected_reason' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved'  => true,
            'approved_at'  => now(),
            'approved_by'  => 1,
            'rejected_at'  => null,
            'rejected_by'  => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved'     => false,
            'rejected_at'     => now(),
            'rejected_by'     => 1,
            'rejected_reason' => 'Contenido inapropiado',
        ]);
    }
}
