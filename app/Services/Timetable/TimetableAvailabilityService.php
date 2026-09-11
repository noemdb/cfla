<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableTeacherAvailability;

class TimetableAvailabilityService
{
    /** @var array<string, \Illuminate\Support\Collection> */
    private array $rowsByScope = [];

    public function isAvailable(
        int $calendarId,
        int $profesorId,
        TimetablePeriod $period,
    ): bool {
        $scope = implode(':', [$calendarId, $profesorId, $period->shift_id, $period->day_of_week]);
        $rows = $this->rowsByScope[$scope] ??= TimetableTeacherAvailability::query()
            ->where('calendar_id', $calendarId)
            ->where('profesor_id', $profesorId)
            ->where('shift_id', $period->shift_id)
            ->where('day_of_week', $period->day_of_week)
            ->get(['start_time', 'end_time', 'order_in_day', 'is_available']);

        foreach ($rows as $row) {
            if ($row->start_time === null || $row->end_time === null) {
                if ((int) $row->order_in_day === (int) $period->order_in_day && ! $row->is_available) {
                    return false;
                }

                continue;
            }

            $overlaps = $this->overlaps(
                $row->start_time,
                $row->end_time,
                (string) $period->start_time,
                (string) $period->end_time,
            );

            if ($overlaps && ! $row->is_available) {
                return false;
            }
        }

        return true;
    }

    private function overlaps(string $startA, string $endA, string $startB, string $endB): bool
    {
        $aStart = $this->minutes($startA);
        $aEnd = $this->minutes($endA);
        $bStart = $this->minutes($startB);
        $bEnd = $this->minutes($endB);

        return $aStart < $bEnd && $bStart < $aEnd;
    }

    private function minutes(string $time): int
    {
        [$hours, $minutes] = array_pad(array_map('intval', explode(':', $time)), 2, 0);

        return ($hours * 60) + $minutes;
    }
}
