<?php

namespace Tests\Unit\Timetable;

use App\Services\Timetable\Solver\SchedulingContext;
use PHPUnit\Framework\TestCase;

class SchedulingContextTest extends TestCase
{
    public function test_full_section_lesson_cannot_share_period_with_half_group(): void
    {
        $context = new SchedulingContext(2);

        $context->occupy(10, 1, 20, null, null, true);

        $this->assertFalse($context->isFree(10, 2, 20, null, null, false));
    }

    public function test_two_half_group_lessons_can_share_period_without_full_section_lesson(): void
    {
        $context = new SchedulingContext(2);

        $context->occupy(10, 1, 20, null, null, true);

        $this->assertTrue($context->isFree(10, 2, 20, null, null, true));
    }
}
