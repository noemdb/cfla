<?php

namespace Tests\Unit\Timetable;

use App\Services\Timetable\Solver\SchedulingContext;
use PHPUnit\Framework\TestCase;

/**
 * ADR-TT-017 — ocupación por sub-franja (mitad de bloque).
 *
 * `half = null` ocupa/libera ambas mitades; `0`/`1` solo la suya.
 */
class HalfBlockSchedulingContextTest extends TestCase
{
    public function test_half_block_occupies_only_its_half(): void
    {
        $context = new SchedulingContext(2);

        // Lección (1) en la mitad superior del período 10.
        $context->occupy(10, 1, 20, null, null, false, 1, false, 0);

        $this->assertFalse(
            $context->isFree(10, 1, 20, null, null, false, 2, false, 0),
            'La misma sub-franja está ocupada.',
        );
        $this->assertTrue(
            $context->isFree(10, 1, 20, null, null, false, 3, false, 1),
            'La otra mitad del período sigue libre.',
        );
    }

    public function test_full_block_conflicts_with_any_half(): void
    {
        $context = new SchedulingContext(2);

        // Bloque completo (half = null) del período 10.
        $context->occupy(10, 1, 20, null, null, false, 1);

        // Mismo docente y sección: ninguna mitad puede entrar.
        $this->assertFalse($context->isFree(10, 1, 20, null, null, false, 2, false, 0));
        $this->assertFalse($context->isFree(10, 1, 20, null, null, false, 2, false, 1));
        $this->assertFalse($context->isFree(10, 1, 20, null, null, false, 2, false, null));
    }

    public function test_half_block_blocks_full_block(): void
    {
        $context = new SchedulingContext(2);

        $context->occupy(10, 1, 20, null, null, false, 1, false, 0);

        $this->assertFalse(
            $context->isFree(10, 2, 20, null, null, false, 2),
            'Un bloque completo no cabe si una mitad está ocupada.',
        );
    }

    public function test_teacher_reusable_across_halves(): void
    {
        $context = new SchedulingContext(2);

        $context->occupy(10, 1, 20, null, null, false, 1, false, 0);

        $this->assertTrue($context->isFree(10, 1, 30, null, null, false, 2, false, 1));
        $this->assertFalse($context->isFree(10, 1, 30, null, null, false, 2, false, 0));
    }

    public function test_room_reusable_across_halves(): void
    {
        $context = new SchedulingContext(2);

        $context->occupy(10, 1, 20, 500, null, false, 1, false, 0);

        $this->assertTrue($context->isFree(10, 2, 30, 500, null, false, 2, false, 1));
        $this->assertFalse($context->isFree(10, 2, 30, 500, null, false, 2, false, 0));
    }

    public function test_release_frees_both_halves_for_full_block(): void
    {
        $context = new SchedulingContext(2);

        $context->occupy(10, 1, 20, 500, null, false, 1);
        $context->release(10, 1, 20, 500, null, false, 1);

        $this->assertTrue($context->isFree(10, 1, 20, 500, null, false, 1, false, 0));
        $this->assertTrue($context->isFree(10, 1, 20, 500, null, false, 1, false, 1));
    }
}
