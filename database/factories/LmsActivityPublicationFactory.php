<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Database\Eloquent\Factories\Factory;

class LmsActivityPublicationFactory extends Factory
{
    protected $model = LmsActivityPublication::class;

    public function definition(): array
    {
        return [
            'activity_id'    => Activity::factory(),
            'published_by'   => User::factory(),
            'status'         => 'DRAFT',
            'allow_comments' => true,
            'allow_downloads' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn(array $attrs) => [
            'status'       => 'PUBLISHED',
            'published_at' => now(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn(array $attrs) => [
            'status'     => 'SCHEDULED',
            'publish_at' => now()->addHour(),
        ]);
    }
}
