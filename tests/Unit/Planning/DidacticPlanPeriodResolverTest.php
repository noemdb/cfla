<?php

namespace Tests\Unit\Planning;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use App\Services\Planning\DidacticPlanPeriodResolver;
use Tests\TestCase;

class DidacticPlanPeriodResolverTest extends TestCase
{
    private DidacticPlanPeriodResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new DidacticPlanPeriodResolver;
    }

    public function test_range_comes_from_valid_activity_dates(): void
    {
        $activities = [
            $this->activity('2026-11-17', '2026-11-17'),
            $this->activity('2026-11-20', '2026-11-20'),
            $this->activity('0000-00-00', null),
            $this->activity(null, null),
        ];

        $range = $this->resolver->resolveRange($activities);

        $this->assertSame('activities', $range['origin']);
        $this->assertSame('2026-11-17', $range['start']);
        $this->assertSame('2026-11-20', $range['end']);
    }

    public function test_dates_outside_the_lapso_are_ignored(): void
    {
        $lapso = new Lapso(['finicial' => '2026-09-14', 'ffinal' => '2026-12-03']);
        $activities = [
            $this->activity('2026-08-01', '2026-08-01'),
            $this->activity('2026-11-17', '2026-11-17'),
            $this->activity('2026-12-20', '2026-12-20'),
        ];

        $range = $this->resolver->resolveRange($activities, $lapso);

        $this->assertSame('activities', $range['origin']);
        $this->assertSame('2026-11-17', $range['start']);
        $this->assertSame('2026-11-17', $range['end']);
    }

    public function test_falls_back_to_lapso_when_there_are_no_valid_dates(): void
    {
        $lapso = new Lapso(['finicial' => '2026-09-14', 'ffinal' => '2026-12-03']);

        $range = $this->resolver->resolveRange([$this->activity(null, null)], $lapso);

        $this->assertSame('lapso', $range['origin']);
        $this->assertSame('2026-09-14', $range['start']);
        $this->assertSame('2026-12-03', $range['end']);
    }

    public function test_empty_when_no_dates_and_no_lapso(): void
    {
        $range = $this->resolver->resolveRange([]);

        $this->assertNull($range['start']);
        $this->assertNull($range['end']);
        $this->assertNull($range['origin']);
    }

    public function test_weeks_are_monday_to_sunday_and_can_be_cut(): void
    {
        $weeks = $this->resolver->weeks('2026-11-17', '2026-11-24');

        $this->assertCount(2, $weeks);
        $this->assertSame('2026-11-17', $weeks[0]['period_start']);
        $this->assertSame('2026-11-22', $weeks[0]['period_end']);
        $this->assertSame(6, $weeks[0]['days']);
        $this->assertSame('2026-11-23', $weeks[1]['period_start']);
        $this->assertSame('2026-11-24', $weeks[1]['period_end']);
        $this->assertSame(2, $weeks[1]['days']);
    }

    public function test_full_week_is_seven_days(): void
    {
        $weeks = $this->resolver->weeks('2026-11-16', '2026-11-22');

        $this->assertCount(1, $weeks);
        $this->assertSame(7, $weeks[0]['days']);
    }

    public function test_validate_week_rejects_ranges_over_seven_days(): void
    {
        $this->assertSame([], $this->resolver->validateWeek('2026-11-16', '2026-11-22'));
        $this->assertNotEmpty($this->resolver->validateWeek('2026-11-16', '2026-11-24'));
        $this->assertNotEmpty($this->resolver->validateWeek('2026-11-22', '2026-11-16'));
    }

    public function test_activities_in_week_uses_overlap(): void
    {
        $activities = [
            $this->activity('2026-11-17', '2026-11-17'),
            $this->activity('2026-11-20', '2026-11-23'),
            $this->activity('2026-11-24', '2026-11-24'),
        ];

        $filtered = $this->resolver->activitiesInWeek($activities, '2026-11-17', '2026-11-21');

        $this->assertCount(2, $filtered);
    }

    private function activity(?string $start, ?string $end): Activity
    {
        return new Activity([
            'finicial' => $start,
            'ffinal' => $end,
        ]);
    }
}
