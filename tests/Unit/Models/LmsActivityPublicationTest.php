<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsActivityPublicationTest extends TestCase
{
    use RefreshDatabase;

    private function makePublication(array $overrides = []): LmsActivityPublication
    {
        return LmsActivityPublication::factory()->make($overrides);
    }

    public function test_is_visible_to_students_returns_true_when_published_without_dates(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => null,
            'unpublish_at' => null,
        ]);

        $this->assertTrue($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_draft(): void
    {
        $publication = $this->makePublication(['status' => 'DRAFT']);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_scheduled(): void
    {
        $publication = $this->makePublication(['status' => 'SCHEDULED']);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_before_publish_at(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => now()->addDay(),
        ]);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_after_unpublish_at(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'unpublish_at' => now()->subDay(),
        ]);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_archived(): void
    {
        $publication = $this->makePublication(['status' => 'ARCHIVED']);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_scope_visible_now_filters_correctly(): void
    {
        LmsActivityPublication::factory()->create(['status' => 'PUBLISHED']);
        LmsActivityPublication::factory()->create(['status' => 'DRAFT']);
        LmsActivityPublication::factory()->create(['status' => 'PUBLISHED', 'publish_at' => now()->addDay()]);
        LmsActivityPublication::factory()->create(['status' => 'ARCHIVED']);

        $visible = LmsActivityPublication::visibleNow()->get();

        $this->assertCount(1, $visible);
    }
}
